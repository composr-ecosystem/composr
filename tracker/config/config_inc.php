<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

/*
This is a fork of Mantis.

It integrates with Composr's _config.php, and database installation is handled from within Composr.

Custom fields with special names will have been installed for you:
 - Time estimation (hours)		a number
 - Sponsorship open				a checkbox

Fix any TODOs in this file.
*/

require_once(__DIR__ . '/../../_config.php');
global $SITE_INFO;

// Database Configuration (we use the Composr database)
$g_hostname = $SITE_INFO['db_site_host'];
$g_db_username = $SITE_INFO['db_site_user'];
$g_db_password = $SITE_INFO['db_site_password'];
$g_database_name = $SITE_INFO['db_site'];
$g_db_type = $SITE_INFO['db_type'];
if ($g_db_type == 'mysql_pdo') {
	$g_db_type = 'mysqli';
}

$cms_sc_db_prefix = $SITE_INFO['table_prefix'];
$cms_sc_session_cookie_name = $SITE_INFO['session_cookie'];
$cms_sc_multi_lang_content = (!isset($SITE_INFO['multi_lang_content'])) || ($SITE_INFO['multi_lang_content'] == '1');

// Attachments / File Uploads
$g_allow_file_upload = ON;
$g_file_upload_method = DISK;
$g_max_file_size = 25000000; // in bytes
$g_preview_attachments_inline_max_size = 256 * 1024;
$g_allowed_files = '1st,3g2,3gp,3gp2,3gpp,3p,7z,aac,ai,aif,aifc,aiff,br,bz2,cur,dot,dotx,f4v,ico,ics,iso,jpe,keynote,log,m2v,m4v,mdb,mid,mp2,mpa,mpe,mpv2,odb,odc,odi,ogv,otf,rtf,tgz,tiff,ttf,vsd,vtt,weba,webm,webp,wma,pages,numbers,patch,diff,sql,odg,odp,odt,ods,ps,pdf,doc,ppt,csv,xls,docx,pptx,xlsx,pub,txt,psd,tga,tif,gif,png,bmp,jpg,jpeg,avi,mov,mpg,mpeg,mp4,asf,wmv,ram,ra,rm,qt,zip,tar,rar,gz,wav,mp3,ogg,torrent,php,tpl,ini,eml';
$g_disallowed_files = '';
$g_absolute_path_default_upload_folder = __DIR__ . '/../uploads/';

// Email Configuration
$g_phpMailer_method = PHPMAILER_METHOD_MAIL; // or PHPMAILER_METHOD_SMTP, PHPMAILER_METHOD_SENDMAIL
$g_smtp_host = 'localhost'; // used with PHPMAILER_METHOD_SMTP
$g_smtp_username = ''; // used with PHPMAILER_METHOD_SMTP
$g_smtp_password = ''; // used with PHPMAILER_METHOD_SMTP
$g_administrator_email = 'tracker@composr.app'; // TODO: Customise
$g_webmaster_email = $g_administrator_email;
$g_from_name = 'Composr CMS feature tracker'; // TODO: Customise
$g_from_email = $g_administrator_email; // the "From: " field in emails
$g_return_path_email = $g_administrator_email; // the return address for bounced mail
$g_email_receive_own = OFF;
$g_email_send_using_cronjob = ON;

// User integration with Composr (the plugin performs more operations)
$g_allow_signup = OFF; // Signup is through the Composr site
$g_allow_anonymous_login = ON;
$g_anonymous_account = 'Guest'; // This should be the exact username of ComposrPlugin::cms_guest_id
$g_reauthentication = OFF;
$g_lost_password_feature = OFF;

// Force install the Composr plugin
$g_plugins_force_installed = [
    'Composr' => PLUGIN_PRIORITY_HIGH
];

// Branding
$g_window_title = 'Composr CMS feature tracker'; // TODO: Customise
$g_logo_image = '../themes/default/images/EN/logo/standalone_logo.png'; // TODO: Customise
$g_favicon_image = '../themes/default/images/favicon.ico';

// Security Settings
$g_show_user_email_threshold = ADMINISTRATOR;
$g_upload_bug_file_threshold = ANYBODY;
$g_set_view_status_threshold = ANYBODY;
$g_update_readonly_bug_threshold = VIEWER;
$g_tag_attach_threshold = ANYBODY;
$g_tag_create_threshold = DEVELOPER;
$g_crypto_master_salt = 'uSQCKx+lVIlwZqKZ2r630GwIHlNO0kcCWGP8pTzLVKs=';

// Lets make it so only website-visitors can post. Otherwise spam happens
$g_add_bugnote_threshold = isset($_COOKIE[$SITE_INFO['session_cookie']]) ? ANYBODY : REPORTER;
$g_report_bug_threshold = isset($_COOKIE[$SITE_INFO['session_cookie']]) ? ANYBODY : REPORTER;

// Sponsorship Settings
$g_enable_sponsorship = ON;
$g_sponsorship_currency = ''; // TODO: put in points once MantisBT supports having currency after the number
$g_minimum_sponsorship_amount = 100;

// Simplify by removing unneeded complexity
$g_default_bug_severity = FEATURE; // We primarily track features
$g_default_bug_reproducibility = 100; // We primarily track features
$g_bug_reopen_status = NEW_;
$g_bug_feedback_status = NEW_;
$g_status_enum_string = '10:not assigned,50:assigned,80:resolved,90:closed';
$g_status_colors = array(
    'not assigned' => '//fcbdbd', // red
    'feedback' => '//e3b7eb', // purple
    'acknowledged' => '//ffcd85', // orange
    'confirmed' => '//fff494', // yellow
    'assigned' => '//c2dfff', // blue
    'resolved' => '//d2f5b0', // green
    'closed' => '//c9ccc4', // grey
);
$g_bug_report_page_fields = array(
    'additional_info',
    'attachments',
    'category_id',
    //'due_date',
    'handler',
    //'os',
    //'os_version',
    //'platform',
    //'priority',
    //'product_build',
    'product_version',
    //'reproducibility',
    'severity',
    'steps_to_reproduce',
    'tags',
    //'target_version',
    'view_state',
);
$g_bug_view_page_fields = array(
    'additional_info',
    'attachments',
    'category_id',
    'date_submitted',
    'description',
    //'due_date',
    //'eta',
    'fixed_in_version',
    'handler',
    'id',
    'last_updated',
    //'os',
    //'os_version',
    //'platform',
    //'priority',
    //'product_build',
    'product_version',
    'project',
    'projection',
    'reporter',
    //'reproducibility',
    'resolution',
    'severity',
    'status',
    'steps_to_reproduce',
    'summary',
    'tags',
    //'target_version',
    'view_state',
);
$g_bug_update_page_fields = array(
    'additional_info',
    'category_id',
    'date_submitted',
    'description',
    //'due_date',
    //'eta',
    'fixed_in_version',
    'handler',
    'id',
    'last_updated',
    //'os',
    //'os_version',
    //'platform',
    //'priority',
    //'product_build',
    'product_version',
    'project',
    'projection',
    'reporter',
    //'reproducibility',
    'resolution',
    'severity',
    'status',
    'steps_to_reproduce',
    'summary',
    //'target_version',
    'view_state',
);
$g_severity_enum_string = '10:Feature-request,20:Trivial-bug,50:Minor-bug,60:Major-bug,95:Security-hole';

// Turn off changelogs and roadmaps; we don't use them nor guarantee any roadmaps
$g_view_changelog_threshold = NOBODY;
$g_roadmap_view_threshold = NOBODY;

// Misc
$g_show_realname = OFF;
$g_show_user_realname_threshold = NOBODY; // Set to access level (e.g. VIEWER, REPORTER, DEVELOPER, MANAGER, etc)
$g_cookie_time_length = 60 * 60 * 24 * 30;
$g_html_valid_tags = 'p, li, ul, ol, br, pre, i, b, u, em';
$g_rss_enabled = OFF;
$g_default_home_page = 'my_view_page.php'; // Set to name of page to go to after login
$g_logo_url = './';

// Debugging
//$g_show_detailed_errors = ON;
//$g_log_level = LOG_ALL;
ini_set('error_log', __DIR__ . '/../../data_custom/errorlog.php');
$g_log_destination = 'file:' . dirname(dirname(__DIR__)) . '/data_custom/errorlog.php';

// Show errors if in ocProducts PHP
if (function_exists('ocp_mark_as_escaped')) {
    $g_display_errors = array(
        E_ALL => DISPLAY_ERROR_HALT,
    );
    $g_show_detailed_errors = ON;
}

// software-specific
$cms_sc_site_url = $SITE_INFO['base_url'];
$cms_sc_site_name = 'composr.app';
$cms_sc_profile_url = $cms_sc_site_url . '/members/view.htm';
$cms_sc_commercial_support_url = $cms_sc_site_url . '/professional-support.htm';
$cms_sc_report_guidance_url = $cms_sc_site_url . '/docs/tut-software-feedback.htm';
$cms_sc_join_url = $cms_sc_site_url . '/join.htm';
$cms_sc_lostpassword_url = $cms_sc_site_url . '/lost_password.htm';
$cms_sc_member_view_url = $cms_sc_site_url . '/members/view/%1$d.htm'; // Set the user id as variable in the url ie %1$d
$cms_sc_sourcecode_url = 'https://gitlab.com/composr-foundation/composr';
$cms_sc_home_url = 'https://composr.app';
$cms_sc_product_name = 'Composr';
$cms_sc_business_name = 'Composr';
$cms_sc_business_name_possesive = 'Composr\'s';
$cms_sc_credits_per_hour = 6;
$cms_sc_price_per_credit = 5.5;
$cms_sc_main_currency = 'GBP';
$cms_sc_main_currency_symbol = '&pound';
$cms_sc_alternate_currencies = array('USD', 'CAD', 'EUR');
$cms_sc_custom_profile_field = 'cms_support_credits';
$cms_sponsorship_locked_until = mktime(0, 0, 0, 7, 1, 2016); // Useful to deal with work back-logs, as unscheduled work can be a major problem sometimes (as much as sponsorship is valued and important)

$cms_updater_groups = array();
$cms_developer_groups = array(22);
$cms_manager_groups = array();
$cms_admin_groups = array(2, 3);
$cms_guest_id = 1;
$cms_extra_signin_sql = ' AND field_46 IN(\'\',\'Content Management System\')'; // TODO: Customise
