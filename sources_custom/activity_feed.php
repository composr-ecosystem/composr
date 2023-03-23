<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * AJAX script for refreshing the post list.
 */
function activity_feed_updater_script()
{
    if (!addon_installed('activity_feed')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('activity_feed')));
    }

    $map = [];

    $max = get_param_integer('max', 10);

    $last_id = get_param_integer('last_id', null);
    $mode = get_param_string('mode', 'all');

    require_lang('activity_feed');
    require_code('addons');
    require_code('xml');

    $proceed_selection = true; // There are some cases in which even glancing at the database is a waste of precious time.

    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());
    $viewer_id = intval(get_member()); //We'll need this later anyway.

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    //Getting the member viewed IDs if available, member viewing if not
    $member_ids = array_map('intval', explode(',', get_param_string('member_ids', strval($viewer_id))));

    list($proceed_selection, $where_clause) = get_activity_querying_sql($viewer_id, $mode, $member_ids);

    prepare_backend_response();

    $response = '<' . '?xml version="1.0" encoding="' . escape_html(get_charset()) . '" ?' . '>';

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    if ($proceed_selection === true) {
        $query = 'SELECT * FROM ' . get_table_prefix() . 'activities WHERE (' . $where_clause . ')';
        if ($last_id !== null) {
            $last_time = $GLOBALS['SITE_DB']->query_select_value_if_there('activities', 'a_time', ['id' => $last_id]);
            if ($last_time !== null) {
                $query .= ' AND a_time>=' . strval($last_time) . ' AND id<>' . strval($last_id);
            }
        }
        $query .= ' ORDER BY a_time DESC';
        $activities = $GLOBALS['SITE_DB']->query($query, intval($max));

        if (!empty($activities)) {
            $list_items = '';
            foreach ($activities as $row) {
                list($message, $member_avatar, $timestamp, $member_url, $is_public) = render_activity($row);

                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['a_member_id']);

                $list_item = do_template('BLOCK_MAIN_ACTIVITY_FEED_XML', [
                    '_GUID' => '02dfa8b02040f56d76b783ddb8fb382f',
                    'LANG_STRING' => 'RAW_DUMP',
                    'ADDON' => $row['a_addon'],
                    'ADDON_ICON' => ($row['a_addon'] == '') ? '' : find_addon_icon($row['a_addon']),
                    'MESSAGE' => $message,
                    'AVATAR' => $member_avatar,
                    'MEMBER_ID' => strval($row['a_member_id']),
                    'USERNAME' => $username,
                    'TIMESTAMP' => strval($timestamp),
                    'MEMBER_URL' => $member_url,
                    'LIID' => strval($row['id']),
                    'ALLOW_REMOVE' => (($row['a_member_id'] == $viewer_id) || $can_remove_others),
                    'IS_PUBLIC' => $is_public,
                ]);

                // We dump our response in CDATA, since that lets us work around the fact that our list elements aren't actually in a list, etc.
                // However, we allow Comcode but some tags make use of CDATA. Since CDATA can't be nested (as it's a form of comment), we take this
                // into account by base64 encoding the whole template and decoding it in the browser. We wrap it in some arbitrary XML and a
                // CDATA tag so that the JavaScript knows what it's received
                $list_items .= '<listitem id="' . strval($row['id']) . '"><![CDATA[' . base64_encode($list_item->evaluate()) . ']]></listitem>';
            }
            $response .= '<response><success>1</success><feedlen>' . strval($max) . '</feedlen><content>' . $list_items . '</content><supp>' . xmlentities($where_clause) . '</supp></response>';
        } else {
            $response .= '<response><success>2</success><content>NU - Nothing new.</content></response>';
        }
    } else {
        $response .= '<response><success>2</success><content>NU - No feeds to select from.</content></response>';
    }

    if (function_exists('ocp_mark_as_escaped')) {
        ocp_mark_as_escaped($response);
    }

    echo $response;

    cms_safe_exit_flow();
}

/**
 * Get SQL for querying activities, appropriate to the given settings.
 *
 * @param  MEMBER $viewer_member The viewing member; permissions are checked against this, NOT against the member_ids parameter
 * @param  ID_TEXT $mode The view mode
 * @set some_members some_members_direct friends all
 * @param  array $member_ids A list of member IDs
 * @return array A pair: SQL WHERE clause to use on the activities table, a boolean indicating whether it is worth querying
 */
function get_activity_querying_sql(int $viewer_member, string $mode, array $member_ids) : array
{
    $proceed_selection = true; // There are some cases in which even glancing at the database is a waste of precious time.

    require_all_lang();

    /*if (isset($member_ids[0])) // Useful for testing
        $viewer_member = $member_ids[0];*/

    $guest_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
    $is_guest = is_guest($viewer_member); // Can't be doing with over-complicated SQL breakages. Weed it out.

    // Find out your blocks, and who is blocking you - both must be respected
    $blocking = '';
    $blocked_by = '';
    if (addon_installed('chat')) {
        if (!$is_guest) { // If not a guest, get all blocks
            // Grabbing who you're blocked-by
            $_blocked_by = $GLOBALS['SITE_DB']->query_select('chat_blocking', ['member_blocker'], ['member_blocked' => $viewer_member]);
            $blocked_by = implode(',', collapse_1d_complexity('member_blocker', $_blocked_by));

            // Grabbing who you've blocked
            $_blocking = $GLOBALS['SITE_DB']->query_select('chat_blocking', ['member_blocked'], ['member_blocker' => $viewer_member]);
            $blocking = implode(',', array_map('strval', collapse_1d_complexity('member_blocked', $_blocking)));
        }
    }

    $where_clause = '';

    switch ($mode) {
        case 'some_members': // This is used to view one's own activity (e.g. on a profile)
        case 'some_members_direct':
            foreach ($member_ids as $member_id) {
                if ($where_clause != '') {
                    $where_clause .= ' AND ';
                }

                $_where_clause = '';
                $_where_clause .= '(';
                $_where_clause .= 'a_member_id=' . strval($member_id);
                if ($mode == 'some_members') {
                    $_where_clause .= ' OR ';
                    $_where_clause .= '(';
                    $_where_clause .= 'a_also_involving=' . strval($member_id);
                    if ($blocking != '') {
                        $_where_clause .= ' AND a_member_id NOT IN (' . $blocking . ')';
                    }
                    if (addon_installed('chat')) { // Limit to stuff from this member's friends about them
                        $_where_clause .= ' AND a_member_id IN (SELECT member_liked FROM ' . get_table_prefix() . 'chat_friends WHERE member_likes=' . strval($member_id) . ')';
                    }
                    $_where_clause .= ')';
                }
                $_where_clause .= ')';

                // If the chat addon is installed then there may be 'friends-only' posts, which we may need to filter out. Otherwise we don't need to care.
                if (($member_id != $viewer_member) && (addon_installed('chat'))) {
                    if (!$is_guest) {
                        $friends_check_where = 'member_likes=' . strval($member_id) . ' AND member_liked=' . strval($viewer_member);
                        if ($blocked_by != '') {
                            $friends_check_where .= ' AND member_likes NOT IN (' . $blocked_by . ')';
                        }

                        $view_private = ($GLOBALS['SITE_DB']->query_value_if_there('SELECT member_likes FROM ' . get_table_prefix() . 'chat_friends WHERE ' . $friends_check_where, false, true) !== null);
                    } else {
                        $view_private = false;
                    }

                    if (!$view_private) { // If not friended by this person, the view is filtered.
                        $_where_clause = '(' . $_where_clause . ' AND a_is_public=1)';
                    }
                }

                $where_clause .= $_where_clause;
            }
            break;

        case 'friends':
            // "friends" only makes sense if the chat addon is installed
            if ((addon_installed('chat')) && (!$is_guest)) { // If not a guest, get all reciprocal friendships.
                // Working on the principle that you only want to see people you like on this, only those you like and have not blocked will be selected
                // Exclusions will be based on whether they like and have not blocked you.

                $lm_ids = '';
                if (strlen($blocking) > 0) { // Also setting who gets discarded from outgoing like selection
                    if ($lm_ids != '') {
                        $lm_ids .= ',';
                    }
                    $lm_ids .= $blocking;
                }

                // Also look at friends we like but they don't like back - and include public statuses from them
                $_where_clause = 'member_likes=' . strval($viewer_member);
                if ($blocking != '') {
                    $_where_clause = ' AND member_liked NOT IN (' . $blocking . ')';
                }
                if ($lm_ids != '') {
                    $_where_clause .= ' AND member_liked NOT IN (' . $lm_ids . ')';
                }
                $like_outgoing = $GLOBALS['SITE_DB']->query('SELECT member_liked FROM ' . get_table_prefix() . 'chat_friends WHERE ' . $_where_clause);
                $lo_ids = '';
                foreach ($like_outgoing as $l_o) {
                    if ($lo_ids != '') {
                        $lo_ids .= ',';
                    }
                    $lo_ids .= strval($l_o['member_liked']);
                }

                // Build query
                if ($lm_ids == '' && $lo_ids == '') { // We have no friends yet, so optimise out the query
                    $proceed_selection = false;
                } else {
                    $where_clause = '(';
                    if ($lm_ids != '') {
                        $where_clause .= 'a_member_id IN (' . $lm_ids . ')';
                    }
                    if ($lo_ids != '') {
                        if ($where_clause != '(') {
                            $where_clause .= ' OR ';
                        }
                        $where_clause .= '(a_member_id IN (' . $lo_ids . ') AND a_is_public=1)';
                    }
                    $where_clause .= ')';
                }
            } else {
                $proceed_selection = false; // Optimise out the query
            }
            break;

        case 'all': // Frontpage, 100% permissions dependent.
        default:
            // Work out what the private content the current member can view
            $vp = '';
            if ((addon_installed('chat')) && (!$is_guest)) {
                $friends_check_where = 'member_liked=' . strval($viewer_member);
                if ($blocked_by != '') {
                    $friends_check_where .= ' AND member_likes NOT IN (' . $blocked_by . ')';
                }

                $view_private = $GLOBALS['SITE_DB']->query('SELECT member_likes FROM ' . get_table_prefix() . 'chat_friends WHERE ' . $friends_check_where);
                $view_private[] = ['member_likes' => $viewer_member];
                foreach ($view_private as $v_p) {
                    if ($vp != '') {
                        $vp .= ',';
                    }
                    $vp .= strval($v_p['member_likes']);
                }
            }

            // Build query
            $where_clause = '((a_is_public=1 AND a_member_id<>' . strval($guest_id) . ')';
            if ($vp != '') {
                $where_clause .= ' OR (a_member_id IN (' . $vp . '))';
            }
            $where_clause .= ')';
            if ($blocking != '') {
                $where_clause .= ' AND a_member_id NOT IN (' . $blocking . ')';
            }
            break;
    }

    return [$proceed_selection, $where_clause];
}

/**
 * Render an activity to Tempcode/HTML.
 *
 * @param  array $row Database row
 * @param  boolean $use_inside_cms Whether the rendered activity will be shown in a live Composr (as opposed to being e-mailed, for example)
 * @return array Rendered activity
 */
function render_activity(array $row, bool $use_inside_cms = true) : array
{
    $guest_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();

    // Details of member
    $member_id = $row['a_member_id'];
    $member_avatar = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
    $member_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id, $use_inside_cms);

    $timestamp = $row['a_time'];

    $message = new Tempcode();

    $test = do_lang($row['a_language_string_code'], '{1}', '{2}', '{3}', null, false);
    if ($test === null) {
        $test = do_lang('UNKNOWN');
    }

    // Convert our parameters and links to Tempcode
    $label = [];
    $link = [];
    for ($i = 1; $i <= 3; $i++) {
        $label[$i] = comcode_to_tempcode($row['a_label_' . strval($i)], $guest_id, false);
        $link[$i] = ($row['a_page_link_' . strval($i)] == '') ? new Tempcode() : page_link_to_tempcode_url($row['a_page_link_' . strval($i)], !$use_inside_cms);
        if (($row['a_page_link_' . strval($i)] != '') && (strpos($test, '{' . strval($i + 3) . '}') === false)) {
            $label[$i] = hyperlink($link[$i], $label[$i]->evaluate(), false, false);
        }
    }

    // Render primary language string
    $extra_lang_string_params = [
        $label[3],
        symbol_tempcode('ESCAPE', [$link[1]]),
        symbol_tempcode('ESCAPE', [$link[2]]),
        symbol_tempcode('ESCAPE', [$link[3]]),
    ];
    if ($row['a_also_involving'] !== null) {
        $_username = $GLOBALS['FORUM_DRIVER']->get_username($row['a_also_involving'], true);
        $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($row['a_also_involving'], $use_inside_cms);
        $hyperlink = hyperlink($url, $_username, false, true);
        $extra_lang_string_params[] = $hyperlink;
    } else {
        $extra_lang_string_params[] = do_lang_tempcode('GUEST');
    }
    $message->attach(do_lang_tempcode(
        $row['a_language_string_code'],
        $label[1],
        $label[2],
        $extra_lang_string_params
    ));

    // Language string may not use all params, so add extras on if were unused
    for ($i = 1; $i <= 3; $i++) {
        if ((strpos($row['a_language_string_code'], '_UNTYPED') === false) && (strpos($test, '{1}') === false) && (strpos($test, '{2}') === false) && (strpos($test, '{3}') === false) && ($row['a_label_' . strval($i)] != '')) {
            if (!$message->is_empty()) {
                $message->attach(': ');
            }

            $message->attach($label[$i]->evaluate());
        }
    }

    return [$message, $member_avatar, $timestamp, $member_url, $row['a_language_string_code'], $row['a_is_public'] == 1];
}
