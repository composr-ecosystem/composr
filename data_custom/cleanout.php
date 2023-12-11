<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

/*
This script cleans up stuff in the database after finishing beta testing a new site.

FUDGE It assumes multi-language content is turned off because it doesn't bother cleaning up translate table references.
If that feature is needed the code could be improved.
*/

$out = cleanup();
if (!headers_sent()) {
    header('Content-type: text/plain; charset=' . get_charset());
    safe_ini_set('ocproducts.xss_detect', '0');
    if (!is_null($out)) {
        echo is_object($out) ? $out->evaluate() : (is_bool($out) ? ($out ? 'true' : 'false') : $out);
    }
    echo do_lang('SUCCESS');
}

/**
 * Execute some temporary code put into this function.
 *
 * @return  mixed    Arbitrary result to output, if no text has already gone out
 */
function cleanup()
{
    $password = post_param_string('password', null);
    if (is_null($password)) {
        @exit('<form action="#" method="post"><label>Master password <input type="password" name="password" value="" /></label><input class="menu___generic_admin__delete button_screen" type="submit" value="Delete programmed data" /></form>');
    }
    require_code('crypt_master');
    if (!check_master_password($password)) {
        warn_exit('Access denied - you must pass the master password through correctly');
    }

    /* Customise this. This is the list of delete functions needed */
    $purge = array(
        /*'delete_calendar_event',
        'delete_news_category',
        'delete_news',
        'cns_delete_topic',
        'cns_delete_forum',
        'cns_delete_forum_grouping',
        'cns_delete_group',
        'cns_delete_member',*/
    );

    $log_cache_wip_cleanup = true;
    $aggressive_cleanup = true;
    $clean_all_attachments = true;

    /* Actioning code follows... */

    if (php_function_allowed('set_time_limit')) {
        @set_time_limit(0);
    }

    $GLOBALS['SITE_INFO']['no_email_output'] = '1';

    $purgeable = array(
        array(
            'delete_author',
            'authors',
            'authors',
            'author',
            array(),
        ),

        array(
            'delete_award_type',
            'awards',
            'award_types',
            'id',
            array(),
        ),

        array(
            'delete_bookmark',
            'bookmarks',
            'bookmarks',
            'id',
            array(),
        ),

        array(
            'delete_event_type',
            'calendar2',
            'calendar_types',
            'id',
            array(db_get_first_id(), db_get_first_id() + 1),
        ),

        array(
            'delete_calendar_event',
            'calendar2',
            'calendar_events',
            'id',
            array(),
        ),

        array(
            'delete_chatroom',
            'chat2',
            'chat_rooms',
            'room_name',
            array(),
        ),

        array(
            'delete_download',
            'downloads2',
            'download_downloads',
            'id',
            array(),
        ),

        array(
            'delete_download_licence',
            'downloads2',
            'download_licences',
            'id',
            array(),
        ),

        array(
            'delete_download_category',
            'downloads2',
            'download_categories',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'delete_usergroup_subscription',
            'ecommerce',
            'f_usergroup_subs',
            'id',
            array(),
        ),

        array(
            'delete_flagrant',
            'flagrant',
            'text',
            'id',
            array(),
        ),

        array(
            'delete_image',
            'galleries2',
            'images',
            'id',
            array(),
        ),

        array(
            'delete_video',
            'galleries2',
            'videos',
            'id',
            array(),
        ),

        array(
            'delete_gallery',
            'galleries2',
            'galleries',
            'name',
            array('root'),
        ),

        /*array( Probably unwanted
            'delete_menu_item',
            'menus2',
            'menu_items',
            'id',
        ),*/

        array(
            'delete_news_category',
            'news',
            'news_categories',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'delete_news',
            'news',
            'news',
            'id',
            array(),
        ),

        array(
            'delete_newsletter',
            'newsletter',
            'newsletters',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'cns_delete_topic',
            'cns_topics_action2',
            'f_topics',
            'id',
            array(),
            array('', null, false),
        ),

        array(
            'cns_delete_forum',
            'cns_forums_action2',
            'f_forums',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'cns_delete_forum_grouping',
            'cns_forums_action2',
            'f_categories',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'cns_delete_post_template',
            'cns_general_action2',
            'f_post_templates',
            'id',
            array(),
        ),

        /*array(  Probably not wanted
            'cns_delete_emoticon',
            'cns_general_action2',
            'f_emoticons',
            'e_code',
            array(),
        ),*/

        array(
            'cns_delete_welcome_email',
            'cns_general_action2',
            'f_welcome_emails',
            'id',
            array(),
        ),

        array(
            'cns_delete_group',
            'cns_groups_action2',
            'f_groups',
            'id',
            array(db_get_first_id(), db_get_first_id() + 1, db_get_first_id() + 2, db_get_first_id() + 3, db_get_first_id() + 4, db_get_first_id() + 5, db_get_first_id() + 6, db_get_first_id() + 7, db_get_first_id() + 8),
        ),

        array(
            'cns_delete_member',
            'cns_members_action2',
            'f_members',
            'id',
            array(db_get_first_id(), db_get_first_id() + 1),
        ),

        /*array(  Probably not wanted
            'cns_delete_custom_field',
            'cns_members_action2',
            'f_custom_fields',
            'id',
            array(),
        ),*/

        array(
            'cns_delete_warning',
            'cns_moderation_action2',
            'f_warnings',
            'id',
            array(),
        ),

        array(
            'cns_delete_multi_moderation',
            'cns_moderation_action2',
            'f_multi_moderations',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'delete_poll',
            'polls',
            'f_polls',
            'id',
            array(),
        ),

        array(
            'delete_quiz',
            'quiz',
            'quizzes',
            'id',
            array(),
        ),

        array(
            'delete_ticket_type',
            'tickets2',
            'ticket_types',
            'id',
            array(db_get_first_id()),
        ),

        array(
            'actual_delete_catalogue',
            'catalogues2',
            'catalogues',
            'c_name',
            array(),
        ),

        array(
            'actual_delete_catalogue_category',
            'catalogues2',
            'catalogue_categories',
            'id',
            array(),
        ),

        array(
            'actual_delete_catalogue_entry',
            'catalogues2',
            'catalogue_entries',
            'id',
            array(),
        ),

        /*array(  Probably not wanted
            'actual_delete_zone',
            'zones2',
            'zones',
            'zone_name',
            array('', 'site', 'adminzone', 'cms', 'collaboration', 'forum'),
        ),*/

        /*array(  Probably not wanted
            'delete_cms_page',
            'zones3',
            'comcode_pages',
            array('the_zone', 'the_page'),
            array(
                    array('adminzone', 'netlink'),
                    array('adminzone', 'panel_top'),
                    array('adminzone', 'quotes'),
                    array('adminzone', 'start'),
                    array('cms', 'panel_top'),
                    array('collaboration', 'about'),
                    array('collaboration', 'panel_left'),
                    array('collaboration', 'start'),
                    array('forum', 'panel_left'),
                    array('site', 'help'),
                    array('site', 'panel_left'),
                    array('site', 'panel_right'),
                    array('site', 'start'),
                    array('site', 'userguide_chatcode'),
                    array('site', 'userguide_comcode'),
                    array('', '404'),
                    array('', 'feedback'),
                    array('', 'keymap'),
                    array('', 'panel_bottom'),
                    array('', 'panel_left'),
                    array('', 'panel_right'),
                    array('', 'panel_top'),
                    array('', 'privacy'),
                    array('', 'recommend_help'),
                    array('', 'rules'),
                    array('', 'sitemap'),
                    array('', 'start'),
            ),
        ),*/

        array(
            'wiki_delete_post',
            'wiki',
            'wiki_posts',
            'id',
            array(),
        ),

        array(
            'wiki_delete_page',
            'wiki',
            'wiki_pages',
            'id',
            array(db_get_first_id()),
        ),

        /*wordfilter - not really wanted */
    );

    $GLOBALS['NO_DB_SCOPE_CHECK'] = true;

    foreach ($purgeable as $p) {
        list($function, $codefile, $table, $id_field, $skip) = $p;
        $extra_params = array_key_exists(5, $p) ? $p[5] : array();
        if (in_array($function, $purge)) {
            require_code($codefile);

            $start = 0;
            do {
                $select = is_array($id_field) ? $id_field : array($id_field);
                if ($function == 'actual_delete_catalogue_category') {
                    $select[] = 'cc_parent_id';
                    $select[] = 'c_name';
                }
                $rows = $GLOBALS['SITE_DB']->query_select($table, $select, null, '', 100, $start);
                foreach ($rows as $i => $row) {
                    if (($function == 'actual_delete_catalogue_category') && ($row['cc_parent_id'] === null) && ($GLOBALS['SITE_DB']->query_select_value('catalogue_catalogues', 'c_is_tree', array('c_name' => $row['c_name'])) == 1)) {
                        unset($rows[$i]);
                        continue;
                    }

                    if (($function == 'cns_delete_member') && ($GLOBALS['FORUM_DRIVER']->is_super_admin($row['id']))) {
                        $GLOBALS['SITE_DB']->query_update('comcode_pages', array('p_submitter' => 2), array('p_submitter' => $row['id']));
                    }

                    if (in_array(is_array($id_field) ? $row : $row[$id_field], $skip)) {
                        unset($rows[$i]);
                        continue;
                    }

                    call_user_func_array($function, array_merge($row, $extra_params));
                }
                //$start+=100;   Actually, don't do this - as deletion will have changed offsets
            } while (count($rows) != 0);
        }
    }

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
        foreach ($table_purposes as $table => $purpose) {
            if ((table_has_purpose_flag($table, TABLE_PURPOSE__FLUSHABLE)) && ($GLOBALS['SITE_DB']->table_exists($table))) {
                $GLOBALS['SITE_DB']->query_delete($table);
            }
        }

        delete_value('user_peak');
        delete_value('users_online');
        delete_value('last_space_check');
        delete_value('last_commandr_command');
        delete_value('site_bestmember');

        $hooks = find_all_hooks('systems', 'disposable_values');
        foreach (array_keys($hooks) as $hook) {
            $GLOBALS['SITE_DB']->query_delete('values', array('the_name' => $hook), '', 1);
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
}
