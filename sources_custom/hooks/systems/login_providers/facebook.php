<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
     * @param  ?MEMBER $member Member ID already detected as logged in (null: none). May be a guest ID.
     * @param  boolean $quick_only Whether to just do a quick check, don't establish new sessions
     * @return ?MEMBER Member ID now detected as logged in (null: none). May be a guest ID.
     */
    public function try_login($member, $quick_only = false) // NB: if $member is set (but not Guest), then it will bind to that account
    {
        /*if (($member !== null) && (!is_guest($member))) {     Speeds up slighlty, but we don't want to test with this because we need to ensure startup always works right, and it also stops some stuff working
            return $member;
        }*/

        require_code('facebook_connect');

        // Facebook connect
        if ((get_forum_type() == 'cns') && (get_option('facebook_allow_signups') == '1')) {
            safe_ini_set('ocproducts.type_strictness', '0');

            global $FACEBOOK_CONNECT;
            if (!is_null($FACEBOOK_CONNECT)) {
                try {
                    if ($FACEBOOK_CONNECT->getUser() != 0) {
                        $member = handle_facebook_connection_login($member, $quick_only);
                    }
                } catch (Exception $e) {
                    header('Facebook-Error: ' . escape_header($e->getMessage()));

                    // User will know what is wrong already (Facebook wil have said), so don't show on our end
                }
            }

            //safe_ini_set('ocproducts.type_strictness', '1');
        }
        return $member;
    }
}
