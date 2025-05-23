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

// Attachments / File Uploads
$g_allow_file_upload = ON;
$g_file_upload_method = DISK;
$g_max_file_size = (1024 * 1024 * 32); // in bytes
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
$g_from_name = 'Composr CMS issue tracker'; // TODO: Customise
$g_from_email = $g_administrator_email; // the "From: " field in emails
$g_return_path_email = $g_administrator_email; // the return address for bounced mail
$g_email_receive_own = OFF;
$g_email_send_using_cronjob = ON;

// User integration with Composr (the plugin performs more operations)
$g_allow_signup = OFF; // Signup is through the Composr site
$g_allow_anonymous_login = ON; // Anonymous users should be allowed to view issues
$g_anonymous_account = 'Guest'; // This should be the exact username of ComposrPlugin::cms_guest_id
$g_reauthentication = OFF; // Done through Composr
$g_lost_password_feature = OFF; // Done through Composr

// Force install the Composr plugin
$g_plugins_force_installed = [
    'Composr' => PLUGIN_PRIORITY_HIGH
];

// Branding
$g_window_title = 'Composr CMS feature tracker'; // TODO: Customise
$g_logo_image = '../themes/default/images/EN/logo/standalone_logo.png'; // TODO: Customise
$g_favicon_image = '../themes/default/images/favicon.ico';

// Access Settings
$g_access_levels_enum_string = '10:Guest,25:Member,40:Updater,55:Community Developer,70:Core Developer,90:Lead Developer';
$g_show_user_email_threshold = MANAGER; // Prevent spam
$g_upload_bug_file_threshold = REPORTER; // Prevent spam
$g_add_bugnote_threshold = REPORTER; // Prevent spam
$g_report_bug_threshold = REPORTER; // Prevent spam
$g_set_view_status_threshold = ANYBODY;
$g_private_bug_threshold = ADMINISTRATOR; // We use private bugs for serious security issues. Only lead developers should see these.
$g_update_readonly_bug_threshold = DEVELOPER; // If a bug is resolved / closed, we want members to open a new issue, not re-open the existing one
$g_allow_reporter_reopen = OFF;
$g_tag_attach_threshold = REPORTER;
$g_tag_create_threshold = MANAGER;
$g_view_changelog_threshold = NOBODY; // We use our own change logs on Composr
$g_roadmap_view_threshold = NOBODY; // We don't use road maps because nothing is guaranteed
$g_set_status_threshold = array(
    NEW_ => REPORTER,
    CLOSED => MANAGER,
    RESOLVED => DEVELOPER,
    ASSIGNED => DEVELOPER,
);

$g_crypto_master_salt = 'uSQCKx+lVIlwZqKZ2r630GwIHlNO0kcCWGP8pTzLVKs=';


// Sponsorship Settings
$g_enable_sponsorship = ON;
$g_sponsorship_currency = 'points';
$g_minimum_sponsorship_amount = 25;

// Simplify by removing unneeded complexity
$g_display_bug_padding = 0; // We don't want leading 0s
$g_display_bugnote_padding = 0; // We don't want leading 0s
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
    //'priority', // We don't want the public setting this, so we only make it visible on edit
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
    'priority',
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
    'priority',
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

// We use priorities to track if and how a bug was sponsored
$g_priority_enum_string = '20:Deferred,30:Not Sponsored,40:Sponsored by Community,50:Sponsored by Client,60:Critical';
$g_show_priority_text = ON;
$g_default_email_on_priority = ON;

// Misc
$g_show_realname = OFF;
$g_show_user_realname_threshold = NOBODY;
$g_cookie_time_length = 60 * 60 * 24 * 30;
$g_html_valid_tags = 'p, li, ul, ol, br, pre, i, b, u, em';
$g_rss_enabled = OFF;
$g_default_home_page = 'my_view_page.php'; // Set to name of page to go to after login
$g_logo_url = './';
$g_summary_category_include_project = ON;
$g_html_make_links = LINKS_NEW_WINDOW | LINKS_NOOPENER | LINKS_NOREFERRER; // Prevent SEO benefit on spam links
$g_issue_activity_note_attachments_seconds_threshold = 180; // Might be using the submit bugfix tool in Composr

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
