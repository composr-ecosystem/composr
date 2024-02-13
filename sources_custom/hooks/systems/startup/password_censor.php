<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_startup_password_censor
{
    public function run()
    {
        if (!addon_installed('password_censor')) {
            return;
        }

        foreach ($_POST as $key => $val) {
            if ((is_string($val)) && (strpos($val, '[encrypt') !== false)) {
                require_code('password_censor');
                $_POST[$key] = _password_censor(post_param_string($key), PASSWORD_CENSOR__PRE_SCAN);
            }
        }
    }
}
