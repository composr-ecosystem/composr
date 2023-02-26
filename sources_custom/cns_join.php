<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

function init__cns_join($in)
{
    if (!addon_installed('referrals')) {
        return $in;
    }

    // More referral fields in form
    $path = get_custom_file_base() . '/text_custom/referrals.txt';
    if (!is_file($path)) {
        $path = get_file_base() . '/text_custom/referrals.txt';
    }
    require_code('files');
    $ini_file = cms_parse_ini_file_safe($path, true);
    if ((!isset($ini_file['visible_referrer_field'])) || ($ini_file['visible_referrer_field'] == '1')) {
        $extra_code = "
            if ((!isset(\$adjusted_config_options['referrals'])) || (\$adjusted_config_options['referrals'] == '1')) {
                \$fields->attach(get_referrer_field(true));
            } else {
                \$hidden->attach(get_referrer_field(false));
            }
        ";
    } else {
        $extra_code = "\$hidden->attach(get_referrer_field(false));";
    }
    $in = override_str_replace_exactly(
        "/*PSEUDO-HOOK: cns_join_form special fields*/",
        $extra_code,
        $in
    );

    // Better referral detection, and proper qualification management
    $in = override_str_replace_exactly(
        "/*PSEUDO-HOOK: cns_join_actual referrals*/",
        "set_from_referrer_field();",
        $in
    );

    // Handle signup referrals
    $in = override_str_replace_exactly(
        "/*PSEUDO-HOOK: cns_join_actual ends*/",
        "require_code('referrals'); assign_referral_awards(\$member_id, 'join');",
        $in
    );

    return $in;
}

function get_referrer_field($visible)
{
    require_lang('referrals');
    $tracking_codes = find_session_tracking_codes();
    $known_referrer = null;
    foreach ($tracking_codes as $tracking_code) {
        if (is_numeric($tracking_code)) {
            $known_referrer = $GLOBALS['FORUM_DRIVER']->get_username(intval($tracking_code), false, USERNAME_DEFAULT_BLANK);
            break;
        }
    }

    if ($visible) {
        $field = form_input_username(do_lang_tempcode('TYPE_REFERRER'), do_lang_tempcode('DESCRIPTION_TYPE_REFERRER'), 'referrer', $known_referrer, false, true);
    } else {
        $field = form_input_hidden('referrer', $known_referrer);
    }

    return $field;
}

function set_from_referrer_field()
{
    require_lang('referrals');

    $referrer = post_param_string('referrer', '');
    if ($referrer == '') {
        return; // NB: This doesn't mean failure, it may already have been set by the recommend module when the recommendation was *made*
    }

    $referrer_member = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . db_string_equal_to('m_username', $referrer) . ' OR ' . db_string_equal_to('m_email_address', $referrer));
    if ($referrer_member !== null) {
        $GLOBALS['FORUM_DB']->query_delete('f_invites', [
            'i_inviter' => $referrer_member,
            'i_email_address' => post_param_string('email', false, INPUT_FILTER_POST_IDENTIFIER | INPUT_FILTER_EMAIL_ADDRESS),
        ]);
        $GLOBALS['FORUM_DB']->query_insert('f_invites', [
            'i_time' => time(),
            'i_taken' => 1,
            'i_inviter' => $referrer_member,
            'i_email_address' => post_param_string('email', false, INPUT_FILTER_POST_IDENTIFIER | INPUT_FILTER_EMAIL_ADDRESS),
        ]);
    }
}
