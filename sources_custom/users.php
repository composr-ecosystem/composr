<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 * @package    openid
 */

function init__users()
{
    //if (addon_installed('Facebook Support', true))
    if (is_file(get_file_base() . '/sources/facebook_connect.php')) {
        require_code('facebook_connect');
    }
}

function unused_other_func()
{
    // Just works as a flag that this isn't a "pure" file and hence to run the original's init function
}

/**
 * Find whether the current member is logged in via httpauth. For Facebook/OpenID we put in a bit of extra code to notify that the session must also be auto-marked as confirmed (which is why the function is called in some cases).
 *
 * @return boolean Whether the current member is logged in via httpauth
 */
function is_httpauth_login()
{
    if (get_forum_type() != 'cns') {
        return false;
    }
    if (is_guest()) {
        return false;
    }

    $ret = non_overridden__is_httpauth_login();

    $compat = $GLOBALS['FORUM_DRIVER']->get_member_row_field(get_member(), 'm_password_compat_scheme');
    if (($compat == 'facebook') || ($compat == 'openid')) {
        global $SESSION_CONFIRMED_CACHE;
        $SESSION_CONFIRMED_CACHE = true;
    }

    return $ret;
}
