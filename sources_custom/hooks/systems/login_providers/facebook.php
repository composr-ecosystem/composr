<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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

                if (!is_guest($member_id)) {
                    if (is_file(get_file_base() . '/sources_custom/hooks/systems/syndication/facebook.php')) {
                        if (post_param_integer('auto_syndicate', 0) == 1) {
                            set_value('facebook_oauth_token' . '__' . strval($member_id), facebook_get_access_token_from_js_sdk(), true);
                        } else {
                            set_value('facebook_oauth_token' . '__' . strval($member_id), '', true);
                        }
                    }
                }
            }
        }
        return $member_id;
    }
}
