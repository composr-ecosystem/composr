<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_startup_facebook
{
    public function run()
    {
        if (!addon_installed('facebook_support')) {
            return;
        }

        if (running_script('index') || running_script('preview') || running_script('iframe')) {
            require_lang('facebook');
        }
    }
}
