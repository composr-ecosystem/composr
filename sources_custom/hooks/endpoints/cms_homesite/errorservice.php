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
class Hook_endpoint_cms_homesite_errorservice
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
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
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }

        require_code('errorservice');

        $version = get_param_string('version', $id);
        $error_message = post_param_string('error_message', false, INPUT_FILTER_GET_COMPLEX);
        $error_message = html_entity_decode($error_message);

        $output = get_problem_match_nearest($error_message);
        if ($output !== null) {
            return [
                'matched_error' => $output
            ];
        }

        return ['success' => false, 'error_details' => 'Could not find this error. It is possible it is not common enough for us to provide information on it.'];
    }
}
