<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_referrals
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('referrals')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'cns_join':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                // More referral fields in form
                $ref_path = get_custom_file_base() . '/text_custom/referrals.txt';
                if (!is_file($ref_path)) {
                    $ref_path = get_file_base() . '/text_custom/referrals.txt';
                }

                require_code('files');

                $ini_file = cms_parse_ini_file_safe($ref_path, true);
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

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                // Inject code, but do not cause full-on critical error if it is broken so members can still register.
                insert_code_after__by_command(
                    $code,
                    'cns_join_form',
                    "/*PSEUDO-HOOK: cns_join_form special fields*/",
                    $extra_code,
                    1,
                    true
                );

                // Better referral detection, and proper qualification management
                insert_code_after__by_command(
                    $code,
                    'cns_join_form',
                    "/*PSEUDO-HOOK: cns_join_actual referrals*/",
                    "
                    set_from_referrer_field();
                    ",
                    1,
                    true
                );

                // Handle signup referrals
                insert_code_after__by_command(
                    $code,
                    'cns_join_actual',
                    "/*PSEUDO-HOOK: cns_join_actual ends*/",
                    "
                    require_code('referrals');
                    assign_referral_awards(\$member_id, 'join');
                    ",
                    1,
                    true
                );
                break;
            case 'hooks/systems/ecommerce/cart_orders':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                override_str_replace_exactly(
                    "send_shopping_order_purchased_staff_mail(\$order_id);",
                    "
                    <ditto>
                    require_code('referrals');
                    \$member_id = \$GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'member_id', ['id' => \$purchase_id]);
                    if (\$member_id !== null) {
                        assign_referral_awards(\$member_id, 'misc_purchase');
                        \$products = \$GLOBALS['SITE_DB']->query_select('shopping_order_details', ['id'], ['id' => \$purchase_id]);
                        foreach (\$products as \$p) {
                            assign_referral_awards(\$member_id, 'purchase_' . strval(\$p['id']));
                        }
                    }
                    ",
                    $code,
                    1,
                    true
                ); // TODO: Needs updating
                break;
            case 'hooks/systems/ecommerce/usergroup':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                override_str_replace_exactly(
                    "cns_add_member_to_secondary_group(\$member_id, \$new_group);",
                    "
                        cns_add_member_to_secondary_group(\$member_id, \$new_group);
                        if (floatval(\$myrow['s_price']) != 0.0) {
                            require_code('referrals');
                            assign_referral_awards(\$member_id, 'usergroup_subscribe');
                            assign_referral_awards(\$member_id, 'usergroup_subscribe_' . strval(\$usergroup_subscription_id));
                        }
                    ",
                    $code,
                    1,
                    true
                );
                break;
        }
    }
}
