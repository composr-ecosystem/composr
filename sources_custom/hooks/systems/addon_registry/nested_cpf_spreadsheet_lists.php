<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_spreadsheet_lists
 */

/**
 * Hook class.
 */
class Hook_addon_registry_nested_cpf_spreadsheet_lists
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
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
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
        return 'Common Public Attribution License';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Allows complex chained dropdown choices of Custom Profile Fields.

Set Custom Profile Field list fields to have a default value like [tt]countries.csv|country[/tt] to source list options from CSV spreadsheet files under [tt]/private_data[/tt]. You can set up chained list fields (e.g. chain a state field to a country field), via a syntax like [tt]countries.csv|state|countries.csv|country[/tt]. You can use this with multiple spreadsheet files to essentially use spreadsheet files like normalised database tables (hence why [tt]countries.csv[/tt] is repeated twice in the example). The first line in the spreadsheet file is for the header names (which [tt]country[/tt] and [tt]state[/tt] reference in these examples).';
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
     * @return array File permissions to set
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
            'sources_custom/hooks/systems/addon_registry/nested_cpf_spreadsheet_lists.php',
            'sources_custom/nested_spreadsheet.php',
            'sources_custom/hooks/systems/contentious_overrides/nested_cpf_spreadsheet_lists.php',
            'lang_custom/EN/nested_spreadsheet.ini',
        ];
    }
}
