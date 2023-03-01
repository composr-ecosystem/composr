<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    shoutr
 */

/**
 * Block class.
 */
class Block_side_shoutbox
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 3;
        $info['locked'] = false;
        $info['parameters'] = ['param', 'max'];
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('shoutr', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('chat')) {
            return do_template('RED_ALERT', ['_GUID' => 'y8h6nmz8sgqyr3pyc0u3utctjxqomeje', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('chat'))]);
        }

        require_lang('chat');
        require_css('chat');
        require_code('chat');

        require_javascript('chat');

        $block_id = get_block_id($map);

        $room_id = array_key_exists('param', $map) ? intval($map['param']) : null;
        $num_messages = array_key_exists('max', $map) ? intval($map['max']) : 5;

        if ($room_id === null) {
            $room_id = $GLOBALS['SITE_DB']->query_select_value_if_there('chat_rooms', 'MIN(id)', ['is_im' => 0/*, 'room_language' => user_lang()*/]);
            if ($room_id === null) {
                return do_template('RED_ALERT', ['_GUID' => '5g9vvms33t368bjqdlceztmd2gxn0vec', 'TEXT' => do_lang_tempcode('NO_CATEGORIES', 'chat')]);
            }
        }

        $room_check = $GLOBALS['SITE_DB']->query_select('chat_rooms', ['*'], ['id' => $room_id], '', 1);
        if (!array_key_exists(0, $room_check)) {
            return do_template('RED_ALERT', ['_GUID' => 'bb4e9iolej3agl06lk4z38gy1zgtz5vc', 'TEXT' => do_lang_tempcode('MISSING_RESOURCE', 'chat')]);
        }
        require_code('chat');
        if (!check_chatroom_access($room_check[0], true)) {
            global $DO_NOT_CACHE_THIS; // We don't cache against access, so we have a problem and can't cache
            $DO_NOT_CACHE_THIS = true;

            return new Tempcode();
        }

        $last_message_id = -1;

        $zone = get_module_zone('chat');

        if ($room_id === null) {
            $room_id = $GLOBALS['SITE_DB']->query_select_value_if_there('chat_rooms', 'MIN(id)', ['is_im' => 0, 'room_language' => user_lang()]);
            if ($room_id === null) {
                $room_id = $GLOBALS['SITE_DB']->query_select_value_if_there('chat_rooms', 'MIN(id)', ['is_im' => 0]);
            }
            if ($room_id === null) {
                return paragraph(do_lang_tempcode('NONE_EM'), 'bwkc04vf6j5ebavzfnbxlh161qctdwtb', 'nothing-here');
            }
        }

        $room_check = $GLOBALS['SITE_DB']->query_select('chat_rooms', ['*'], ['id' => $room_id], '', 1);
        if (!array_key_exists(0, $room_check)) {
            return paragraph(do_lang_tempcode('MISSING_RESOURCE', 'chat'), 'qgkdgqhgdd9yymwqc6t9ma3nt9rp9w2x', 'nothing-here');
        }

        // Did a message get sent last time?
        $shoutbox_message = post_param_string('shoutbox_message', '');
        if ($shoutbox_message != '') {
            if (!chat_post_message($room_id, $shoutbox_message, get_option('chat_default_post_font'), get_option('chat_default_post_colour'))) {
                // Error. But actually we'll get it from below
            }
        }

        $messages = chat_get_room_content($room_id, $room_check, $num_messages * 3, false, false, null, null, -1, $zone, null, true, $shoutbox_message != '');
        $_tpl = [];
        foreach ($messages as $_message) {
            $evaluated = $_message['the_message']->evaluate();

            // We are only interested in private-message system messages and flood-control system messages, no other kinds of system message
            if (($_message['system_message'] == 1) && (strpos($evaluated, '[private') === false) && (preg_match('#' . str_replace('\{1\}', '\d+', preg_quote(do_lang('FLOOD_CONTROL_BLOCKED'))) . '#', $evaluated) == 0)) {
                continue;
            }

            if ((strpos($evaluated, '[private') === false) || (($shoutbox_message != '') && (strpos($evaluated, '[private="' . $GLOBALS['FORUM_DRIVER']->get_username(get_member()) . '"]') !== false))) {
                $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($_message['username']);
                $member_link = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($member_id, $_message['username']);
                $_tpl[] = do_template('BLOCK_SIDE_SHOUTBOX_MESSAGE', [
                    'MEMBER_ID' => strval($member_id),
                    'MEMBER_URL' => $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id),
                    'MEMBER_LINK' => $member_link,
                    'MESSAGE' => $_message['the_message'],
                    '_TIME' => strval($_message['date_and_time']),
                    'DATE' => $_message['date_and_time_nice'],
                ]);
            }
        }

        $tpl = new Tempcode();
        while (count($_tpl) > $num_messages) {
            array_shift($_tpl);
        }
        foreach ($_tpl as $t) {
            $tpl->attach($t);
        }

        $url = get_self_url(false, false, ['room_id' => $room_id]);

        return do_template('BLOCK_SIDE_SHOUTBOX', [
            '_GUID' => '023aef81ed14e33f1b9337c5aa3b7bc9',
            'BLOCK_ID' => $block_id,
            'LAST_MESSAGE_ID' => strval($last_message_id),
            'MESSAGES' => $tpl,
            'URL' => $url,
            'CHATROOM_ID' => strval($room_id),
            'NUM_MESSAGES' => strval($num_messages),
            'BLOCK_PARAMS' => '',
        ]);
    }
}
