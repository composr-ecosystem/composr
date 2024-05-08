<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

function get_user_currency()
{
    require_code('users');
    $return_default = false;
    $safe_currency = 'USD';
    $the_id = intval(get_member());
    $member_id = is_guest($the_id) ? mixed() : $the_id;
    if ($member_id !== null) {
        $cpf_id = get_credits_profile_field_id('cms_currency');
        if ($cpf_id !== null) {
            require_code('cns_members_action2');
            $_fields = cns_get_custom_field_mappings($member_id);
            $result = $_fields['field_' . strval($cpf_id)];
            $user_currency = ($result !== null) ? $result : null;
            $return_default = ($user_currency === null);
            if ($return_default === false) {
                if (preg_match('/^[a-zA-Z]$/', $user_currency) == 0) {
                    log_hack_attack_and_exit('HACK_ATTACK');
                }
            }
        } else {
            $return_default = true;
        }
    } else {
        $return_default = true;
    }
    $_system_currency = get_option('currency', true);
    $system_currency = ($_system_currency === null) ? $safe_currency : $_system_currency;
    return $return_default ? $system_currency : $user_currency;
}

function get_credits_profile_field_id($field_name = 'cms_support_credits')
{
    require_code('cns_members');
    if (preg_match('/\W/', $field_name)) {
        log_hack_attack_and_exit('HACK_ATTACK');
    }
    $fields = cns_get_all_custom_fields_match(
        null, // groups
        null, // public view
        null, // owner view
        null, // owner set
        null, // required
        null, // show in posts
        null, // show in post previews
        1 // special start
    );
    $field_id = null;
    foreach ($fields as $field) {
        if ($field['trans_name'] == $field_name) {
            $field_id = $field['id'];
            break;
        }
    }
    return $field_id;
}
