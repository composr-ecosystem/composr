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
 * @package    commandr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_commandr
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
        return 'A command-line environment for managing your website, designed for Linux/Unix lovers.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_commandr',
            'tut_repository',
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
        return 'themes/default/images/icons/48x48/menu/adminzone/tools/commandr.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/adminzone/tools/commandr.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/commandr.png',
            'themes/default/images/icons/24x24/tool_buttons/commandr_off.png',
            'themes/default/images/icons/24x24/tool_buttons/commandr_on.png',
            'themes/default/images/icons/48x48/tool_buttons/commandr_off.png',
            'themes/default/images/icons/48x48/tool_buttons/commandr_on.png',
            'sources/hooks/systems/commandr_commands/find_guid_via_id.php',
            'sources/hooks/systems/commandr_commands/find_id_via_guid.php',
            'sources/hooks/systems/commandr_commands/find_id_via_label.php',
            'sources/hooks/systems/commandr_commands/find_id_via_commandr_fs_filename.php',
            'sources/hooks/systems/commandr_commands/find_label_via_id.php',
            'sources/hooks/systems/commandr_commands/find_commandr_fs_filename_via_id.php',
            'sources/hooks/systems/commandr_fs_extended_config/index.html',
            'sources_custom/hooks/systems/commandr_fs_extended_config/index.html',
            'sources/hooks/systems/commandr_fs_extended_config/.htaccess',
            'sources_custom/hooks/systems/commandr_fs_extended_config/.htaccess',
            'sources/hooks/systems/commandr_fs_extended_member/index.html',
            'sources_custom/hooks/systems/commandr_fs_extended_member/index.html',
            'sources/hooks/systems/commandr_fs_extended_member/.htaccess',
            'sources_custom/hooks/systems/commandr_fs_extended_member/.htaccess',
            'sources/hooks/systems/commandr_fs_extended_config/privileges.php',
            'sources/hooks/systems/config/bottom_show_commandr_button.php',
            'sources/hooks/systems/config/commandr_chat_announce.php',
            'sources/hooks/systems/commandr_fs/.htaccess',
            'sources_custom/hooks/systems/commandr_fs/.htaccess',
            'sources/hooks/systems/commandr_notifications/.htaccess',
            'sources_custom/hooks/systems/commandr_notifications/.htaccess',
            'sources/hooks/systems/addon_registry/commandr.php',
            'sources/hooks/systems/commandr_commands/antispam_check.php',
            'sources/hooks/systems/commandr_commands/set_comment_forum.php',
            'sources/resource_fs.php',
            'sources/resource_fs_base_class.php',
            'data/modules/admin_commandr/.htaccess',
            'themes/default/templates/COMMANDR_HELP.tpl',
            'themes/default/templates/COMMANDR_LS.tpl',
            'themes/default/templates/COMMANDR_MAIN.tpl',
            'themes/default/javascript/commandr.js',
            'themes/default/templates/COMMANDR_ARRAY.tpl',
            'themes/default/templates/COMMANDR_BOX.tpl',
            'themes/default/templates/COMMANDR_CHAT_NOTIFICATION.tpl',
            'themes/default/templates/COMMANDR_COMMAND.tpl',
            'themes/default/templates/COMMANDR_COMMANDS.tpl',
            'themes/default/templates/COMMANDR_EDIT.tpl',
            'themes/default/templates/COMMANDR_ENTRY_POINTS.tpl',
            'themes/default/templates/COMMANDR_FIND_CODES.tpl',
            'themes/default/templates/COMMANDR_MAIN_SCREEN.tpl',
            'themes/default/templates/COMMANDR_NOTIFICATION.tpl',
            'themes/default/templates/COMMANDR_COMMANDRCHAT_NOTIFICATION.tpl',
            'themes/default/templates/COMMANDR_CNS_NOTIFICATION.tpl',
            'themes/default/templates/COMMANDR_PT_NOTIFICATION.tpl',
            'themes/default/templates/COMMANDR_RSS.tpl',
            'themes/default/templates/COMMANDR_USERS_ONLINE.tpl',
            'themes/default/templates/COMMANDR_WHOIS.tpl',
            'themes/default/javascript/button_commandr.js',
            'adminzone/pages/modules/admin_commandr.php',
            'themes/default/css/commandr.css',
            'data/modules/admin_commandr/admin_commandrsample_script',
            'data/modules/admin_commandr/index.html',
            'data/modules/admin_commandr/sample_script',
            'data/modules/admin_commandr/test_script',
            'data/commandr.php',
            'lang/EN/commandr.ini',
            'sources/hooks/systems/commandr_commands/.htaccess',
            'sources_custom/hooks/systems/commandr_commands/.htaccess',
            'sources/hooks/systems/commandr_commands/alien_check.php',
            'sources/hooks/systems/commandr_commands/directory_sizes.php',
            'sources/hooks/systems/commandr_commands/db_table_sizes.php',
            'sources/hooks/systems/commandr_commands/sql_dump.php',
            'sources/hooks/systems/commandr_commands/db_search.php',
            'sources/hooks/systems/commandr_commands/append.php',
            'sources/hooks/systems/commandr_commands/ban_ip.php',
            'sources/hooks/systems/commandr_commands/ban_member.php',
            'sources/hooks/systems/commandr_commands/bsod.php',
            'sources/hooks/systems/commandr_commands/phpinfo.php',
            'sources/hooks/systems/commandr_commands/call.php',
            'sources/hooks/systems/commandr_commands/cat.php',
            'sources/hooks/systems/commandr_commands/cd.php',
            'sources/hooks/systems/commandr_commands/clear.php',
            'sources/hooks/systems/commandr_commands/clear_caches.php',
            'sources/hooks/systems/commandr_commands/closed.php',
            'sources/hooks/systems/commandr_commands/commands.php',
            'sources/hooks/systems/commandr_commands/cp.php',
            'sources/hooks/systems/commandr_commands/cpdir.php',
            'sources/hooks/systems/commandr_commands/date.php',
            'sources/hooks/systems/commandr_commands/echo.php',
            'sources/hooks/systems/commandr_commands/edit.php',
            'sources/hooks/systems/commandr_commands/exit.php',
            'sources/hooks/systems/commandr_commands/feedback.php',
            'sources/hooks/systems/commandr_commands/find.php',
            'sources/hooks/systems/commandr_commands/find_codes.php',
            'sources/hooks/systems/commandr_commands/find_entry_points.php',
            'sources/hooks/systems/commandr_commands/fix_perms.php',
            'sources/hooks/systems/commandr_commands/grep.php',
            'sources/hooks/systems/commandr_commands/help.php',
            'sources/hooks/systems/commandr_commands/index.html',
            'sources_custom/hooks/systems/commandr_commands/index.html',
            'sources/hooks/systems/commandr_commands/ls.php',
            'sources/hooks/systems/commandr_commands/mkdir.php',
            'sources/hooks/systems/commandr_commands/mv.php',
            'sources/hooks/systems/commandr_commands/mvdir.php',
            'sources/hooks/systems/commandr_commands/commandrchat.php',
            'sources/hooks/systems/commandr_commands/passwd.php',
            'sources/hooks/systems/commandr_commands/pwd.php',
            'sources/hooks/systems/commandr_commands/read.php',
            'sources/hooks/systems/commandr_commands/reset.php',
            'sources/hooks/systems/commandr_commands/rm.php',
            'sources/hooks/systems/commandr_commands/rmdir.php',
            'sources/hooks/systems/commandr_commands/time.php',
            'sources/hooks/systems/commandr_commands/untar.php',
            'sources/hooks/systems/commandr_commands/users_online.php',
            'sources/hooks/systems/commandr_commands/version.php',
            'sources/hooks/systems/commandr_commands/whoami.php',
            'sources/hooks/systems/commandr_commands/whois.php',
            'sources/hooks/systems/commandr_commands/write.php',
            'sources/hooks/systems/commandr_commands/database_upgrade.php',
            'sources/hooks/systems/commandr_commands/check_perms.php',
            'sources/hooks/systems/commandr_commands/integrity_check.php',
            'sources/hooks/systems/commandr_commands/deep_clean.php',
            'sources/hooks/systems/commandr_commands/obfuscate_directory.php',
            'sources/hooks/systems/commandr_fs/bin.php',
            'sources/hooks/systems/commandr_fs/database.php',
            'sources/hooks/systems/commandr_fs/etc.php',
            'sources/hooks/systems/commandr_fs/index.html',
            'sources_custom/hooks/systems/commandr_fs/index.html',
            'sources/hooks/systems/commandr_fs/raw.php',
            'sources/hooks/systems/commandr_fs/root.php',
            'sources/hooks/systems/commandr_notifications/index.html',
            'sources_custom/hooks/systems/commandr_notifications/index.html',
            'sources/hooks/systems/commandr_notifications/commandrchat.php',
            'sources/hooks/systems/page_groupings/commandr.php',
            'sources/hooks/systems/snippets/commandr.php',
            'sources/commandr.php',
            'sources/commandr_fs.php',
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
            'templates/COMMANDR_MAIN.tpl' => 'administrative__commandr_main',
            'templates/COMMANDR_MAIN_SCREEN.tpl' => 'administrative__commandr_main_screen',
            'templates/COMMANDR_COMMAND.tpl' => 'administrative__commandr_command',
            'templates/COMMANDR_ARRAY.tpl' => 'administrative__commandr_array',
            'templates/COMMANDR_NOTIFICATION.tpl' => 'administrative__commandr_command',
            'templates/COMMANDR_HELP.tpl' => 'administrative__commandr_help',
            'templates/COMMANDR_BOX.tpl' => 'administrative__commandr_box',
            'templates/COMMANDR_COMMANDS.tpl' => 'administrative__commandr_commands',
            'templates/COMMANDR_RSS.tpl' => 'administrative__commandr_rss',
            'templates/COMMANDR_EDIT.tpl' => 'administrative__commandr_edit',
            'templates/COMMANDR_USERS_ONLINE.tpl' => 'administrative__commandr_users_online',
            'templates/COMMANDR_FIND_CODES.tpl' => 'administrative__commandr_find_codes',
            'templates/COMMANDR_WHOIS.tpl' => 'administrative__commandr_whois',
            'templates/COMMANDR_ENTRY_POINTS.tpl' => 'administrative__commandr_entry_points',
            'templates/COMMANDR_LS.tpl' => 'administrative__commandr_ls',
            'templates/COMMANDR_CHAT_NOTIFICATION.tpl' => 'administrative__commandr_chat_notification',
            'templates/COMMANDR_COMMANDRCHAT_NOTIFICATION.tpl' => 'administrative__commandr_commandrchat_notification',
            'templates/COMMANDR_CNS_NOTIFICATION.tpl' => 'administrative__commandr_cns_notification',
            'templates/COMMANDR_PT_NOTIFICATION.tpl' => 'administrative__commandr_pt_notification'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__commandr_main()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_MAIN', array(
                'COMMANDS' => lorem_phrase(),
                'SUBMIT_URL' => placeholder_url(),
                'page' => lorem_phrase(),
                'PROMPT' => lorem_phrase(),
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
    public function tpl_preview__administrative__commandr_main_screen()
    {
        $content = do_lorem_template('COMMANDR_MAIN', array(
            'SUBMIT_URL' => placeholder_url(),
            'PROMPT' => lorem_phrase(),
            'COMMANDS' => lorem_phrase(),
        ));

        return array(
            lorem_globalise(do_lorem_template('COMMANDR_MAIN_SCREEN', array(
                'TITLE' => lorem_title(),
                'CONTENT' => $content,
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
    public function tpl_preview__administrative__commandr_command()
    {
        $notifications = do_lorem_template('COMMANDR_NOTIFICATION', array(
            'SECTION' => lorem_phrase(),
            'TYPE' => lorem_phrase(),
            'NOTIFICATION_CONTENT' => lorem_phrase(),
        ));

        return array(
            lorem_globalise(do_lorem_template('COMMANDR_COMMAND', array(
                'NOTIFICATIONS' => $notifications,
                'METHOD' => lorem_phrase(),
                'STDOUT' => lorem_phrase(),
                'STDHTML' => lorem_phrase(),
                'STDCOMMAND' => lorem_word_2(),
                'STDERR' => lorem_phrase(),
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
    public function tpl_preview__administrative__commandr_array()
    {
        $elements = array();
        $elements[] = array(
            'KEY' => lorem_phrase(),
            'VALUE' => lorem_phrase(),
        );

        return array(
            lorem_globalise(do_lorem_template('COMMANDR_ARRAY', array(
                'ELEMENTS' => $elements,
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
    public function tpl_preview__administrative__commandr_help()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_HELP', array(
                'INTRODUCTION' => lorem_phrase(),
                'OPTIONS' => placeholder_array(),
                'PARAMETERS' => placeholder_array(),
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
    public function tpl_preview__administrative__commandr_box()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_BOX', array(
                'HTML' => lorem_sentence_html(),
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
    public function tpl_preview__administrative__commandr_commands()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_COMMANDS', array(
                'COMMANDS' => placeholder_array(),
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
    public function tpl_preview__administrative__commandr_rss()
    {
        $content = array();
        $content[] = array(
            'FULL_URL' => placeholder_url(),
            'NEWS_TITLE' => lorem_phrase(),
            'DATE' => placeholder_date(),
            'SUMMARY' => lorem_paragraph(),
        );

        return array(
            lorem_globalise(do_lorem_template('COMMANDR_RSS', array(
                'TITLE' => lorem_phrase(),
                'CONTENT' => $content,
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
    public function tpl_preview__administrative__commandr_edit()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_EDIT', array(
                'FILE' => lorem_phrase(),
                'SUBMIT_URL' => placeholder_url(),
                'FILE_CONTENTS' => lorem_sentence(),
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
    public function tpl_preview__administrative__commandr_users_online()
    {
        $memb = array();
        foreach (placeholder_array() as $k => $v) {
            $memb[$v] = $k;
        }
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_USERS_ONLINE', array(
                'MEMBERS' => $memb,
                'GUESTS' => placeholder_number(),
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
    public function tpl_preview__administrative__commandr_find_codes()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_FIND_CODES', array(
                'FILES' => placeholder_array(),
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
    public function tpl_preview__administrative__commandr_whois()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_WHOIS', array(
                'STATS' => lorem_phrase(),
                'IP_LIST' => placeholder_list_item(),
                'ID' => placeholder_id(),
                'IP' => placeholder_ip(),
                'NAME' => lorem_word(),
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
    public function tpl_preview__administrative__commandr_entry_points()
    {
        $points = array();
        foreach (placeholder_array() as $k => $v) {
            $points[$v] = $k;
        }
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_ENTRY_POINTS', array(
                'ENTRY_POINTS' => $points,
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
    public function tpl_preview__administrative__commandr_ls()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_LS', array(
                'DIRECTORY' => lorem_phrase(),
                'DIRECTORIES' => placeholder_array(),
                'FILES' => placeholder_array(),
                'FILENAME' => lorem_word(),
                'FILESIZE' => lorem_word(),
                'MTIME' => lorem_word(),
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
    public function tpl_preview__administrative__commandr_chat_notification()
    {
        if (!addon_installed('chat')) {
            return array();
        }

        require_lang('chat');

        $rooms = array();
        foreach (placeholder_array() as $k => $v) {
            $rooms[$v] = $k;
        }
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_CHAT_NOTIFICATION', array(
                'MESSAGE_COUNT' => placeholder_number(),
                'ROOMS' => $rooms,
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
    public function tpl_preview__administrative__commandr_commandrchat_notification()
    {
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_COMMANDRCHAT_NOTIFICATION', array(
                'MESSAGE_COUNT' => placeholder_number(),
                'MESSAGES' => placeholder_array(),
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
    public function tpl_preview__administrative__commandr_cns_notification()
    {
        $topics = array();
        foreach (placeholder_array() as $val) {
            $topics[] = array(
                '_loop_var' => placeholder_url(),
                '_loop_key' => lorem_word(),
            );
        }
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_CNS_NOTIFICATION', array(
                'TOPICS' => $topics,
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
    public function tpl_preview__administrative__commandr_pt_notification()
    {
        $topics = array();
        foreach (placeholder_array() as $val) {
            $topics[] = array(
                '_loop_var' => placeholder_url(),
                '_loop_key' => lorem_word(),
            );
        }
        return array(
            lorem_globalise(do_lorem_template('COMMANDR_PT_NOTIFICATION', array(
                'TOPICS' => $topics,
            )), null, '', true)
        );
    }
}
