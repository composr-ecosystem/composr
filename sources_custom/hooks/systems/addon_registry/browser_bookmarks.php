<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    browser_bookmarks
 */

/**
 * Hook class.
 */
class Hook_addon_registry_browser_bookmarks
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Export the site-map as browser bookmarks.

Ever wished it was quicker to navigate around your site? If you\'re anything like us you get tired of having to move the mouse all around screens, and wait for half a dozen page loads, to get where you\'re going. There is an admin menu and sitemap in Composr, but that\'s still not as fast as a native desktop interface. Coming to the rescue is this simple addon that will export your whole sitemap to your web browsers bookmarks, so you can easily access anything on it. All you need to do is:
1) install the addon
2) call up /adminzone/?page=admin_generate_bookmarks
3) save the HTML file to your desktop
4) tell your web browser to import the HTML file as bookmarks/favorites (it\'s a Netscape/Mozilla/Firefox format bookmarks file)
5) And that\'s it- you\'ll be able to access all your links from a submenu of your regular browser bookmarks. (Note that there is no magic syncing here, so you might want to delete and reimport from time to time.)';
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
        return 'themes/default/images/icons/tool_buttons/sitemap.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/browser_bookmarks.php',
            'adminzone/pages/minimodules_custom/admin_generate_bookmarks.php',
            'sources_custom/hooks/systems/page_groupings/browser_bookmarks.php',
        ];
    }
}
