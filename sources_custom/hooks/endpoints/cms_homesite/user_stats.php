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
class Hook_endpoint_cms_homesite_user_stats
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

        return [
            'authorization' => false,
            'log_stats_event' => 'cms_homesite/user_stats',
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
        $site_id = $site[0]['id'];

        // Flood control
        $existing = $GLOBALS['SITE_DB']->query_select_value('telemetry_stats', 'COUNT(*)', ['s_site' => $site_id], ' AND date_and_time>' . strval(time() - 60 * 60));
        if ($existing > 0) {
            http_response_code(429);
            return ['success' => false, 'error_details' => 'You may only report telemetry stats once per hour.'];
        }

        // Decrypt our message
        require_code('encryption');
        $_data = decrypt_data_site_telemetry($data['nonce'], $data['encrypted_data'], $public_key, $sign_public_key, floatval($data['version']));
        $decrypted_data = @unserialize($_data);
        if (($decrypted_data === false) || !is_array($decrypted_data) || !array_key_exists('count_members', $decrypted_data) || !array_key_exists('count_daily_hits', $decrypted_data)) {
            http_response_code(400);
            return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
        }

        $GLOBALS['SITE_DB']->query_insert('telemetry_stats', [
            's_site' => $site_id,
            'software_version' => $decrypted_data['version'],
            'date_and_time' => time(),
            'count_members' => $decrypted_data['count_members'],
            'count_daily_hits' => $decrypted_data['count_daily_hits'],
        ]);

        $GLOBALS['SITE_DB']->query_update('telemetry_sites', [
            'software_version' => $decrypted_data['version'],
            'website_installed' => 'Yes',
        ], ['s_site' => $site_id]);

        if ($id === '_LEGACY_') { // LEGACY
            echo serialize([]);
            exit();
        }

        return ['success' => true];
    }
}
