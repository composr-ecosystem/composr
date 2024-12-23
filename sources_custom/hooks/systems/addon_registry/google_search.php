<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    google_search
 */

// FRAGILE: This code can not be automatically tested, but may break due to 3rd party API changes.

/**
 * Hook class.
 */
class Hook_addon_registry_google_search
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
        return '11'; // addon_version_auto_update 1731608409
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
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen Blaginov';
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
        return 'Embed Google site search onto your site.

This addon consists of two blocks: a side block with the google search form and a main block, where google results are displayed. When you install this addon it will create a standard results page called [tt]_google_search[/tt]; this page can be edited or customised in the same way as any other page.

In addition you can create your own page or put a [tt]main_google_results[/tt] block on an existing page. When you add the [tt]side_google_search[/tt] block you can choose a page_name parameter, but it is only needed if you are sending the results to a non-default page you have added the google results block on.

Example:
[code=\'Comcode\'][block id="xxx"]side_google_search[/block][/code]
The [tt]xxx[/tt] is what [url="Google provides"]https://cse.google.com/cse/[/url]. We use our own customised JavaScript rather than Google\'s, but we need the ID they embed in it. On the Google Look and feel settings set the layout to "Results only".

You must enable the "Allow unsafe JavaScript" for this addon to work, which will lower your security. Unfortunately Google require this.

Note that it is a requirement that your [tt]_google_search[/tt] page is in a zone where the side search block displays, as these two interface together once a search is initiated.';
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
        return 'themes/default/images/icons/buttons/search.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/google_search.ini',
            'pages/comcode_custom/EN/_google_search.txt',
            'sources_custom/blocks/main_google_results.php',
            'sources_custom/blocks/side_google_search.php',
            'sources_custom/hooks/systems/addon_registry/google_search.php',
            'sources_custom/hooks/systems/trusted_sites/google_search.php',
            'themes/default/css_custom/google_search.css',
            'themes/default/javascript_custom/google_search.js',
            'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_SEARCH_RESULTS.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_GOOGLE_SEARCH.tpl',
        ];
    }
}
