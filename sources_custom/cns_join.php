<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

function init__cns_join($in = null)
{
    // More referral fields in form
    $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);
    if ((!isset($ini_file['visible_referrer_field'])) || ($ini_file['visible_referrer_field'] == '1')) {
        $extra_code = "\$fields->attach(get_referrer_field(true));";
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
    $known_referrer = get_param_string('keep_referrer', '');
    if ($known_referrer != '') {
        if (is_numeric($known_referrer)) {
            $known_referrer = $GLOBALS['FORUM_DRIVER']->get_username(intval($known_referrer));
            if (is_null($known_referrer)) {
                $known_referrer = '';
            }
        }
    } else {
        $known_referrer = cms_admirecookie('referrer', '');
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
    if (!is_null($referrer_member)) {
        $GLOBALS['FORUM_DB']->query_delete('f_invites', array(
            'i_email_address' => post_param_string('email_address'),
        ), '', 1); // Delete old invites for this email address
        $GLOBALS['FORUM_DB']->query_insert('f_invites', array(
            'i_inviter' => $referrer_member,
            'i_email_address' => post_param_string('email_address'),
            'i_time' => time(),
            'i_taken' => 0
        ));
    }
}
