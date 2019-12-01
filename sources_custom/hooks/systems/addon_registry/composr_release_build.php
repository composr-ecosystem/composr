<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
        return 'Development';
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
        return [];
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
        return 'The Composr release build platform. It should be run from a developers machine, not the server.

If running on Windows, you need to install the following commands in your path or C:\cygwin64...
 - Infozip\'s zip.exe (ftp://ftp.info-zip.org/pub/infozip/win32/zip231xn-x64.zip)
 - gzip.exe (http://gnuwin32.sourceforge.net/packages/gzip.htm), and gunzip.exe (copy gzip.exe to gunzip.exe)
 - tar.exe (http://gnuwin32.sourceforge.net/packages/gtar.htm)
You may want to put them in your git \'cmd\' directory, as that is in your path.
';
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
                'meta_toolkit',
            ],
            'recommends' => [
                'composr_homesite',
                'composr_homesite_support_credits',
                'composr_tutorials',
            ],
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
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
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
            'aps/.htaccess',
            'aps/images/.htaccess',
            'aps/images/index.html',
            'aps/index.html',
            'aps/scripts/.htaccess',
            'aps/scripts/index.html',
            'aps/scripts/templates/.htaccess',
            'aps/scripts/templates/index.html',
            'aps/test/.htaccess',
            'aps/test/index.html',
            'exports/builds/build/index.html',
            'exports/builds/hotfixes/index.html',
        ];
    }
}
