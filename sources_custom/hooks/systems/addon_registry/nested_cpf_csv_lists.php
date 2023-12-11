<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_csv_lists
 */

/**
 * Hook class.
 */
class Hook_addon_registry_nested_cpf_csv_lists
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
        return 'Common Public Attribution License';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Allows complex chained dropdown choices of custom profile fields.

Set Custom Profile Field list fields to have a default value like [tt]countries.csv|country[/tt] to source list options from CSV files under [tt]/private_data[/tt]. You can set up chained list fields (e.g. chain a state field to a country field), via a syntax like [tt]countries.csv|state|countries.csv|country[/tt]. You can use this with multiple CSV files to essentially use CSV files like normalised database tables (hence why countries.csv is repeated twice in the example). The first line in the CSV file is for the header names (which [tt]country[/tt] and [tt]state[/tt] reference in these examples).';
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
            'requires' => array(
                'PHP5.2',
            ),
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
            'sources_custom/hooks/systems/addon_registry/nested_cpf_csv_lists.php',
            'sources_custom/miniblocks/nested_csv_lists_javascript.php',
            'sources_custom/nested_csv.php',
            'themes/default/javascript_custom/custom_globals.js',
            'lang_custom/EN/nested_csv.ini',
        );
    }
}
