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
                if (!array_key_exists('nonce', $data) || !array_key_exists('encrypted_data', $data) || !array_key_exists('encrypted_session_key', $data) || !array_key_exists('version', $data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Invalid telemetry data sent.'];
                }

                // Decrypt our message
                require_code('encryption');
                $_data = decrypt_data_telemetry($data['nonce'], $data['encrypted_data'], $data['encrypted_session_key'], floatval($data['version']));
                $decrypted_data = @unserialize($_data);
                if (($decrypted_data === false) || !is_array($decrypted_data) || !array_key_exists('website_url', $decrypted_data) || !array_key_exists('error_message', $decrypted_data)) {
                    http_response_code(400);
                    return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
                }

                $error_message = $decrypted_data['error_message'];

                if ($error_message == 'TEST') { // Unit test; return dummy ID
                    return ['success' => true, 'relayed_error_id' => 0];
                }

                // FUDGE: Ignore cases that might not yet be mainstream in the relay_error_notification function for the software
                if (
                    (strpos($error_message, 'Cannot write to ') !== false) ||
                    (strpos($error_message, 'telemetry: ') !== false)
                ) {
                    return ['success' => true, 'error_details' => '(This relay was ignored)'];
                }

                // For our error hash, start with the full error message
                $_error_hash = $error_message;

                // Now we must remove unnecessary unique junk from the error message to improve duplicate checking capabilities
                $_error_hash = preg_replace('/[a-fA-F0-9]{13,}/', '', $_error_hash); // Remove what might be MD5 hashes, uniqids, or GUIDs
                $_error_hash = preg_replace('/\$\S{59,}/', '', $_error_hash); // Remove bcrypt hashes
                $_error_hash = preg_replace('/\d{8,}/', '', $_error_hash); // Remove numbers with 8 or more digits... probably a timestamp or identifier
                $_error_hash = preg_replace('/tcpfunc_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/do_runtime_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/string_attach_([0-9a-zA-Z_\.])*/', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/\$(keep_)?tpl_funcs\[\'([^\'\]]*)\'\]/i', '', $_error_hash); // Tempcode uses unique IDs
                $_error_hash = preg_replace('/([\d,\.])* (second|seconds|minute|minutes|hour|hours|day|days|month|months|week|weeks|year|years) ago/i', '', $_error_hash); // Contextual dates

                // Actual generate the hash with what we have left
                $error_hash = md5($_error_hash);

                // See if this error was already reported
                $row = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['id', 'error_count', 'note', 'resolved'], [
                    // Every relay is specific to a website; treat separate websites as separate relays
                    'website_url' => $decrypted_data['website_url'],

                    'error_hash' => $error_hash,

                    // We want to treat same errors from different versions as a new / separate telemetry relays (this indicates the error might still be present even after a fix was attempted)
                    'e_version' => $decrypted_data['version'],
                ]);

                // See if this error should be ignored (auto-resolved)
                $auto_resolve = null;
                $ignore_rows = $GLOBALS['SITE_DB']->query_select('relayed_errors_ignore', ['id', 'ignore_string'], []);
                foreach ($ignore_rows as $ignore_row) {
                    if (strpos($error_message, $ignore_row['ignore_string']) !== false) {
                        $_auto_resolve = $GLOBALS['SITE_DB']->query_select_value('relayed_errors_ignore', 'resolve_message', ['id' => $ignore_row['id']]);
                        $auto_resolve = get_translated_text($_auto_resolve);
                        break;
                    }
                }

                // Clear the checklist cache so we have an updated number
                require_code('caches');
                delete_cache_entry('main_staff_checklist');

                if (array_key_exists(0, $row)) { // We have a match; just update the matched record
                    $map = [
                        'last_date_and_time' => time(),
                        'error_count' => $row[0]['error_count'] + 1,
                    ];
                    $GLOBALS['SITE_DB']->query_update('relayed_errors', $map, ['id' => $row[0]['id']]);

                    // Also auto-resolve it when necessary
                    if (($auto_resolve !== null) && ($row[0]['resolved'] == 0)) {
                        $map = ['resolved' => 1];
                        $map += lang_remap_comcode('note', $row[0]['note'], $auto_resolve);
                        $GLOBALS['SITE_DB']->query_update('relayed_errors', $map, ['id' => $row[0]['id']]);
                    }
                    return ['success' => true, 'relayed_error_id' => $row[0]['id']];
                } else { // No match; create a new relay
                    $map = [
                        'first_date_and_time' => time(),
                        'last_date_and_time' => time(),
                        'website_url' => $decrypted_data['website_url'],
                        'e_version' => $decrypted_data['version'],
                        'error_message' => $decrypted_data['error_message'],
                        'error_hash' => $error_hash,
                        'error_count' => 1,
                        'resolved' => 0,
                    ];
                    if ($auto_resolve !== null) { // Auto-resolve it when necessary
                        $map['resolved'] = 1;
                        $map += insert_lang_comcode('note', $auto_resolve, 4);
                    } else {
                        $map += insert_lang_comcode('note', '', 4);
                    }
                    $id = $GLOBALS['SITE_DB']->query_insert('relayed_errors', $map, true);
                    return ['success' => true, 'relayed_error_id' => $id];
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

            default:
                return []; // Not implemented
        }
    }
}
