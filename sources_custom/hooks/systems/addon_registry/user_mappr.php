<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_mappr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_user_mappr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return is_maintained('google_maps') ? 'Information Display' : 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'temp1024 / Chris Graham / Kamen Blaginov';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'A {$IS_MAINTAINED,google_maps,google map} with markers of users locations.

The addon adds extra Custom Profile Fields to store members coordinates to store their latitude and logitude. The addon can automatically populate the members when members visit the block page. Members can edit their locations in their profile.

Before you can use the block you must first configure the API:
1) Configure the Google API Key in the configuration (Admin Zone > Configuration > Setup > Composr API options > Google API)
2) Make sure that Google Maps JavaScript API is enabled on Google\'s end
If you do not you may get a "Oops! Something went wrong." error and a corresponding "MissingKeyMapError" error in the browser console.

Parameters:
 - Title -- The Name of the block which will appear on screen for example Store Locator.
 - Description -- a Description of the block.
 - Width -- Defaults to 100% of the column.
 - Height -- Defaults to 300px but can be set to how ever many pixels (px) you need it to be.
 - Zoom -- A number between 1 and 17, the higher the number the more zoomed in the map will start at.
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return [
            'requires' => [
                'Conversr',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/user_mappr.php',
            'lang_custom/EN/google_map_users.ini',
            'sources_custom/blocks/main_google_map_users.php',
            'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_MAP_USERS.tpl',
            'themes/default/templates_custom/FORM_SCREEN_INPUT_MAP_POSITION.tpl',
            'sources_custom/hooks/systems/fields/float.php',
            'data_custom/set_coordinates.php',
            'sources_custom/hooks/systems/cns_cpf_filter/latitude.php',
            'data_custom/get_member_tooltip.php',
            'themes/default/javascript_custom/user_mappr.js',
            'sources_custom/hooks/systems/upon_query/google_maps_users.php',
        ];
    }
}
