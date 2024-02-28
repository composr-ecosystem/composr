<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    meta_toolkit
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $FILE_BASE = $_SERVER['SCRIPT_FILENAME']; // this is with symlinks-unresolved (__FILE__ has them resolved); we need as we may want to allow zones to be symlinked into the base directory without getting path-resolved
    $FILE_BASE = dirname($FILE_BASE);
    if (!is_file($FILE_BASE . '/sources/global.php')) {
        $RELATIVE_PATH = basename($FILE_BASE);
        $FILE_BASE = dirname($FILE_BASE);
    } else {
        $RELATIVE_PATH = '';
    }
}
@chdir($FILE_BASE);

global $NON_PAGE_SCRIPT;
$NON_PAGE_SCRIPT = true;
global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

/*
This script cleans up stuff in the database after finishing beta testing a new site.

FUDGE It assumes multi-language content is turned off because it doesn't bother cleaning up translate table references.
If that feature is needed the code could be improved.
*/

if (!addon_installed('meta_toolkit')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('meta_toolkit')));
}

header('X-Robots-Tag: noindex');

$out = cleanup();
if (!headers_sent()) {
    header('Content-Type: text/plain; charset=' . get_charset());
    cms_ini_set('ocproducts.xss_detect', '0');
    if ($out !== null) {
        echo is_object($out) ? $out->evaluate() : (is_bool($out) ? ($out ? 'true' : 'false') : $out);
    }
    echo do_lang('SUCCESS');
}

/**
 * Execute some temporary code put into this function.
 *
 * @return mixed Arbitrary result to output, if no text has already gone out
 */
function cleanup()
{
    if (get_forum_type() != 'cns') {
        warn_exit(do_lang_tempcode('NO_CNS'));
    }

    $password = post_param_string('password', null, INPUT_FILTER_PASSWORD);
    if ($password === null) {
        @exit('<form action="#" method="post"><label>Maintenance password <input type="password" name="password" value="" /></label><button class="btn btn-danger btn-scr" type="submit">' . do_template('ICON', ['_GUID' => '57a3cb1a0e4ea819fdc934eb2f123c82', 'NAME' => 'admin/delete3'])->evaluate() . ' Delete programmed data</button></form>');
    }
    require_code('crypt_master');
    if (!check_maintenance_password($password)) {
        warn_exit('Access denied - incorrect maintenance password. Your login attempt has been logged with your IP address.');
    }

    /* Customise this. This is the list of delete functions needed */
    $purge = [
        /*'delete_calendar_event',
        'delete_news_category',
        'delete_news',
        'cns_delete_topic',
        'cns_delete_forum',
        'cns_delete_forum_grouping',
        'cns_delete_group',
        'cns_delete_member',*/
    ];

    $log_cache_wip_cleanup = true;
    $aggressive_cleanup = true;
    $clean_all_attachments = true;

    /* Actioning code follows... */

    $GLOBALS['SITE_INFO']['no_email_output'] = '1';

    require_code('cns_groups2');
    $all_groups_to_preserve = get_all_preserved_groups();

    $purgeable = [
        [
            'delete_author',
            'authors',
            'authors',
            'author',
            [],
        ],

        [
            'delete_award_type',
            'awards',
            'award_types',
            'id',
            [],
        ],

        [
            'delete_event_type',
            'calendar2',
            'calendar_types',
            'id',
            [db_get_first_id(), db_get_first_id() + 1],
        ],

        [
            'delete_calendar_event',
            'calendar2',
            'calendar_events',
            'id',
            [],
        ],

        [
            'delete_chatroom',
            'chat2',
            'chat_rooms',
            'room_name',
            [],
        ],

        [
            'delete_download',
            'downloads2',
            'download_downloads',
            'id',
            [],
        ],

        [
            'delete_download_licence',
            'downloads2',
            'download_licences',
            'id',
            [],
        ],

        [
            'delete_download_category',
            'downloads2',
            'download_categories',
            'id',
            [db_get_first_id()],
        ],

        [
            'delete_usergroup_subscription',
            'ecommerce',
            'f_usergroup_subs',
            'id',
            [],
        ],

        [
            'delete_flagrant',
            'flagrant',
            'text',
            'id',
            [],
        ],

        [
            'delete_image',
            'galleries2',
            'images',
            'id',
            [],
        ],

        [
            'delete_video',
            'galleries2',
            'videos',
            'id',
            [],
        ],

        [
            'delete_gallery',
            'galleries2',
            'galleries',
            'name',
            ['root'],
        ],

        /*[ Probably unwanted
            'delete_menu_item',
            'menus2',
            'menu_items',
            'id',
        ],*/

        [
            'delete_news_category',
            'news',
            'news_categories',
            'id',
            [db_get_first_id()],
        ],

        [
            'delete_news',
            'news',
            'news',
            'id',
            [],
        ],

        [
            'delete_newsletter',
            'newsletter',
            'newsletters',
            'id',
            [db_get_first_id()],
        ],

        [
            'cns_delete_topic',
            'cns_topics_action2',
            'f_topics',
            'id',
            [],
            ['', null, false],
        ],

        [
            'cns_delete_forum',
            'cns_forums_action2',
            'f_forums',
            'id',
            [db_get_first_id()],
        ],

        [
            'cns_delete_forum_grouping',
            'cns_forums_action2',
            'f_categories',
            'id',
            [db_get_first_id()],
        ],

        [
            'cns_delete_post_template',
            'cns_general_action2',
            'f_post_templates',
            'id',
            [],
        ],

        /*[  Probably not wanted
            'cns_delete_emoticon',
            'cns_general_action2',
            'f_emoticons',
            'e_code',
            [],
        ],*/

        [
            'cns_delete_welcome_email',
            'cns_general_action2',
            'f_welcome_emails',
            'id',
            [],
        ],

        [
            'cns_delete_group',
            'cns_groups_action2',
            'f_groups',
            'id',
            $all_groups_to_preserve,
        ],

        [
            'cns_delete_member',
            'cns_members_action2',
            'f_members',
            'id',
            [db_get_first_id(), db_get_first_id() + 1],
        ],

        /*[  Probably not wanted
            'cns_delete_custom_field',
            'cns_members_action2',
            'f_custom_fields',
            'id',
            [],
        ],*/

        [
            'cns_delete_warning',
            'cns_moderation_action2',
            'f_warnings',
            'id',
            [],
        ],

        [
            'cns_delete_multi_moderation',
            'cns_moderation_action2',
            'f_multi_moderations',
            'id',
            [db_get_first_id()],
        ],

        [
            'delete_poll',
            'polls',
            'f_polls',
            'id',
            [],
        ],

        [
            'delete_quiz',
            'quiz',
            'quizzes',
            'id',
            [],
        ],

        [
            'delete_ticket_type',
            'tickets2',
            'ticket_types',
            'id',
            [db_get_first_id()],
        ],

        [
            'actual_delete_catalogue',
            'catalogues2',
            'catalogues',
            'c_name',
            [],
        ],

        [
            'actual_delete_catalogue_category',
            'catalogues2',
            'catalogue_categories',
            'id',
            [],
        ],

        [
            'actual_delete_catalogue_entry',
            'catalogues2',
            'catalogue_entries',
            'id',
            [],
        ],

        /*[  Probably not wanted
            'actual_delete_zone',
            'zones2',
            'zones',
            'zone_name',
            ['', 'site', 'adminzone', 'cms', 'forum'],
        ],*/

        /*[  Probably not wanted
            'delete_cms_page',
            'zones3',
            'comcode_pages',
            ['the_zone', 'the_page'],
            [
                ['adminzone', 'netlink'],
                ['adminzone', 'panel_top'],
                ['adminzone', 'quotes'],
                ['adminzone', DEFAULT_ZONE_PAGE_NAME],
                ['cms', 'panel_top'],
                ['forum', 'panel_left'],
                ['site', 'help'],
                ['site', 'panel_left'],
                ['site', 'panel_right'],
                ['site', DEFAULT_ZONE_PAGE_NAME],
                ['site', 'userguide_chatcode'],
                ['site', 'userguide_comcode'],
                ['', '404'],
                ['', 'feedback'],
                ['', 'keymap'],
                ['', 'panel_bottom'],
                ['', 'panel_left'],
                ['', 'panel_right'],
                ['', 'panel_top'],
                ['', 'privacy'],
                ['', 'recommend_help'],
                ['', 'rules'],
                ['', 'sitemap'],
                ['', DEFAULT_ZONE_PAGE_NAME],
            ],
        ],*/

        [
            'wiki_delete_post',
            'wiki',
            'wiki_posts',
            'id',
            [],
        ],

        [
            'wiki_delete_page',
            'wiki',
            'wiki_pages',
            'id',
            [db_get_first_id()],
        ],

        /*wordfilter - not really wanted */
    ];

    push_db_scope_check(false);

    foreach ($purgeable as $p) {
        list($function, $codefile, $table, $id_field, $skip) = $p;
        $extra_params = array_key_exists(5, $p) ? $p[5] : [];
        if (in_array($function, $purge)) {
            require_code($codefile);

            $start = 0;
            do {
                $select = is_array($id_field) ? $id_field : [$id_field];
                if ($function == 'actual_delete_catalogue_category') {
                    $select[] = 'cc_parent_id';
                    $select[] = 'c_name';
                }
                $rows = $GLOBALS['SITE_DB']->query_select($table, $select, [], '', 100, $start);
                foreach ($rows as $i => $row) {
                    $old_limit = cms_set_time_limit(10);

                    if (($function == 'actual_delete_catalogue_category') && ($row['cc_parent_id'] === null) && ($GLOBALS['SITE_DB']->query_select_value('catalogue_catalogues', 'c_is_tree', ['c_name' => $row['c_name']]) == 1)) {
                        unset($rows[$i]);
                        continue;
                    }

                    if (($function == 'cns_delete_member') && ($GLOBALS['FORUM_DRIVER']->is_super_admin($row['id']))) {
                        $GLOBALS['SITE_DB']->query_update('comcode_pages', ['p_submitter' => 2], ['p_submitter' => $row['id']]);
                    }

                    if (in_array(is_array($id_field) ? $row : $row[$id_field], $skip)) {
                        unset($rows[$i]);
                        continue;
                    }

                    call_user_func_array($function, array_merge($row, $extra_params));

                    cms_set_time_limit($old_limit);
                }
                //$start+=100;   Actually, don't do this - as deletion will have changed offsets
            } while (!empty($rows));
        }
    }

    cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

    require_code('database_relations');
    $table_purposes = get_table_purpose_flags();

    require_code('files');

    if ($clean_all_attachments) {
        deldir_contents(get_custom_file_base() . '/uploads/attachments', true);
        $GLOBALS['SITE_DB']->query_delete('attachment_refs');
        $GLOBALS['SITE_DB']->query_delete('attachments');
    }

    if ($log_cache_wip_cleanup) {
        deldir_contents(get_custom_file_base() . '/uploads/incoming_uploads', true);
        deldir_contents(get_custom_file_base() . '/uploads/auto_thumbs', true);
        deldir_contents(get_custom_file_base() . '/uploads/captcha', true);
        foreach ($table_purposes as $table => $purpose) {
            if ((table_has_purpose_flag($table, TABLE_PURPOSE__FLUSHABLE)) && ($GLOBALS['SITE_DB']->table_exists($table))) {
                $GLOBALS['SITE_DB']->query_delete($table);
            }
        }

        delete_value('user_peak');
        delete_value('users_online');
        delete_value('last_space_check');
        delete_value('last_commandr_command');

        $hooks = find_all_hooks('systems', 'disposable_values');
        foreach (array_keys($hooks) as $hook) {
            $GLOBALS['SITE_DB']->query_delete('values', ['the_name' => $hook], '', 1);
        }
        persistent_cache_delete('VALUES');
    }

    if ($aggressive_cleanup) {
        foreach ($table_purposes as $table => $purpose) {
            if ((table_has_purpose_flag($table, TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE)) && ($GLOBALS['SITE_DB']->table_exists($table))) {
                $GLOBALS['SITE_DB']->query_delete($table);
            }
        }
    }

    return null;
}
