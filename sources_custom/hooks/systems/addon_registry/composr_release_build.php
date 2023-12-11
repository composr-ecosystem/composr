<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/**
 * Hook class.
 */
class Hook_addon_registry_composr_release_build
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
        return 'The Composr release build platform. Should be run from a developers machine, not the server.';
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
                'meta_toolkit',
            ),
            'recommends' => array(
                'composr_homesite',
                'composr_homesite_support_credits',
                'composr_tutorials',
            ),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/composr_release_build.php',
            'data_custom/build_rewrite_rules.php',
            'sources_custom/make_release.php',
            'adminzone/pages/minimodules_custom/make_release.php',
            'sources_custom/hooks/systems/page_groupings/make_release.php',
            'adminzone/pages/minimodules_custom/push_bugfix.php',
            'adminzone/pages/minimodules_custom/plug_guid.php',
            'data_custom/build_db_meta_file.php',
            'exports/builds/index.html',
            'data_custom/builds/index.html',
            'data_custom/builds/readme.txt',
            '_config.php.template',
            'install.sql',
            'install1.sql',
            'install2.sql',
            'install3.sql',
            'install4.sql',
            'user.sql',
            'postinstall.sql',
            'parameters.xml',
            'manifest.xml',
            'data_custom/execute_temp.php.bundle',
            'aps/APP-LIST.xml',
            'aps/APP-META.xml',
            'aps/images/icon.png',
            'aps/images/screenshot.png',
            'aps/scripts/configure',
            'aps/scripts/templates/_config.php.in',
            'aps/test/composrIDEtest.xml',
            'aps/test/TEST-META.xml',
        );
    }
}
