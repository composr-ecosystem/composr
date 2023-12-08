<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Hook_startup_facebook
{
    public function run()
    {
        if (running_script('index') || running_script('preview') || running_script('iframe')) {
            require_javascript('facebook');
            require_code('site');

            $tpl = do_template('FACEBOOK_FOOTER', null, null, true, null, '.tpl', 'templates', 'default');
            attach_to_screen_footer($tpl);
        }
    }
}
