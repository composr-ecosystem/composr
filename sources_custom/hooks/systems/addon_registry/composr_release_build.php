<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'The Composr release build platform. It should be run from a developers machine, not the server.

If running on Windows, you need to install the following commands in your path or C:\cygwin64...
 - Infozip\'s zip.exe (ftp://ftp.info-zip.org/pub/infozip/win32/zip231xn-x64.zip)
 - gzip.exe (http://gnuwin32.sourceforge.net/packages/gzip.htm), and gunzip.exe (copy gzip.exe to gunzip.exe)
 - tar.exe (http://gnuwin32.sourceforge.net/packages/gtar.htm)
You may want to put them in your Git \'cmd\' directory, as that is in your path.
';
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
                'meta_toolkit',
                'MySQL',
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
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
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
