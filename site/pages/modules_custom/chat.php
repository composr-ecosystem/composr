<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/*FORCE_ORIGINAL_LOAD_FIRST*/

/**
 * Module page class.
 */
class Mx_chat extends Module_chat
{
    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        parent::pre_run();

        $type = get_param_string('type', 'browse');

        if ($type == 'global_room') { // Similar to room
            // Make it harder for clickjacking by requiring encrypted data (via telemetry keys) to be passed in for validation
            require_code('encryption');

            $token = get_param_string('token');
            $decoded_base64 = base64_decode($token);
            if ($decoded_base64 === false) {
                access_denied();
            }
            $decoded_json = json_decode($decoded_base64, true);
            if ($decoded_json === null) {
                access_denied();
            }
            $contents = decrypt_data_telemetry($decoded_json['nonce'], $decoded_json['encrypted_data'], $decoded_json['encrypted_session_key'], $decoded_json['version']);
            if ($contents != 'Grant me le accezz 2 de chat!') {
                access_denied();
            }

            require_code('csp');
            load_csp(['csp_allowed_iframe_ancestors' => "*"]);

            $room_id = $this->get_or_create_global_room();
            inject_feed_url('?mode=chat&select=' . strval($room_id), do_lang('CHATROOM'));
            $this->room_id = $room_id;

            $room_check = $GLOBALS['SITE_DB']->query_select('chat_rooms', ['*'], ['id' => $room_id], '', 1);
            if (!array_key_exists(0, $room_check)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'chat'));
            }
            $this->room_row = $room_check[0];
            $this->room_name = $this->room_row['room_name'];

            breadcrumb_set_parents([['_SELF:_SELF:browse', do_lang_tempcode('CHAT_LOBBY_END_CHAT')]]);
            breadcrumb_set_self($this->room_row['room_name']);

            $this->title = get_screen_title('_CHATROOM', true, [escape_html($this->room_row['room_name'])]);

            // Metadata
            set_extra_request_metadata([
                'identifier' => '_SEARCH:chat:room:' . strval($room_id),
            ], $room_check[0], 'chat', strval($room_id));
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $parent = parent::run();
        if (!$parent->is_empty()) {
            return $parent;
        }

        $type = get_param_string('type', 'browse');

        if ($type == 'global_room') {
            return $this->global_chat_room();
        }

        return new Tempcode();
    }

    /**
     * Gets or creates the global room for use with global chatting across sites.
     *
     * @return AUTO_LINK The chat room ID
     */
    protected function get_or_create_global_room() : int
    {
        $room_id = $GLOBALS['SITE_DB']->query_select_value_if_there('chat_rooms', 'id', ['room_name' => 'Global chat']);
        if ($room_id !== null) {
            return $room_id;
        }

        // We need to create the chat room
        require_code('chat2');
        return add_chatroom('', 'Global chat', 2, '', '', '', '', 'EN');
    }

    /**
     * The UI for a global chatroom.
     *
     * @return Tempcode The UI
     */
    public function global_chat_room() : object
    {
        require_javascript('posting');

        $your_name = get_param_string('nick'); // TODO: implement

        $prefs = [];
        $prefs_cookie = isset($_COOKIE['software_chat_prefs']) ? $_COOKIE['software_chat_prefs'] : false;
        if (!empty($prefs_cookie)) {
            $prefs = explode(';', $prefs_cookie);
        }
        $room_id = $this->room_id;
        $room_row = $this->room_row;
        $room_name = $this->room_name;

        $help_zone = get_comcode_zone('userguide_comcode', false);
        $comcode_help = ($help_zone === null) ? new Tempcode() : build_url(['page' => 'userguide_comcode'], $help_zone);
        $help_zone = get_comcode_zone('userguide_chatcode', false);
        $chatcode_help = ($help_zone === null) ? new Tempcode() : build_url(['page' => 'userguide_chatcode'], $help_zone);

        $posting_name = do_lang_tempcode('SEND_MESSAGE');
        $keep = symbol_tempcode('KEEP');
        $posting_url = find_script('messages') . '?mode=2&room_id=' . strval($room_id) . $keep->evaluate();
        $messages_link = find_script('messages') . '?room_id=' . strval($room_id) . '&zone=' . urlencode(get_zone_name()) . $keep->evaluate();
        $buttons = new Tempcode();
        $_buttons = [
            //'url' => do_lang_tempcode('ADD_LINK'), Bloat
            'thumb' => do_lang_tempcode('ADD_COMCODE_IMAGE'),
            //'email' => do_lang_tempcode('config:_EMAIL'), Bloat
            'quote' => do_lang_tempcode('QUOTE'),
            'code' => do_lang_tempcode('CODE'),
            'hide' => do_lang_tempcode('HIDE'),
        ];
        foreach ($_buttons as $button => $label) {
            $buttons->attach(do_template('COMCODE_EDITOR_BUTTON', ['_GUID' => '4fd75edb2d091b1c78a71c653efb18f0', 'DIVIDER' => false, 'IS_POSTING_FIELD' => false, 'FIELD_NAME' => 'post', 'LABEL' => $label, 'TITLE' => do_lang_tempcode('INPUT_COMCODE_' . $button), 'B' => $button]));
        }

        $micro_buttons = new Tempcode();
        $_micro_buttons = [
            'b',
            'i',
        ];
        foreach ($_micro_buttons as $button) {
            $micro_buttons->attach(do_template('COMCODE_EDITOR_MICRO_BUTTON', ['_GUID' => '3ced1e569e0c6feaeadbc09f7f89e7ee', 'IS_POSTING_FIELD' => false, 'FIELD_NAME' => 'post', 'TITLE' => do_lang_tempcode('INPUT_COMCODE_' . $button), 'B' => $button]));
        }

        $user_colour = ((array_key_exists(0, $prefs)) && ($prefs[0] != '')) ? $prefs[0] : get_option('chat_default_post_colour');
        $line_contents = ((array_key_exists(1, $prefs)) && ($prefs[1] != '')) ? $prefs[1] : get_option('chat_default_post_font');

        $cs_post_url = build_url(['page' => '_SELF', 'type' => 'options', 'id' => $room_id], '_SELF');

        $messages_php = find_script('messages');
        $debug = 'none';

        return do_template('CHAT_ROOM_SCREEN', [
            'TITLE' => $this->title,
            'CHATTERS' => get_chatters_in_room_tpl(get_chatters_in_room($room_id)),
            'CHAT_SOUND' => get_chat_sound_tpl(),
            'CHATROOM_ID' => strval($room_id),
            'DEBUG' => $debug,
            'MESSAGES_PHP' => $messages_php,
            'TEXT_COLOUR_DEFAULT' => $user_colour,
            'FONT_NAME_DEFAULT' => $line_contents,
            'OPTIONS_URL' => $cs_post_url,
            'COMCODE_HELP' => $comcode_help,
            'CHATCODE_HELP' => $chatcode_help,
            'CHATROOM_NAME' => $room_name,
            'MICRO_BUTTONS' => $micro_buttons,
            'BUTTONS' => $buttons,
            'YOUR_NAME' => $your_name,
            'MESSAGES_URL' => $messages_link,
            'POSTING_URL' => $posting_url,
            'SUBMIT_VALUE' => $posting_name,
            'INTRODUCTION' => 'Welcome to the global chat! Here, you can chat with other webmasters using the ' . brand_name() . ' software directly from your website. Please be civil and respectful. If you are experiencing a software bug, it is best to report it to the tracker.',
            'LINKS' => [],
        ]);
    }
}
