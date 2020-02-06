<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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
        return 'New Features';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return ['Matt Kruse'];
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
        return 'This addon creates a main_sortable_table block that shows the contents of a CSV spreadsheet file (or a database table) in a sortable and filterable table. To use, place the [abbr="Comma-separated Values"]CSV[/abbr] spreadsheet file in [tt]uploads/website_specific[/tt], and place the block like:
[code]
[block=""example.csv""]main_sortable_table[/block]
[/code]

([tt]example.csv[/tt] is supplied with the addon)

We will automatically detect what columns can be filtered, how to sort each column, and display numbers in an attractive way.

Additionally, there are blocks for rendering graphs and maps from CSV spreadsheet files. This is not currently documented, although sample spreadsheet files are provided under [tt]uploads/website_specific/graph_test/[/tt] and a page of sample usage is provided at [tt]site:_graph_test[/tt].';
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
                'all_icons',
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
            'sources_custom/hooks/systems/addon_registry/sortable_tables.php',
            'lang_custom/EN/sortable_tables.ini',
            'themes/default/javascript_custom/sortable_tables.js',
            'themes/default/templates_custom/SORTABLE_TABLE.tpl',
            'themes/default/templates_custom/SORTABLE_TABLE_ROW.tpl',
            'themes/default/css_custom/sortable_tables.css',
            'sources_custom/blocks/main_sortable_table.php',
            'uploads/website_specific/example.csv',
            'uploads/website_specific/graph_test/bar_chart.csv',
            'uploads/website_specific/graph_test/countries_on_map.csv',
            'uploads/website_specific/graph_test/line_chart.csv',
            'uploads/website_specific/graph_test/pie_chart.csv',
            'uploads/website_specific/graph_test/pins_on_map.csv',
            'uploads/website_specific/graph_test/scatter_diagram.csv',
            'uploads/website_specific/graph_test/index.html',
            'themes/default/images_custom/icons/sortable_tables/index.html',
            'themes/default/images_custom/icons/sortable_tables/sorted_down.svg',
            'themes/default/images_custom/icons/sortable_tables/sorted_up.svg',
            'themes/default/images_custom/icons_monochrome/sortable_tables/index.html',
            'themes/default/images_custom/icons_monochrome/sortable_tables/sorted_down.svg',
            'themes/default/images_custom/icons_monochrome/sortable_tables/sorted_up.svg',
            'sources_custom/maps.php',
            'sources_custom/miniblocks/countries_on_map.php',
            'sources_custom/miniblocks/graph_bar_chart.php',
            'sources_custom/miniblocks/graph_line_chart.php',
            'sources_custom/miniblocks/graph_pie_chart.php',
            'sources_custom/miniblocks/graph_scatter_diagram.php',
            'sources_custom/miniblocks/pins_on_map.php',
            'themes/default/templates_custom/COUNTRIES_ON_MAP.tpl',
            'themes/default/templates_custom/PINS_ON_MAP.tpl',
            'site/pages/comcode_custom/EN/_graph_test.txt',
            'themes/default/images/icons/sortable_tables/filter.svg',
            'themes/default/images/icons/sortable_tables/sortable.svg',
            'themes/default/images/icons/sortable_tables/index.html',
            'themes/default/images/icons_monochrome/sortable_tables/filter.svg',
            'themes/default/images/icons_monochrome/sortable_tables/sortable.svg',
            'themes/default/images/icons_monochrome/sortable_tables/index.html',
        ];
    }
}
