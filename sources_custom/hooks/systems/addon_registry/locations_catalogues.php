<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    locations_catalogues
 */

/**
 * Hook class.
 */
class Hook_addon_registry_locations_catalogues
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
        return '11'; // addon_version_auto_update 1734831282
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
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
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
        return 'Locations API, allows building out tree catalogues with all the world cities.';
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
            'requires' => [],
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
            'data_custom/locations/index.html',
            'data_custom/locations/readme.txt',
            'data_custom/locations/sources.zip',
            'data_custom/locations_catalogues_geoposition.php',
            'sources_custom/hooks/systems/addon_registry/locations_catalogues.php',
            'sources_custom/locations_catalogues_geopositioning.php',
            'sources_custom/locations_catalogues_install.php',
        ];
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('locations');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('locations', [
                'id' => '*AUTO',
                'l_place' => 'SHORT_TEXT',
                'l_type' => 'ID_TEXT',
                'l_continent' => 'ID_TEXT',
                'l_country' => 'ID_TEXT',
                'l_parent_1' => 'ID_TEXT',
                'l_parent_2' => 'ID_TEXT',
                'l_parent_3' => 'ID_TEXT',
                'l_population' => '?INTEGER',
                'l_latitude' => '?REAL',
                'l_longitude' => '?REAL',
                //'l_postcode' => 'ID_TEXT',   Actually often many postcodes per location and/or poor alignment
            ]);
            $GLOBALS['SITE_DB']->create_index('locations', 'l_place', ['l_place']);
            $GLOBALS['SITE_DB']->create_index('locations', 'l_country', ['l_country']);
            $GLOBALS['SITE_DB']->create_index('locations', 'l_latitude', ['l_latitude']);
            $GLOBALS['SITE_DB']->create_index('locations', 'l_longitude', ['l_longitude']);
            //$GLOBALS['SITE_DB']->create_index('locations', 'l_postcode', ['l_postcode']);
        }
    }
}
