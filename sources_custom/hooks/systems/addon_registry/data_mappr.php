<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    data_mappr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_data_mappr
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
        return '11.0.0'; // addon_version_auto_update
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
        return 'Kamen Blaginov / Chris Graham / temp1024';
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
        return 'Shows different catalogue entries\' longitude/latitude values as pins on a {$IS_MAINTAINED,google_maps,Google map}. Clicking on the pin shows the catalogue entry in a little box (as a link to the entry).

Before you can use the block you must first configure the API:
1) Configure the Google API Key in the configuration (Admin Zone > Configuration > Setup > Composr API options > Google API)
2) Make sure that Google Maps JavaScript API is enabled on Google\'s end
If you do not you may get a "Oops! Something went wrong." error and a corresponding "MissingKeyMapError" error in the browser console.

The names of the fields to take longitude/latitude from are configured inside block parameters.

Example:
[code="Comcode"][block title="store locator" description="This is a Store Locator" latfield="Latitude" longfield="Longitude" catalogue="stores" width="100%" height="300px" zoom="6" latitude="24.2135" longitude="-1.4654"]main_google_map[/block][/code]

If you use the field names of exactly "Latitude" and "Longitude" then you\'ll get a visual location selector when adding entries. Otherwise you\'ll need to manually enter the coordinates. A tool like [url="https://itouchmap.com/latlong.html"]Latitude and Longitude of a Point[/url] can help you.

It is advisable to set the field options as [tt]decimal_points=6[/tt] for your latitude and longitude fields, otherwise there will not be enough precision.

Add at least 1 entry to your catalogue with the latitude and longitude fields filled in to see the block work.

When you add the block you see various block parameters to be filled in including:
 - title -- The Name of the block which will appear on screen (for example, Store Locator)
 - description -- a Description of the block
 - latfield -- This is the field you chose in the catalogue which has all the latitude coordinates in it
 - longfield -- This is the field you chose in the catalogue which has all the longitude coordinates in it
 - catalogue -- This is the name of the catalogue you chose and want to display the pins for
 - width -- Defaults to 100% of the column
 - height -- Defaults to 300px but can be set to how ever many pixels(px) you need it to be
 - zoom -- A number between 1 and 17, the higher the number the more zoomed in the map will start at
 - latitude -- The Latitude coordinates where you want the centre of the map to be when first loaded
 - longitude -- The Longitude coordinates where you want the centre of the map to be when first loaded

Coordinates of the Google map centre point and zoom level are configurable. You can find the coordinates by using the option in Google Maps Labs or via a tool like [url="https://itouchmap.com/latlong.html"]Latitude and Longitude of a Point[/url].
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
                'catalogues',
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
            'lang_custom/EN/google_map.ini',
            'sources_custom/blocks/main_google_map.php',
            'sources_custom/hooks/systems/addon_registry/data_mappr.php',
            'sources_custom/hooks/systems/contentious_overrides/data_mappr.php',
            'sources_custom/hooks/systems/fields/float.php',
            'sources_custom/hooks/systems/trusted_sites/data_mappr.php',
            'sources_custom/hooks/systems/upon_query/google_maps.php',
            'themes/default/images_custom/maps/index.html',
            'themes/default/images_custom/maps/star_highlight.svg',
            'themes/default/javascript_custom/data_mappr.js',
            'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_MAP.tpl',
            'themes/default/templates_custom/FORM_SCREEN_INPUT_MAP_POSITION.tpl',
        ];
    }
}
