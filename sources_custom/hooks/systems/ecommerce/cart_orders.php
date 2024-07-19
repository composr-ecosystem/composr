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
        \$member_id = \$GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'member_id', ['id' => \$purchase_id]);
        if (\$member_id !== null) {
            assign_referral_awards(\$member_id, 'misc_purchase');
            \$products = \$GLOBALS['SITE_DB']->query_select('shopping_order_details', ['id'], ['id' => \$purchase_id]);
            foreach (\$products as \$p) {
                assign_referral_awards(\$member_id, 'purchase_' . strval(\$p['id']));
            }
        }
        ",
        $in,
        1,
        true
    ); // TODO: Needs updating
}
