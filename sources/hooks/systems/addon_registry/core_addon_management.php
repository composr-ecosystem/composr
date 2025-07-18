<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_addon_management
 */

/**
 * Hook class.
 */
class Hook_addon_registry_core_addon_management
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
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Install or uninstall addons.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_framework',
        );
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
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/adminzone/structure/addons.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/adminzone/structure/addons.png',
            'themes/default/images/icons/48x48/menu/adminzone/structure/addons.png',
            'themes/default/images/icons/24x24/menu/_generic_admin/component.png',
            'themes/default/images/icons/48x48/menu/_generic_admin/component.png',
            'themes/default/css/addons_editor.css',
            'sources/hooks/systems/addon_registry/core_addon_management.php',
            'sources/addons.php',
            'sources/addons2.php',
            'themes/default/templates/ADDON_SCREEN_ADDON.tpl',
            'themes/default/templates/ADDON_MULTI_CONFIRM_SCREEN.tpl',
            'themes/default/templates/ADDON_SCREEN.tpl',
            'themes/default/templates/ADDON_EXPORT_FILE_CHOICE.tpl',
            'themes/default/templates/ADDON_EXPORT_LINE.tpl',
            'themes/default/templates/ADDON_EXPORT_SCREEN.tpl',
            'themes/default/templates/ADDON_INSTALL_CONFIRM_SCREEN.tpl',
            'themes/default/templates/ADDON_INSTALL_FILES.tpl',
            'themes/default/templates/ADDON_INSTALL_FILES_WARNING.tpl',
            'themes/default/templates/ADDON_INSTALL_WARNING.tpl',
            'themes/default/templates/ADDON_UNINSTALL_CONFIRM_SCREEN.tpl',
            'themes/default/templates/MODULE_SCREEN.tpl',
            'themes/default/templates/ADDON_NAME.tpl',
            'adminzone/pages/modules/admin_addons.php',
            'exports/index.html',
            'exports/addons/index.html',
            'imports/index.html',
            'imports/.htaccess',
            'imports/addons/.htaccess',
            'imports/addons/index.html',
            'lang/EN/addons.ini',
            'sources/hooks/systems/ajax_tree/choose_composr_homesite_addon.php',
            'themes/default/images/icons/14x14/install.png',
            'themes/default/images/icons/14x14/reinstall.png',
            'themes/default/images/icons/14x14/upgrade.png',
            'themes/default/images/icons/28x28/install.png',
            'themes/default/images/icons/28x28/reinstall.png',
            'themes/default/images/icons/28x28/upgrade.png',
        );
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return array(
            'templates/ADDON_SCREEN.tpl' => 'administrative__addon_screen',
            'templates/ADDON_SCREEN_ADDON.tpl' => 'administrative__addon_screen',
            'templates/ADDON_NAME.tpl' => 'administrative__addon_screen',
            'templates/ADDON_MULTI_CONFIRM_SCREEN.tpl' => 'administrative__addon_multi_confirm_screen',
            'templates/ADDON_INSTALL_CONFIRM_SCREEN.tpl' => 'administrative__addon_install_confirm_screen',
            'templates/ADDON_UNINSTALL_CONFIRM_SCREEN.tpl' => 'administrative__addon_uninstall_confirm_screen',
            'templates/ADDON_EXPORT_LINE.tpl' => 'administrative__addon_export_screen',
            'templates/ADDON_EXPORT_FILE_CHOICE.tpl' => 'administrative__addon_export_screen',
            'templates/ADDON_EXPORT_SCREEN.tpl' => 'administrative__addon_export_screen',
            'templates/MODULE_SCREEN.tpl' => 'administrative__module_screen',
            'templates/ADDON_INSTALL_FILES_WARNING.tpl' => 'administrative__addon_install_confirm_screen',
            'templates/ADDON_INSTALL_FILES.tpl' => 'administrative__addon_install_confirm_screen',
            'templates/ADDON_INSTALL_WARNING.tpl' => 'administrative__addon_install_confirm_screen'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__addon_screen()
    {
        $add_ons = new Tempcode();

        foreach (placeholder_array() as $value) {
            $actions = do_lorem_template('COLUMNED_TABLE_ACTION_INSTALL_ENTRY', array(
                'GET' => true,
                'NAME' => lorem_phrase(),
                'HIDDEN' => '',
                'URL' => placeholder_url(),
            ));
            $status = do_lang_tempcode('STATUS_NOT_INSTALLED');

            $pretty_name = do_lorem_template('ADDON_NAME', array('IMAGE_URL' => placeholder_image_url(), 'NAME' => lorem_word()));

            $add_ons->attach(do_lorem_template('ADDON_SCREEN_ADDON', array(
                'DESCRIPTION' => lorem_paragraph(),
                'DESCRIPTION_PARSED' => lorem_paragraph_html(),
                'FILE_LIST' => array(lorem_phrase()),
                'COLOUR' => 'orange',
                'STATUS' => $status,
                'NAME' => $value,
                'PRETTY_NAME' => $pretty_name,
                'FILENAME' => lorem_word(),
                'AUTHOR' => lorem_phrase(),
                'ORGANISATION' => lorem_word(),
                'VERSION' => placeholder_number(),
                'CATEGORY' => lorem_phrase(),
                'COPYRIGHT_ATTRIBUTION' => lorem_phrase(),
                'LICENCE' => lorem_phrase(),
                'ACTIONS' => $actions,
                'TYPE' => lorem_phrase(),
                'PASSTHROUGH' => lorem_phrase(),
                'UPDATED' => false,
                'BUNDLED' => true,
            )));
        }

        return array(
            lorem_globalise(do_lorem_template('ADDON_SCREEN', array(
                'TITLE' => lorem_title(),
                'ADDONS' => $add_ons,
                'MULTI_ACTION' => placeholder_url(),
                'UPDATED_ADDONS' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__addon_multi_confirm_screen()
    {
        $install_files = new Tempcode();
        $uninstall_files = new Tempcode();

        $install = array(
            'news',
            'gallery'
        );
        $uninstall = array(
            'catalogues'
        );

        foreach ($install as $name) {
            $uninstall_files->attach('<li>' . $name . '</li>');
        }

        foreach ($uninstall as $file) {
            $install_files->attach('<li>' . $file . '</li>');
        }

        return array(
            lorem_globalise(do_lorem_template('ADDON_MULTI_CONFIRM_SCREEN', array(
                'TITLE' => lorem_title(),
                'HIDDEN' => '',
                'URL' => placeholder_url(),
                'INSTALL_FILES' => $install_files,
                'UNINSTALL_FILES' => $uninstall_files,
                'WARNINGS' => lorem_phrase(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__addon_install_confirm_screen()
    {
        $_dependencies = new Tempcode();
        foreach (placeholder_array() as $in) {
            if (!$_dependencies->is_empty()) {
                $_dependencies->attach(do_lang_tempcode('LIST_SEP'));
            }
            $_dependencies->attach(escape_html($in));
        }

        $warning = do_lorem_template('ADDON_INSTALL_WARNING', array(
            'WARNING' => do_lang_tempcode('ADDON_WARNING_PRESENT_DEPENDENCIES', $_dependencies, lorem_phrase()),
            'ADDON_WARNING_OVERWRITE' => lorem_phrase(),
        ));
        $files = new Tempcode();
        foreach (placeholder_array() as $k => $val) {
            $files->attach(do_lorem_template('ADDON_INSTALL_FILES_WARNING', array(
                'PATH' => lorem_phrase(),
                'ABOUT' => do_lang_tempcode('ADDON_FILE_WILL_OVERWRITE'),
                'I' => strval($k),
            )));
        }
        foreach (placeholder_array() as $k => $val) {
            $files->attach(do_lorem_template('ADDON_INSTALL_FILES', array(
                'PATH' => lorem_phrase(),
                'ABOUT' => do_lang_tempcode('ADDON_FILE_NORMAL'),
                'I' => strval(count(placeholder_array()) + $k),
            )));
        }
        return array(
            lorem_globalise(do_lorem_template('ADDON_INSTALL_CONFIRM_SCREEN', array(
                'TITLE' => lorem_title(),
                'FILE' => lorem_phrase(),
                'URL' => placeholder_url(),
                'FILES' => $files,
                'WARNINGS' => $warning,
                'NAME' => lorem_word(),
                'AUTHOR' => lorem_word_2(),
                'ORGANISATION' => lorem_phrase(),
                'VERSION' => placeholder_number(),
                'CATEGORY' => lorem_phrase(),
                'COPYRIGHT_ATTRIBUTION' => lorem_phrase(),
                'LICENCE' => lorem_phrase(),
                'DESCRIPTION' => lorem_paragraph_html(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__addon_uninstall_confirm_screen()
    {
        return array(
            lorem_globalise(do_lorem_template('ADDON_UNINSTALL_CONFIRM_SCREEN', array(
                'TITLE' => lorem_title(),
                'URL' => placeholder_url(),
                'NAME' => lorem_word(),
                'WARNINGS' => lorem_phrase(),
                'FILES' => placeholder_list_item(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__addon_export_screen()
    {
        $tpl_languages = new Tempcode();
        $tpl_themes = new Tempcode();

        foreach (array('en', 'mal') as $value) {
            $frm_langs = new Tempcode();
            $i = 0;
            foreach (placeholder_array() as $file) {
                $frm_langs->attach(form_input_hidden('file_' . strval($i), $file));
                $i++;
            }
            $tpl_languages->attach(do_lorem_template('ADDON_EXPORT_LINE', array(
                'NAME' => $value,
                'URL' => placeholder_url(),
                'FILES' => $frm_langs,
            )));
        }

        foreach (array('default', 'cms') as $value) {
            $frm_themes = new Tempcode();
            foreach (placeholder_array() as $file) {
                $frm_themes->attach(form_input_hidden('file_' . strval($i), $file));
                $i++;
            }
            $tpl_themes->attach(do_lorem_template('ADDON_EXPORT_LINE', array(
                'NAME' => $value,
                'URL' => placeholder_url(),
                'FILES' => $frm_themes,
            )));
        }

        $frm_files = new Tempcode();
        $i = 0;
        foreach (placeholder_array() as $file) {
            $frm_files->attach(do_lorem_template('ADDON_EXPORT_FILE_CHOICE', array(
                'ID' => strval($i),
                'PATH' => $file,
            )));
            $i++;
        }

        return array(
            lorem_globalise(do_lorem_template('ADDON_EXPORT_SCREEN', array(
                'TITLE' => lorem_title(),
                'LANGUAGES' => $tpl_languages,
                'URL' => placeholder_url(),
                'FILES' => $frm_files,
                'THEMES' => $tpl_themes,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__module_screen()
    {
        $modules = array(
            'gallery',
            'downloads',
            'catalogues'
        );
        $tpl_modules = array();
        foreach ($modules as $module) {
            $actions = new Tempcode();
            $status = do_lang_tempcode('STATUS_TO_UPGRADE');

            if ($module == 'downloads') {
                $actions->attach(do_lorem_template('COLUMNED_TABLE_ACTION_REINSTALL_ENTRY', array(
                    'HIDDEN' => '',
                    'NAME' => lorem_phrase(),
                    'URL' => placeholder_url(),
                )));
                $status = do_lang_tempcode('STATUS_CURRENT');
            } else {
                $actions->attach(do_lorem_template('COLUMNED_TABLE_ACTION_UPGRADE_ENTRY', array(
                    'HIDDEN' => '',
                    'NAME' => lorem_phrase(),
                    'URL' => placeholder_url(),
                )));
            }

            $tpl_modules[] = array(
                'STATUS' => $status,
                'NAME' => $module,
                'AUTHOR' => lorem_word(),
                'ORGANISATION' => lorem_phrase(),
                'VERSION' => placeholder_number(),
                'HACKED_BY' => lorem_word_2(),
                'HACK_VERSION' => placeholder_number(),
                'ACTIONS' => $actions,
            );
        }

        return array(
            lorem_globalise(do_lorem_template('MODULE_SCREEN', array(
                'TITLE' => lorem_title(),
                'MODULES' => $tpl_modules,
            )), null, '', true)
        );
    }
}
