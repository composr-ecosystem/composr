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
class Hook_endpoint_cms_homesite_get_session
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
        require_code('telemetry');

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

        // Decrypt our message (this is just to validate that the request probably came from a Composr site)
        $_data = decrypt_data_telemetry($data['nonce'], $data['encrypted_data'], $data['encrypted_session_key'], floatval($data['version']));
        $decrypted_data = @unserialize($_data);
        if (($decrypted_data === false) || !is_array($decrypted_data) || !array_key_exists('request', $decrypted_data) || ($decrypted_data['request'] != 'get_session')) {
            http_response_code(400);
            return ['success' => false, 'error_details' => 'Telemetry data sent is corrupt and cannot be decrypted.'];
        }

        return [
            //'session_id' => symbol_tempcode('SESSION'), // Disabled by default; not secure
            'session_id_hashed' => symbol_tempcode('SESSION_HASHED')->evaluate(),
        ];
    }
}
