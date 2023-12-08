<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/*
For Demonstratr (personal demos / sites system)...

Note that you must define some additional passwords in _config.php...

$SITE_INFO['mysql_root_password']='xxx';
$SITE_INFO['mysql_demonstratr_password']='xxx';

You also need:
 - wildcard DNS configured
 - uploads/website_specific/compo.sr/demonstratr/template.sql and uploads/website_specific/compo.sr/demonstratr/template.tar defined appropriately
 - an uploads/website_specific/compo.sr/demonstratr/sites/demonstratr directory for sites to be built into
*/

/**
 * Module page class.
 */
class Module_sites
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('sites');
        $GLOBALS['SITE_DB']->drop_table_if_exists('sites_email');
        $GLOBALS['SITE_DB']->drop_table_if_exists('sites_deletion_codes');
        $GLOBALS['SITE_DB']->drop_table_if_exists('sites_advert_pings');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        $GLOBALS['SITE_DB']->create_table('sites', array(
            's_codename' => '*ID_TEXT',
            's_name' => 'SHORT_TEXT',
            's_description' => 'LONG_TEXT',
            's_category' => 'SHORT_TEXT', // Entertainment, Computers, Sport, Art, Music, Television/Movies, Businesses, Other, Informative/Factual, Political, Humour, Geographical/Regional, Games, Personal/Family, Hobbies, Culture/Community, Religious, Health
            's_domain_name' => 'SHORT_TEXT',
            's_server' => 'ID_TEXT',
            's_member_id' => 'MEMBER',
            's_add_time' => 'TIME',
            's_last_backup_time' => '?TIME',
            's_subscribed' => 'BINARY',
            's_sponsored_in_category' => 'BINARY',
            's_show_in_directory' => 'BINARY',
            's_sent_expire_message' => 'BINARY',
        ));
        $GLOBALS['SITE_DB']->create_index('sites', 'sponsored', array('s_sponsored_in_category'));
        $GLOBALS['SITE_DB']->create_index('sites', 'timeorder', array('s_add_time'));
        $GLOBALS['SITE_DB']->create_index('sites', '#s_name', array('s_name'));
        $GLOBALS['SITE_DB']->create_index('sites', '#s_description', array('s_description'));
        $GLOBALS['SITE_DB']->create_index('sites', '#s_codename', array('s_codename'));

        $GLOBALS['SITE_DB']->create_table('sites_email', array(
            's_codename' => '*ID_TEXT',
            's_email_from' => '*ID_TEXT',
            's_email_to' => 'SHORT_TEXT',
        ));

        $GLOBALS['SITE_DB']->create_table('sites_deletion_codes', array(
            's_codename' => '*ID_TEXT',
            's_code' => 'ID_TEXT',
            's_time' => 'TIME',
        ));

        $GLOBALS['SITE_DB']->create_table('sites_advert_pings', array(
            'id' => '*AUTO',
            's_codename' => 'ID_TEXT',
            's_time' => 'TIME',
        ));
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        $ret = array(
            'demonstratr' => array('CMS_ADD_SITE', 'menu/_generic_admin/add_one'),
        );

        if (addon_installed('search')) {
            $ret['hostingcopy_step1'] = array('HOSTING_COPY', 'menu/rich_content/downloads');
        }

        return $ret;
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('composr_homesite');

        if ($type == 'hostingcopy_step1' || $type == 'hostingcopy_step2' || $type == 'hostingcopy_step3') {
            $this->title = get_screen_title('HOSTING_COPY');
        }

        if ($type == 'demonstratr' || $type == '_demonstratr') {
            require_lang('sites');

            $this->title = get_screen_title('CMS_ADD_SITE');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        require_code('composr_homesite');
        require_lang('installer');
        require_lang('downloads');
        require_code('form_templates');
        require_lang('ecommerce');
        require_code('ecommerce');
        require_lang('cns');

        $type = get_param_string('type', 'browse');

        // Hosting copy
        if ($type == 'hostingcopy_step1') {
            return $this->hostingcopy_step1();
        }
        if ($type == 'hostingcopy_step2') {
            return $this->hostingcopy_step2();
        }
        if ($type == 'hostingcopy_step3') {
            return $this->hostingcopy_step3();
        }

        // Demonstratr (sites)
        if ($type == 'demonstratr') {
            return $this->demonstratr();
        }
        if ($type == '_demonstratr') {
            return $this->_demonstratr();
        }

        return new Tempcode();
    }

    /**
     * The UI to choose a path.
     *
     * @return Tempcode The UI.
     */
    public function hostingcopy_step1()
    {
        if (!addon_installed('search')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_lang('search');

        // Put together hosting-copy form
        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('FTP_DOMAIN'), '', 'ftp_domain', '', true));
        $fields->attach(form_input_line(do_lang_tempcode('FTP_USERNAME'), '', 'ftp_username', '', true));
        $fields->attach(form_input_password(do_lang_tempcode('FTP_PASSWORD'), '', 'ftp_password', true));
        $fields->attach(form_input_line(do_lang_tempcode('SEARCH_UNDERNEATH'), do_lang_tempcode('DESCRIPTION_FTP_SEARCH_UNDER'), 'search_under', '/', false));
        $post_url = build_url(array('page' => '_SELF', 'type' => 'hostingcopy_step2'), '_SELF');
        $submit_name = do_lang('PROCEED');
        return do_template('FORM_SCREEN', array('_GUID' => '32928b56f4f4b0e7d7e835673dc5aff8', 'TITLE' => $this->title, 'HIDDEN' => '', 'URL' => $post_url, 'FIELDS' => $fields, 'TEXT' => do_lang_tempcode('CMS_COPYWAIT'), 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => $submit_name));
    }

    /**
     * Worker function to do an FTP import.
     *
     * @param  resource $conn_id The FTP connection
     * @param  PATH $directory The directory we are scanning
     * @param  integer $depth The depth of the current scan level
     * @return Tempcode The list of directories
     */
    public function _hostingcopy_do_dir($conn_id, $directory = '/', $depth = 0)
    {
        if ($directory == '') {
            $directory = '/';
        }
        if ($directory[strlen($directory) - 1] != '/') {
            $directory .= '/';
        }

        $list = new Tempcode();
        if (!@ftp_chdir($conn_id, $directory)) {
            return $list; // Can't rely on ftp_nlist if not a directory
        }
        $contents = ftp_nlist($conn_id, $directory);
        if ($contents === false) {
            return $list;
        }
        $list->attach(form_input_list_entry($directory, ($directory == '/public_html/') || ($directory == '/www/') || ($directory == '/httpdocs/') || ($directory == '/htdocs/')));
        if ($depth == 2) {
            return $list;
        }
        foreach ($contents as $entry) {
            $full_entry = $entry;
            $parts = explode('/', $entry);
            $entry = $parts[count($parts) - 1];
            if ($entry == '') {
                if (!array_key_exists(count($parts) - 2, $parts)) {
                    continue;
                }
                $entry = $parts[count($parts) - 2];
            }
            if (($entry == '.') || ($entry == '..') || ($entry == '') || ($entry == 'conf') || ($entry == 'anon_ftp') || ($entry == 'logs') || ($entry == 'imap') || ($entry == 'statistics') || ($entry == 'error_docs') || ($entry == 'tmp') || ($entry == 'mail') || ($entry[0] == '.') || ($entry == 'etc') || (strpos($entry, 'logs') !== false) || ($entry == 'public_ftp')) {
                continue;
            }

            // Is the entry a directory?
            if ((strpos($entry, '.') === false) && (@ftp_chdir($conn_id, $directory . '/' . $entry))) {
                $full_path = $directory . $entry . '/';
                $list->attach($this->_hostingcopy_do_dir($conn_id, $full_path, $depth + 1));
            }
        }

        return $list;
    }

    /**
     * Try to make an FTP connection as specified by POST details. Dies if it can't.
     *
     * @return resource The connection.
     */
    public function _hostingcopy_ftp_connect()
    {
        $domain = trim(post_param_string('ftp_domain'));
        $port = 21;
        if (strpos($domain, ':') !== false) {
            list($domain, $_port) = explode(':', $domain, 2);
            $port = intval($_port);
        }
        $conn_id = @ftp_connect($domain, $port);

        if ($conn_id === false) {
            warn_exit(do_lang_tempcode('COULD_NOT_CONNECT_SERVER', escape_html(post_param_string('ftp_domain')), @strval($php_errormsg)));
        }
        $login_result = @ftp_login($conn_id, post_param_string('ftp_username'), post_param_string('ftp_password'));

        // Check connection
        if (!$login_result) {
            warn_exit(do_lang_tempcode('FTP_ERROR'));
        }

        return $conn_id;
    }

    /**
     * The UI to choose a path.
     *
     * @return Tempcode The UI.
     */
    public function hostingcopy_step2()
    {
        if (!addon_installed('search')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        $hidden = build_keep_post_fields();

        $search_under = post_param_string('search_under', '/');

        // Find paths
        $conn_id = $this->_hostingcopy_ftp_connect();
        $list = $this->_hostingcopy_do_dir($conn_id, $search_under);
        ftp_close($conn_id);

        if ($list->is_empty()) {
            warn_exit(do_lang_tempcode('HOSTING_NO_FIND_DIR'));
        }

        $base_url = 'http://' . preg_replace('#^ftp\.#', '', post_param_string('ftp_domain')) . preg_replace('#/(public_html|www|httpdocs|htdocs)/#', '/', $search_under);

        $fields = new Tempcode();
        $fields->attach(form_input_list(do_lang_tempcode('FTP_DIRECTORY'), '', 'path', $list));
        $fields->attach(form_input_line(do_lang_tempcode('NEW_DIRECTORY'), do_lang_tempcode('DESCRIPTION_NEW_DIRECTORY'), 'extra_path', '', false));
        $fields->attach(form_input_line(do_lang_tempcode('BASE_URL'), do_lang_tempcode('DESCRIPTION_BASE_URL'), 'base_url', $base_url, true));
        $post_url = build_url(array('page' => '_SELF', 'type' => 'hostingcopy_step3'), '_SELF');
        $submit_name = do_lang('HOSTING_COPY');

        return do_template('FORM_SCREEN', array('_GUID' => '0758605aeb4ee00f1eee562c14d16a5f', 'HIDDEN' => $hidden, 'TITLE' => $this->title, 'URL' => $post_url, 'FIELDS' => $fields, 'TEXT' => '', 'SUBMIT_ICON' => 'buttons__upload', 'SUBMIT_NAME' => $submit_name));
    }

    /**
     * The actualiser.
     *
     * @return Tempcode The result of execution.
     */
    public function hostingcopy_step3()
    {
        if (!addon_installed('search')) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        $conn_id = $this->_hostingcopy_ftp_connect();
        $path = post_param_string('path');
        $extra_path = post_param_string('extra_path');
        if (!@ftp_chdir($conn_id, $path)) {
            warn_exit(do_lang_tempcode('HOSTING_NO_FIND_DIR'));
        }
        if ($extra_path != '') {
            @ftp_mkdir($conn_id, $extra_path);
            if (!@ftp_chdir($conn_id, $extra_path)) {
                warn_exit(do_lang_tempcode('HOSTING_NO_MAKE_DIR'));
            }
        }

        // Check there's a latest version
        $t = get_latest_version_pretty();
        if (is_null($t)) {
            warn_exit(do_lang_tempcode('ARCHIVE_NOT_AVAILABLE'));
        }

        // Do upload to hosting
        $array = array('install.php' => get_file_base() . '/uploads/downloads/install.php', 'data.cms' => get_file_base() . '/uploads/downloads/data.cms');
        foreach ($array as $filename => $tmp_file) {
            if (!@ftp_put($conn_id, $filename, $tmp_file, FTP_BINARY)) {
                ftp_close($conn_id);
                warn_exit(do_lang_tempcode('HOSTING_NO_UPLOAD', @strval($php_errormsg)));
            }
        }
        $file_list = ftp_nlist($conn_id, '.');
        if ((is_array($file_list)) && (!in_array($filename, $file_list))) {
            warn_exit(do_lang_tempcode('HOSTING_FILE_GONE_MISSING'));
        }
        ftp_close($conn_id);

        // Generate URL to installer on hosting
        $base_url = post_param_string('base_url');
        if (substr($base_url, -1) != '/') {
            $base_url .= '/';
        }
        $install_url = $base_url . 'install.php';

        return do_template('CMS_HOSTING_COPY_SUCCESS_SCREEN', array(
            '_GUID' => '5946fe2252fe1a67ba54e2c20a1d4d63',
            'TITLE' => $this->title,
            'FTP_FOLDER' => $path . (($extra_path == '') ? '' : ($extra_path . '/')),
            'HIDDEN' => build_keep_post_fields(array('path', 'extra_path')),
            'INSTALL_URL' => $install_url,
        ));
    }

    /**
     * Site setup wizard step.
     *
     * @return Tempcode The interface.
     */
    public function demonstratr()
    {
        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('CMS_CODENAME'), do_lang('CMS_CODENAME_DESCRIPTION'), 'codename', '', true));
        $fields->attach(form_input_email(do_lang_tempcode('EMAIL_ADDRESS'), do_lang_tempcode('CMS_YOUR_EMAIL_ADDRESS'), 'email_address', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()), true));
        $fields->attach(form_input_password(do_lang_tempcode('PASSWORD'), do_lang_tempcode('CMS_PASSWORD'), 'password', true));
        $fields->attach(form_input_password(do_lang_tempcode('CONFIRM_PASSWORD'), '', 'confirm_password', true));

        $text = do_lang_tempcode('CMS_ENTER_DETAILS');
        $post_url = build_url(array('page' => '_SELF', 'type' => '_demonstratr'), '_SELF');

        return do_template('FORM_SCREEN', array('_GUID' => '0ed12af5b64c65a673b9837bd47a80b1', 'TITLE' => $this->title, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => do_lang('PROCEED'), 'FIELDS' => $fields, 'URL' => $post_url, 'TEXT' => $text, 'HIDDEN' => ''));
    }

    /**
     * Site setup wizard step.
     *
     * @return Tempcode The interface.
     */
    public function _demonstratr()
    {
        $codename = strtolower(post_param_string('codename'));
        $name = post_param_string('name', '');
        $email_address = post_param_string('email_address');
        $description = post_param_string('description', '');
        $category = post_param_string('category', '');
        $show_in_directory = post_param_integer('show_in_directory', 0);
        $password = post_param_string('password');
        $confirm_password = post_param_string('confirm_password');

        if ($password != $confirm_password) {
            warn_exit(do_lang_tempcode('PASSWORD_MISMATCH'));
        }

        demonstratr_add_site($codename, $name, $email_address, $password, $description, $category, $show_in_directory);

        return do_template('INFORM_SCREEN', array('_GUID' => 'bedc8955800508d6b91515e44e8a58ef', 'TITLE' => $this->title, 'TEXT' => do_lang_tempcode('CMS_NEW_SITE', escape_html($codename))));
    }
}
