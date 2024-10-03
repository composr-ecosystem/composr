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

        $website_url = substr(either_param_string('url', false, INPUT_FILTER_URL_GENERAL), 0, 255);
        $website_name = substr(post_param_string('name', get_param_string('name', false, INPUT_FILTER_GET_COMPLEX)), 0, 255);

        require_code('version2');

        $version = either_param_string('version');
        $num_members = either_param_integer('num_members');
        $num_hits_per_day = either_param_integer('num_hits_per_day');
        $addons_installed = post_param_string('addons_installed', '');

        $GLOBALS['SITE_DB']->query_insert('logged', [
            'website_url' => $website_url,
            'website_name' => $website_name,
            'l_version' => $version,
            'hittime' => time(),
            'count_members' => $num_members,
            'num_hits_per_day' => $num_hits_per_day,
            'addons_installed' => $addons_installed,
        ]);

        if ($id === '_LEGACY_') { // LEGACY
            echo serialize([]);
            exit;
        }

        return [];
    }
}
