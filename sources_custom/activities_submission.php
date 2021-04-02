<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Syndicate human-intended descriptions of activities performed to the internal wall, and external listeners.
 *
 * @param  string $a_language_string_code Language string ID
 * @param  string $a_label_1 Label 1 (given as a parameter to the language string ID)
 * @param  string $a_label_2 Label 2 (given as a parameter to the language string ID)
 * @param  string $a_label_3 Label 3 (given as a parameter to the language string ID)
 * @param  string $a_page_link_1 Page-link 1
 * @param  string $a_page_link_2 Page-link 2
 * @param  string $a_page_link_3 Page-link 3
 * @param  string $a_addon Addon that caused the event
 * @param  BINARY $a_is_public Whether this post should be public or friends-only
 * @param  ?MEMBER $a_member_id Member being written for (null: current member)
 * @param  boolean $sitewide_too Whether to push this out as a site event if user requested
 * @param  ?MEMBER $a_also_involving Member also 'intimately' involved, such as a content submitter who is a friend (null: none)
 * @return ?AUTO_LINK The activity ID.
 */
function activities_addon_syndicate_described_activity($a_language_string_code = '', $a_label_1 = '', $a_label_2 = '', $a_label_3 = '', $a_page_link_1 = '', $a_page_link_2 = '', $a_page_link_3 = '', $a_addon = '', $a_is_public = 1, $a_member_id = null, $sitewide_too = false, $a_also_involving = null)
{
    require_code('activities');
    require_lang('activities');

    if ((get_db_type() == 'xml') && (get_param_integer('keep_testing_logging', 0) != 1)) {
        return null;
    }

    $stored_id = 0;
    if (is_null($a_member_id)) {
        $a_member_id = get_member();
    }
    if (is_guest($a_member_id)) {
        return null;
    }

    $go = array(
        'a_language_string_code' => $a_language_string_code,
        'a_label_1' => $a_label_1,
        'a_label_2' => $a_label_2,
        'a_label_3' => $a_label_3,
        'a_is_public' => $a_is_public
    );

    $stored_id = mixed();

    // Check if this has been posted previously (within the last 10 minutes) to
    // stop spamming but allow generalised repeat status messages.
    $test = $GLOBALS['SITE_DB']->query('SELECT a_language_string_code,a_label_1,a_label_2,a_label_3,a_is_public FROM ' . get_table_prefix() . 'activities WHERE a_member_id=' . strval($a_member_id) . ' AND a_time>' . strval(time() - 600) . ' AND a_time<=' . strval(time()), 1);
    if ((!array_key_exists(0, $test)) || ($test[0] != $go) || (running_script('execute_temp')) || ($GLOBALS['SEMI_DEV_MODE'])) {
        // Log the activity
        $row = $go + array(
                'a_member_id' => $a_member_id,
                'a_also_involving' => $a_also_involving,
                'a_page_link_1' => $a_page_link_1,
                'a_page_link_2' => $a_page_link_2,
                'a_page_link_3' => $a_page_link_3,
                'a_time' => time(),
                'a_addon' => $a_addon,
            );
        $stored_id = $GLOBALS['SITE_DB']->query_insert('activities', $row, true);

        // Update the latest activity file
        log_newest_activity($stored_id, 1000, true/*We do want to force it, IDs can get out of sync on dev sites*/);

        // External places
        if (($a_is_public == 1) && (($a_language_string_code == 'RAW_DUMP') || (!$GLOBALS['IS_ACTUALLY_ADMIN']/*SU means oauth'd user is not intended user*/))) {
            $dests = find_all_hooks('systems', 'syndication');
            foreach (array_keys($dests) as $hook) {
                require_code('hooks/systems/syndication/' . $hook);
                $ob = object_factory('Hook_syndication_' . $hook);
                if ($ob->is_available($a_member_id)) {
                    $ob->syndicate_user_activity($a_member_id, $row);
                }
                if ($ob->is_available()) {
                    if (($sitewide_too) && (has_privilege(get_member(), 'syndicate_site_activity')) && (post_param_integer('syndicate_this', 0) == 1)) {
                        $ob->syndicate_site_activity($row);
                    }
                }
            }
        }

        list($message) = render_activity($row, false);
        require_code('notifications');
        $username = $GLOBALS['FORUM_DRIVER']->get_username($a_member_id);
        $displayname = $GLOBALS['FORUM_DRIVER']->get_username($a_member_id, true);
        $subject = do_lang('ACTIVITY_NOTIFICATION_MAIL_SUBJECT', get_site_name(), $username, strip_html($message->evaluate()));
        $mail = do_notification_lang('ACTIVITY_NOTIFICATION_MAIL', comcode_escape(get_site_name()), comcode_escape($username), array('[semihtml]' . $message->evaluate() . '[/semihtml]', $displayname));
        dispatch_notification('activity', strval($a_member_id), $subject, $mail);
    }

    return $stored_id;
}

/**
 * AJAX script handler for submitting posts.
 */
function activities_ajax_submit_handler()
{
    prepare_for_known_ajax_response();

    header('Content-Type: text/xml');
    header("Content-Security-Policy: default-src 'none'"); // Don't allow special execution via a vector of namespace-injected HTML

    $response = '<' . '?xml version="1.0" encoding="' . get_charset() . '" ?' . '>';
    $response .= '<response><content>';

    $map = array();

    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());

    if (!is_guest(get_member())) {
        $map['STATUS'] = trim(post_param_string('status', ''));

        if ((post_param_string('zone', '') != '') && ($map['STATUS'] != '') && ($map['STATUS'] != do_lang('activities:TYPE_HERE'))) {
            comcode_to_tempcode($map['STATUS'], $guest_id, false, null);

            $map['PRIVACY'] = post_param_string('privacy', 'private');

            if (strlen(strip_tags($map['STATUS'])) < strlen($map['STATUS'])) {
                $help_zone = get_comcode_zone('userguide_comcode', false);
                if (is_null($help_zone)) {
                    $response .= '<success>0</success><feedback><![CDATA[No HTML allowed. Use Comcode.]]></feedback>';
                } else {
                    $cc_guide = build_url(array('page' => 'userguide_comcode'), $help_zone);
                    $response .= '<success>0</success><feedback><![CDATA[No HTML allowed. See <a href="' . escape_html($cc_guide->evaluate()) . '">Comcode Help</a> for info on the alternative.]]></feedback>';
                }
            } else {
                if (strlen($map['STATUS']) > 255) {
                    $response .= '<success>0</success><feedback>Message is ' . strval(strlen($map['STATUS']) - 255) . ' characters too long</feedback>';
                } else {
                    $stored_id = activities_addon_syndicate_described_activity('RAW_DUMP',
                        $map['STATUS'],
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ($map['PRIVACY'] == 'public') ? 1 : 0
                    );

                    if ($stored_id !== null) {
                        $response .= '<success>1</success><feedback>Message received.</feedback>';
                    } else {
                        $response .= '<success>0</success><feedback>Message already received.</feedback>';
                    }
                }
            }
        }
    } else {
        $response .= '<success>0</success><feedback>' . do_lang('LOGIN_EXPIRED_POST') . '</feedback>';
    }

    $response .= '</content></response>';

    echo $response;

    cms_safe_exit_flow();
}

/**
 * AJAX script handler for refreshing the post list.
 */
function activities_ajax_update_list_handler()
{
    $max = post_param_integer('max', 10);
    $last_id = post_param_string('last_id', '-1');
    $mode = post_param_string('mode', 'all');

    require_lang('activities');
    require_code('activities');
    require_code('addons');
    require_code('xml');

    $proceed_selection = true; //There are some cases in which even glancing at the database is a waste of precious time.

    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());
    $viewer_id = intval(get_member()); //We'll need this later anyway.

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    //Getting the member viewed IDs if available, member viewing if not
    $member_ids = array_map('intval', explode(',', post_param_string('member_ids', strval($viewer_id))));

    list($proceed_selection, $where_clause) = get_activity_querying_sql($viewer_id, $mode, $member_ids);

    prepare_for_known_ajax_response();

    header('Content-Type: text/xml');
    header("Content-Security-Policy: default-src 'none'"); // Don't allow special execution via a vector of namespace-injected HTML

    $response = '<' . '?xml version="1.0" encoding="' . get_charset() . '" ?' . '>';

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    if ($proceed_selection === true) {
        $activities = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'activities WHERE ((' . $where_clause . ') AND id>' . (($last_id == '') ? '-1' : $last_id) . ') ORDER BY a_time DESC', intval($max));

        if (count($activities) > 0) {
            $list_items = '';
            foreach ($activities as $row) {
                list($message, $member_avatar, $datetime, $member_url, $is_public) = render_activity($row);

                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['a_member_id']);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }

                $list_item = do_template('BLOCK_MAIN_ACTIVITIES_XML', array(
                    '_GUID' => '02dfa8b02040f56d76b783ddb8fb382f',
                    'LANG_STRING' => 'RAW_DUMP',
                    'ADDON' => $row['a_addon'],
                    'ADDON_ICON' => ($row['a_addon'] == '') ? '' : find_addon_icon($row['a_addon']),
                    'MESSAGE' => $message,
                    'AVATAR' => $member_avatar,
                    'MEMBER_ID' => strval($row['a_member_id']),
                    'USERNAME' => $username,
                    'DATETIME' => strval($datetime),
                    'MEMBER_URL' => $member_url,
                    'LIID' => strval($row['id']),
                    'ALLOW_REMOVE' => (($row['a_member_id'] == $viewer_id) || $can_remove_others),
                    'IS_PUBLIC' => $is_public,
                ));

                // We dump our response in CDATA, since that lets us work around the
                // fact that our list elements aren't actually in a list, etc.
                // However, we allow comcode but some tags make use of CDATA. Since
                // CDATA can't be nested (as it's a form of comment), we take this
                // into account by base64 encoding the whole template and decoding
                // it in the browser. We wrap it in some arbitrary XML and a CDATA
                // tag so that the JavaScript knows what it's received
                $list_items .= '<listitem id="' . strval($row['id']) . '"><![CDATA[' . base64_encode($list_item->evaluate()) . ']]></listitem>';
            }
            $response .= '<response><success>1</success><feedlen>' . strval($max) . '</feedlen><content>' . $list_items . '</content><supp>' . xmlentities($where_clause) . '</supp></response>';
        } else {
            $response .= '<response><success>2</success><content>NU - Nothing new.</content></response>';
        }
    } else {
        $response .= '<response><success>2</success><content>NU - No feeds to select from.</content></response>';
    }

    echo $response;

    cms_safe_exit_flow();
}

/**
 * AJAX script handler for removing posts.
 */
function activities_ajax_removal_handler()
{
    $is_guest = false; // Can't be doing with over-complicated SQL breakages. Weed it out.
    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());
    $viewer_id = intval(get_member()); //We'll need this later anyway.
    if ($guest_id == $viewer_id) {
        $is_guest = true;
    }

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    prepare_for_known_ajax_response();

    header('Content-Type: text/xml');
    header("Content-Security-Policy: default-src 'none'"); // Don't allow special execution via a vector of namespace-injected HTML

    $response = '<' . '?xml version="1.0" encoding="' . get_charset() . '" ?' . '>';
    $response .= '<response>';

    $stat_id = post_param_integer('removal_id', -1);
    $stat_owner = ($stat_id != -1) ? $GLOBALS['SITE_DB']->query_select_value_if_there('activities', 'a_member_id', array('id' => $stat_id)) : null;

    if (($is_guest !== true) && (!is_null($stat_owner))) {
        if (($stat_owner != $viewer_id) && ($can_remove_others !== true)) {
            $response .= '<success>0</success><err>perms</err>';
            $response .= '<feedback>You do not have permission to remove this status message.</feedback><status_id>' . strval($stat_id) . '</status_id>';
        } else { // I suppose we can proceed now.
            $GLOBALS['SITE_DB']->query_delete('activities', array('id' => $stat_id), '', 1);

            $response .= '<success>1</success><feedback>Message deleted.</feedback><status_id>' . strval($stat_id) . '</status_id>';
        }
    } elseif (is_null($stat_owner)) {
        $response .= '<success>0</success><err>missing</err><feedback>Missing ID for status removal or ID does not exist.</feedback>';
    } else {
        $response .= '<success>0</success><feedback>Login expired, you must log in again to post</feedback>';
    }

    $response .= '</response>';

    echo $response;

    cms_safe_exit_flow();
}

/**
 * Maintains a text file in data_custom. This contains the latest activity's ID.
 * Since the JavaScript polls for updates, it can check against this before
 * running any PHP.
 *
 * @param  integer $id The ID we are going to write to the file
 * @param  integer $timeout Our timeout in milliseconds (how long we should keep trying). Default: 1000
 * @param  boolean $force Whether to force this ID to be the newest, even if it's less than the current value
 */
function log_newest_activity($id, $timeout = 1000, $force = false)
{
    $file_path = get_custom_file_base() . '/data_custom/latest_activity.txt';

    $old_id = @file_get_contents($file_path);
    if (($force) || ($old_id === false) || (intval($old_id) < $id)) {
        require_code('files');
        cms_file_put_contents_safe($file_path, strval($id), FILE_WRITE_FAILURE_SOFT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}
