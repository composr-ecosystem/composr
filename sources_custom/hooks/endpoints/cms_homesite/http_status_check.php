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
class Hook_endpoint_cms_homesite_http_status_check
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
        $url = get_param_string('url', false, INPUT_FILTER_URL_GENERAL);
        $http_result = null;
        $result = null;
        $result_b = null;
        for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or Google issue
            $http_result = cms_http_request($url, ['convert_to_internal_encoding' => true, 'bytes_limit' => 0, 'trigger_error' => false]);
            $result = @strval($http_result->message);
            $result_b = @strval($http_result->message_b);

            if ($result === '200') {
                break;
            }
            if (php_function_allowed('usleep')) {
                usleep(5000000);
            }
        }

        return [
            'success' => ($result === '200'),
            'error_details' => $result_b,
            'status' => $result,
        ];
    }
}
