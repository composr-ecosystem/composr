<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_release_build
 */

/**
 * Hook class.
 */
class Hook_addon_registry_cms_release_build
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
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.2'; // addon_version_auto_update 9703c7a0b3c3f66770f0eaee08642952
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
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'The Composr release build platform. It should be run from a developers machine, not the server / homesite.

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
                'cms_homesite',
                'cms_homesite_tracker',
                'composr_tutorials',
            ],
            'conflicts_with' => [],
            'previously_in_addon' => ['composr_release_build'],
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
            '_config.php.template',
            'adminzone/pages/minimodules_custom/plug_guid.php',
            'adminzone/pages/modules_custom/admin_make_release.php',
            'adminzone/pages/modules_custom/admin_modularisation.php',
            'adminzone/pages/modules_custom/admin_push_bugfix.php',
            'aps/.htaccess',
            'aps/APP-LIST.xml',
            'aps/APP-META.xml',
            'aps/images/.htaccess',
            'aps/images/icon.png',
            'aps/images/index.html',
            'aps/images/screenshot.png',
            'aps/index.html',
            'aps/scripts/.htaccess',
            'aps/scripts/configure',
            'aps/scripts/index.html',
            'aps/scripts/templates/.htaccess',
            'aps/scripts/templates/_config.php.in',
            'aps/scripts/templates/index.html',
            'aps/test/.htaccess',
            'aps/test/TEST-META.xml',
            'aps/test/composrIDEtest.xml',
            'aps/test/index.html',
            'data_custom/build_db_meta_file.php',
            'data_custom/build_rewrite_rules.php',
            'data_custom/builds/index.html',
            'data_custom/builds/readme.txt',
            'data_custom/execute_temp.php.bundle',
            'exports/builds/build/index.html',
            'exports/builds/hotfixes/index.html',
            'exports/builds/index.html',
            'install.sql',
            'lang_custom/EN/cms_release_build.ini',
            'sources_custom/hooks/systems/addon_registry/cms_release_build.php',
            'sources_custom/hooks/systems/page_groupings/make_release.php',
            'sources_custom/make_release.php',
            'sources_custom/modularisation.php',
            'sources_custom/modularisation2.php',
            'themes/admin/javascript_custom/push_bugfix.js',
            'themes/admin/templates_custom/ADMIN_PUSH_BUGFIX_STEP2.tpl',
            'themes/default/templates_custom/MAKE_RELEASE_STEP4_SCREEN.tpl',
        ];
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them.
     *
     * @return array The mapping
     */
    public function tpl_previews() : array
    {
        return [
            'templates_custom/ADMIN_PUSH_BUGFIX_STEP2.tpl' => 'administrative__admin_push_bugfix_step2',
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return Tempcode Preview
     */
    public function tpl_preview__administrative__admin_push_bugfix_step2() : object
    {
        return lorem_globalise(do_lorem_template('ADMIN_PUSH_BUGFIX_STEP2', [
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => '',
            'TITLE' => lorem_word(),
            'TEXT' => lorem_paragraph_html(),
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => placeholder_form(),
            'URL' => placeholder_url(),

            'REMOTE_BASE_URL' => placeholder_url(),
            'GIT_FOUND' => placeholder_array(),
            'DEFAULT_PROJECT_ID' => placeholder_number(),
        ]), null, '', true);
    }
}
