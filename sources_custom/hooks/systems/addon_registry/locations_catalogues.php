<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    locations_catalogues
 */

/**
 * Hook class.
 */
class Hook_addon_registry_locations_catalogues
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Locations API, allows building out tree catalogues with all the world cities.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/locations_catalogues.php',
            'data_custom/locations/index.html',
            'data_custom/locations/sources.zip',
            'data_custom/locations/readme.txt',
            'sources_custom/locations_catalogues_install.php',
            'sources_custom/locations_catalogues_geopositioning.php',
            'data_custom/locations_catalogues_geoposition.php',
        );
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
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('locations', array(
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
            ));
            $GLOBALS['SITE_DB']->create_index('locations', 'l_place', array('l_place'));
            $GLOBALS['SITE_DB']->create_index('locations', 'l_country', array('l_country'));
            $GLOBALS['SITE_DB']->create_index('locations', 'l_latitude', array('l_latitude'));
            $GLOBALS['SITE_DB']->create_index('locations', 'l_longitude', array('l_longitude'));
            //$GLOBALS['SITE_DB']->create_index('locations', 'l_postcode', array('l_postcode'));
        }
    }
}
