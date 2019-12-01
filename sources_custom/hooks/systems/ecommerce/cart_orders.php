<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

function init__hooks__systems__ecommerce__cart_orders($in)
{
    if (!addon_installed('referrals')) {
        return $in;
    }

    if (get_forum_type() != 'cns') {
        return $in;
    }

    require_code('referrals');

    return override_str_replace_exactly(
        "send_shopping_order_purchased_staff_mail(\$order_id);",
        "
        <ditto>
        \$member_id = \$GLOBALS['SITE_DB']->query_select_value('shopping_order', 'c_member', ['id' => \$purchase_id]);
        if (\$member_id !== null) {
            assign_referral_awards(\$member_id, 'misc_purchase');
            \$products = \$GLOBALS['SITE_DB']->query_select('shopping_order_details', ['p_id'], ['p_order_id' => \$purchase_id]);
            foreach (\$products as \$p) {
                assign_referral_awards(\$member_id, 'purchase_' . strval(\$p['p_id']));
            }
        }
        ",
        $in
    );
}
