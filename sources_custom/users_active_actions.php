<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Process a logout.
 */
function handle_active_logout()
{
    non_overridden__handle_active_logout();

    if (!addon_installed('facebook_support')) {
        return;
    }

    if (get_forum_type() == 'cns') {
        $compat = $GLOBALS['FORUM_DRIVER']->get_member_row_field(get_member(), 'm_password_compat_scheme');
    } else {
        $compat = '';
    }

    if ($compat == 'facebook') {
        $GLOBALS['FACEBOOK_LOGOUT'] = true;
        cms_ob_end_clean();
        echo ' ';
        cms_flush_safe(); // FUDGE: Force headers to be sent so it's not an HTTP header request so Facebook can do it's JS magic
    }
    $GLOBALS['MEMBER_CACHED'] = $GLOBALS['FORUM_DRIVER']->get_guest_id();
}
