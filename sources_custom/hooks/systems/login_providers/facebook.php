<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_login_provider_facebook
{
    /**
     * Standard login provider hook.
     *
     * @param  ?MEMBER $member_id Member ID already detected as logged in (null: none). May be a guest ID.
     * @param  boolean $quick_only Whether to just do a quick check, don't establish new sessions
     * @return ?MEMBER Member ID now detected as logged in (null: none). May be a guest ID.
     */
    public function try_login($member_id, $quick_only = false) // NB: if $member_id is set (but not Guest), then it will bind to that account
    {
        if (!addon_installed('facebook_support')) {
            return $member_id;
        }

        /*if (($member_id !== null) && (!is_guest($member_id))) {     Speeds up slightly, but we don't want to test with this because we need to ensure startup always works right, and it also stops some stuff working
            return $member_id;
        }*/

        // Facebook Connect
        if ((get_forum_type() == 'cns') && (get_option('facebook_allow_signups') == '1')) {
            require_code('facebook_connect');
            $facebook_uid = facebook_get_current_user_id();
            if ($facebook_uid !== null) {
                $member_id = handle_facebook_connection_login($member_id, $quick_only);
            }
        }
        return $member_id;
    }
}
