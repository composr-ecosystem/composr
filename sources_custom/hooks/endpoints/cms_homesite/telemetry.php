<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_telemetry
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('downloads')) {
            return null;
        }
        if (!addon_installed('news')) {
            return null;
        }

        return [
            'authorization' => false,
            'log_stats_event' => 'cms_homesite/telemetry',
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        require_code('cms_homesite');

        switch ($type) {
            case 'add':
                // The JSON payload was coerced by the main endpoints script
                $data = json_decode($_POST['data'], true);

                // Sanity checks
                if ($data === false) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent was not in JSON format.'];
                }
                if (!array_key_exists('nonce', $data) || !array_key_exists('encrypted_data', $data) || !array_key_exists('website_url', $data) || !array_key_exists('version', $data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Invalid telemetry data sent.'];
                }

                // Grab public keys; exit if the site is not yet registered
                $site = $GLOBALS['SITE_DB']->query_select('telemetry_sites', ['*'], ['website_url' => $data['website_url']]);
                if (!array_key_exists(0, $site)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'This website is not yet registered with the telemetry service.'];
                }
                $public_key = $site[0]['public_key'];
                $sign_public_key = $site[0]['sign_public_key'];

                // Decrypt our message
                require_code('encryption');
                $_data = decrypt_data_site_telemetry($data['nonce'], $data['encrypted_data'], $public_key, $sign_public_key, floatval($data['version']));
                $decrypted_data = @unserialize($_data);
                if (($decrypted_data === false) || !is_array($decrypted_data) || !array_key_exists('error_message', $decrypted_data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
                }

                $error_message = $decrypted_data['error_message'];

                // Prevent infinite loops where telemetry fails on an error about telemetry
                if (
                    (strpos($error_message, 'telemetry: ') !== false)
                ) {
                    return ['success' => true, 'error_details' => '(This relay was ignored)'];
                }

                // For our error hash, start with the full error message
                $_error_hash = $error_message;

                // Now we must remove unnecessary unique junk from the error message to improve duplicate checking capabilities
                $_error_hash = preg_replace('/[a-zA-Z0-9\-\_]{13,}/', '', $_error_hash); // Remove what might be hex hashes, uniqids, crypto strings, or GUIDs
                $_error_hash = preg_replace('/\$\S{59,}/', '', $_error_hash); // Remove bcrypt hashes
                $_error_hash = preg_replace('/\d{8,}/', '', $_error_hash); // Remove numbers with 8 or more digits... probably a timestamp or identifier
                $_error_hash = preg_replace('/tcpfunc_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/do_runtime_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/string_attach_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/\$(keep_)?tpl_funcs\[\'([^\'\]]*)\'\]/i', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/([\d,\.])* (second|seconds|minute|minutes|hour|hours|day|days|month|months|week|weeks|year|years) ago/i', '', $_error_hash); // Contextual dates

                // Actual generate the hash with what we have left
                $error_hash = hash('sha256', $_error_hash);

                // See if this error was already reported
                $row = $GLOBALS['SITE_DB']->query_select('telemetry_errors', ['id', 'e_guid', 'e_error_count', 'e_note', 'e_resolved'], [
                    // Every relay is specific to a website; treat separate websites as separate relays
                    'e_site' => $site[0]['id'],

                    'e_error_hash' => $error_hash,

                    // We want to treat same errors from different versions as a new / separate telemetry relays (this indicates the error might still be present even after a fix was attempted)
                    'e_version' => $decrypted_data['version'],
                ], '', 1);

                // See if this error should be ignored (auto-resolved)
                $auto_resolve = null;
                $ignore_rows = $GLOBALS['SITE_DB']->query_select('telemetry_errors_ignore', ['id', 'ignore_string'], []);
                foreach ($ignore_rows as $ignore_row) {
                    if (strpos($error_message, $ignore_row['ignore_string']) !== false) {
                        $_auto_resolve = $GLOBALS['SITE_DB']->query_select_value('telemetry_errors_ignore', 'resolve_message', ['id' => $ignore_row['id']]);
                        $auto_resolve = get_translated_text($_auto_resolve);
                        break;
                    }
                }

                // Auto-resolve any errors matching what is in the error service spreadsheet
                if ($auto_resolve === null) {
                    require_code('errorservice');
                    $match = get_problem_match_nearest($error_message, false);
                    if ($match !== null) {
                        $auto_resolve = $match . "\n\n" . '[b]If you strongly feel this is a software bug, please report to the tracker.[/b]'; // FUDGE
                    }
                }

                // Clear the checklist cache so we have an updated number
                require_code('caches');
                delete_cache_entry('main_staff_checklist');

                if (array_key_exists(0, $row)) { // We have a match; just update the matched record
                    $map = [
                        'e_last_date_and_time' => time(),
                        'e_error_count' => $row[0]['e_error_count'] + 1,
                    ];

                    // Also auto-resolve it when necessary
                    if (($auto_resolve !== null) && ($row[0]['e_resolved'] == 0)) {
                        $map['e_resolved'] = 1;
                        $map += lang_remap_comcode('e_note', $row[0]['e_note'], $auto_resolve);
                    }

                    $GLOBALS['SITE_DB']->query_update('telemetry_errors', $map, ['id' => $row[0]['id']]);

                    return ['success' => true, 'relayed_error_id' => $row[0]['e_guid']];
                } else { // No match; create a new relay
                    $refs_compiled = (strpos($decrypted_data['error_message'], '_compiled/') !== false);
                    $refs_compiled = $refs_compiled || (strpos($decrypted_data['error_message'], '_compiled\\') !== false);

                    require_code('crypt');

                    do {
                        $guid = get_secure_random_v4_guid();
                        $check = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_errors', 'id', ['e_guid' => $guid]);
                    } while ($check !== null);

                    $map = [
                        'e_guid' => $guid,
                        'e_first_date_and_time' => time(),
                        'e_last_date_and_time' => time(),
                        'e_site' => $site[0]['id'],
                        'e_version' => $decrypted_data['version'],
                        'e_error_message' => $decrypted_data['error_message'],
                        'e_error_hash' => $error_hash,
                        'e_error_count' => 1,
                        'e_resolved' => 0,
                        'e_refs_compiled' => ($refs_compiled ? 1 : 0),
                    ];
                    if ($auto_resolve !== null) { // Auto-resolve it when necessary
                        $map['e_resolved'] = 1;
                        $map += insert_lang_comcode('e_note', $auto_resolve, 4);
                    } else {
                        $map += insert_lang_comcode('e_note', '', 4);
                    }
                    $GLOBALS['SITE_DB']->query_insert('telemetry_errors', $map);

                    return ['success' => true, 'relayed_error_id' => $guid];
                }
                break;

            case 'key':
                require_code('global3');
                $file = cms_file_get_contents_safe(get_file_base() . '/data_custom/keys/telemetry-' . strval($id) . '.pub');
                if ($file === false) {
                    http_response_code(404);
                    return ['success' => false, 'error_details' => 'A public key for the specified version was not found.'];
                }
                return ['success' => true, 'key' => $file];

            case 'register':
                // The JSON payload was coerced by the main endpoints script
                $data = json_decode($_POST['data'], true);

                // Sanity checks
                if ($data === false) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent was not in JSON format.'];
                }
                if (!array_key_exists('nonce', $data) || !array_key_exists('encrypted_data', $data) || !array_key_exists('encrypted_session_key', $data) || !array_key_exists('version', $data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Invalid telemetry data sent.'];
                }

                // Decrypt our message using the software keys
                require_code('encryption');
                $_data = decrypt_data_telemetry($data['nonce'], $data['encrypted_data'], $data['encrypted_session_key'], floatval($data['version']));
                $decrypted_data = @unserialize($_data);
                if (($decrypted_data === false) || !is_array($decrypted_data) || !array_key_exists('website_url', $decrypted_data) || !array_key_exists('website_name', $decrypted_data) || !array_key_exists('public_key', $decrypted_data) || !array_key_exists('sign_public_key', $decrypted_data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
                }

                // Same public and signing key provided? The site may have changed their base URL.
                $website_url = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_sites', 'website_url', ['public_key' => $decrypted_data['public_key'], 'sign_public_key' => $decrypted_data['sign_public_key']]);

                // Different keys? Check if the URL matches against one already registered; may be the same site but re-generated their keys
                if ($website_url === null) {
                    $website_url = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_sites', 'website_url', ['website_url' => $decrypted_data['website_url']]);
                }

                // Register the site
                if ($website_url !== null) {
                    $GLOBALS['SITE_DB']->query_update('telemetry_sites', [
                        'website_url' => $decrypted_data['website_url'],
                        'public_key' => $decrypted_data['public_key'],
                        'sign_public_key' => $decrypted_data['sign_public_key'],
                        'website_name' => $decrypted_data['website_name'],
                        'software_version' => $decrypted_data['version'],
                        'may_feature' => $decrypted_data['may_feature'],
                        'website_installed' => 'Yes',
                    ], ['website_url' => $website_url]);
                } else {
                    $GLOBALS['SITE_DB']->query_insert('telemetry_sites', [
                        'website_url' => $decrypted_data['website_url'],
                        'public_key' => $decrypted_data['public_key'],
                        'sign_public_key' => $decrypted_data['sign_public_key'],
                        'website_name' => $decrypted_data['website_name'],
                        'software_version' => $decrypted_data['version'],
                        'may_feature' => $decrypted_data['may_feature'],
                        'website_installed' => 'Yes',
                        'addons_installed' => serialize([]),
                    ]);
                }
                return ['success' => true];

            case 'is_registered':
                $website_url = substr(get_param_string('url', false, INPUT_FILTER_URL_GENERAL), 0, 255);
                $id = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_sites', 'id', ['website_url' => $website_url]);
                return ['success' => true, 'registered' => ($id !== null)];

            case 'get_data':
                // The JSON payload was coerced by the main endpoints script
                $data = json_decode($_POST['data'], true);

                // Sanity checks
                if ($data === false) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent was not in JSON format.'];
                }
                if (!array_key_exists('nonce', $data) || !array_key_exists('encrypted_data', $data) || !array_key_exists('website_url', $data) || !array_key_exists('version', $data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Invalid telemetry data sent.'];
                }

                // Grab public keys; exit if the site is not yet registered (we say successful but just indicate the site is not registered with a null)
                $site = $GLOBALS['SITE_DB']->query_select('telemetry_sites', ['*'], ['website_url' => $data['website_url']]);
                if (!array_key_exists(0, $site)) {
                    return ['success' => true, 'data' => null];
                }
                $site_id = $site[0]['id'];
                $public_key = $site[0]['public_key'];
                $sign_public_key = $site[0]['sign_public_key'];

                // Decrypt our message (this is just to validate that the request actually came from the site specified)
                require_code('encryption');
                $_data = decrypt_data_site_telemetry($data['nonce'], $data['encrypted_data'], $public_key, $sign_public_key, floatval($data['version']));
                if ($_data != $data['website_url']) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
                }

                // Include number of relayed errors by this site
                $site[0]['relayed_errors'] = $GLOBALS['SITE_DB']->query_select_value('telemetry_errors', 'SUM(e_error_count)', ['e_site' => $site_id]);

                // Include the latest stats
                $latest_stats = $GLOBALS['SITE_DB']->query_select('telemetry_stats', ['*'], ['s_site' => $site_id], ' ORDER BY date_and_time DESC', 1);
                if (array_key_exists(0, $latest_stats)) {
                    $site[0]['latest_stats'] = $latest_stats[0];
                } else {
                    $site[0]['latest_stats'] = null;
                }

                // Do not transmit the database record ID or the public keys
                unset($site[0]['id']);
                unset($site[0]['public_key']);
                unset($site[0]['sign_public_key']);

                return ['success' => true, 'data' => $site[0]];

            default:
                return ['success' => false, 'error_details' => 'Method not implemented.'];
        }
    }
}
