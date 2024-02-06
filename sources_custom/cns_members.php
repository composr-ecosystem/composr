<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/**
 * Find whether a member is bound to HTTP authentication (an exceptional situation, only for sites that use it).
 *
 * @param  MEMBER $member_id The member
 * @return boolean The answer
 */
function cns_is_httpauth_member(int $member_id) : bool
{
    $special_type = $GLOBALS['CNS_DRIVER']->get_member_row_field($member_id, 'm_password_compat_scheme');

    if (addon_installed('hybridauth')) {
        require_code('hybridauth');
        $is_hybridauth_account = is_hybridauth_special_type($special_type);

        if ($is_hybridauth_account) {
            return true;
        }
    }

    return $special_type == 'httpauth';
}
