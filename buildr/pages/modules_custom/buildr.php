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
 * Module page class.
 */
class Module_buildr
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 5;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'buildr';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'w_attempts',
            'w_inventory',
            'w_itemdef',
            'w_items',
            'w_members',
            'w_messages',
            'w_portals',
            'w_realms',
            'w_rooms',
            'w_travelhistory',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);

        require_code('files');
        if (!$GLOBALS['DEV_MODE']) {
            deldir_contents(get_custom_file_base() . '/uploads/buildr_addon', true);
        }

        if ($GLOBALS['SITE_DB']->table_exists('ecom_prods_prices')) {
            require_code('buildr');
            $prices = get_buildr_prices_default();
            foreach (array_keys($prices) as $name) {
                $GLOBALS['SITE_DB']->query_delete('ecom_prods_prices', ['name' => $name], '', 1);
            }
        }

        delete_privilege('administer_buildr');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if (($upgrade_from === null) || ($upgrade_from < 3)) {
            add_privilege('BUILDR', 'administer_buildr');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('w_attempts', 'datetime', 'TIME', 'a_datetime');
            $GLOBALS['SITE_DB']->alter_table_field('w_items', 'count', 'INTEGER', 'i_count');
            $GLOBALS['SITE_DB']->alter_table_field('w_messages', 'message', 'SHORT_TEXT', 'm_message');
            $GLOBALS['SITE_DB']->alter_table_field('w_messages', 'datetime', 'TIME', 'm_datetime');
            $GLOBALS['SITE_DB']->alter_table_field('w_portals', 'text', 'ID_TEXT', 'p_text');
            $GLOBALS['SITE_DB']->alter_table_field('w_rooms', 'text', 'LONG_TEXT', 'r_text');
            $GLOBALS['SITE_DB']->alter_table_field('w_realms', 'private', 'BINARY', 'r_private');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 4)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('w_items', 'cost', 'INTEGER', 'price');
        }

        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('w_attempts', [
                'id' => '*AUTO',
                'a_datetime' => 'TIME',
                'attempt' => 'SHORT_TEXT',
                'x' => 'INTEGER',
                'y' => 'INTEGER',
                'realm' => 'INTEGER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_inventory', [
                'item_name' => '*ID_TEXT',
                'item_count' => 'INTEGER',
                'item_owner' => '*MEMBER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_itemdef', [
                'name' => '*ID_TEXT',
                'bribable' => 'BINARY',
                'healthy' => 'BINARY',
                'picture_url' => 'URLPATH',
                'owner' => 'MEMBER',
                'replicateable' => 'BINARY',
                'max_per_player' => 'INTEGER',
                'the_description' => 'SHORT_TEXT',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_items', [
                'name' => '*ID_TEXT',
                'location_realm' => '*INTEGER',
                'location_x' => '*INTEGER',
                'location_y' => '*INTEGER',
                'not_infinite' => 'BINARY',
                'price' => 'INTEGER',
                'i_count' => 'INTEGER',
                'copy_owner' => '*MEMBER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_members', [
                'id' => '*INTEGER', // NOT MEMBER because it can be negative for a troll
                'location_realm' => 'INTEGER',
                'location_x' => 'INTEGER',
                'location_y' => 'INTEGER',
                'banned' => 'BINARY',
                'health' => 'INTEGER',
                'trolled' => 'INTEGER',
                'lastactive' => 'TIME',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_messages', [
                'id' => '*AUTO',
                'location_realm' => 'INTEGER',
                'location_x' => 'INTEGER',
                'location_y' => 'INTEGER',
                'm_message' => 'SHORT_TEXT',
                'originator_id' => 'MEMBER',
                'm_datetime' => 'TIME',
                'destination' => 'MEMBER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_portals', [
                'name' => 'ID_TEXT',
                'p_text' => 'ID_TEXT',
                'start_location_realm' => '*INTEGER',
                'start_location_x' => '*INTEGER',
                'start_location_y' => '*INTEGER',
                'end_location_realm' => '*INTEGER',
                'end_location_x' => 'INTEGER',
                'end_location_y' => 'INTEGER',
                'owner' => '?MEMBER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_rooms', [
                'name' => 'ID_TEXT',
                'location_realm' => '*INTEGER',
                'location_x' => '*INTEGER',
                'location_y' => '*INTEGER',
                'r_text' => 'LONG_TEXT',
                'password_question' => 'LONG_TEXT',
                'password_answer' => 'SHORT_TEXT',
                'password_fail_message' => 'SHORT_TEXT',
                'required_item' => 'SHORT_TEXT', // blank means none
                'locked_up' => 'BINARY',
                'locked_down' => 'BINARY',
                'locked_right' => 'BINARY',
                'locked_left' => 'BINARY',
                'picture_url' => 'URLPATH',
                'owner' => '?MEMBER',
                'allow_portal' => 'BINARY',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_travelhistory', [
                'member_id' => '*MEMBER',
                'x' => '*INTEGER',
                'y' => '*INTEGER',
                'realm' => '*INTEGER',
            ]);

            $GLOBALS['SITE_DB']->create_table('w_realms', [
                'id' => '*INTEGER',
                'name' => 'SHORT_TEXT',
                'troll_name' => 'SHORT_TEXT',
                'owner' => '?MEMBER',
                'r_private' => 'BINARY',
                'q1' => 'LONG_TEXT',
                'q2' => 'LONG_TEXT',
                'q3' => 'LONG_TEXT',
                'q4' => 'LONG_TEXT',
                'q5' => 'LONG_TEXT',
                'q6' => 'LONG_TEXT',
                'q7' => 'LONG_TEXT',
                'q8' => 'LONG_TEXT',
                'q9' => 'LONG_TEXT',
                'q10' => 'LONG_TEXT',
                'q11' => 'LONG_TEXT',
                'q12' => 'LONG_TEXT',
                'q13' => 'LONG_TEXT',
                'q14' => 'LONG_TEXT',
                'q15' => 'LONG_TEXT',
                'q16' => 'LONG_TEXT',
                'q17' => 'LONG_TEXT',
                'q18' => 'LONG_TEXT',
                'q19' => 'LONG_TEXT',
                'q20' => 'LONG_TEXT',
                'q21' => 'LONG_TEXT',
                'q22' => 'LONG_TEXT',
                'q23' => 'LONG_TEXT',
                'q24' => 'LONG_TEXT',
                'q25' => 'LONG_TEXT',
                'q26' => 'LONG_TEXT',
                'q27' => 'LONG_TEXT',
                'q28' => 'LONG_TEXT',
                'q29' => 'LONG_TEXT',
                'q30' => 'LONG_TEXT',
                'a1' => 'SHORT_TEXT',
                'a2' => 'SHORT_TEXT',
                'a3' => 'SHORT_TEXT',
                'a4' => 'SHORT_TEXT',
                'a5' => 'SHORT_TEXT',
                'a6' => 'SHORT_TEXT',
                'a7' => 'SHORT_TEXT',
                'a8' => 'SHORT_TEXT',
                'a9' => 'SHORT_TEXT',
                'a10' => 'SHORT_TEXT',
                'a11' => 'SHORT_TEXT',
                'a12' => 'SHORT_TEXT',
                'a13' => 'SHORT_TEXT',
                'a14' => 'SHORT_TEXT',
                'a15' => 'SHORT_TEXT',
                'a16' => 'SHORT_TEXT',
                'a17' => 'SHORT_TEXT',
                'a18' => 'SHORT_TEXT',
                'a19' => 'SHORT_TEXT',
                'a20' => 'SHORT_TEXT',
                'a21' => 'SHORT_TEXT',
                'a22' => 'SHORT_TEXT',
                'a23' => 'SHORT_TEXT',
                'a24' => 'SHORT_TEXT',
                'a25' => 'SHORT_TEXT',
                'a26' => 'SHORT_TEXT',
                'a27' => 'SHORT_TEXT',
                'a28' => 'SHORT_TEXT',
                'a29' => 'SHORT_TEXT',
                'a30' => 'SHORT_TEXT',
            ], true, false, true);

            require_code('buildr');

            require_code('buildr_action');
            add_realm_wrap(null, do_lang('W_DEFAULT_REALM'), do_lang('W_DEFAULT_TROLL'), do_lang('W_DEFAULT_JAIL'), do_lang('W_DEFAULT_IN_JAIL'), '', do_lang('W_DEFAULT_JAIL_HOUSE'), do_lang('W_DEFAULT_IN_JAIL_HOUSE'), '', do_lang('W_DEFAULT_LOBBY'), do_lang('W_DEFAULT_LOBBY_TEXT'), '', [], 0, false);
        }

        if (($upgrade_from === null) || ($upgrade_from < 5)) {
            $GLOBALS['SITE_DB']->create_index('w_messages', 'destination', ['destination']);
            $GLOBALS['SITE_DB']->create_index('w_messages', 'originator_id', ['originator_id']);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 5)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('w_itemdef', 'description', 'SHORT_TEXT', 'the_description');
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('buildr')) {
            return null;
        }

        return [
            'browse' => ['BUILDR', 'spare/world'],
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('buildr', $error_msg)) {
            return $error_msg;
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

        $type = either_param_string('type', 'room');

        require_lang('buildr');
        require_lang('metadata');

        if ($type == 'confirm') {
            $this->title = get_screen_title('W_CONFIRM_TITLE');
        }

        if ($type == 'reallocate') {
            $this->title = get_screen_title('W_REALLOCATE');
        }

        if ($type == 'additem') {
            $this->title = get_screen_title('W_ADD_ITEM_TITLE');
        }

        if ($type == 'additemcopy') {
            $this->title = get_screen_title('W_ADD_ITEM_COPY_TITLE');
        }

        if ($type == 'addroom') {
            $this->title = get_screen_title('W_ADD_ROOM_TITLE');
        }

        if ($type == 'addrealm') {
            $this->title = get_screen_title('W_ADD_REALM_TITLE');
        }

        if ($type == 'addportal') {
            $this->title = get_screen_title('W_ADD_PORTAL_TITLE');
        }

        if ($type == 'edititem') {
            $this->title = get_screen_title('W_EDIT_ITEM_TITLE');
        }

        if ($type == 'edititemcopy') {
            $this->title = get_screen_title('W_EDIT_ITEM_COPY_TITLE');
        }

        if ($type == 'editroom') {
            $this->title = get_screen_title('W_EDIT_ROOM_TITLE');
        }

        if ($type == 'editrealm') {
            $this->title = get_screen_title('W_EDIT_REALM_TITLE');
        }

        if ($type == 'editportal') {
            $this->title = get_screen_title('W_EDIT_PORTAL_TITLE');
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
        require_code('buildr');
        require_code('buildr_screens');

        if (get_value('buildr_prices_installed', '0', true) !== '1') {
            if (addon_installed('ecommerce')) {
                $prices = get_buildr_prices_default();
                foreach ($prices as $name => $price) {
                    $GLOBALS['SITE_DB']->query_insert('ecom_prods_prices', ['name' => $name, 'price' => null, 'tax_code' => '', 'price_points' => $price]);
                }

                set_value('buildr_prices_installed', '1', true);
            }
        }

        // Decide what functions to execute for this command
        $type = either_param_string('type', 'room');
        $param = either_param_string('param', '');
        $dest_member_id = either_param_integer('member', -1);
        $member_id = get_member();
        if (is_guest($member_id)) {
            buildr_refresh_with_message(do_lang_tempcode('W_NOT_LOGGED_IN'), 'warn');
            return new Tempcode();
        }
        $item = either_param_string('item', '');

        // Create the member if they aren't already in the system
        $member_rows = $GLOBALS['SITE_DB']->query_select('w_members', ['*'], ['id' => $member_id], '', 1);
        if (!array_key_exists(0, $member_rows)) {
            $member_rows[0] = [
                'id' => $member_id,
                'location_realm' => 0,
                'location_x' => 0,
                'location_y' => 0,
                'banned' => 0,
                'health' => 10,
                'trolled' => 0,
                'lastactive' => time(),
            ];
            $GLOBALS['SITE_DB']->query_insert('w_members', $member_rows[0]);
        }
        $member_row = $member_rows[0];

        // Check for banning
        if ($member_row['banned'] == 1) {
            buildr_refresh_with_message(do_lang_tempcode('W_YOU_BANNED'), 'warn');
        }

        // Check for death
        if ($member_row['health'] < 1) {
            take_items($member_id);
            $GLOBALS['SITE_DB']->query_update('w_members', ['location_realm' => 0, 'location_x' => 0, 'location_y' => 0, 'banned' => 0, 'health' => 10], ['id' => $member_id], '', 1);
            buildr_refresh_with_message(do_lang_tempcode('W_YOU_DIED'), 'warn');
        }

        // Mark as active
        $GLOBALS['SITE_DB']->query_update('w_members', ['lastactive' => time()], ['id' => $member_id], '', 1);

        destick($member_id);

        // Check to see if the user is locked into answering a trolls questions
        if ($member_row['trolled'] != 0) {
            $realm = $member_row['location_realm'];

            // Get the questions that were asked
            $i = 0;
            $trolled = $member_row['trolled'];
            $q = [];
            $a = [];
            while (($trolled & (255 << ($i * 8))) != 0) {
                $q_num = ($trolled >> ($i * 8)) & 255;
                $q[$i + 1] = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'q' . strval($q_num), ['id' => $realm]);
                $a[$i + 1] = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'a' . strval($q_num), ['id' => $realm]);
                $i++;
            }
            $num_questions = $i;

            // Are we marking or answering?
            if (post_param_string('a1', '!!') != '!!') { // Marking
                // Mark them
                $pass = 0;
                for ($i = 1; $i <= $num_questions; $i++) {
                    $given = cms_mb_strtolower(post_param_string('a' . strval($i)));
                    $stored = cms_mb_strtolower($a[$i]);
                    if ($given == $stored) {
                        $pass++;
                    } elseif (strstr(':' . $stored . ':', ':' . $given . ':') !== false) {
                        $pass++;
                    }
                }

                // Regardless they have had their chance: no more questions
                $GLOBALS['SITE_DB']->query_update('w_members', ['trolled' => 0], ['id' => $member_id], '', 1);

                if ($pass == 0) {
                    $penalty = new Tempcode();
                    $pen_id = mt_rand(0, 2);
                    if ($pen_id == 0) {
                        $joke = mt_rand(1, 10);
                        $penalty = do_lang_tempcode('W_JOKE_' . strval($joke));

                        hurt($member_id);
                    }
                    if ($pen_id == 1) {
                        $penalty = do_lang_tempcode('W_PENALTY_STOLEN');
                        steal($member_id, -$realm - 1);
                    }
                    if ($pen_id == 2) {
                        $penalty = do_lang_tempcode('W_PENALTY_SENT_LOBBY');
                        basic_enter_room($member_id, $realm, 0, 0);
                    }

                    buildr_refresh_with_message(do_lang_tempcode('W_TROLL_YOU', $penalty), 'warn');
                } else {
                    buildr_refresh_with_message(do_lang_tempcode('W_TROLL_THANKYOU', escape_html(integer_format($pass))));
                }
            } else { // Answer question screen
                $troll_name = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'troll_name', ['id' => $realm]);
                $this->title = get_screen_title('W_TROLL_Q', true, [escape_html($troll_name)]);
                $questions = new Tempcode();
                for ($i = 1; $i <= $num_questions; $i++) {
                    $questions->attach(do_template('W_TROLL_QUESTION', ['_GUID' => 'b09eb44e4264a9dca5bdf651ca9a48d4', 'Q' => $q[$i], 'I' => strval($i)]));
                }
                return do_template('W_TROLL', ['_GUID' => 'e108ccaebc5b1adfa9db6b5b23e93602', 'TITLE' => $this->title, 'TROLL' => $troll_name, 'QUESTIONS' => $questions]);
            }
        }

        // There is a chance the troll on this realm will pick this 'turn' to move
        if (mt_rand(0, 6) == 1) {
            $realm = $member_row['location_realm'];
            $troll_id = -$realm - 1;
            $troll_loc = get_loc_details($troll_id, true);
            if ($troll_loc !== null) {
                list(, $troll_x, $troll_y) = $troll_loc;
                do {
                    $dx = mt_rand(-1, 1);
                    $dy = mt_rand(-1, 1);
                } while (!room_exists($troll_x + $dx, $troll_y + $dy, $realm));
                $GLOBALS['SITE_DB']->query_update('w_members', ['location_x' => ($troll_x + $dx), 'location_y' => ($troll_y + $dy)], ['id' => $troll_id], '', 1);
            }
        }

        require_code('uploads');

        // What command are we being asked to do?
        if ($type == 'confirm') {
            $url = build_url(['page' => '_SELF'], '_SELF');
            $type2 = either_param_string('btype', '');
            $item = either_param_string('item', '');
            $member = either_param_integer('member', -1);
            $param = either_param_string('param', '');

            return do_template('W_CONFIRM_SCREEN', [
                '_GUID' => '365870cb4c6cb4282ff6c7a11f4f8a5b',
                'TITLE' => $this->title,
                'URL' => $url,
                'COMMAND' => $type2,
                'ITEM' => $item,
                'MEMBER' => strval($member),
                'PARAM' => $param,
            ]);
        }

        if ($type == 'reallocate') {
            if (!has_privilege(get_member(), 'administer_buildr')) {
                buildr_refresh_with_message(do_lang_tempcode('W_ONLY_STAFF_REALLOC'), 'warn');
            }

            $out = new Tempcode();
            $max = 50;
            $start = 0;

            do {
                $rows = $GLOBALS['SITE_DB']->query_select('items', ['*'], ['copy_owner' => null], 'ORDER BY name', $max, $start);
                foreach ($rows as $myrow) {
                    $owner = $GLOBALS['SITE_DB']->query_select_value('w_itemdef', 'owner', ['name' => $myrow['name']]);
                    if ($owner !== null) {
                        $GLOBALS['SITE_DB']->query_update('w_items', ['copy_owner' => $owner], ['name' => $myrow['name'], 'copy_owner' => null]);
                        $out->attach(paragraph(do_lang_tempcode('W_REALLOCATING', escape_html($myrow['name']), 'tfgdfgd4rf')));
                    }
                }

                $start += $max;
            } while (!empty($rows));

            return do_template('W_REALLOCATE', ['_GUID' => '8fa4b9205310d6bc2fc28348a52898d5', 'TITLE' => $this->title, 'OUT' => $out]);
        }

        if ($type == 'portal') {
            portal($member_id, intval($param));
        }

        if ($type == 'realms') {
            realms();
            return new Tempcode();
        }

        if ($type == 'up') {
            $tpl = try_to_enter_room($member_id, 0, -1, '');
            if ($tpl !== null) {
                return $tpl;
            }
            buildr_refresh_with_message(new Tempcode());
        }
        if ($type == 'down') {
            $tpl = try_to_enter_room($member_id, 0, 1, '');
            if ($tpl !== null) {
                return $tpl;
            }
            buildr_refresh_with_message(new Tempcode());
        }
        if ($type == 'right') {
            $tpl = try_to_enter_room($member_id, 1, 0, '');
            if ($tpl !== null) {
                return $tpl;
            }
            buildr_refresh_with_message(new Tempcode());
        }
        if ($type == 'left') {
            $tpl = try_to_enter_room($member_id, -1, 0, '');
            if ($tpl !== null) {
                return $tpl;
            }
            buildr_refresh_with_message(new Tempcode());
        }

        if ($type == 'answered') {
            $tpl = try_to_enter_room($member_id, post_param_integer('dx'), post_param_integer('dy'), $param);
            if ($tpl !== null) {
                return $tpl;
            }
        }

        if ($type == 'drop') {
            drop_wrap($member_id, $item);
        }

        if ($type == 'give') {
            give($member_id, $dest_member_id, $item);
        }

        if ($type == 'pickpocket') {
            pickpocket($member_id, $dest_member_id);
        }

        if ($type == 'use') {
            useitem($member_id, $item);
        }

        if ($type == 'take') {
            take($member_id, $item, $dest_member_id);
        }

        if ($type == 'buy') {
            buy($member_id, $item, $dest_member_id);
        }

        if ($type == 'inventory') {
            return output_inventory_screen($dest_member_id);
        }

        if ($type == 'findperson') {
            findperson(($param == '') ? strval($dest_member_id) : $param);
        }

        if ($type == 'message') {
            message($member_id, post_param_string('post'), post_param_integer('tmember'));
        }

        if ($type == 'emergency') {
            basic_enter_room($member_id, 0, 0, 0);
        }

        if ($type == 'delete-message-by-person') {
            if ((!has_privilege($member_id, 'administer_buildr')) && ($member_id != $dest_member_id)) {
                buildr_refresh_with_message(do_lang_tempcode('ACCESS_DENIED__I_ERROR', escape_html($GLOBALS['FORUM_DRIVER']->get_username(get_member()))), 'warn');
            }
            delete_message($member_id, $dest_member_id, addslashes($param));
        }

        // Management...

        if ($type == 'additem') {
            require_code('buildr_action');

            $name = post_param_string('item_name', '');
            if ($name == '') {
                return do_template('W_ITEM_SCREEN', [
                    '_GUID' => '0246f7037a360996bdfb4f1dcf96bcfc',
                    'PRICE' => integer_format(get_product_price_points('mud_item'), 0),
                    'TEXT' => paragraph(do_lang_tempcode('W_ADD_ITEM_TEXT')),
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'additem',
                    'ITEM' => '',
                    'DESCRIPTION' => '',
                    'BRIBABLE' => '0',
                    'HEALTHY' => '0',
                    'PICTURE_URL' => '',
                    'MAX_PER_PLAYER' => '10',
                    'REPLICATEABLE' => '1',
                ]);
            }

            $urls = get_url('url', 'pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            add_item_wrap($member_id, $name, post_param_integer('price', 0), post_param_integer('not_infinite', 0), post_param_integer('bribable', 0), post_param_integer('healthy', 0), $urls[0], post_param_integer('max_per_player', -1), post_param_integer('replicateable', 0), post_param_string('description'));
        }

        if ($type == 'additemcopy') {
            require_code('buildr_action');

            $name = post_param_string('item_name', '');
            if ($name == '') {
                $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_itemdef WHERE replicateable=1 OR owner=' . strval(get_member()) . ' ORDER BY name');
                $items = '';
                foreach ($rows as $myrow) {
                    $items .= "<option value=\"" . escape_html($myrow['name']) . "\">" . escape_html($myrow['name']) . '</option>';
                }
                if ($items == '') {
                    buildr_refresh_with_message(do_lang_tempcode('W_NO_ITEMS_YET'), 'warn');
                }

                if ($GLOBALS['XSS_DETECT']) {
                    ocp_mark_as_escaped($items);
                }

                return do_template('W_ITEMCOPY_SCREEN', [
                    '_GUID' => '15799930bca51eafdee3c0a8e197866a',
                    'PRICE' => integer_format(get_product_price_points('mud_item_copy'), 0),
                    'TEXT' => paragraph(do_lang_tempcode('W_ADD_ITEM_COPY_TEXT')),
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'additemcopy',
                    'NOT_INFINITE' => '1',
                    'ITEMS' => $items,
                ]);
            }
            add_item_wrap_copy($member_id, $name, post_param_integer('price'), post_param_integer('not_infinite', 0));
        }

        if ($type == 'addroom') {
            require_code('buildr_action');

            $name = post_param_string('room_name', '');

            if ($name == '') {
                return do_template('W_ROOM_SCREEN', [
                    '_GUID' => '5357a6cf8648c952cf29c2b7234cfa6c',
                    'PRICE' => integer_format(get_product_price_points('mud_room'), 0),
                    'TEXT' => paragraph(do_lang_tempcode('W_ADD_ROOM_TEXT')),
                    'ROOM_TEXT' => '',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'addroom',
                    'NAME' => '',
                    'PASSWORD_QUESTION' => '',
                    'PASSWORD_ANSWER' => '',
                    'PASSWORD_FAIL_MESSAGE' => '',
                    'REQUIRED_ITEM' => '',
                    'LOCKED_UP' => '0',
                    'LOCKED_DOWN' => '0',
                    'LOCKED_LEFT' => '0',
                    'LOCKED_RIGHT' => '0',
                    'ALLOW_PORTAL' => '1',
                    'PICTURE_URL' => '',
                ]);
            }
            $urls = get_url('url', 'pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            add_room_wrap($member_id, post_param_integer('position'), $name, post_param_string('text'), post_param_string('password_question'), post_param_string('password_answer', false, INPUT_FILTER_PASSWORD), post_param_string('password_fail_message'), post_param_string('required_item'), post_param_integer('locked_up', 0), post_param_integer('locked_down', 0), post_param_integer('locked_right', 0), post_param_integer('locked_left', 0), $urls[0], post_param_integer('allow_portal', 0));
        }

        if ($type == 'addrealm') {
            require_code('buildr_action');

            $name = post_param_string('realm_name', '');

            if ($name == '') {
                $fortnights = (time() - $GLOBALS['FORUM_DRIVER']->get_member_join_timestamp(get_member())) / (60 * 60 * 24 * 7 * 2);
                $made = $GLOBALS['SITE_DB']->query_select_value('w_realms', 'COUNT(*)', ['owner' => get_member()]);
                $left = intval(round($fortnights - $made));

                $_qa = new Tempcode();
                for ($i = 1; $i <= 30; $i++) {
                    $_qa->attach(do_template('W_REALM_SCREEN_QUESTION', ['_GUID' => '5fa7725f11b0df7e58ff83f2f1751515', 'I' => strval($i), 'Q' => '', 'A' => '']));
                }

                return do_template('W_REALM_SCREEN', [
                    '_GUID' => '7ae26fe1766aed02233e1be84772759b',
                    'PRICE' => integer_format(get_product_price_points('mud_realm'), 0),
                    'TEXT' => paragraph(do_lang_tempcode('W_ADD_REALM_TEXT', escape_html(integer_format($left, 0)))),
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'addrealm',
                    'QA' => $_qa,
                    'NAME' => '',
                    'TROLL_NAME' => '',
                    'PRIVATE' => '0',
                ]);
            }

            $i = 1;
            $qa = [];
            while (strlen(post_param_string('question' . strval($i), '')) > 0) {
                $qa[$i] = [];
                $qa[$i]['q'] = post_param_string('question' . strval($i));
                $qa[$i]['a'] = post_param_string('answer' . strval($i));
                $i++;
            }
            $urls1 = get_url('jail_pic_url', 'jail_pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            $urls2 = get_url('jail_house_pic_url', 'jail_house_pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            $urls3 = get_url('lobby_pic_url', 'lobby_pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            add_realm_wrap($member_id, $name, post_param_string('troll_name'), post_param_string('jail_name'), post_param_string('jail_text'), $urls1[0], post_param_string('jail_house_name'), post_param_string('jail_house_text'), $urls2[0], post_param_string('lobby_name'), post_param_string('lobby_text'), $urls3[0], $qa, post_param_integer('private', 0));
        }

        if ($type == 'addportal') {
            require_code('buildr_action');

            $name = post_param_string('portal_name', '');

            if ($name == '') {
                $tpl = do_template('W_PORTAL_SCREEN', [
                    '_GUID' => '69e74a964f69721d0381a920c4a25ce5',
                    'PRICE' => integer_format(get_product_price_points('mud_portal'), 0),
                    'TEXT' => paragraph(do_lang_tempcode('W_ABOUT_PORTALS')),
                    'TITLE' => $this->title,
                    'PORTAL_TEXT' => '',
                    'PAGE_TYPE' => 'addportal',
                    'NAME' => '',
                    'END_LOCATION_REALM' => '',
                    'END_LOCATION_X' => '',
                    'END_LOCATION_Y' => '',
                ]);
                return $tpl;
            }

            add_portal_wrap($member_id, $name, post_param_string('text'), post_param_integer('end_location_realm', -1), post_param_integer('end_location_x', -1), post_param_integer('end_location_y', -1));
        }

        if ($type == 'deleteitem') {
            require_code('buildr_action');

            delete_item_wrap($item);
        }

        if ($type == 'deleteroom') {
            require_code('buildr_action');

            delete_room_wrap($member_id);
        }

        if ($type == 'deleterealm') {
            require_code('buildr_action');

            delete_realm_wrap($member_id);
        }

        if ($type == 'deleteportal') {
            require_code('buildr_action');

            delete_portal_wrap($member_id, intval($param));
        }

        // Admin commands...

        if (has_privilege($member_id, 'administer_buildr')) {
            if ($type == 'mergeitems') {
                merge_items($item, either_param_string('item2'));
            }
            if ($type == 'teleport-person') {
                $ast = strpos($param, ':');
                $b = @strpos($param, ':', $ast + 1);
                $realm = intval(substr($param, 0, $ast));
                $x = intval(substr($param, $ast + 1, $b - $ast - 1));
                $y = intval(substr($param, $b + 1));
                basic_enter_room($dest_member_id, $realm, $x, $y);
            }
            if ($type == 'imprison-person') {
                imprison($dest_member_id);
            }
            if ($type == 'hurt-person') {
                hurt($dest_member_id);
            }
            if ($type == 'dehurt-person') {
                dehurt($dest_member_id);
            }
            if ($type == 'ban-person') {
                ban_member($dest_member_id);
            }
            if ($type == 'unban-person') {
                unban_member($dest_member_id);
            }
            if ($type == 'take-from-person') {
                steal($member_id, $dest_member_id);
            }
        }

        if ($type == 'edititem') {
            require_code('buildr_action');

            $name = post_param_string('item_name', '');

            if ($name == '') {
                $rows = $GLOBALS['SITE_DB']->query_select('w_itemdef', ['*'], ['name' => either_param_string('item')], '', 1);
                if (!array_key_exists(0, $rows)) {
                    buildr_refresh_with_message(do_lang_tempcode('MISSING_RESOURCE'), 'warn');
                }
                $row = $rows[0];

                return do_template('W_ITEM_SCREEN', [
                    '_GUID' => '1f581864bd2f0cbe05742e03ab6c2a53',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'edititem',
                    'ITEM' => either_param_string('item'),
                    'DESCRIPTION' => $row['the_description'],
                    'BRIBABLE' => strval($row['bribable']),
                    'HEALTHY' => strval($row['healthy']),
                    'PICTURE_URL' => $row['picture_url'],
                    'OWNER' => ($row['owner'] === null) ? '' : strval($row['owner']),
                    'MAX_PER_PLAYER' => strval($row['max_per_player']),
                    'REPLICATEABLE' => strval($row['replicateable']),
                ]);
            }

            $urls = get_url('url', 'pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            edit_item_wrap($member_id, $item, $name, post_param_integer('bribable', 0), post_param_integer('healthy', 0), $urls[0], grab_new_owner('new_owner'), post_param_integer('max_per_player', -1), post_param_integer('replicateable', 0), post_param_string('description'));
        }

        if ($type == 'edititemcopy') {
            require_code('buildr_action');

            $price = post_param_integer('price', -1);

            if ($price == -1) {
                $owner = get_param_integer('owner');
                list($realm, $x, $y) = get_loc_details($member_id);

                $price = $GLOBALS['SITE_DB']->query_select_value('w_items', 'price', ['copy_owner' => $owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'name' => get_param_string('item', false, INPUT_FILTER_GET_COMPLEX)]);
                $not_infinite = $GLOBALS['SITE_DB']->query_select_value('w_items', 'not_infinite', ['copy_owner' => $owner, 'location_x' => $x, 'location_y' => $y, 'location_realm' => $realm, 'name' => get_param_string('item', false, INPUT_FILTER_GET_COMPLEX)]);

                return do_template('W_ITEMCOPY_SCREEN', [
                    '_GUID' => 'a8d28f6516408dba96a8b57ddcd7cee6',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'edititemcopy',
                    'NOT_INFINITE' => strval($not_infinite),
                    'X' => strval($x),
                    'Y' => strval($y),
                    'REALM' => strval($realm),
                    'ITEM' => get_param_string('item', false, INPUT_FILTER_GET_COMPLEX),
                    'OWNER' => strval($owner),
                    'PRICE' => strval($price),
                ]);
            }

            edit_item_wrap_copy($member_id, $item, $price, post_param_integer('not_infinite', 0), post_param_integer('new_x'), post_param_integer('new_y'), post_param_integer('new_realm'), grab_new_owner('new_owner'));
        }

        if ($type == 'editroom') {
            require_code('buildr_action');

            $name = post_param_string('room_name', '');

            if ($name == '') {
                list($location_realm, $x, $y) = get_loc_details($member_id);

                $rows = $GLOBALS['SITE_DB']->query_select('w_rooms', ['*'], ['location_x' => $x, 'location_y' => $y, 'location_realm' => $location_realm], '', 1);
                if (!array_key_exists(0, $rows)) {
                    buildr_refresh_with_message(do_lang_tempcode('MISSING_RESOURCE'), 'warn');
                }
                $row = $rows[0];

                return do_template('W_ROOM_SCREEN', [
                    '_GUID' => 'a4c5f8ae962cdbaa304135cf07c583a0',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'editroom',
                    'X' => strval($x),
                    'Y' => strval($y),
                    'REALM' => strval($location_realm),
                    'NAME' => $row['name'],
                    'ROOM_TEXT' => $row['r_text'],
                    'PASSWORD_QUESTION' => $row['password_question'],
                    'PASSWORD_ANSWER' => $row['password_answer'],
                    'PASSWORD_FAIL_MESSAGE' => $row['password_fail_message'],
                    'REQUIRED_ITEM' => $row['required_item'],
                    'LOCKED_UP' => strval($row['locked_up']),
                    'LOCKED_DOWN' => strval($row['locked_down']),
                    'LOCKED_LEFT' => strval($row['locked_left']),
                    'LOCKED_RIGHT' => strval($row['locked_right']),
                    'ALLOW_PORTAL' => strval($row['allow_portal']),
                    'PICTURE_URL' => $row['picture_url'],
                    'OWNER' => ($row['owner'] === null) ? '' : strval($row['owner']),
                ]);
            }

            $urls = get_url('url', 'pic', 'uploads/buildr_addon', OBFUSCATE_NEVER, CMS_UPLOAD_IMAGE);
            edit_room_wrap($member_id, $name, post_param_string('text'), post_param_string('password_question'), post_param_string('password_answer', false, INPUT_FILTER_NONE), post_param_string('password_fail_message'), post_param_string('required_item'), post_param_integer('locked_up', 0), post_param_integer('locked_down', 0), post_param_integer('locked_right', 0), post_param_integer('locked_left', 0), $urls[0], post_param_integer('allow_portal', 0), grab_new_owner('new_owner'), post_param_integer('new_x'), post_param_integer('new_y'), post_param_integer('new_realm'));
        }

        if ($type == 'editrealm') {
            require_code('buildr_action');

            $name = post_param_string('realm_name', '');

            if ($name == '') {
                list($realm, ,) = get_loc_details($member_id);

                $rows = $GLOBALS['SITE_DB']->query_select('w_realms', ['*'], ['id' => $realm], '', 1);
                if (!array_key_exists(0, $rows)) {
                    buildr_refresh_with_message(do_lang_tempcode('MISSING_RESOURCE'), 'warn');
                }
                $row = $rows[0];

                $qatc = new Tempcode();
                for ($i = 1; $i <= 30; $i++) {
                    $qatc->attach(do_template('W_REALM_SCREEN_QUESTION', ['_GUID' => '0510427a3895969dede2bd13db7d46a6', 'I' => strval($i), 'Q' => $row['q' . strval($i)], 'A' => $row['a' . strval($i)]]));
                }

                return do_template('W_REALM_SCREEN', [
                    '_GUID' => 'f2503e0be6e45a296baa8625cafb4d72',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'editrealm',
                    'OWNER' => ($row['owner'] === null) ? '' : strval($row['owner']),
                    'QA' => $qatc,
                    'NAME' => $row['name'],
                    'TROLL_NAME' => $row['troll_name'],
                    'PRIVATE' => strval($row['r_private']),
                ]);
            }

            for ($i = 1; $i <= 30; $i++) {
                $qa[$i]['q'] = post_param_string('question' . strval($i));
                $qa[$i]['a'] = post_param_string('answer' . strval($i));
            }
            edit_realm_wrap($member_id, $name, post_param_string('troll_name'), $qa, post_param_integer('private', 0), grab_new_owner('new_owner'));
        }

        if ($type == 'editportal') {
            require_code('buildr_action');

            $name = post_param_string('portal_name', '');

            if ($name == '') {
                list($realm, $x, $y) = get_loc_details($member_id);
                $end_realm = get_param_integer('param');

                $rows = $GLOBALS['SITE_DB']->query_select('w_portals', ['*'], ['start_location_x' => $x, 'start_location_y' => $y, 'start_location_realm' => $realm, 'end_location_realm' => $end_realm], '', 1);
                if (!array_key_exists(0, $rows)) {
                    buildr_refresh_with_message(do_lang_tempcode('MISSING_RESOURCE'), 'warn');
                }
                $row = $rows[0];

                return do_template('W_PORTAL_SCREEN', [
                    '_GUID' => 'cad0e01c1c4c410e67b775c3ff6eeb3a',
                    'TITLE' => $this->title,
                    'PAGE_TYPE' => 'editportal',
                    'X' => strval($x),
                    'Y' => strval($y),
                    'REALM' => strval($realm),
                    'PARAM' => $param,
                    'NAME' => $row['name'],
                    'PORTAL_TEXT' => $row['p_text'],
                    'END_LOCATION_REALM' => strval($end_realm),
                    'END_LOCATION_X' => strval($row['end_location_x']),
                    'END_LOCATION_Y' => strval($row['end_location_y']),
                    'OWNER' => ($row['owner'] === null) ? '' : strval($row['owner']),
                ]);
            }

            edit_portal_wrap($member_id, intval($param), $name, post_param_string('text'), post_param_integer('end_location_realm'), post_param_integer('end_location_x'), post_param_integer('end_location_y'), grab_new_owner('new_owner'), post_param_integer('new_x'), post_param_integer('new_y'), post_param_integer('new_realm'));
        }

        if ($type == 'room') {
            return output_room_screen($member_id);
        }

        buildr_refresh_with_message(do_lang_tempcode('SUCCESS'));
        return new Tempcode();
    }
}
