<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: TT_Cipher*/

/**
 * Composr API helper class.
 */
class CMSAccountRead
{
    /**
     * Find all members for syncing.
     *
     * @param  integer $start Start position
     * @param  integer $max Maximum results
     * @return array List of members
     */
    public function sync_members(int $start, int $max) : array
    {
        $api_key = get_option('tapatalk_api_key');

        $tt_cipher = new TT_Cipher();

        $_users = $GLOBALS['FORUM_DB']->query_select('f_members', ['*'], [], '', $max, $start);

        $users = [];
        foreach ($_users as $user) {
            $arr = [
                'uid' => mobiquo_val(strval($user['id']), 'string'),
                'username' => mobiquo_val($user['m_username'], 'base64'),
                'encrypt_email' => base64_encode($tt_cipher->encrypt($user['enc_email'], $api_key)),
                'allow_email' => mobiquo_val($user['m_allow_emails'], 'boolean'),
                'language' => mobiquo_val($user['m_language'], 'string'),
                'reg_date' => mobiquo_val($user['m_join_time'], 'dateTime.iso8601'),
                'post_num' => mobiquo_val($user['m_cache_num_posts'], 'int'),
                'last_active' => mobiquo_val($user['m_last_submit_time'], 'dateTime.iso8601'),
            ];
            $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user['id'], true);
            if ($display_text != $user['m_username']) {
                $arr += [
                    'display_text' => mobiquo_val($display_text, 'base64'),
                ];
            }
            $users[] = mobiquo_val($arr, 'struct');
        }
        return $users;
    }

    /**
     * Get basic details of a member, by e-mail address.
     *
     * @param  EMAIL $email E-mail address
     * @return ?array Map of details (null: not found)
     */
    public function prefetch_account(string $email) : ?array
    {
        $user_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email);
        if ($user_id === null) {
            return null;
        }

        $username = $GLOBALS['FORUM_DRIVER']->get_username($user_id);

        return [
            'user_id' => $user_id,
            'login_name' => $username,
            'display_name' => $GLOBALS['FORUM_DRIVER']->get_username($user_id, true),
            'avatar_url' => $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($user_id),
        ];
    }

    /**
     * Get custom fields for registration form.
     *
     * @return array List of custom fields
     */
    public function get_custom_register_fields() : array
    {
        $custom_register_fields = [];
        $_custom_register_fields = $GLOBALS['FORUM_DB']->query_select(
            'f_custom_fields',
            ['id', 'cf_name', 'cf_description', 'cf_type', 'cf_default', 'cf_order'],
            ['cf_show_on_join_form' => 1, 'cf_required' => 1],
            'ORDER BY cf_order,' . $GLOBALS['FORUM_DB']->translate_field_ref('cf_name')
        );
        foreach ($_custom_register_fields as $_custom_register_field) {
            $name = get_translated_text($_custom_register_field['cf_name'], $GLOBALS['FORUM_DB']);

            $default = $_custom_register_field['cf_default'];
            $options = '';

            switch ($_custom_register_field['cf_type']) {
                case 'author':
                case 'codename':
                case 'color':
                case 'combo':
                case 'combo_multi':
                case 'email':
                case 'float':
                case 'integer':
                case 'isbn':
                case 'password':
                case 'reference':
                case 'short_text':
                case 'short_text_multi':
                case 'short_trans':
                case 'short_trans_multi':
                case 'url':
                    $type = 'input';
                    break;

                case 'list':
                    require_code('fields');
                    $widget = option_value_from_field_array($_custom_register_field, 'widget', 'dropdown');

                    if ($widget == 'radio') {
                        $type = 'radio';
                    } else {
                        $type = 'drop';
                    }
                    $options = '';
                    foreach (explode('|', $default) as $index => $option) {
                        if ($options != '') {
                            $options .= '|';
                        }
                        $options .= strval($index) . '=' . $option;
                    }
                    $default = preg_replace('#\|.*$#', '', $default);
                    break;

                case 'long_text':
                case 'long_trans':
                case 'posting_field':
                    $type = 'textarea';
                    break;

                case 'tick':
                case 'tick_multi':
                    $type = 'cbox';
                    break;

                default:
                    continue 2; // Not supported
            }

            $custom_register_fields[] = [
                'id' => $_custom_register_field['id'],
                'name' => $name,
                'description' => get_translated_text($_custom_register_field['cf_description'], $GLOBALS['FORUM_DB']),
                'key' => $name,
                'default' => $default,
                'type' => $type,
                'options' => $options,
            ];
        }
        return $custom_register_fields;
    }
}
