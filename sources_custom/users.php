<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

function unused_other_func()
{
    // Just works as a flag that this isn't a "pure" file and hence to run the original's init function
}

/**
 * Find whether the current member is logged in via httpauth. For Facebook we put in a bit of extra code to notify that the session must also be auto-marked as confirmed (which is why the function is called in some cases).
 *
 * @return boolean Whether the current member is logged in via httpauth
 */
function is_httpauth_login() : bool
{
    if (!addon_installed('hybridauth')) {
        return non_overridden__is_httpauth_login();
    }

    if (get_forum_type() != 'cns') {
        return false;
    }
    if (is_guest()) {
        return false;
    }
    if (!isset($GLOBALS['CNS_DRIVER'])) {
        return false;
    }

    $ret = non_overridden__is_httpauth_login();

    require_code('hybridauth');

    $special_type = $GLOBALS['CNS_DRIVER']->get_member_row_field(get_member(), 'm_password_compat_scheme');

    require_code('hybridauth');
    $is_hybridauth_account = is_hybridauth_special_type($special_type);

    if ($is_hybridauth_account) {
        global $SESSION_CONFIRMED_CACHE;
        $SESSION_CONFIRMED_CACHE = true;
    }

    return $ret;
}
