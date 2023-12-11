<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sortable_tables
 */

/**
 * Hook class.
 */
class Hook_addon_registry_sortable_tables
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
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'ocProducts';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array('Matt Kruse');
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
        return 'This addon creates a main_sortable_table block that shows the contents of a CSV file (or a database table) in a sortable and filterable table. To use, place the CSV file in uploads/website_specific, and place the block like:
[code]
[block=""example.csv""]main_sortable_table[/block]
[/code]

(example.csv is supplied with the addon)

We will automatically detect what columns can be filtered, how to sort each column, and display numbers in an attractive way.';
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
            'sources_custom/hooks/systems/addon_registry/sortable_tables.php',
            'lang_custom/EN/sortable_tables.ini',
            'themes/default/javascript_custom/sortable_tables.js',
            'themes/default/templates_custom/SORTABLE_TABLE.tpl',
            'themes/default/templates_custom/SORTABLE_TABLE_ROW.tpl',
            'themes/default/css_custom/sortable_tables.css',
            'sources_custom/blocks/main_sortable_table.php',
            'uploads/website_specific/example.csv',
            'themes/default/images_custom/sortable_tables/filter.gif',
            'themes/default/images_custom/sortable_tables/index.html',
            'themes/default/images_custom/sortable_tables/sortable.gif',
            'themes/default/images_custom/sortable_tables/sorted_down.gif',
            'themes/default/images_custom/sortable_tables/sorted_up.gif',
        );
    }
}
