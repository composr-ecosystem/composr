<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__buildr()
{
    require_css('buildr');
    require_lang('buildr');
    require_code('points');
    require_lang('permissions');
}

/**
 * Helper function. From a given GET/POST parameter name, tries to identify a member ID. Parameter may be given as a direct member ID or via a username.
 *
 * @param  string $param_name The given parameter name.
 * @return ?MEMBER Member ID (null: blank requested)
 */
function grab_new_owner($param_name)
{
    $new_owner_raw = either_param_string($param_name);
    if ($new_owner_raw == '') {
        return null;
    }
    if (is_numeric($new_owner_raw)) {
        return intval($new_owner_raw);
    }
    return $GLOBALS['FORUM_DRIVER']->get_member_from_username($param_name);
}

/**
 * Show the current user a message. Function does not return.
 *
 * @param  Tempcode $message The message to show
 * @param  ID_TEXT $msg_type Code of message type to show
 * @set    warn inform fatal
 */
function buildr_refresh_with_message($message, $msg_type = 'inform')
{
    $url = build_url(array('page' => 'buildr'), '_SELF');

    $title = get_screen_title('MESSAGE');
    $tpl = redirect_screen($title, $url, $message, false, $msg_type);

    $echo = globalise($tpl, null, '', true);
    $echo->evaluate_echo();
    exit();
}

/**
 * Get default Buildr prices.
 *
 * @return array Map of items=>prices.
 */
function get_buildr_prices_default()
{
    return array(
        'mud_portal' => 2,
        'mud_item_copy' => 1,
        'mud_item' => 3,
        'mud_room' => 4,
        'mud_realm' => 30,
    );
}

/**
 * Get a member's position.
 *
 * @param  MEMBER $member_id The member
 * @param  boolean $null_ok Whether it's excusable if the member does not exist (i.e. doesn't exit with error)
 * @return ?array Tuple: Realm, X ordinate, Y ordinate (null: no such member)
 */
function get_loc_details($member_id, $null_ok = false)
{
    $rows = $GLOBALS['SITE_DB']->query_select('w_members', array('location_realm', 'location_x', 'location_y'), array('id' => $member_id), '', 1);
    if (!array_key_exists(0, $rows)) {
        if ($null_ok) {
            return null;
        }
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    return array($rows[0]['location_realm'], $rows[0]['location_x'], $rows[0]['location_y']);
}

/**
 * Merge two items (i.e. merge within inventories, update tallies to reflect new singularity, etc). Function does not return.
 *
 * @param  string $from The first item. The one being deleted.
 * @param  string $to The second item. The one the first is being merged into.
 */
function merge_items($from, $to)
{
    if ($from == $to) {
        warn_exit('Cannot merge item into itself.');
    }

    $GLOBALS['SITE_DB']->query_delete('w_itemdef', array('name' => $from), '', 1);

    $rows = $GLOBALS['SITE_DB']->query_select('w_items', array('*'), array('name' => $from));
    foreach ($rows as $myrow) {
        $amount = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'i_count', array('location_x' => $myrow['location_x'], 'location_y' => $myrow['location_y'], 'location_realm' => $myrow['location_realm'], 'name' => $to, 'copy_owner' => $myrow['copy_owner']));
        if (is_null($amount)) {
            $GLOBALS['SITE_DB']->query_update('w_items', array('name' => $to), array('location_x' => $myrow['location_x'], 'location_y' => $myrow['location_y'], 'location_realm' => $myrow['location_realm'], 'copy_owner' => $myrow['copy_owner'], 'name' => $from));
        } else {
            $GLOBALS['SITE_DB']->query_delete('w_items', array('location_x' => $myrow['location_x'], 'location_y' => $myrow['location_y'], 'location_realm' => $myrow['location_realm'], 'copy_owner' => $myrow['copy_owner'], 'name' => $from));
            $GLOBALS['SITE_DB']->query_update('w_items', array('i_count' => $myrow['i_count'] + $amount), array('location_x' => $myrow['location_x'], 'location_y' => $myrow['location_y'], 'location_realm' => $myrow['location_realm'], 'copy_owner' => $myrow['copy_owner'], 'name' => $to));
        }
    }

    $rows = $GLOBALS['SITE_DB']->query_select('w_inventory', array('*'), array('item_name' => $from));
    foreach ($rows as $myrow) {
        $amount = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_count', array('item_owner' => $myrow['item_owner'], 'item_name' => $to));
        if (is_null($amount)) {
            $GLOBALS['SITE_DB']->query_update('w_inventory', array('item_name' => $to), array('item_owner' => $myrow['item_owner'], 'item_name' => $from));
        } else {
            $GLOBALS['SITE_DB']->query_delete('w_inventory', array('item_owner' => $myrow['item_owner'], 'item_name' => $from));
            $GLOBALS['SITE_DB']->query_update('w_inventory', array('item_count' => $myrow['item_count'] + $amount), array('item_owner' => $myrow['item_owner'], 'item_name' => $to));
        }
    }

    buildr_refresh_with_message(do_lang_tempcode('W_MERGED', escape_html($from), escape_html($to)));
}

/**
 * See if a member is stuck (in some place which is not a room, and if so move them to the realm origin).
 *
 * @param  MEMBER $member_id The member who may be stuck
 */
function destick($member_id)
{
    // Make sure people can't get stuck
    require_code('buildr');
    list($realm, $x, $y) = get_loc_details($member_id);
    $name = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
    if (is_null($name)) {
        $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => 0, 'location_y' => 0, 'location_realm' => 0), array('id' => $member_id), '', 1);
    }
}

/**
 * Delete all messages in room by a person.
 *
 * @param  MEMBER $member_id The member who's message is being deleted.
 * @param  MEMBER $dest_member_id The member who's the message was addressed to.
 * @param  string $message The message.
 */
function delete_message($member_id, $dest_member_id, $message)
{
    list($realm, $x, $y) = get_loc_details($member_id);

    $query = 'DELETE FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_messages WHERE originator_id=' . strval($dest_member_id) . ' AND location_x=' . strval($x) . ' AND location_y=' . strval($y) . ' AND location_realm=' . strval($realm) . ' AND m_message LIKE \'%' . db_encode_like($message) . '%\'';
    $GLOBALS['SITE_DB']->query($query);
}

/**
 * Go through a portal. Function does not return.
 *
 * @param  MEMBER $member_id The member going through
 * @param  AUTO_LINK $dest_realm The chosen destination realm
 */
function portal($member_id, $dest_realm)
{
    // Find destination realm for the portal in the users current room
    list($realm, $x, $y) = get_loc_details($member_id);
    $portals = $GLOBALS['SITE_DB']->query_select('w_portals', array('*'), array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $dest_realm), '', 1);
    if (!array_key_exists(0, $portals)) {
        buildr_refresh_with_message(do_lang_tempcode('INTERNAL_ERROR'), 'warn');
    }
    $text = comcode_to_tempcode($portals[0]['p_text'], $portals[0]['owner']);
    $dest_x = $portals[0]['end_location_x'];
    $dest_y = $portals[0]['end_location_y'];

    // Check $end_location_realm exists
    $name = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_x' => $dest_x, 'location_y' => $dest_y, 'location_realm' => $dest_realm));
    if (is_null($name)) {
        buildr_refresh_with_message(do_lang_tempcode('INTERNAL_ERROR'), 'warn');
    }

    // Move member there
    $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => $dest_x, 'location_y' => $dest_y, 'location_realm' => $dest_realm), array('id' => $member_id), '', 1);

    // Show move message
    buildr_refresh_with_message($text);
}

/**
 * Take all a members items and give it to the realm's troll.
 *
 * @param  MEMBER $member_id The member
 */
function take_items($member_id)
{
    list($realm, ,) = get_loc_details($member_id);
    $realm_troll = -$realm - 1;

    while ($GLOBALS['SITE_DB']->query_select_value('w_inventory', 'COUNT(*)', array('item_owner' => $member_id)) > 0) {
        $item_name = $GLOBALS['SITE_DB']->query_select_value('w_inventory', 'item_name', array('item_owner' => $member_id));

        remove_item_person($member_id, $item_name);
        add_item_person($realm_troll, $item_name);
    }
}

/**
 * Post a message in the current room, addressed to a certain member.
 *
 * @param  MEMBER $member_id The member sending the message
 * @param  string $message The message
 * @param  MEMBER $destination Who the message is being sent to
 */
function message($member_id, $message, $destination)
{
    if ($message == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_MESSAGE_GIVEN'), 'warn');
    }

    list($realm, $x, $y) = get_loc_details($member_id);

    $GLOBALS['SITE_DB']->query_insert('w_messages', array(
        'location_realm' => $realm,
        'location_x' => $x,
        'location_y' => $y,
        'm_message' => $message,
        'originator_id' => $member_id,
        'm_datetime' => time(),
        'destination' => $destination,
    ));
}

/**
 * See if a room exists at a coordinate.
 *
 * @param  integer $x X ordinate
 * @param  integer $y Y ordinate
 * @param  AUTO_LINK $realm Realm
 * @return boolean Whether there is a room there
 */
function room_exists($x, $y, $realm)
{
    $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    return !is_null($r);
}

/**
 * Someone is trying to move around in the environment: we need to put all the rules in place accordingly.
 *
 * @param  MEMBER $member_id The member moving
 * @param  integer $dx X distance
 * @param  integer $dy Y distance
 * @param  string $given_password The access password they have given
 * @return ?Tempcode Error message (null: no error)
 */
function try_to_enter_room($member_id, $dx, $dy, $given_password)
{
    // Validate that we aren't cheating
    if (($dx > 1) || ($dx < -1) || ($dy > 1) || ($dy < -1)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Get current position of member
    list($realm, $x, $y) = get_loc_details($member_id);
    $_x = $x + $dx;
    $_y = $y + $dy;

    // Make sure they are not passing through a locked (solid) wall
    if ($dx == 1) {
        $locked = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_right', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    } elseif ($dx == -1) {
        $locked = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_left', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    } elseif ($dy == 1) {
        $locked = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_down', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    } elseif ($dy == -1) {
        $locked = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_up', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    }
    if ($locked == 1) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Does the room exist?
    if (!room_exists($_x, $_y, $realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_ROOM_NO_EXIST'), 'warn');
    }

    $rooms = $GLOBALS['SITE_DB']->query_select('w_rooms', array('*'), array('location_realm' => $realm, 'location_x' => $_x, 'location_y' => $_y), '', 1);
    $room = $rooms[0];

    $owner = $room['owner'];

    // Is there a password?
    $question = $room['password_question'];
    if (strlen($question) > 0) {
        // Was no password given? (thus we must give the question)
        if ($given_password == '') {
            return output_question_screen($member_id, $dx, $dy);
        }

        // Or Was the given password wrong? (give them fail message)
        $answer = $room['password_answer'];
        if (!((strtolower($given_password) == 'cheat') && ((has_privilege($member_id, 'administer_buildr')) || ($owner == $member_id)))) { // Admins may enter 'cheat' to always-get-in
            if ((strtolower($answer) != strtolower($given_password))) {
                $GLOBALS['SITE_DB']->query_insert('w_attempts', array(
                    'a_datetime' => time(),
                    'attempt' => $given_password,
                    'x' => $_x,
                    'y' => $_y,
                    'realm' => $realm,
                ));
                buildr_refresh_with_message($room['password_fail_message'], 'warn');
            }
        }
    }

    // Is an item required?
    $owner_cheat = false;
    $required_item = $room['required_item'];
    if (strlen($required_item) > 0) {
        // Is it just marked as BRIBE (special case: lose any one bribe item)
        if ($required_item == 'BRIBE') {
            // Check we have one
            $item_name = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_itemdef on item_name=name', 'item_name', array('bribable' => 1, 'item_owner' => $member_id));
            if (is_null($item_name)) {
                buildr_refresh_with_message(do_lang_tempcode('W_NO_BRIBE'), 'warn');
            }

            // Transfer it to the troll
            $troll_id = -$realm - 1;
            if (!has_privilege($member_id, 'administer_buildr')) {
                remove_item_person($member_id, $item_name);
                add_item_person($troll_id, $item_name);
            }
        } else { // Otherwise just a simple check
            $item_name = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_name', array('item_owner' => $member_id, 'item_name' => $required_item));
            if (is_null($item_name)) {
                if ((!has_privilege($member_id, 'administer_buildr')) && ($owner != $member_id)) {
                    $fail = $room['password_fail_message'];
                    if ($fail == '') {
                        $fail = do_lang_tempcode('W_MISSING_REQ_ITEM', escape_html($required_item));
                    }
                    buildr_refresh_with_message($fail, 'warn');
                } else {
                    $owner_cheat = true;
                }
            }
        }
    }

    // Move them in if we are still here
    basic_enter_room($member_id, $realm, $_x, $_y);

    // Is there a troll here?
    $troll_id = $GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_members WHERE location_x=' . strval($_x) . ' AND location_y=' . strval($_y) . ' AND location_realm=' . strval($realm) . ' AND id<0');
    if (!is_null($troll_id)) {
        // Are they unlucky enough for an encounter?
        if (mt_rand(0, 3) == 0) { // 1 in 4 chance of being trolled
            // Make up a trolled value containing 3 (or less) questions (8 bits per question bitmasked/shifted together)
            $num_troll_questions = 0;
            $realms = $GLOBALS['SITE_DB']->query_select('w_realms', array('*'), array('id' => $realm), '', 1);
            $realm = $realms[0];
            while ('' != @$realm['q' . strval($num_troll_questions + 1)]) {
                $num_troll_questions++;
            }
            $questions = min(3, $num_troll_questions);
            $trolled = 0;
            $picked = array();
            for ($i = 0; $i < $questions; $i++) {
                $p = 1;
                do {
                    $p = mt_rand(1, $num_troll_questions);
                } while ((array_key_exists($p, $picked)) && ($picked[$p] == 1));
                $picked[$p] = 1;

                $trolled = $trolled | ($p << (8 * $i));
            }

            // Put trolled into the database
            $GLOBALS['SITE_DB']->query_update('w_members', array('trolled' => $trolled), array('id' => $member_id), '', 1);
            require_code('site2');
            smart_redirect(get_self_url(true));
        }
    }

    if ($owner_cheat) {
        buildr_refresh_with_message(do_lang_tempcode('W_ENTER_CHEAT'), 'warn');
    }
    return null;
}

/**
 * Knock one of the health of this member.
 *
 * @param  MEMBER $member_id The member
 */
function hurt($member_id)
{
    $health = $GLOBALS['SITE_DB']->query_select_value('w_members', 'health', array('id' => $member_id)) - 1;
    $GLOBALS['SITE_DB']->query_update('w_members', array('health' => $health), array('id' => $member_id), '', 1);
}

/**
 * Give one health to this member.
 *
 * @param  MEMBER $member_id The member
 */
function dehurt($member_id)
{
    $health = $GLOBALS['SITE_DB']->query_select_value('w_members', 'health', array('id' => $member_id)) + 1;
    $GLOBALS['SITE_DB']->query_update('w_members', array('health' => $health), array('id' => $member_id), '', 1);
}

/**
 * See if members are in same room and realm.
 *
 * @param  MEMBER $member_id A member
 * @param  MEMBER $dest_member_id Another member
 * @return boolean Whether they are in the same room
 */
function check_coexist($member_id, $dest_member_id)
{
    list($realm_a, $x_a, $y_a) = get_loc_details($member_id);
    list($realm_b, $x_b, $y_b) = get_loc_details($dest_member_id);
    if (($realm_a != $realm_b) || ($x_a != $x_b) || ($y_a != $y_b)) {
        return false;
    }
    return true;
}

/**
 * You can try and pickpocket people in your room, but you have a chance of being imprisoned. Does not return.
 *
 * @param  MEMBER $member_id The member who is doing the pickpocketing.
 * @param  MEMBER $dest_member_id The victim.
 */
function pickpocket($member_id, $dest_member_id)
{
    if ($member_id == $dest_member_id) {
        buildr_refresh_with_message(do_lang_tempcode('W_TOUCHY'), 'warn');
    }

    // Check they are not already in prison. Although it would be very amusing for jailbreakers to have they're stuff pickpocketed and left behind locked up, it would ruin the dynamic
    list(, $x, $y) = get_loc_details($member_id);
    if (($x == 0) && (($y == 1) || ($y == 2))) {
        buildr_refresh_with_message(do_lang_tempcode('W_POCKET_PRISON'), 'warn');
    }

    // Check members are in same room/realm
    if (!check_coexist($member_id, $dest_member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NOT_SAME_REALM'), 'warn');
    }

    // Were they lucky or unlucky?
    $rand = mt_rand(0, 400);
    if ($rand > 100) {
        $lucky = false;
    } else {
        $lucky = true;
    }

    if ($lucky) {
        $item_name = steal($member_id, $dest_member_id);
        buildr_refresh_with_message(do_lang_tempcode('W_STOLEN', escape_html($item_name)), 'warn');
    } else {
        // Send them to jail
        imprison($member_id);
        buildr_refresh_with_message(do_lang_tempcode('W_JAILED'), 'warn');
    }
}

/**
 * Send a member to prison.
 *
 * @param  MEMBER $member_id Member to imprison
 */
function imprison($member_id)
{
    list($realm, ,) = get_loc_details($member_id);
    basic_enter_room($member_id, $realm, 0, 2);
    take_items($member_id);
}

/**
 * Find a members coordinates/named-position by their username. Does not return (shows info on an output screen).
 *
 * @param  string $dest_member_name The username of the member
 */
function findperson($dest_member_name)
{
    if (is_numeric($dest_member_name)) {
        $dest_member_id = intval($dest_member_name);

        if ($dest_member_id >= 0) {
            $dest_member_name = $GLOBALS['FORUM_DRIVER']->get_username($dest_member_id);
            if (is_null($dest_member_name)) {
                $dest_member_name = do_lang('UNKNOWN');
            }
        } else {
            $dest_member_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', array('id' => (-$dest_member_id - 1)));
        }
    }

    $dest_member_name = str_replace('*', '%', $dest_member_name); // People use *'s for wildcards normally, so let them

    $dest_member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($dest_member_name);
    if (is_null($dest_member_id)) {
        $dest_member_id = $GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_realms WHERE troll_name LIKE \'' . db_encode_like($dest_member_name) . '\'');
        if (is_null($dest_member_id)) {
            buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
        }
        $dest_member_id = -$dest_member_id - 1;
    }

    // Get coordinates
    $location_x = $GLOBALS['SITE_DB']->query_select_value_if_there('w_members', 'location_x', array('id' => $dest_member_id));
    if (is_null($location_x)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NOT_PLAYING'), 'warn');
    }
    list($location_realm, $location_x, $location_y) = get_loc_details($dest_member_id);

    // Get pleasant name
    $room_name = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'name', array('location_x' => $location_x, 'location_y' => $location_y, 'location_realm' => $location_realm));
    $realm_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'name', array('id' => $location_realm));

    // Now give them a screen with the info
    buildr_refresh_with_message(do_lang_tempcode('W_FOUND_PERSON', escape_html($dest_member_name), strval($location_realm), array(strval($location_x), strval($location_y), escape_html($realm_name), escape_html($room_name))));
}

/**
 * Have a member steal a random item from somebody else. If there are no items to steal, an error message is triggered (no return).
 *
 * @param  MEMBER $member_id The member doing the stealing.
 * @param  MEMBER $target The victim.
 * @return string The item stolen.
 */
function steal($member_id, $target)
{
    // Check they have at least one item
    $count = $GLOBALS['SITE_DB']->query_select_value('w_inventory', 'COUNT(*)', array('item_owner' => $target));
    if (!($count >= 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NOTHING_STEAL'), 'warn');
    }

    // Pick one item name from their inventory at random
    $item_nth = mt_rand(0, $count - 1);
    for ($i = 0; $i <= $item_nth; $i++) {
        $item_name = $GLOBALS['SITE_DB']->query_select_value('w_inventory', 'item_name', array('item_owner' => $target));
    }

    remove_item_person($target, $item_name);
    add_item_person($member_id, $item_name);

    return $item_name;
}

/**
 * Ban a member from Buildr. Does not return.
 *
 * @param  MEMBER $member_id Member to ban
 */
function ban_member($member_id)
{
    $GLOBALS['SITE_DB']->query_update('w_members', array('banned' => 1), array('id' => $member_id), '', 1);
    buildr_refresh_with_message(do_lang_tempcode('W_BANNED', strval($member_id)));
}

/**
 * Unban a member from Buildr. Does not return.
 *
 * @param  MEMBER $member_id Member to unban
 */
function unban_member($member_id)
{
    $GLOBALS['SITE_DB']->query_update('w_members', array('banned' => 0), array('id' => $member_id), '', 1);
    buildr_refresh_with_message(do_lang_tempcode('W_UNBANNED', strval($member_id)));
}

/**
 * Have a member use an item they hold. Does not return.
 *
 * @param  MEMBER $member_id The member
 * @param  string $item_name The item
 */
function useitem($member_id, $item_name)
{
    // Is the item held by the user
    if (!item_held($member_id, $item_name)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Is it a healthy item?
    $healthy = $GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'healthy', array('name' => $item_name));
    if ($healthy == 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_USELESS'));
    } else {
        dehurt($member_id);
        remove_item_person($member_id, $item_name);
        buildr_refresh_with_message(do_lang_tempcode('W_HEALTHED', escape_html($item_name)));
    }
}

/**
 * Move a member into a room.
 *
 * @param  MEMBER $member_id The member
 * @param  AUTO_LINK $realm The realm
 * @param  integer $x The X ordinate
 * @param  integer $y The Y ordinate
 */
function basic_enter_room($member_id, $realm, $x, $y)
{
    // Does the room exist?
    if (!room_exists($x, $y, $realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_ROOM'), 'warn');
    }

    // Move them in
    $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm), array('id' => $member_id), '', 1);

    // Show that they've been here
    $GLOBALS['SITE_DB']->query_delete('w_travelhistory', array('member_id' => $member_id, 'x' => $x, 'y' => $y, 'realm' => $realm), '', 1);
    $GLOBALS['SITE_DB']->query_insert('w_travelhistory', array('member_id' => $member_id, 'x' => $x, 'y' => $y, 'realm' => $realm));
}

/**
 * Does a member hold an item?
 *
 * @param  MEMBER $member_id The member
 * @param  string $item_name The item
 * @return boolean The answer
 */
function item_held($member_id, $item_name)
{
    // Is the item held by the user
    $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_count', array('item_owner' => $member_id, 'item_name' => $item_name));
    return !is_null($r);
}

/**
 * Interface for a member to give an item to another member. Does not return.
 *
 * @param  MEMBER $member_id The member giving the item
 * @param  MEMBER $dest_member_id The member receiving the item
 * @param  string $item_name The name of the item
 */
function give($member_id, $dest_member_id, $item_name)
{
    // Is the item held by the user
    if (!item_held($member_id, $item_name)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Check members are in same $realm, $x and $y (admins can give to anyone, anywhere)
    if ((!has_privilege($member_id, 'administer_buildr')) && (!has_privilege($dest_member_id, 'administer_buildr'))) {
        // Check members are in same room/realm
        if (!check_coexist($member_id, $dest_member_id)) {
            buildr_refresh_with_message(do_lang_tempcode('W_NOT_SAME_REALM'), 'warn');
        }
    }

    // Transfer it
    remove_item_person($member_id, $item_name);
    add_item_person($dest_member_id, $item_name);

    buildr_refresh_with_message(do_lang_tempcode('W_GIVEN', escape_html($item_name)));
}

/**
 * Interface for a member to drop an item in the room they are in. Does not return.
 *
 * @param  MEMBER $member_id The member dropping the item
 * @param  string $item The name of the item
 */
function drop_wrap($member_id, $item)
{
    drop($member_id, $item);
    buildr_refresh_with_message(do_lang_tempcode('W_DROPPED', escape_html($item)));
}

/**
 * The actualiser for a member to drop an item in the room they are in.
 *
 * @param  MEMBER $member_id The member dropping the item
 * @param  string $item_name The name of the item
 */
function drop($member_id, $item_name)
{
    // Is the item held by the user
    if (!item_held($member_id, $item_name)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Load $realm, $x and $y for $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    add_item_to_room($realm, $x, $y, $item_name, 1, 0, $member_id);
    remove_item_person($member_id, $item_name);
}

/**
 * The actualiser for a member to buy an item from the room they are in. Does not return.
 *
 * @param  MEMBER $member_id The member buying the item
 * @param  string $item_name The name of the item
 * @param  MEMBER $copy_owner The owner of the item copy
 */
function buy($member_id, $item_name, $copy_owner)
{
    // Check we have the points and that it exists
    list($realm, $x, $y) = get_loc_details($member_id);
    $cost = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'cost', array('name' => $item_name, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'copy_owner' => $copy_owner));
    if (is_null($cost)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ($cost > available_points($member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($cost))), 'warn');
    }
    if ($cost == 0) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Charge them
    if ((!has_privilege($member_id, 'administer_buildr')) || (!is_guest($copy_owner))) {
        require_code('points2');

        $price = $cost;
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($price))), 'warn');
        }
        charge_member($member_id, $price, do_lang('W_BOUGHT_BUILDR', escape_html($item_name)));

        charge_member($copy_owner, -$price * 0.7, do_lang('W_SOLD_BUILDR', escape_html($item_name)));
    }

    basic_pickup($member_id, $item_name, $copy_owner);

    buildr_refresh_with_message(do_lang_tempcode('W_BOUGHT', escape_html($item_name), escape_html(integer_format($cost))));
}

/**
 * The actualiser for a member to take an item from the room they are in. Does not return.
 *
 * @param  MEMBER $member_id The member taking the item
 * @param  string $item_name The name of the item
 * @param  MEMBER $copy_owner The owner of the item copy
 */
function take($member_id, $item_name, $copy_owner)
{
    // Check its free and exists
    list($realm, $x, $y) = get_loc_details($member_id);
    $cost = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'cost', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
    if (is_null($cost)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ($cost != 0) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    $max_per_player = $GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'max_per_player', array('name' => $item_name));
    $count = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_count', array('item_name' => $item_name, 'item_owner' => $member_id));
    if (($count >= $max_per_player) && ($count >= 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_EXCESS'), 'warn');
    }

    basic_pickup($member_id, $item_name, $copy_owner);

    buildr_refresh_with_message(do_lang_tempcode('W_TOOK', escape_html($item_name)));
}

/**
 * Wrapper actualiser for a member to pick up an item from the room they are in.
 *
 * @param  MEMBER $member_id The member picking up the item
 * @param  string $item_name The name of the item
 * @param  MEMBER $copy_owner The owner of the item copy
 */
function basic_pickup($member_id, $item_name, $copy_owner)
{
    add_item_person($member_id, $item_name);

    list($realm, $x, $y) = get_loc_details($member_id);

    take_an_item_from_room($realm, $x, $y, $item_name, $copy_owner);
}

/**
 * Actualiser to remove an item to a room.
 *
 * @param  AUTO_LINK $realm The realm
 * @param  integer $x The X ordinate
 * @param  integer $y The Y ordinate
 * @param  string $item_name The name of the item
 * @param  MEMBER $copy_owner The owner of the item copy
 */
function take_an_item_from_room($realm, $x, $y, $item_name, $copy_owner)
{
    // Does the item need to be removed? (is it infinite, Or maybe it has a count to be decremented?)
    // =================================

    $not_infinite = $GLOBALS['SITE_DB']->query_select_value('w_items', 'not_infinite', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
    if ($not_infinite == 1) {
        // Not an infinite one, so decrement the count / delete it
        $count = $GLOBALS['SITE_DB']->query_select_value('w_items', 'i_count', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($count == 0) {
            fatal_exit(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())));
        }
        if ($count > 1) {
            $GLOBALS['SITE_DB']->query_update('w_items', array('i_count' => $count - 1), array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        } else {
            $GLOBALS['SITE_DB']->query_delete('w_items', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        }
    }
}

/**
 * Actualiser to add an item to a room.
 *
 * @param  AUTO_LINK $realm The realm
 * @param  integer $x The X ordinate
 * @param  integer $y The Y ordinate
 * @param  string $item_name The name of the item
 * @param  BINARY $not_infinite Whether the item source is finite in quantity
 * @param  integer $cost The cost of the item
 * @param  MEMBER $copy_owner The owner of the item copy
 */
function add_item_to_room($realm, $x, $y, $item_name, $not_infinite, $cost, $copy_owner)
{
    // Does the item need to be created or is it there? (and is it infinite, or maybe it has a count to be incremented?)
    // ================================================

    $count = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'i_count', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
    if (!is_null($count)) { // It already exists
        $_not_infinite = $GLOBALS['SITE_DB']->query_select_value('w_items', 'not_infinite', array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($_not_infinite == 1) {
            // Not an infinite one, so increment the count
            if ($count == 0) {
                fatal_exit(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())));
            }
            $GLOBALS['SITE_DB']->query_update('w_items', array('i_count' => $count + 1), array('name' => $item_name, 'copy_owner' => $copy_owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm), '', 1);
        } // No need for an else, because an infinite item doesn't need adding to
    } else { // We just create it
        $GLOBALS['SITE_DB']->query_insert('w_items', array(
            'name' => $item_name,
            'location_realm' => $realm,
            'location_x' => $x,
            'location_y' => $y,
            'not_infinite' => $not_infinite,
            'cost' => $cost,
            'i_count' => 1,
            'copy_owner' => $copy_owner,
        ));
    }
}

/**
 * Actualiser to remove an item to somebodies inventory.
 *
 * @param  MEMBER $member_id The member who loses the item
 * @param  string $item_name The item to be taken from the members inventory
 */
function remove_item_person($member_id, $item_name)
{
    // Make sure we don't remove all items of the same name at once (decrement count if count>1)
    $count = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_count', array('item_name' => $item_name, 'item_owner' => $member_id));
    if ($count > 1) {
        $GLOBALS['SITE_DB']->query_update('w_inventory', array('item_count' => $count - 1), array('item_name' => $item_name, 'item_owner' => $member_id));
    } else {
        $GLOBALS['SITE_DB']->query_delete('w_inventory', array('item_name' => $item_name, 'item_owner' => $member_id));
    }
}

/**
 * Actualiser to add an item to somebodies inventory.
 *
 * @param  MEMBER $member_id The member who gets the item
 * @param  string $item_name The item to be added to the members inventory
 */
function add_item_person($member_id, $item_name)
{
    // Do they already have one
    $count = $GLOBALS['SITE_DB']->query_select_value_if_there('w_inventory', 'item_count', array('item_name' => $item_name, 'item_owner' => $member_id));
    if (!is_null($count)) {
        $GLOBALS['SITE_DB']->query_update('w_inventory', array('item_count' => $count + 1), array('item_name' => $item_name, 'item_owner' => $member_id));
    } else {
        $GLOBALS['SITE_DB']->query_insert('w_inventory', array(
            'item_name' => $item_name,
            'item_count' => 1,
            'item_owner' => $member_id,
        ));
    }
}
