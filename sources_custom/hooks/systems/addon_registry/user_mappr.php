<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update b3986a40bf55bd057113c0d854180f0e
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        // Best to just categorise properly as it's not bundled
        //return is_maintained('google_maps') ? 'Information Display' : 'Development';
        return 'Information Display';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'temp1024 / Chris Graham / Kamen Blaginov';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
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
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
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
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/get_member_tooltip.php',
            'data_custom/set_coordinates.php',
            'lang_custom/EN/google_map_users.ini',
            'sources_custom/blocks/main_google_map_users.php',
            'sources_custom/hooks/systems/addon_registry/user_mappr.php',
            'sources_custom/hooks/systems/cns_cpf_filter/latitude.php',
            'sources_custom/hooks/systems/fields/float.php',
            'sources_custom/hooks/systems/trusted_sites/user_mappr.php',
            'sources_custom/hooks/systems/upon_query/google_maps_users.php',
            'themes/default/javascript_custom/user_mappr.js',
            'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_MAP_USERS.tpl',
            'themes/default/templates_custom/FORM_SCREEN_INPUT_MAP_POSITION.tpl',
        ];
    }
}
