<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    visualisation
 */

/**
 * Hook class.
 */
class Hook_addon_registry_visualisation
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
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
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
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Information Display';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return ['Matt Kruse'];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon provides various visualisation blocks for Composr, including:
 - Sortable tables served from spreadsheets or database tables ([tt]main_sortable_table[/tt])
 - Bar charts from spreadsheets ([tt]graph_bar_chart[/tt])
 - Line charts from spreadsheets ([tt]graph_line_chart[/tt])
 - Pie charts from spreadsheets ([tt]graph_pie_chart[/tt])
 - Scatter diagrams from spreadsheets ([tt]graph_scatter_diagram[/tt])
 - Maps with pins from spreadsheets ([tt]pins_on_map[/tt])
 - Maps with country data overlaid from spreadsheets ([tt]countries_on_map[/tt])

To use the [tt]main_sortable_table[/tt] block, place a [abbr="Comma-separated Values"]CSV[/abbr] spreadsheet file in [tt]uploads/website_specific[/tt], and place the block on a Comcode page like:
[code]
[block=""example.csv""]main_sortable_table[/block]
[/code]
([tt]example.csv[/tt] is supplied with the addon)
We will automatically detect what columns can be filtered, how to sort each column, and display numbers in an attractive way. Additionally though, the block has many advanced options for customising the output.

Sample spreadsheet files for all the other blocks are provided under [tt]uploads/website_specific/graph_test/[/tt] and a page of sample usage is provided at [tt]site:_graph_test[/tt]. There is currently no UI/documentation for how to use the blocks, but if you look at the code you can see what parameters are available.';
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
                //'core_all_icons',
                'IE 11+',
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
            'sources_custom/hooks/systems/addon_registry/visualisation.php',
            'sources_custom/hooks/systems/trusted_sites/visualisation.php',
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
            'uploads/website_specific/graph_test/stacked_bar_chart.csv',
            'uploads/website_specific/graph_test/bubble_bar_chart.csv',
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
            'sources_custom/miniblocks/graph_stacked_bar_chart.php',
            'sources_custom/miniblocks/graph_bubble_bar_chart.php',
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
            'site/pages/minimodules_custom/_graph_color_pool.php',
        ];
    }
}
