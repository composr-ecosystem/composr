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
 * Wrapper and actualiser to add an item. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $name The name of the item
 * @param  integer $cost The cost the item
 * @param  BINARY $not_infinite Whether the item is finite
 * @param  BINARY $bribable Whether the item may be used for bribes
 * @param  BINARY $healthy Whether the item may be used to provide a health boost
 * @param  URLPATH $picture_url The picture of the item
 * @param  integer $max_per_player The maximum number of these items a player may have
 * @param  BINARY $replicateable Whether the item may be replicated via a new item copy source
 * @param  string $description Description for the item
 */
function add_item_wrap($member_id, $name, $cost, $not_infinite, $bribable, $healthy, $picture_url, $max_per_player, $replicateable, $description)
{
    if ($healthy != 1) {
        $healthy = 0;
    }
    if ($bribable != 1) {
        $bribable = 0;
    }
    if ($not_infinite != 1) {
        $not_infinite = 0;
    }
    if (!($cost > 0)) {
        $cost = 0;
    }
    if (!($max_per_player > 0)) {
        $max_per_player = 0;
    }
    if ($replicateable != 1) {
        $replicateable = 0;
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_EDIT_ACCESS_PRIVATE_REALM'), 'warn');
    }

    // Make sure the item does not already exist! (people aren't allowed to arbitrarily duplicate items for security reasons)
    $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_itemdef', 'bribable', array('name' => $name));
    if (!is_null($r)) {
        buildr_refresh_with_message(do_lang_tempcode('W_DUPE_ITEM'), 'warn');
    }

    // Make sure that they aren't in the brig and adding a bribable!
    if (($x == 0) && ($y == 2) && ($bribable == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Charge them
    if (!has_privilege($member_id, 'administer_buildr')) {
        $price = get_price('mud_item');
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html($price)), 'warn');
        }
        require_code('points2');
        charge_member($member_id, $price, do_lang('W_MADE_BUILDR', $name));
    }

    add_item($name, $bribable, $healthy, $picture_url, $member_id, $max_per_player, $replicateable, $description);

    add_item_to_room($realm, $x, $y, $name, $not_infinite, $cost, $member_id);

    buildr_refresh_with_message(do_lang_tempcode('W_MADE_ITEM_AT', escape_html($name)));
}

/**
 * Actualiser to add an item.
 *
 * @param  string $name The name of the item
 * @param  BINARY $bribable Whether the item may be used for bribes
 * @param  BINARY $healthy Whether the item may be used to provide a health boost
 * @param  URLPATH $picture_url The picture of the item
 * @param  MEMBER $owner The owner of the item
 * @param  integer $max_per_player The maximum number of these items a player may have
 * @param  BINARY $replicateable Whether the item may be replicated via a new item copy source
 * @param  string $description Description for the item
 */
function add_item($name, $bribable, $healthy, $picture_url, $owner, $max_per_player, $replicateable, $description)
{
    $GLOBALS['SITE_DB']->query_insert('w_itemdef', array(
        'name' => $name,
        'bribable' => $bribable,
        'healthy' => $healthy,
        'picture_url' => $picture_url,
        'owner' => $owner,
        'replicateable' => $replicateable,
        'max_per_player' => $max_per_player,
        'description' => $description,
    ));
}

/**
 * Wrapper and actualiser to add an item copy. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $name The name of the item
 * @param  integer $cost The cost of the item copy
 * @param  BINARY $not_infinite Whether the item is finite.
 */
function add_item_wrap_copy($member_id, $name, $cost, $not_infinite)
{
    if ($not_infinite != 1) {
        $not_infinite = 0;
    }
    if (!($cost > 0)) {
        $cost = 0;
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_EDIT_ACCESS_PRIVATE_REALM'), 'warn');
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'owner', array('name' => $name)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'replicateable', array('name' => $name)) == 0)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Make sure that they aren't in the brig and adding a bribable!
    $bribable = $GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'bribable', array('name' => $name));
    if (($x == 0) && ($y == 2) && ($bribable == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NICE_TRY'), 'warn');
    }

    // Charge them
    if (!has_privilege($member_id, 'administer_buildr')) {
        $price = get_price('mud_item_copy');
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($price))), 'warn');
        }
        require_code('points2');
        charge_member($member_id, $price, do_lang('W_MADE_BUILDR', $name));
    }

    add_item_to_room($realm, $x, $y, $name, $not_infinite, $cost, $member_id);

    buildr_refresh_with_message(do_lang_tempcode('W_MADE_ITEM_COPY_AT', escape_html($name)));
}

/**
 * Wrapper and actualiser to add a room. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  integer $relative Code showing where the room will be relative to the members current room. 0=above. 1=below. 2=right. 3=left.
 * @set     0 1 2 3
 * @param  string $name Name for the room
 * @param  string $text Description of room
 * @param  string $password_question Hint for password to enter room
 * @param  string $password_answer Password to enter room
 * @param  string $password_fail_message Message to give if a given entrance password is wrong
 * @param  string $required_item Item required for a member to have to enter the room
 * @param  BINARY $locked_up Whether the room is locked to the top
 * @param  BINARY $locked_down Whether the room is locked to the bottom
 * @param  BINARY $locked_right Whether the room is locked to the right
 * @param  BINARY $locked_left Whether the room is locked to the left
 * @param  URLPATH $picture_url The room's picture
 * @param  BINARY $allow_portal Whether portals may be placed in the room
 */
function add_room_wrap($member_id, $relative, $name, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $allow_portal)
{
    if ($locked_up != 1) {
        $locked_up = 0;
    }
    if ($locked_down != 1) {
        $locked_down = 0;
    }
    if ($locked_right != 1) {
        $locked_right = 0;
    }
    if ($locked_left != 1) {
        $locked_left = 0;
    }
    if ($allow_portal != 1) {
        $allow_portal = 0;
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    // Get $realm,$x,$y from $member_id, $relative
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ($relative == 0) {
        $l = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_up', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($l == 1) {
            buildr_refresh_with_message(do_lang_tempcode('W_WALL_LOCKED'), 'warn');
        }
    }
    if ($relative == 1) {
        $l = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_down', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($l == 1) {
            buildr_refresh_with_message(do_lang_tempcode('W_WALL_LOCKED'), 'warn');
        }
    }
    if ($relative == 2) {
        $l = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_right', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($l == 1) {
            buildr_refresh_with_message(do_lang_tempcode('W_WALL_LOCKED'), 'warn');
        }
    }
    if ($relative == 3) {
        $l = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'locked_left', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
        if ($l == 1) {
            buildr_refresh_with_message(do_lang_tempcode('W_WALL_LOCKED'), 'warn');
        }
    }
    if ($relative == 0) {
        $x += 0;
        $y -= 1;
    }
    if ($relative == 1) {
        $x += 0;
        $y += 1;
    }
    if ($relative == 2) {
        $x += 1;
        $y += 0;
    }
    if ($relative == 3) {
        $x -= 1;
        $y += 0;
    }

    // Check there is no room already there
    $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_realm' => $realm, 'location_x' => $x, 'location_y' => $y));
    if (!is_null($r)) {
        buildr_refresh_with_message(do_lang_tempcode('W_ROOM_ALREADY'), 'warn');
    }

    // Charge them
    if (!has_privilege($member_id, 'administer_buildr')) {
        $price = get_price('mud_room');
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($price))), 'warn');
        }
        require_code('points2');
        charge_member($member_id, $price, do_lang('W_MADE_ROOM_BUILDR', $name));
    }

    add_room($name, $realm, $x, $y, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $member_id, $allow_portal);

    buildr_refresh_with_message(do_lang_tempcode('W_ROOM_CREATED', escape_html($name), strval($realm), array(strval($x), strval($y))));
}

/**
 * Actualiser to edit a room.
 *
 * @param  string $name Name for the room
 * @param  AUTO_LINK $realm The room's realm
 * @param  integer $x The room's X ordinate
 * @param  integer $y The room's  Y ordinate
 * @param  string $text Description of room
 * @param  string $password_question Hint for password to enter room
 * @param  string $password_answer Password to enter room
 * @param  string $password_fail_message Message to give if a given entrance password is wrong
 * @param  string $required_item Item required for a member to have to enter the room
 * @param  BINARY $locked_up Whether the room is locked to the top
 * @param  BINARY $locked_down Whether the room is locked to the bottom
 * @param  BINARY $locked_right Whether the room is locked to the right
 * @param  BINARY $locked_left Whether the room is locked to the left
 * @param  URLPATH $picture_url The room's picture
 * @param  MEMBER $owner Owner of the room
 * @param  BINARY $allow_portal Whether portals may be placed in the room
 */
function add_room($name, $realm, $x, $y, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $owner, $allow_portal)
{
    $GLOBALS['SITE_DB']->query_insert('w_rooms', array(
        'name' => $name,
        'location_realm' => $realm,
        'location_x' => $x,
        'location_y' => $y,
        'r_text' => $text,
        'password_question' => $password_question,
        'password_answer' => $password_answer,
        'password_fail_message' => $password_fail_message,
        'required_item' => $required_item,
        'locked_up' => $locked_up,
        'locked_down' => $locked_down,
        'locked_right' => $locked_right,
        'locked_left' => $locked_left,
        'picture_url' => $picture_url,
        'owner' => $owner,
        'allow_portal' => $allow_portal,
    ));
}

/**
 * Wrapper and actualiser to add a realm. Does not return.
 *
 * @param  ?MEMBER $member_id The member performing the action (null: no member)
 * @param  string $name Name for the realm
 * @param  string $troll_name Name of the realm's troll
 * @param  string $jail_name Name of the jail room
 * @param  string $jail_text Text for the jail room
 * @param  URLPATH $jail_pic_url The picture for the jail room
 * @param  string $jail_house_name Name of the jail house room
 * @param  string $jail_house_text Text for the jail house room
 * @param  URLPATH $jail_house_pic_url The picture for the jail house room
 * @param  string $lobby_name Name of the lobby room
 * @param  string $lobby_text Text for the lobby room
 * @param  URLPATH $lobby_pic_url The picture for the lobby room
 * @param  array $qa List of maps (holding 'q' and 'a') for the trolls questions and answers. Must consist of indices 1 to 30. Blank entries mean 'not set'.
 * @param  BINARY $private Whether the realm is private
 * @param  boolean $redirect Whether to redirect after
 */
function add_realm_wrap($member_id, $name, $troll_name, $jail_name, $jail_text, $jail_pic_url, $jail_house_name, $jail_house_text, $jail_house_pic_url, $lobby_name, $lobby_text, $lobby_pic_url, $qa, $private, $redirect = true)
{
    if ($private != 1) {
        $private = 0;
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }
    if ($troll_name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_TROLL_NAME'), 'warn');
    }
    if ($jail_house_name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_SPECIAL_ROOM_NAME'), 'warn');
    }
    if ($lobby_name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_SPECIAL_ROOM_NAME'), 'warn');
    }
    if ($jail_name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_SPECIAL_ROOM_NAME'), 'warn');
    }

    // Charge them
    if ((!is_null($member_id)) && (!has_privilege($member_id, 'administer_buildr'))) {
        // Have they been a member long enough for a new realm?
        $fortnights = (time() - $GLOBALS['FORUM_DRIVER']->get_member_join_timestamp($member_id)) / (60 * 60 * 24 * 7 * 2);
        $made = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'COUNT(*)', array('owner' => $member_id));
        if ($fortnights < $made) {
            buildr_refresh_with_message(do_lang_tempcode('W_MEMBER_NOT_LONG_ENOUGH'), 'warn');
        }

        $price = get_price('mud_realm');
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($price))), 'warn');
        }
        require_code('points2');
        charge_member($member_id, $price, do_lang('W_MADE_REALM_BUILDR', $name));
    }

    // Find the next available $realm
    $temp = $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'id');
    if (is_null($temp)) {
        $realm = 0; // Our first realm
    } else {
        $temp = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'MAX(id)');
        $realm = $temp + 1;
    }

    add_realm($realm, $name, $troll_name, $qa, $member_id, $private);

    // Add realms default rooms
    add_room($jail_name, $realm, 0, 2, $jail_text, '', '', '', '', 0, 1, 1, 1, $jail_pic_url, $member_id, 1); // Add jail
    add_room($jail_house_name, $realm, 0, 1, $jail_house_text, do_lang('W_DEFAULT_JAIL_TEXT'), 'yes', do_lang('W_DEFAULT_CANCEL'), 'BRIBE', 0, 0, 1, 1, $jail_house_pic_url, $member_id, 0); // Add jailhouse
    add_room($lobby_name, $realm, 0, 0, $lobby_text, '', '', '', '', 0, 0, 0, 0, $lobby_pic_url, $member_id, 1); // Add lobby

    // Add troll
    $troll_id = -$realm - 1;
    $GLOBALS['SITE_DB']->query_insert('w_members', array(
        'id' => $troll_id,
        'location_realm' => $realm,
        'location_x' => 0,
        'location_y' => 0,
        'banned' => 0,
        'health' => 1000,
        'trolled' => 0,
        'lastactive' => time(),
    ));

    if ($redirect) {
        buildr_refresh_with_message(do_lang_tempcode('W_REALM_CREATED', escape_html($name), strval($realm)));
    }
}

/**
 * Output a room screen.
 *
 * @param  AUTO_LINK $id The ID of the realm (this is not an auto-increment)
 * @param  string $name Name for the realm
 * @param  string $troll_name Name of the realm's troll
 * @param  array $qa List of maps (holding 'q' and 'a') for the trolls questions and answers. Must consist of indices 1 to 30. Blank entries mean 'not set'.
 * @param  MEMBER $owner The owner of the realm
 * @param  BINARY $private Whether the realm is private
 */
function add_realm($id, $name, $troll_name, $qa, $owner, $private)
{
    $i = 1;
    $_qa = array(
        'q1' => '', 'q2' => '', 'q3' => '', 'q4' => '',
        'q5' => '', 'q6' => '', 'q7' => '', 'q8' => '',
        'q9' => '', 'q10' => '', 'q11' => '', 'q12' => '',
        'q13' => '', 'q14' => '', 'q15' => '', 'q16' => '',
        'q17' => '', 'q18' => '', 'q19' => '', 'q20' => '',
        'q21' => '', 'q22' => '', 'q23' => '', 'q24' => '',
        'q25' => '', 'q26' => '', 'q27' => '', 'q28' => '',
        'q29' => '', 'q30' => '',
        'a1' => '', 'a2' => '', 'a3' => '', 'a4' => '',
        'a5' => '', 'a6' => '', 'a7' => '', 'a8' => '',
        'a9' => '', 'a10' => '', 'a11' => '', 'a12' => '',
        'a13' => '', 'a14' => '', 'a15' => '', 'a16' => '',
        'a17' => '', 'a18' => '', 'a19' => '', 'a20' => '',
        'a21' => '', 'a22' => '', 'a23' => '', 'a24' => '',
        'a25' => '', 'a26' => '', 'a27' => '', 'a28' => '',
        'a29' => '', 'a30' => '',
    );
    while (array_key_exists($i, $qa)) {
        $_qa['q' . strval($i)] = $qa[$i]['q'];
        $_qa['a' . strval($i)] = $qa[$i]['a'];
        $i++;
    }
    $GLOBALS['SITE_DB']->query_insert('w_realms', array_merge($_qa, array(
        'id' => $id,
        'name' => $name,
        'troll_name' => $troll_name,
        'owner' => $owner,
        'r_private' => $private,
    )));
}

/**
 * Wrapper and actualiser to add a portal. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action (portal comes from where member currently is)
 * @param  string $name Name of the portal
 * @param  string $text Description of the portal
 * @param  AUTO_LINK $end_location_realm The realm the portal goes to
 * @param  integer $end_location_x The X ordinate the portal goes to
 * @param  integer $end_location_y The Y ordinate the portal goes to
 */
function add_portal_wrap($member_id, $name, $text, $end_location_realm, $end_location_x, $end_location_y)
{
    if ($end_location_realm == -1) {
        $end_location_realm = null;
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);
    if (is_null($end_location_realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_DESTINATION_REALM'), 'warn');
    }
    if ((is_null($end_location_x)) || (is_null($end_location_y))) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_DESTINATION'), 'warn');
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    // Check $end_location_realm exists
    $allow_portal = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'allow_portal', array('location_x' => $end_location_x, 'location_y' => $end_location_y, 'location_realm' => $end_location_realm));
    if (is_null($allow_portal)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_END'), 'warn');
    }
    if ($allow_portal == 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_BAD_END'), 'warn');
    }

    // Check we don't have a portal to this realm here already
    $t = $GLOBALS['SITE_DB']->query_select_value_if_there('w_portals', 'name', array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $end_location_realm));
    if (!is_null($t)) {
        buildr_refresh_with_message(do_lang_tempcode('W_DUPE_END'), 'warn');
    }

    if ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'allow_portal', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm)) == 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_BAD_START'), 'warn');
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_EDIT_ACCESS_PRIVATE_REALM'), 'warn');
    }

    /* if ((!has_privilege($member_id,'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $end_location_realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $end_location_realm)) == 1)) Can make a portal to a realm you don't control
            buildr_refresh_with_message(do_lang_tempcode('W_NO_EDIT_ACCESS_PRIVATE_REALM'), 'warn');*/

    // Charge them
    if (!has_privilege($member_id, 'administer_buildr')) {
        require_code('points2');
        $price = get_price('mud_portal');
        if (available_points($member_id) < $price) {
            buildr_refresh_with_message(do_lang_tempcode('W_EXPENSIVE', escape_html(integer_format($price))), 'warn');
        }
        charge_member($member_id, $price, do_lang('W_MADE_PORTAL_BUILDR', $name));
    }

    add_portal($name, $text, $realm, $x, $y, $end_location_realm, $member_id, $end_location_x, $end_location_y);

    $destname = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'name', array('id' => $end_location_realm));

    buildr_refresh_with_message(do_lang_tempcode('W_PORTAL_CREATED', escape_html($name), escape_html($destname)));
}

/**
 * Actualiser to add a portal.
 *
 * @param  string $name Name of the portal
 * @param  string $text Description of the portal
 * @param  AUTO_LINK $realm The realm the portal goes to
 * @param  integer $x The X ordinate the portal goes to
 * @param  integer $y The Y ordinate the portal goes to
 * @param  AUTO_LINK $end_location_realm The realm the portal comes from
 * @param  MEMBER $owner The owner of the realm
 * @param  integer $end_location_x The X ordinate the portal comes from
 * @param  integer $end_location_y The Y ordinate the portal comes from
 */
function add_portal($name, $text, $realm, $x, $y, $end_location_realm, $owner, $end_location_x, $end_location_y)
{
    $GLOBALS['SITE_DB']->query_insert('w_portals', array(
        'name' => $name,
        'p_text' => $text,
        'start_location_realm' => $realm,
        'start_location_x' => $x,
        'start_location_y' => $y,
        'end_location_realm' => $end_location_realm,
        'end_location_x' => $end_location_x,
        'end_location_y' => $end_location_y,
        'owner' => $owner,
    ));
}

/**
 * Wrapper and actualiser to delete an item. Does not return.
 *
 * @param  string $name The name of the item
 */
function delete_item_wrap($name)
{
    $attempt_member = get_member();
    if ((!has_privilege($attempt_member, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'owner', array('name' => $name)) != $attempt_member)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }
    //if (!has_privilege($attempt_member,'administer_buildr'))
    //{
    if ($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_inventory WHERE ' . db_string_equal_to('item_name', $name) . ' AND item_owner<>' . strval($attempt_member)) > 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_CONFISCATE_1'), 'warn');
    }
    if ($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_items WHERE ' . db_string_equal_to('name', $name) . ' AND copy_owner<>' . strval($attempt_member)) > 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_CONFISCATE_2'), 'warn');
    }
    //}

    // Refund them
    require_code('points2');
    $price = get_price('mud_item');
    charge_member($attempt_member, intval(-0.7 * $price), do_lang('W_DELETE_BUILDR', $name));

    // Remove item from all rooms and people
    $GLOBALS['SITE_DB']->query_delete('w_items', array('name' => $name));
    $GLOBALS['SITE_DB']->query_delete('w_inventory', array('item_name' => $name));

    // Delete from db
    $GLOBALS['SITE_DB']->query_delete('w_itemdef', array('name' => $name), '', 1);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Wrapper and actualiser to delete a room. Does not return.
 *
 * @param  MEMBER $member_id The member, who is at the room being deleted
 */
function delete_room_wrap($member_id)
{
    list($realm, $x, $y) = get_loc_details($member_id);

    $attempt_member = $member_id;
    if ((!has_privilege($attempt_member, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'owner', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm)) != $attempt_member)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if (($x == 0) && (($y == 0) || ($y == 1) || ($y == 2))) {
        buildr_refresh_with_message(do_lang_tempcode('W_DEL_MAIN'), 'warn');
    }

    $name = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'name', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));

    // Refund them
    require_code('points2');
    $price = get_price('mud_room');
    charge_member($attempt_member, intval(-0.7 * $price), do_lang('W_DELETE_ROOM_BUILDR', $name));

    delete_room($x, $y, $realm);
}

/**
 * Actualiser to delete a room. Does not return.
 *
 * @param  integer $x The room's source X ordinate
 * @param  integer $y The room's source Y ordinate
 * @param  AUTO_LINK $realm The room's source realm
 */
function delete_room($x, $y, $realm)
{
    // Remove all items from room
    $GLOBALS['SITE_DB']->query_delete('w_items', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));

    // Send all people in room back to lobby
    $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => 0, 'location_y' => 0), array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));

    // Delete from db
    $GLOBALS['SITE_DB']->query_delete('w_rooms', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm), '', 1);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Wrapper and actualiser to delete a portal. Does not return.
 *
 * @param  MEMBER $member_id The member, who is at the room the portal is in
 * @param  AUTO_LINK $dest_realm The portal's destination realm (identifies the portal from those in the room)
 */
function delete_portal_wrap($member_id, $dest_realm)
{
    list($realm, $x, $y) = get_loc_details($member_id);

    $attempt_member = $member_id;
    if ((!has_privilege($attempt_member, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_portals', 'owner', array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $dest_realm)) != $attempt_member)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    $name = $GLOBALS['SITE_DB']->query_select_value('w_portals', 'name', array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm));

    // Refund them
    require_code('points2');
    $price = get_price('mud_portal');
    charge_member($attempt_member, intval(-0.7 * $price), do_lang('W_DELETE_PORTAL_BUILDR', $name));

    delete_portal($x, $y, $realm, $dest_realm);
}

/**
 * Actualiser to delete a portal. Does not return.
 *
 * @param  integer $x The portal's source X ordinate
 * @param  integer $y The portal's source Y ordinate
 * @param  AUTO_LINK $realm The portal's source realm
 * @param  AUTO_LINK $dest_realm The portal's destination realm
 */
function delete_portal($x, $y, $realm, $dest_realm)
{
    // Delete from db
    $GLOBALS['SITE_DB']->query_delete('w_portals', array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $dest_realm), '', 1);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Wrapper and actualiser to delete a realm. Does not return.
 *
 * @param  MEMBER $member_id The member who is in the room
 */
function delete_realm_wrap($member_id)
{
    $attempt_member = $member_id;

    $realm = $GLOBALS['SITE_DB']->query_select_value('w_members', 'location_realm', array('id' => $member_id));
    if ($realm == 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_DEL_PRIMARY_REALM'), 'warn');
    }

    if ((!has_privilege($attempt_member, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $attempt_member)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ($GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_rooms WHERE location_realm=' . strval($realm) . ' AND owner<>' . strval($attempt_member)) > 0) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_DEL_OTHERS_ROOMS_REALM'), 'warn');
    }

    // Refund them
    require_code('points2');
    $price = get_price('mud_realm');
    charge_member($attempt_member, intval(-0.7 * $price), do_lang('W_DELETE_REALM_BUILDR', strval($realm)));

    delete_realm($realm);
}

/**
 * Actualiser to delete a realm. Does not return.
 *
 * @param  AUTO_LINK $realm Realm to delete
 */
function delete_realm($realm)
{
    // Remove all items from realm
    $GLOBALS['SITE_DB']->query_delete('w_items', array('location_realm' => $realm));

    // Remove all rooms from realm
    $GLOBALS['SITE_DB']->query_delete('w_rooms', array('location_realm' => $realm));

    // Delete troll
    $GLOBALS['SITE_DB']->query_delete('w_members', array('id' => -$realm - 1));

    // Send all people in realm back to 0
    $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => 0, 'location_y' => 0), array('location_realm' => $realm));

    // Delete from db
    $GLOBALS['SITE_DB']->query_delete('w_realms', array('id' => $realm), '', 1);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Wrapper and actualiser to edit a room. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $name Name for the room
 * @param  string $text Description of room
 * @param  string $password_question Hint for password to enter room
 * @param  string $password_answer Password to enter room
 * @param  string $password_fail_message Message to give if a given entrance password is wrong
 * @param  AUTO_LINK $required_item Item required for a member to have to enter the room
 * @param  BINARY $locked_up Whether the room is locked to the top
 * @param  BINARY $locked_down Whether the room is locked to the bottom
 * @param  BINARY $locked_right Whether the room is locked to the right
 * @param  BINARY $locked_left Whether the room is locked to the left
 * @param  URLPATH $picture_url The room's picture
 * @param  BINARY $allow_portal Whether portals may be placed in the room
 * @param  MEMBER $new_owner Owner of the room
 * @param  AUTO_LINK $new_x The room's realm
 * @param  integer $new_y The room's X ordinate
 * @param  integer $new_realm The room's Y ordinate
 */
function edit_room_wrap($member_id, $name, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $allow_portal, $new_owner, $new_x, $new_y, $new_realm)
{
    if ($locked_up != 1) {
        $locked_up = 0;
    }
    if ($locked_down != 1) {
        $locked_down = 0;
    }
    if ($locked_right != 1) {
        $locked_right = 0;
    }
    if ($locked_left != 1) {
        $locked_left = 0;
    }
    if ($allow_portal != 1) {
        $allow_portal = 0;
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'owner', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm)) != $member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $new_realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $new_realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Make sure the new x,y,realm is free
    if (($x != $new_x) || ($y != $new_y) || ($realm != $new_realm)) {
        $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', array('location_realm' => $new_realm, 'location_x' => $new_x, 'location_y' => $new_y));
        if (!is_null($r)) {
            buildr_refresh_with_message(do_lang_tempcode('W_ROOM_ALREADY'), 'warn');
        }
    }

    edit_room($name, $realm, $x, $y, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $allow_portal, $new_owner, $new_x, $new_y, $new_realm);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Actualiser to edit a room.
 *
 * @param  string $name Name for the room
 * @param  AUTO_LINK $realm The room's current realm
 * @param  integer $x The room's current X ordinate
 * @param  integer $y The room's current Y ordinate
 * @param  string $text Description of room
 * @param  string $password_question Hint for password to enter room
 * @param  string $password_answer Password to enter room
 * @param  string $password_fail_message Message to give if a given entrance password is wrong
 * @param  AUTO_LINK $required_item Item required for a member to have to enter the room
 * @param  BINARY $locked_up Whether the room is locked to the top
 * @param  BINARY $locked_down Whether the room is locked to the bottom
 * @param  BINARY $locked_right Whether the room is locked to the right
 * @param  BINARY $locked_left Whether the room is locked to the left
 * @param  URLPATH $picture_url The room's picture
 * @param  BINARY $allow_portal Whether portals may be placed in the room
 * @param  MEMBER $new_owner Owner of the room
 * @param  AUTO_LINK $new_x The room's realm
 * @param  integer $new_y The room's X ordinate
 * @param  integer $new_realm The room's Y ordinate
 */
function edit_room($name, $realm, $x, $y, $text, $password_question, $password_answer, $password_fail_message, $required_item, $locked_up, $locked_down, $locked_right, $locked_left, $picture_url, $allow_portal, $new_owner, $new_x, $new_y, $new_realm)
{
    $GLOBALS['SITE_DB']->query_update('w_rooms', array('r_text' => $text, 'password_question' => $password_question, 'password_answer' => $password_answer, 'password_fail_message' => $password_fail_message, 'required_item' => $required_item, 'picture_url' => $picture_url, 'locked_up' => $locked_up, 'locked_down' => $locked_down, 'locked_right' => $locked_right, 'locked_left' => $locked_left, 'name' => $name, 'allow_portal' => $allow_portal, 'location_x' => $new_x, 'location_y' => $new_y, 'location_realm' => $new_realm, 'owner' => $new_owner), array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm), '', 1);
    $GLOBALS['SITE_DB']->query_update('w_members', array('location_x' => $new_x, 'location_y' => $new_y, 'location_realm' => $new_realm), array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm));
    $GLOBALS['SITE_DB']->query_update('w_portals', array('start_location_x' => $new_x, 'start_location_y' => $new_y, 'start_location_realm' => $new_realm), array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm));
}

/**
 * Wrapper and actualiser to edit a realm. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $name Name for the realm
 * @param  string $troll_name Name of the realm's troll
 * @param  array $qa List of maps (holding 'q' and 'a') for the trolls questions and answers. Must consist of indices 1 to 30. Blank entries mean 'not set'.
 * @param  BINARY $private Whether the realm is private
 * @param  ?MEMBER $new_owner The owner of the realm (null: same as $member_id)
 */
function edit_realm_wrap($member_id, $name, $troll_name, $qa, $private, $new_owner)
{
    if ($private != 1) {
        $private = 0;
    }
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }
    if (is_null($new_owner)) {
        $new_owner = $member_id;
    }
    if ($troll_name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_TROLL_NAME'), 'warn');
    }

    $realm = $GLOBALS['SITE_DB']->query_select_value('w_members', 'location_realm', array('id' => $member_id));

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $realm)) != $member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    edit_realm($realm, $name, $troll_name, $qa, $private, $new_owner);
    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Actualiser to edit a realm.
 *
 * @param  AUTO_LINK $realm The realm
 * @param  string $name Name for the realm
 * @param  string $troll_name Name of the realms troll
 * @param  array $qa List of maps (holding 'q' and 'a') for the trolls questions and answers. Must consist of indices 1 to 30. Blank entries mean 'not set'.
 * @param  BINARY $private Whether the realm is private
 * @param  MEMBER $new_owner The owner of the realm
 */
function edit_realm($realm, $name, $troll_name, $qa, $private, $new_owner)
{
    $_qa = array();
    for ($i = 1; $i <= 30; $i++) {
        if (strlen($qa[$i]['q']) > 0) {
            $_qa['q' . strval($i)] = $qa[$i]['q'];
            $_qa['a' . strval($i)] = $qa[$i]['a'];
        } else {
            $_qa['q' . strval($i)] = '';
            $_qa['a' . strval($i)] = '';
        }
    }

    $GLOBALS['SITE_DB']->query_update('w_realms', array_merge($_qa, array('name' => $name, 'troll_name' => $troll_name, 'r_private' => $private, 'owner' => $new_owner)), array('id' => $realm), '', 1);
}

/**
 * Wrapper to edit a portal. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  AUTO_LINK $dest_realm The realm the portal heads to
 * @param  string $name Name of the portal
 * @param  string $text Description of the portal
 * @param  AUTO_LINK $end_location_realm The current realm the portal comes from
 * @param  integer $end_location_x The current X ordinate the portal comes from
 * @param  integer $end_location_y The current Y ordinate the portal comes from
 * @param  MEMBER $new_owner The owner of the realm
 * @param  AUTO_LINK $new_x The realm the portal comes from
 * @param  integer $new_y The X ordinate the portal comes from
 * @param  integer $new_realm The Y ordinate the portal comes from
 */
function edit_portal_wrap($member_id, $dest_realm, $name, $text, $end_location_realm, $end_location_x, $end_location_y, $new_owner, $new_x, $new_y, $new_realm)
{
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    if (is_null($end_location_realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_DESTINATION_REALM'), 'warn');
    }
    if ((is_null($end_location_x)) || (is_null($end_location_y))) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_DESTINATION'), 'warn');
    }
    if (is_null($new_realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_START_REALM'), 'warn');
    }
    if ((is_null($new_x)) || (is_null($new_y))) {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_START'), 'warn');
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_portals', 'owner', array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $dest_realm)) != $member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $new_realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $new_realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_PORTAL_TO_PRIVATE'), 'warn');
    }

    if ($new_realm != $realm) {
        if ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'allow_portal', array('location_x' => $new_x, 'location_y' => $new_y, 'location_realm' => $new_realm)) == 0) {
            buildr_refresh_with_message(do_lang_tempcode('W_BAD_START'), 'warn');
        }
    }

    // Check $end_location_realm exists
    $allow_portal = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'allow_portal', array('location_x' => $end_location_x, 'location_y' => $end_location_y, 'location_realm' => $end_location_realm));
    if (is_null($allow_portal)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_END'), 'warn');
    }
    if (($allow_portal == 0) && ($dest_realm == $end_location_realm)) {
        buildr_refresh_with_message(do_lang_tempcode('W_BAD_END'), 'warn');
    }

    if (($dest_realm != $end_location_realm) || ($x != $new_x) || ($y != $new_y) || ($realm != $new_realm)) {
        // Check we don't have a portal to this realm here already
        $t = $GLOBALS['SITE_DB']->query_select_value_if_there('w_portals', 'name', array('start_location_x' => $new_x, 'start_location_y' => $new_y, 'start_location_realm' => $new_realm, 'end_location_realm' => $end_location_realm));
        if (!is_null($t)) {
            buildr_refresh_with_message(do_lang_tempcode('W_DUPE_END'), 'warn');
        }
    }

    $GLOBALS['SITE_DB']->query_update('w_portals', array('name' => $name, 'p_text' => $text, 'end_location_realm' => $end_location_realm, 'end_location_x' => $end_location_x, 'end_location_y' => $end_location_y, 'start_location_realm' => $new_realm, 'start_location_x' => $new_x, 'start_location_y' => $new_y, 'owner' => $new_owner), array('start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $dest_realm), '', 1);

    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Wrapper to edit an item. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $original_name The name of the item
 * @param  string $name The old name for the item
 * @param  BINARY $bribable Whether the item may be used for bribes
 * @param  BINARY $healthy Whether the item may be used to provide a health boost
 * @param  URLPATH $picture_url The picture of the item
 * @param  MEMBER $new_owner The owner of the item
 * @param  integer $max_per_player The maximum number of these items a player may have
 * @param  BINARY $replicateable Whether the item may be replicated via a new item copy source
 * @param  string $description Description for the item
 */
function edit_item_wrap($member_id, $original_name, $name, $bribable, $healthy, $picture_url, $new_owner, $max_per_player, $replicateable, $description)
{
    if ($name == '') {
        buildr_refresh_with_message(do_lang_tempcode('W_MISSING_NAME'), 'warn');
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'owner', array('name' => $original_name)) != $member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    if ($healthy != 1) {
        $healthy = 0;
    }
    if ($bribable != 1) {
        $bribable = 0;
    }
    if ($replicateable != 1) {
        $replicateable = 0;
    }
    if (!($max_per_player > 0)) {
        $max_per_player = 0;
    }
    edit_item($name, $original_name, $bribable, $healthy, $picture_url, $new_owner, $max_per_player, $replicateable, $description);
    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Edit an item.
 *
 * @param  string $name The name of the item
 * @param  string $original_name The old name for the item
 * @param  BINARY $bribable Whether the item may be used for bribes
 * @param  BINARY $healthy Whether the item may be used to provide a health boost
 * @param  URLPATH $picture_url The picture of the item
 * @param  MEMBER $new_owner The owner of the item
 * @param  integer $max_per_player The maximum number of these items a player may have
 * @param  BINARY $replicateable Whether the item may be replicated via a new item copy source
 * @param  string $description Description for the item
 */
function edit_item($name, $original_name, $bribable, $healthy, $picture_url, $new_owner, $max_per_player, $replicateable, $description)
{
    // Support reuploading
    if (strlen($picture_url) > 0) {
        $GLOBALS['SITE_DB']->query_update('w_itemdef', array('picture_url' => $picture_url), array('name' => $original_name), '', 1);
    }

    // Support renaming
    if ((strlen($name) > 0) && ($name != $original_name)) {
        $r = $GLOBALS['SITE_DB']->query_select_value_if_there('w_itemdef', 'bribable', array('name' => $name));
        if (!is_null($r)) {
            buildr_refresh_with_message(do_lang_tempcode('W_DUPE_ITEM'), 'warn');
        }
        $GLOBALS['SITE_DB']->query_update('w_itemdef', array('name' => $name), array('name' => $original_name), '', 1);
        $GLOBALS['SITE_DB']->query_update('w_items', array('name' => $name), array('name' => $original_name), '', 1);
        $GLOBALS['SITE_DB']->query_update('w_inventory', array('item_name' => $name), array('item_name' => $original_name), '', 1);
    }

    // General editing of template
    $GLOBALS['SITE_DB']->query_update('w_itemdef', array('owner' => $new_owner, 'description' => $description, 'max_per_player' => $max_per_player, 'replicateable' => $replicateable, 'bribable' => $bribable, 'healthy' => $healthy), array('name' => $name), '', 1);
}

/**
 * Wrapper to edit an item copy. Does not return.
 *
 * @param  MEMBER $member_id The member performing the action
 * @param  string $name The name of the item
 * @param  integer $cost The cost of the item copy
 * @param  BINARY $not_infinite Whether the item is finite.
 * @param  integer $new_x The X ordinate of the item copy
 * @param  integer $new_y The Y ordinate of the item copy
 * @param  AUTO_LINK $new_realm The realm of the item copy
 * @param  MEMBER $member The owner of the item copy source
 */
function edit_item_wrap_copy($member_id, $name, $cost, $not_infinite, $new_x, $new_y, $new_realm, $member)
{
    if (!($cost > 0)) {
        $cost = 0;
    }

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_items', 'copy_owner', array('name' => $name)) != $member_id)) {
        buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', $GLOBALS['FORUM_DRIVER']->get_username(get_member())), 'warn');
    }

    // Get $realm,$x,$y from $member_id
    list($realm, $x, $y) = get_loc_details($member_id);

    if ((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', array('id' => $new_realm)) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', array('id' => $new_realm)) == 1)) {
        buildr_refresh_with_message(do_lang_tempcode('W_NO_ACCESS_MOVE'), 'warn');
    }

    if (($x != $new_x) || ($y != $new_y) || ($realm != $new_realm)) {
        // Check we don't have a copy of this item at new dest already
        $t = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'name', array('location_x' => $new_x, 'location_y' => $new_y, 'location_realm' => $new_realm, 'copy_owner' => $member, 'name' => $name));
        if (!is_null($t)) {
            buildr_refresh_with_message(do_lang_tempcode('W_COPY_SOURCE_ALREADY'), 'warn');
        }
    }

    // Fix infinity source thing... we can never make a non-infinite source into an infinite source
    $old_not_infinite = $GLOBALS['SITE_DB']->query_select_value_if_there('w_items', 'not_infinite', array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'copy_owner' => $member, 'name' => $name));
    if (is_null($old_not_infinite)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    if ($old_not_infinite == 1) {
        $not_infinite = 1;
    } elseif ($not_infinite != 1) {
        $not_infinite = 0;
    }

    edit_item_copy($member, $name, $not_infinite, $cost, $new_x, $new_y, $new_realm, $x, $y, $realm);
    buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
}

/**
 * Actualiser to edit an item copy.
 *
 * @param  MEMBER $member_id The owner of the item copy source
 * @param  string $name The name of the item
 * @param  BINARY $not_infinite Whether the item is finite.
 * @param  integer $cost The cost of the item copy
 * @param  integer $new_x The X ordinate of the item copy
 * @param  integer $new_y The Y ordinate of the item copy
 * @param  AUTO_LINK $new_realm The realm of the item copy
 * @param  integer $x The new X ordinate of the item copy
 * @param  integer $y The new Y ordinate of the item copy
 * @param  AUTO_LINK $realm The new realm of the item copy
 */
function edit_item_copy($member_id, $name, $not_infinite, $cost, $new_x, $new_y, $new_realm, $x, $y, $realm)
{
    $GLOBALS['SITE_DB']->query_update('w_items', array('not_infinite' => $not_infinite, 'cost' => $cost, 'location_x' => $new_x, 'location_y' => $new_y, 'location_realm' => $new_realm), array('location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'copy_owner' => $member_id, 'name' => $name));
}
