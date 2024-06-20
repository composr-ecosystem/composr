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

function init__hooks__systems__ecommerce__usergroup($in)
{
    if (!addon_installed('referrals')) {
        return $in;
    }

    require_code('referrals');

    return override_str_replace_exactly(
        "cns_add_member_to_secondary_group(\$member_id, \$new_group);",
        "
            cns_add_member_to_secondary_group(\$member_id, \$new_group);
            if (floatval(\$myrow['s_price']) != 0.0) {
                assign_referral_awards(\$member_id, 'usergroup_subscribe');
                assign_referral_awards(\$member_id, 'usergroup_subscribe_' . strval(\$usergroup_subscription_id));
            }
        ",
        $in,
        1,
        true
    );
}
