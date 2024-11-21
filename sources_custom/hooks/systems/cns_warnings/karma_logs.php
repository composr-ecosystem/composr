<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_cns_warnings_karma_logs
{
    /**
     * Get details for this hook.
     *
     * @return ?array The details (null: hook disabled)
     */
    public function get_details() : ?array
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return null;
        }

        return [
            'order' => 301, // Karma has its own separator, so order 300-399 is reserved for karma
        ];
    }

    /**
     * Generate punitive action text from a punitive action database row.
     *
     * @param  array $row The database row
     * @return string The punitive action text
     */
    public function generate_text(array $row) : string
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return '';
        }

        require_lang('karma');

        switch ($row['p_action']) {
            case '_PUNITIVE_REVERSE_KARMA':
                return do_lang('_PUNITIVE_REVERSE_KARMA', $row['p_param_a'], $row['p_param_b']);

            default:
                return '';
        }
    }

    /**
     * Render form fields for the warnings screen.
     *
     * @param  Tempcode $add_text Tempcode to be included on the intro paragraph of the warnings screen (passed by reference)
     * @param  Tempcode $fields The fields to be rendered (passed by reference)
     * @param  Tempcode $hidden The hidden fields to be included (passed by reference)
     * @param  boolean $new Whether it is a new warning/punishment record
     * @param  LONG_TEXT $explanation The explanation for the warning/punishment record
     * @param  BINARY $is_warning Whether to make this a formal warning
     * @param  MEMBER $member_id The member the warning is for
     * @param  BINARY $spam_mode Whether this is a spam warning
     * @param  ?AUTO_LINK $post_id The ID of the forum post of which we clicked warn (null: we are not warning on a forum post)
     * @param  ?SHORT_TEXT $ip_address The IP address of the poster (null: we are not warning on a forum post)
     */
    public function get_form_fields(object &$add_text, object &$fields, object &$hidden, bool $new, string $explanation, int $is_warning, int $member_id, int $spam_mode, ?int $post_id, ?string $ip_address)
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return;
        }

        if (!$new) {
            return;
        }

        if (has_privilege(get_member(), 'moderate_karma')) {
            require_lang('karma');
            require_code('karma');

            $from_time = time() - (60 * 60 * 24 * 7);
            list($max_rows, $rows) = karma_get_logs('sender_recipient', $member_id, get_member(), 50, 0, 'k_date_and_time', 'DESC', 0, $from_time);
            $_fields = new Tempcode();
            foreach ($rows as $row) {
                $reason = get_translated_tempcode('karma', $row, 'k_reason');
                if ($row['k_member_from'] == $member_id) {
                    if ($row['k_member_to'] == $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
                        $pretty_name = do_lang_tempcode('_ACTIVITY_SEND_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount']))]);
                    } else {
                        $username = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_to']);
                        $pretty_name = do_lang_tempcode('ACTIVITY_SEND_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount'])), escape_html($username)]);
                    }
                } elseif ($row['k_member_to'] == $member_id) {
                    if ($row['k_member_from'] == $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
                        $pretty_name = do_lang_tempcode('_ACTIVITY_RECEIVE_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount']))]);
                    } else {
                        $username = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_from']);
                        $pretty_name = do_lang_tempcode('ACTIVITY_RECEIVE_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount'])), escape_html($username)]);
                    }
                } else {
                    continue;
                }

                $description = $pretty_name;
                if ((has_privilege(get_member(), 'moderate_karma')) && (has_actual_page_access(get_member(), 'admin_karma'))) {
                    $description = hyperlink(build_url(['page' => 'admin_karma', 'type' => 'view', 'id' => $row['id']]), $description, false, true, '', null, null, null, '_self');
                }
                $description->attach('.');

                $_fields->attach(form_input_list_entry($row['id'], false, $pretty_name, false, false)); // TODO: need to use proper software tooltips for description
            }
            if (!$_fields->is_empty()) {
                $fields->attach(form_input_multi_list(do_lang_tempcode('PUNITIVE_KARMA_LOGS'), do_lang_tempcode('DESCRIPTION_PUNITIVE_KARMA_LOGS'), 'karma_reverse', $_fields, null, 10));
            }
        }
    }

    /**
     * Actualise punitive actions.
     * Note that this assumes action was applied through the warnings form, and that post parameters still exist.
     *
     * @param  array $punitive_messages Punitive action text to potentially be included in the PT automatically (passed by reference)
     * @param  AUTO_LINK $warning_id The ID of the warning that was created for this punitive action
     * @param  MEMBER $member_id The member this warning is being applied to
     * @param  SHORT_TEXT $username The username of the member this warning is being applied to
     * @param  SHORT_TEXT $explanation The defined explanation for this warning
     * @param  LONG_TEXT $message The message to be sent as a PT (passed by reference; you should generally use $punitive_text instead if you want to add PT text)
     */
    public function actualise_punitive_action(array &$punitive_messages, int $warning_id, int $member_id, string $username, string $explanation, string &$message)
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return;
        }

        if (has_privilege(get_member(), 'moderate_karma')) {
            if (!isset($_POST['karma_reverse']) || !is_array($_POST['karma_reverse']) || (count($_POST['karma_reverse']) <= 0)) { // Nothing to do
                return;
            }

            require_code('karma');
            require_code('karma2');
            require_lang('karma');

            foreach ($_POST['karma_reverse'] as $log_id) {
                $_row = $GLOBALS['SITE_DB']->query_select('karma', ['*'], ['id' => intval($log_id)], '', 1);
                if (!array_key_exists(0, $_row)) { // Sanity check
                    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('131734ae9d1750e8a9bc0147f1ee79c8')));
                }
                $row = $_row[0];

                $reason = get_translated_tempcode('karma', $row, 'k_reason');

                if ($row['k_member_from'] == $member_id) {
                    if ($row['k_member_to'] == $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
                        $pretty_name = do_lang_tempcode('_ACTIVITY_SEND_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount']))]);
                    } else {
                        $username = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_to']);
                        $pretty_name = do_lang_tempcode('ACTIVITY_SEND_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount'])), escape_html($username)]);
                    }
                } elseif ($row['k_member_to'] == $member_id) {
                    if ($row['k_member_from'] == $GLOBALS['FORUM_DRIVER']->get_guest_id()) {
                        $pretty_name = do_lang_tempcode('_ACTIVITY_RECEIVE_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount']))]);
                    } else {
                        $username = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_from']);
                        $pretty_name = do_lang_tempcode('ACTIVITY_RECEIVE_KARMA', $reason, escape_html($row['k_type']), [escape_html(integer_format($row['k_amount'])), escape_html($username)]);
                    }
                } else {
                    $pretty_name = do_lang_tempcode('_GOOD_BAD_KARMA', escape_html($row['k_type']), escape_html(integer_format($row['k_amount'])));
                }

                reverse_karma($row['id']);

                $GLOBALS['FORUM_DB']->query_insert('f_warnings_punitive', [
                    'p_warning_id' => $warning_id,
                    'p_member_id' => $member_id,
                    'p_ip_address' => '',
                    'p_email_address' => '',
                    'p_hook' => 'karma_logs',
                    'p_action' => '_PUNITIVE_REVERSE_KARMA',
                    'p_param_a' => strval($row['id']),
                    'p_param_b' => $pretty_name->evaluate(),
                    'p_reversed' => 0,
                ]);

                $punitive_messages[] = do_lang_tempcode('PUNITIVE_REVERSE_KARMA', $pretty_name/*Tempcode*/);
            }
        }
    }
}
