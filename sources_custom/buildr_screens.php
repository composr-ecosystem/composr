<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Output the Buildr realms screen.
 */
function realms()
{
    $rows = $GLOBALS['SITE_DB']->query_select('w_realms', ['*']);
    $out = new Tempcode();
    foreach ($rows as $myrow) {
        $owner = ($myrow['owner'] !== null) ? $GLOBALS['FORUM_DRIVER']->get_username($myrow['owner'], false, USERNAME_DEFAULT_NULL) : null;
        if ($owner === null) {
            $owner = do_lang('UNKNOWN');
            $url = '';
        } else {
            $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($myrow['owner'], true);
        }
        if ($myrow['r_private'] == 1) {
            $pp = do_lang_tempcode('W_PRIVATE_REALM');
        } else {
            $pp = do_lang_tempcode('W_PUBLIC_REALM');
        }
        $rooms = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'COUNT(*)', ['location_realm' => $myrow['id']]);

        $out->attach(do_template('W_REALM_LIST_ENTRY', ['_GUID' => '7dc973deec3b92b1b2c189ae14451a5b', 'NAME' => $myrow['name'], 'ID' => strval($myrow['id']), 'ROOMS' => strval($rooms), 'PP' => $pp, 'OWNER' => $owner, 'URL' => $url]));
    }
    buildr_refresh_with_message($out);
}

/**
 * Allow them to answer a room question.
 *
 * @param  MEMBER $member_id The member answering
 * @param  integer $dx The X-offset of where they WISH to travel to
 * @param  integer $dy The Y-offset of where they WISH to travel to
 * @return Tempcode Interface
 */
function output_question_screen(int $member_id, int $dx, int $dy) : object
{
    list($realm, $x, $y) = get_loc_details($member_id);
    $x += $dx;
    $y += $dy;
    $question = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'password_question', ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm]);

    $title = get_screen_title('W_PROTECTED_ROOM');

    return do_template('W_QUESTION_SCREEN', ['_GUID' => '15c560f0f0ae3570f31beac8684e1e0d', 'DX' => strval($dx), 'DY' => strval($dy), 'QUESTION' => $question, 'TITLE' => $title]);
}

/**
 * Output an inventory screen.
 *
 * @param  MEMBER $member_id The member the inventory is of
 * @return Tempcode Interface
 */
function output_inventory_screen(int $member_id) : object
{
    if (($member_id === null) || ($member_id == -1)) {
        $member_id = get_member();
    }

    $photo = '';
    $avatar = '';
    if ($member_id > $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
        if (method_exists($GLOBALS['FORUM_DRIVER'], 'get_member_photo_url')) {
            $photo = $GLOBALS['FORUM_DRIVER']->get_member_photo_url($member_id);
        }
        if (method_exists($GLOBALS['FORUM_DRIVER'], 'get_member_avatar_url')) {
            $avatar = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
        }
    } else {
        $username = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', ['id' => (-$member_id - 1)]);
    }
    $title = get_screen_title('W_INVENTORY_OF', true, [escape_html($username)]);
    $health = $GLOBALS['SITE_DB']->query_select_value('w_members', 'health', ['id' => $member_id]);

    $rows = $GLOBALS['SITE_DB']->query_select('w_inventory', ['*'], ['item_owner' => $member_id], 'ORDER BY item_name');
    $inventory = new Tempcode();
    foreach ($rows as $myrow) {
        $item_rows = $GLOBALS['SITE_DB']->query_select('w_itemdef', ['*'], ['name' => $myrow['item_name']], '', 1);
        if (!array_key_exists(0, $item_rows)) {
            continue;
        }
        $item_row = $item_rows[0];
        $pic_url = $item_row['picture_url'];
        if ((url_is_local($pic_url)) && ($pic_url != '')) {
            $pic_url = get_custom_base_url() . '/' . str_replace(' ', '%20', $pic_url);
        }
        $description = $item_row['the_description'];
        $bribable = $item_row['bribable'];
        $healthy = $item_row['healthy'];

        $inventory->attach(do_template('W_INVENTORY_ITEM', [
            '_GUID' => '6850866532d2e5a65ca1b74f5ed8e49a',
            'HEALTHY' => $healthy == 1,
            'BRIBABLE' => $bribable == 1,
            'PIC_URL' => $pic_url,
            'ITEM_NAME' => $myrow['item_name'],
            'DESCRIPTION' => $description,
            'ITEM_COUNT' => integer_format($myrow['item_count'], 0),
        ]));
    }

    return do_template('W_INVENTORY_SCREEN', [
        '_GUID' => '74dd29919831eb75212b9805511fdca8',
        'TITLE' => $title,
        'USERNAME' => $username,
        'HEALTH' => integer_format($health, 0),
        'AVATAR' => $avatar,
        'PHOTO' => $photo,
        'INVENTORY' => $inventory,
    ]);
}

/**
 * Output a room screen.
 *
 * @param  MEMBER $member_id The member who is in the room
 * @return Tempcode Interface
 */
function output_room_screen(int $member_id) : object
{
    $title = get_screen_title('BUILDR');

    /*require_code('templates_internalise_screen'); Would require lots of work to make sure all links point right
    $test_tpl = internalise_own_screen($title);
    if (is_object($test_tpl)) return $test_tpl;*/

    destick($member_id);

    require_lang('chat');
    require_lang('menus');
    require_javascript('editing');

    list($realm, $x, $y) = get_loc_details($member_id);

    $rooms = $GLOBALS['SITE_DB']->query_select('w_rooms', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm], '', 1);
    $room = $rooms[0];
    $room_name = $room['name'];
    $room_text = comcode_to_tempcode($room['r_text'], $room['owner']);
    $pic_url = str_replace(' ', '%20', $room['picture_url']);
    if ((url_is_local($pic_url)) && ($pic_url != '')) {
        $pic_url = get_custom_base_url() . '/' . $pic_url;
    }
    $up_room = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_x' => $x, 'location_y' => $y - 1, 'location_realm' => $realm]);
    $locked_up = $room['locked_up'];
    if (($locked_up == 1) || ($up_room === null)) {
        $up_room = '';
    }
    $down_room = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_x' => $x, 'location_y' => $y + 1, 'location_realm' => $realm]);
    $locked_down = $room['locked_down'];
    if (($locked_down == 1) || ($down_room === null)) {
        $down_room = '';
    }
    $right_room = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_x' => $x + 1, 'location_y' => $y, 'location_realm' => $realm]);
    $locked_right = $room['locked_right'];
    if (($locked_right == 1) || ($right_room === null)) {
        $right_room = '';
    }
    $left_room = $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_x' => $x - 1, 'location_y' => $y, 'location_realm' => $realm]);
    $locked_left = $room['locked_left'];
    if (($locked_left == 1) || ($left_room === null)) {
        $left_room = '';
    }
    $realm_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'name', ['id' => $realm]);

    $has_up_room = (!cms_empty_safe($up_room)) && ($locked_up == 0);
    $has_left_room = (!cms_empty_safe($left_room)) && ($locked_left == 0);
    $has_right_room = (!cms_empty_safe($right_room)) && ($locked_right == 0);
    $has_down_room = (!cms_empty_safe($down_room)) && ($locked_down == 0);

    $rows = $GLOBALS['SITE_DB']->query_select('w_portals', ['name', 'end_location_realm', 'owner'], ['start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm], 'ORDER BY name');
    $portals = new Tempcode();
    foreach ($rows as $myrow) {
        $dest_realm = $myrow['end_location_realm'];

        $editable = ((has_privilege($member_id, 'administer_buildr')) || ($myrow['owner'] == $member_id));

        $portals->attach(do_template('W_MAIN_PORTAL', ['_GUID' => 'f5582c18b71be98d74dfb8d2b777afc0', 'NAME' => $myrow['name'], 'EDITABLE' => $editable, 'DEST_REALM' => strval($dest_realm)]));
    }

    $other_person = post_param_integer('tmember', -1);
    $rows = $GLOBALS['SITE_DB']->query_select('w_members', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm], 'ORDER BY lastactive DESC');
    $people_here = new Tempcode();
    foreach ($rows as $myrow) {
        $this_member_name = $GLOBALS['FORUM_DRIVER']->get_username($myrow['id']);
        $people_here->attach(do_template('W_MAIN_PERSON_HERE', ['_GUID' => 'd799e4343da8daa27459b097bc3b2e89', 'THIS_MEMBER_NAME' => $this_member_name, 'ID' => strval($myrow['id']), 'SELECTED' => false]));
    }

    $rows = $GLOBALS['SITE_DB']->query_select('w_members', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm], 'ORDER BY lastactive DESC', 15);
    $members = new Tempcode();
    foreach ($rows as $myrow) {
        $id = $myrow['id'];
        $health = $myrow['health'];

        $aux = new Tempcode();
        if ($id >= 0) {
            $name = $GLOBALS['FORUM_DRIVER']->get_username($id);
            if ($myrow['banned'] == 1) {
                $aux = do_lang_tempcode('W_IS_BANNED');
            } elseif ($myrow['health'] < 1) {
                $aux = do_lang_tempcode('W_DEAD');
            }
        } else {
            $name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', ['id' => (-$id - 1)]);
            $aux = do_lang_tempcode('W_IS_TROLL');
        }
        if ($id == $member_id) {
            $style = 'buildr-self-member';
        } else {
            $style = 'buildr-other-member';
        }

        if ($id < 0) {
            $member_url = '';
        } else {
            $member_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($id, true);
        }

        $members->attach(do_template('W_MAIN_MEMBER', [
            '_GUID' => '83d9f930b68d4988b009b3c06ef783e9',
            'HEALTH' => integer_format($health, 0),
            'ID' => strval($id),
            'MEMBER_URL' => $member_url,
            'STYLE' => $style,
            'NAME' => $name,
            'AUX' => $aux,
        ]));
    }

    $rows = $GLOBALS['SITE_DB']->query_select('w_items', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'price' => 0], 'ORDER BY name');
    $items = new Tempcode();
    foreach ($rows as $myrow) {
        $rows2 = $GLOBALS['SITE_DB']->query_select('w_itemdef', ['*'], ['name' => $myrow['name']], '', 1);
        $myrow2 = $rows2[0];

        $aux = new Tempcode();
        if (($myrow2['healthy'] == 1) && ($myrow2['bribable'] == 1)) {
            $aux = do_lang_tempcode('W_IS_HEALTH_AND_BRIBABLE');
        } elseif ($myrow2['healthy'] == 1) {
            $aux = do_lang_tempcode('W_IS_HEALTHY');
        } elseif ($myrow2['bribable'] == 1) {
            $aux = do_lang_tempcode('W_IS_BRIBABLE');
        }
        $edit_item_copy_access = ((has_privilege($member_id, 'administer_buildr')) || ($myrow['copy_owner'] == $member_id));

        $count = ($myrow['not_infinite'] == 1) ? make_string_tempcode(integer_format($myrow['i_count'], 0)) : do_lang_tempcode('W_INFINITE');

        $picture_url = $myrow2['picture_url'];
        if ((url_is_local($picture_url)) && ($picture_url != '')) {
            $picture_url = get_custom_base_url() . '/' . str_replace(' ', '%20', $picture_url);
        }

        $items->attach(do_template('W_MAIN_ITEM', [
            '_GUID' => 'c144fda9edfe61750aba4afbce7b9b02',
            'ACTION' => do_lang_tempcode('W_TAKE'),
            'EDIT_ACCESS' => $edit_item_copy_access,
            'MEMBER' => strval($myrow['copy_owner']),
            'DESCRIPTION' => $myrow2['the_description'],
            'PICTURE_URL' => $picture_url,
            'AUX' => $aux,
            'NAME' => $myrow['name'],
            'COUNT' => $count,
        ]));
    }

    $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_items WHERE location_x=' . strval($x) . ' AND location_y=' . strval($y) . ' AND location_realm=' . strval($realm) . ' AND price>0');
    $items_sale = new Tempcode();
    foreach ($rows as $myrow) {
        $rows2 = $GLOBALS['SITE_DB']->query_select('w_itemdef', ['*'], ['name' => $myrow['name']], '', 1);
        $myrow2 = $rows2[0];

        $seller = $GLOBALS['FORUM_DRIVER']->get_username($myrow['copy_owner']);

        $aux = new Tempcode();
        if (($myrow2['healthy'] == 1) && ($myrow2['bribable'] == 1)) {
            $aux = do_lang_tempcode('W_IS_HEALTH_AND_BRIBABLE');
        } elseif ($myrow2['healthy'] == 1) {
            $aux = do_lang_tempcode('W_IS_HEALTHY');
        } elseif ($myrow2['bribable'] == 1) {
            $aux = do_lang_tempcode('W_IS_BRIBABLE');
        }
        $edit_item_copy_access = ((has_privilege($member_id, 'administer_buildr')) || ($myrow['copy_owner'] == $member_id));

        $count = ($myrow['not_infinite'] == 1) ? make_string_tempcode(integer_format($myrow['i_count'], 0)) : do_lang_tempcode('W_INFINITE');

        $items_sale->attach(do_template('W_MAIN_ITEM', [
            '_GUID' => 'ab12f8373378abe5852f1d8ee0a05f27',
            'ACTION' => do_lang_tempcode('W_BUY'),
            'SELLER' => $seller,
            'EDIT_ACCESS' => $edit_item_copy_access,
            'MEMBER' => strval($myrow['copy_owner']),
            'DESCRIPTION' => $myrow2['the_description'],
            'PICTURE_URL' => $myrow2['picture_url'],
            'AUX' => $aux,
            'NAME' => $myrow['name'],
            'COUNT' => $count,
            'PRICE' => integer_format($myrow['price'], 0),
        ]));
    }

    $hide_actions = ((array_key_exists('hideActions', $_COOKIE)) && ($_COOKIE['hideActions'] == 1)) ? 'display: block;' : 'display: none;';
    $hide_additions = ((array_key_exists('hideAdditions', $_COOKIE)) && ($_COOKIE['hideAdditions'] == 1)) ? 'display: block;' : 'display: none;';
    $hide_modifications = ((array_key_exists('hideModifications', $_COOKIE)) && ($_COOKIE['hideModifications'] == 1)) ? 'display: block;' : 'display: none;';

    // PEOPLE HERE
    $other_person = post_param_integer('member', -1);
    $rows = $GLOBALS['SITE_DB']->query_select('w_members', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm], 'ORDER BY lastactive DESC');
    $people_here = new Tempcode();
    foreach ($rows as $myrow) {
        if ($myrow['id'] >= 0) {
            $this_member_name = $GLOBALS['FORUM_DRIVER']->get_username($myrow['id']);
        } else {
            $this_member_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', ['id' => (-$myrow['id'] - 1)]);
        }
        $selected = (($myrow['id'] == $other_person) && ($other_person !== null)) || ($myrow['id'] == $other_person);
        $people_here->attach(do_template('W_MAIN_PERSON_HERE', ['_GUID' => '4fd3908b7b47338a4f47710c85a060ac', 'THIS_MEMBER_NAME' => $this_member_name, 'ID' => strval($myrow['id']), 'SELECTED' => $selected]));
    }
    if (has_privilege($member_id, 'administer_buildr')) {
        $people_here->attach(do_template('W_MAIN_PEOPLE_SEP'));
        $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_members WHERE location_x<>' . strval($x) . ' AND location_y<>' . strval($y) . ' AND location_realm<>' . strval($realm) . ' ORDER BY lastactive DESC', 30);
        foreach ($rows as $myrow) {
            if ($myrow['id'] >= 0) {
                $this_member_name = $GLOBALS['FORUM_DRIVER']->get_username($myrow['id']);
            } else {
                $this_member_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', ['id' => (-$myrow['id'] - 1)]);
            }

            $people_here->attach(do_template('W_MAIN_PERSON_HERE', ['_GUID' => '16291e73031cc0254fa6d974d984d353', 'THIS_MEMBER_NAME' => $this_member_name, 'ID' => strval($myrow['id']), 'SELECTED' => false]));
        }
    }
    $people_here = do_template('W_MAIN_PEOPLE_HERE', ['_GUID' => 'c179c25a84cb2919b9bf59f42a77d1dc', 'CONTENT' => $people_here]);

    // ITEMS HELD
    $item = post_param_string('item', '');
    $rows = $GLOBALS['SITE_DB']->query_select('w_inventory', ['*'], ['item_owner' => $member_id], 'ORDER BY item_name');
    $items_held = new Tempcode();
    foreach ($rows as $myrow) {
        $items_held->attach(do_template('W_MAIN_ITEM_OWNED', ['_GUID' => '85b1de9cf8c11b535b28bf033fa11dc9', 'SELECTED' => false, 'NAME' => $myrow['item_name']]));
    }
    $items_held = do_template('W_MAIN_ITEMS_HELD', ['_GUID' => 'bbbc447a1d2ac0aed85ef57ecae79415', 'CONTENT' => $items_held]);

    // ITEMS OWNED
    $item = post_param_string('item', '');
    $rows = $GLOBALS['SITE_DB']->query_select('w_itemdef', ['*'], ['owner' => $member_id], 'ORDER BY name');
    $_items_owned = new Tempcode();
    foreach ($rows as $myrow) {
        $selected = $myrow['name'] == $item;
        $tpl = do_template('W_MAIN_ITEM_OWNED', ['_GUID' => 'dfa2971a5196b3c60d9bbd5240b0d269', 'SELECTED' => $selected, 'NAME' => $myrow['name']]);
        $_items_owned->attach($tpl);
    }
    if (has_privilege($member_id, 'administer_buildr')) {
        $_items_owned->attach(do_template('W_MAIN_ITEM_OWNED_SEP'));

        $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_itemdef WHERE owner<>' . strval($member_id) . ' ORDER BY name');
        foreach ($rows as $myrow) {
            $selected = $myrow['name'] == $item;
            $tpl = do_template('W_MAIN_ITEM_OWNED', ['_GUID' => '84ccf58b51c77257b6a8a76feae19812', 'SELECTED' => $selected, 'NAME' => $myrow['name']]);
            $_items_owned->attach($tpl);
        }
    }
    $items_owned = do_template('W_MAIN_ITEMS_OWNED', ['_GUID' => '6738150dc18abd7695623f98133c60fe', 'NAME' => 'item', 'CONTENT' => $_items_owned]);
    $items_owned_2 = do_template('W_MAIN_ITEMS_OWNED', ['_GUID' => '471f3536533e534fe29ac910cb854c04', 'NAME' => 'item2', 'CONTENT' => $_items_owned]);

    $is_room_owner = (($member_id !== null) || ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'owner', ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm]) == $member_id));
    $is_realm_owner = ((has_privilege($member_id, 'administer_buildr')) || ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', ['id' => $realm]) == $member_id));

    $may_do_stuff = (!((!has_privilege($member_id, 'administer_buildr')) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'owner', ['id' => $realm]) != $member_id) && ($GLOBALS['SITE_DB']->query_select_value('w_realms', 'r_private', ['id' => $realm]) == 1)));
    $may_add_portal = ($GLOBALS['SITE_DB']->query_select_value('w_rooms', 'allow_portal', ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm]) == 1);

    $is_staff = (has_privilege($member_id, 'administer_buildr'));

    return do_template('W_MAIN_SCREEN', [
        '_GUID' => 'c1578b8a755553ae71fdfbb17d94a46e',
        'TITLE' => $title,
        'REALM_NAME' => $realm_name,
        'ROOM_NAME' => $room_name,
        'REALM' => strval($realm),
        'X' => strval($x),
        'Y' => strval($y),
        'ROOM_TEXT' => $room_text,
        'HAS_UP_ROOM' => $has_up_room,
        'HAS_DOWN_ROOM' => $has_down_room,
        'HAS_LEFT_ROOM' => $has_left_room,
        'HAS_RIGHT_ROOM' => $has_right_room,
        'PORTALS' => $portals,
        'PIC_URL' => $pic_url,
        'MEMBERS' => $members,
        'ITEMS' => $items,
        'ITEMS_SALE' => $items_sale,
        'PEOPLE' => $people_here,
        'IS_STAFF' => $is_staff,
        'HIDE_MODIFICATIONS' => $hide_modifications,
        'HIDE_ADDITIONS' => $hide_additions,
        'HIDE_ACTIONS' => $hide_actions,
        'MAY_DO_STUFF' => $may_do_stuff,
        'MAY_ADD_PORTAL' => $may_add_portal,
        'ITEMS_OWNED' => $items_owned,
        'ITEMS_OWNED_2' => $items_owned_2,
        'ITEMS_HELD' => $items_held,
        'IS_ROOM_OWNER' => $is_room_owner,
        'IS_REALM_OWNER' => $is_realm_owner,
        'EMOTICON_CHOOSER' => $GLOBALS['FORUM_DRIVER']->get_emoticon_chooser(),
        'UP_ROOM' => $up_room,
        'DOWN_ROOM' => $down_room,
        'LEFT_ROOM' => $left_room,
        'RIGHT_ROOM' => $right_room,
    ]);
}
