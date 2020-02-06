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

/*
This finds a Composr member's Facebook ID, in offline mode.

Use FB_CONNECT_UID to get the Facebook ID of the currently active user, via an active Facebook Connect session.
*/

/**
 * Hook class.
 */
class Hook_symbol_USER_FB_CONNECT
{
    public function run($param)
    {
        if (!addon_installed('facebook_support')) {
            return '';
        }

        require_code('facebook_connect');

        if ((!isset($param[0])) || (!is_numeric($param[0]))) {
            return '';
        }

        $member_id = intval($param[0]);

        $value = '';
        if ((get_forum_type() == 'cns') && ($GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_password_compat_scheme') == 'facebook')) {
            // Find Facebook ID via their member profile (we stashed it in here; authorisation stuff is never stored in DB, only on Facebook and user's JS)...

            $value = $GLOBALS['FORUM_DRIVER']->get_member_row_field($member_id, 'm_pass_hash_salted');
        } else {
            // Okay, look to see if they have set up syndication permissions instead, which is the other way around: stores authorisation, but not Facebook ID...

            $token = get_value('facebook_oauth_token__' . strval($member_id), null, true);
            if (empty($token)) {
                return '';
            }

            $appid = get_option('facebook_appid');
            $appsecret = get_option('facebook_secret_code');
            try {
                $facebook_connect = new Facebook(['appId' => $appid, 'secret' => $appsecret]);
                $facebook_connect->setAccessToken($token);
                $temp_cookie = $_COOKIE;
                $fb_user = $facebook_connect->getUser();
                $_COOKIE = $temp_cookie;
                $value = ($fb_user === null) ? '' : strval($fb_user);
                if ($value == '0') {
                    $value = '';
                }
            } catch (Exception $e) {
                $value = '';
            }
        }
        return $value;
    }
}
