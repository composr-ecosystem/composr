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
        $GLOBALS['BOOTSTRAPPING'] = false; // We know we've set up enough to be able to do a clean inform_exit screen

        require_lang('facebook');
        require_javascript('facebook_support');
        require_code('site');

        $tpl = do_template('FACEBOOK_FOOTER', [], null, true, null, '.tpl', 'templates', 'default');
        attach_to_screen_footer($tpl);

        inform_exit(do_lang_tempcode('LOGGED_OUT_OF_FACEBOOK_ACCOUNT'));
    }
    $GLOBALS['MEMBER_CACHED'] = $GLOBALS['FORUM_DRIVER']->get_guest_id();
}
