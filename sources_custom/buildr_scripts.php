<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    buildr
 */

/**
 * Output the Buildr messages script.
 */
function buildr_messages_script()
{
    if (!addon_installed('buildr')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('buildr')));
    }

    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }
    if (!addon_installed('ecommerce')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
    }
    if (!addon_installed('chat')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('chat')));
    }

    require_lang('buildr');
    require_lang('chat');
    require_css('buildr');

    $member_id = get_member();
    $rows = $GLOBALS['SITE_DB']->query_select('w_members', ['location_realm', 'location_x', 'location_y'], ['id' => $member_id], '', 1);
    if (!array_key_exists(0, $rows)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    list($realm, $x, $y) = [$rows[0]['location_realm'], $rows[0]['location_x'], $rows[0]['location_y']];

    $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_messages WHERE location_x=' . strval($x) . ' AND location_y=' . strval($y) . ' AND location_realm=' . strval($realm) . ' AND (destination=' . strval($member_id) . ' OR destination IS NULL OR originator_id=' . strval($member_id) . ') ORDER BY m_datetime DESC');
    $messages = new Tempcode();
    foreach ($rows as $myrow) {
        $message_sender = $GLOBALS['FORUM_DRIVER']->get_username($myrow['originator_id']);
        $messages->attach(do_template('W_MESSAGE_' . (($myrow['destination'] === null) ? 'ALL' : 'TO'), ['MESSAGE_SENDER' => $message_sender, 'MESSAGE' => comcode_to_tempcode($myrow['m_message'], $myrow['originator_id']), 'DATE' => get_timezoned_date_time($myrow['m_datetime'])]));
    }

    $tpl = do_template('W_MESSAGES_HTML_WRAP', ['_GUID' => '05b40c794578d3221e2775895ecf8dbb', 'MESSAGES' => $messages]);
    $tpl->evaluate_echo();
}

/**
 * Output the Buildr map script.
 */
function buildr_map_script()
{
    if (!addon_installed('buildr')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('buildr')));
    }

    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }
    if (!addon_installed('ecommerce')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
    }
    if (!addon_installed('chat')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('chat')));
    }

    require_code('buildr');

    $realm = get_param_integer('realm', null);
    download_map_wrap(get_member(), $realm);
}

/**
 * Wrapper function for the direct download of a map (wraps so as to download the map 'for where a member is at' [unless a direct realm given]).
 *
 * @param  MEMBER $member_id The member to get the map of
 * @param  ?integer $realm The realm they are wanting to get (null: where they are at)
 */
function download_map_wrap(int $member_id, ?int $realm)
{
    list($_realm, $x, $y) = get_loc_details($member_id);
    if ($realm === null) {
        $realm = $_realm;
    }
    download_map($realm, $x, $y);
}

/**
 * Direct download of a map.
 *
 * @param  integer $realm The X of where the member is standing
 * @param  integer $sx The Y of where the member is standing
 * @param  integer $sy The realm to get the map for
 */
function download_map(int $realm, int $sx, int $sy)
{
    // "Constants"
    $border_size = 35;
    $room_size = 90;
    $roomnameclip = 15;

    // Get realm mins/maxs/size
    $x_min = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'MIN(location_x)', ['location_realm' => $realm]);
    $y_min = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'MIN(location_y)', ['location_realm' => $realm]);
    $x_max = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'MAX(location_x)', ['location_realm' => $realm]);
    $y_max = $GLOBALS['SITE_DB']->query_select_value('w_rooms', 'MAX(location_y)', ['location_realm' => $realm]);
    $x_rooms = $x_max - $x_min + 1;
    $y_rooms = $y_max - $y_min + 1;

    // Create image
    $width = $border_size * 2 + $room_size * $x_rooms;
    $height = $border_size * 2 + $room_size * $y_rooms;
    $my_img = imagecreate($width, $height);
    $bgcolor = imagecolorallocate($my_img, 0xff, 0xff, 0xff); // Map background colour
    $cucolor = imagecolorallocate($my_img, 0x00, 0xff, 0x00); // Current Room colour
    $rmcolor = imagecolorallocate($my_img, 0xdd, 0xdd, 0xdd); // Room colour
    $txcolor = imagecolorallocate($my_img, 0x00, 0x00, 0xff); // Text colour
    $wlcolor = imagecolorallocate($my_img, 0xff, 0x00, 0x00); // Wall colour
    imagefill($my_img, 0, 0, $bgcolor);

    // Load font
    $my_font = 0;

    $member_id = get_member();

    // Draw rooms
    for ($x = $x_min; $x <= $x_max; $x++) {
        for ($y = $y_min; $y <= $y_max; $y++) {
            // Check the room exists
            $rooms = $GLOBALS['SITE_DB']->query_select('w_rooms', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $realm], '', 1);
            if (!array_key_exists(0, $rooms)) {
                continue;
            }
            $room = $rooms[0];
            $name = $room['name'];

            $portal = ($room['allow_portal'] == 1) ? do_lang('W_PORTAL_SPOT') : '';

            // Check user has been in room, to see if we perhaps need to mask the room name
            $yes = $GLOBALS['SITE_DB']->query_select_value_if_there('w_travelhistory', 'member_id', ['x' => $x, 'y' => $y, 'realm' => $realm, 'member_id' => $member_id]);
            if (!isset($yes) && (($x != $sx) || ($y != $sy))) { // When a member first starts Buildr, their initial location might not be logged in the travel history
                $name = do_lang('W_UNKNOWN_ROOM_NAME');
            }

            // Room surrounding ordinates
            $_x = $x - $x_min;
            $_y = $y - $y_min;
            $ax = $_x * $room_size + $border_size + 1;
            $ay = $_y * $room_size + $border_size + 1;
            $bx = $_x * $room_size + $border_size + $room_size - 1;
            $by = $_y * $room_size + $border_size + $room_size - 1;

            $owner = ($room['owner'] !== null) ? $GLOBALS['FORUM_DRIVER']->get_username($room['owner']) : '';

            // Draw room
            if (($x == $sx) && ($y == $sy)) {
                imagerectangle($my_img, $ax + 3, $ay + 3, $bx - 3, $by - 3, $cucolor);
            }
            imagerectangle($my_img, $ax, $ay, $bx, $by, $rmcolor);

            // Draw room borders if the walls are locked (solid/no-door)
            if ($room['locked_left'] == 1) {
                imageline($my_img, $ax, $ay, $ax, $by, $wlcolor);
            }
            if ($room['locked_right'] == 1) {
                imageline($my_img, $bx, $ay, $bx, $by, $wlcolor);
            }
            if ($room['locked_up'] == 1) {
                imageline($my_img, $ax, $ay, $bx, $ay, $wlcolor);
            }
            if ($room['locked_down'] == 1) {
                imageline($my_img, $ax, $by, $bx, $by, $wlcolor);
            }

            // Draw room name and coordinate
            $room_name1 = substr($name, 0, $roomnameclip);
            $room_name2 = substr($name, $roomnameclip, $roomnameclip);
            $room_name3 = substr($name, $roomnameclip * 2, $roomnameclip);
            $room_name4 = substr($name, $roomnameclip * 3);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 0, cms_empty_safe($room_name1) ? do_lang('NA') : $room_name1, $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 1, cms_empty_safe($room_name2) ? '' : $room_name2, $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 2, cms_empty_safe($room_name3) ? '' : $room_name3, $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 3, cms_empty_safe($room_name4) ? '' : $room_name4, $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 4, $portal, $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 6, ':' . strval($x) . ':' . strval($y), $txcolor);
            imagestring($my_img, $my_font, $ax + 2, $ay + 2 + (imagefontheight($my_font) + 2) * 7, strval($owner), $txcolor);
        }
    }

    // Output to browser
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename=realm' . strval($realm) . '_map.png');
    if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
        return;
    }
    imagepng($my_img);
}
