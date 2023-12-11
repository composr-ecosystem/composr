<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_search
 */

/**
 * Hook class.
 */
class Hook_addon_registry_google_search
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
        return 'Kamen Blaginov';
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
        return 'Embed Google site search onto your site.

This addon consists of two blocks: a side block with the google search form and a main block, where google results are displayed. When you install this addon it will create a standard results page called [tt]_google_search[/tt]; this page can be edited or customised in the same way as any other page.

In addition you can create your own page or put a [tt]main_google_results[/tt] block on an existing page. When you add the [tt]side_google_search[/tt] block you can choose a page_name parameter, but it is only needed if you are sending the results to a non-default page you have added the google results block on.

Example:
[code=\'Comcode\'][block id="xxx"]side_google_search[/block][/code]
The [tt]xxx[/tt] is from [tt]var cx = \'xxx\';[/tt] in the code [url="Google provides"]https://cse.google.com/cse/[/url]. We use our own customised JavaScript rather than Google\'s, but we need the ID they embed in it.

Note that it is a requirement that your [tt]_google_search[/tt] page is in a zone where the side search block displays, as these two interface together once a search is initiated.';
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
        return 'themes/default/images/icons/48x48/buttons/search.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/google_search.php',
            'lang_custom/EN/google_search.ini',
            'sources_custom/blocks/side_google_search.php',
            'sources_custom/blocks/main_google_results.php',
            'themes/default/templates_custom/BLOCK_SIDE_GOOGLE_SEARCH.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_SEARCH_RESULTS.tpl',
            'themes/default/css_custom/google_search.css',
            'pages/comcode_custom/EN/_google_search.txt',
        );
    }
}
