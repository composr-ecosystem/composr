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
class Hook_cns_warnings_karma
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
            'order' => 300, // Karma has its own separator, so order 300-399 is reserved for karma
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
            case '_PUNITIVE_BAD_KARMA':
                return do_lang('_PUNITIVE_BAD_KARMA', integer_format(intval($row['p_param_a'])));

            default:
                return '';
        }
    }

    /**
     * Render form fields for the warnings screen.
     *
     * @param  Tempcode &$add_text Tempcode to be included on the intro paragraph of the warnings screen (passed by reference)
     * @param  Tempcode &$fields The fields to be rendered (passed by reference)
     * @param  Tempcode &$hidden The hidden fields to be included (passed by reference)
     * @param  boolean $new Whether it is a new warning/punishment record
     * @param  LONG_TEXT $explanation The explanation for the warning/punishment record
     * @param  BINARY $is_warning Whether to make this a formal warning
     * @param  MEMBER $member_id The member the warning is for
     * @param  BINARY $spam_mode Whether this is a spam warning
     * @param  ?AUTO_LINK $post_id The ID of the forum post of which we clicked warn (null: we are not warning on a forum post)
     * @param  ?SHORT_TEXT $ip_address The IP address of the poster (null: we are not warning on a forum post)
     */
    public function get_form_fields(&$add_text, &$fields, &$hidden, bool $new, string $explanation, int $is_warning, int $member_id, int $spam_mode, ?int $post_id, ?string $ip_address)
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return;
        }

        if (!$new) {
            return;
        }

        if (has_privilege(get_member(), 'moderate_karma')) {
            require_code('karma');
            require_lang('karma');

            $description = do_lang_tempcode('DESCRIPTION_PUNITIVE_KARMA');
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['TITLE' => do_lang_tempcode('PUNITIVE_KARMA'), 'HELP' => $description, 'SECTION_HIDDEN' => true]));

            $current_karma = get_karma($member_id);

            $fields->attach(form_input_integer(do_lang_tempcode('ASSESS_BAD_KARMA'), do_lang_tempcode('DESCRIPTION_ASSESS_BAD_KARMA', escape_html(integer_format($current_karma[0], 0)), escape_html(integer_format($current_karma[1], 0))), 'bad_karma', 0, true));
        }
    }

    /**
     * Actualise punitive actions.
     * Note that this assumes action was applied through the warnings form, and that post parameters still exist.
     *
     * @param array &$punitive_messages Punitive action text to potentially be included in the PT automatically (passed by reference)
     * @param AUTO_LINK $warning_id The ID of the warning that was created for this punitive action
     * @param MEMBER $member_id The member this warning is being applied to
     * @param SHORT_TEXT $username The username of the member this warning is being applied to
     * @param SHORT_TEXT $explanation The defined explanation for this warning
     * @param LONG_TEXT &$message The message to be sent as a PT (passed by reference; you should generally use $punitive_text instead if you want to add PT text)
     */
    public function actualise_punitive_action(array &$punitive_messages, int $warning_id, int $member_id, string $username, string $explanation, string &$message)
    {
        if (!addon_installed('cns_warnings') || !addon_installed('karma')) {
            return;
        }

        $bad_karma = post_param_integer('bad_karma', 0);

        // Assess bad karma if we have the privilege and requested to do so
        if (($bad_karma > 0) && (has_privilege(get_member(), 'has_karmic_influence'))) {
            require_code('karma');
            require_code('karma2');
            require_lang('karma');

            $punitive_action_id = $GLOBALS['FORUM_DB']->query_insert('f_warnings_punitive', [
                'p_warning_id' => $warning_id,
                'p_member_id' => $member_id,
                'p_ip_address' => '',
                'p_email_address' => '',
                'p_hook' => 'karma',
                'p_action' => '_PUNITIVE_BAD_KARMA',
                'p_param_a' => strval($bad_karma),
                'p_param_b' => '',
                'p_reversed' => 0,
            ], true);

            add_karma('bad', get_member(), $member_id, $bad_karma, 'Warning #' . strval($warning_id), 'warning_punitive', strval($punitive_action_id));

            $current_karma = get_karma($member_id);

            $punitive_messages[] = do_lang('PUNITIVE_BAD_KARMA', integer_format($bad_karma), integer_format($current_karma[0]), integer_format($current_karma[1]), null, false);
        }
    }

    /**
     * Actualiser to undo a certain type of punitive action.
     *
     * @param  array $punitive_action The database row for the punitive action being undone
     * @param  array $warning The database row for the warning associated with the punitive action being undone
     */
    public function undo_punitive_action(array $punitive_action, array $warning)
    {
        $error = new Tempcode();
        if (!addon_installed__messaged('cns_warnings', $error)) {
            warn_exit($error);
        }
        if (!addon_installed__messaged('karma', $error)) {
            warn_exit($error);
        }
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('karma2');
        require_lang('karma');

        $id = intval($punitive_action['id']);

        reverse_karma(null, null, null, 'warning_punitive', strval($id));
    }
}
