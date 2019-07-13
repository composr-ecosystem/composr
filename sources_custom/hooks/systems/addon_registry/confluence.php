<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    confluence
 */

/**
 * Hook class.
 */
class Hook_addon_registry_confluence
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
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
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array('Asa Kusuma');
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
        return 'Show a Confluence wiki on the site, under a [tt]site:docs[/tt] page.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/sync.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/confluence.php',
            'data_custom/confluence_proxy.php',
            'lang_custom/EN/confluence.ini',
            'sources_custom/confluence.php',
            'sources_custom/confluence2.php',
            'sources_custom/hooks/systems/config/confluence_cache_time.php',
            'sources_custom/hooks/systems/config/confluence_password.php',
            'sources_custom/hooks/systems/config/confluence_space.php',
            'sources_custom/hooks/systems/config/confluence_subdomain.php',
            'sources_custom/hooks/systems/config/confluence_username.php',
            'sources_custom/hooks/systems/sitemap/confluence.php',
            'themes/default/css_custom/confluence.css',
            'themes/default/templates_custom/CONFLUENCE_SCREEN.tpl',
            'site/pages/minimodules_custom/docs.php',
            'sources_custom/hooks/systems/health_checks/confluence.php',
            'themes/default/javascript_custom/confluence.js',
            'themes/default/javascript_custom/confluence2.js',
            'data_custom/webfonts/adgs-icons.eot',
            'data_custom/webfonts/adgs-icons.svg',
            'data_custom/webfonts/adgs-icons.ttf',
            'data_custom/webfonts/adgs-icons.woff',
            'data_custom/webfonts/index.html',
            'sources_custom/hooks/modules/search/confluence.php',
        );
    }
}
