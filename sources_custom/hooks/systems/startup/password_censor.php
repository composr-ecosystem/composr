<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_startup_password_censor
{
    public function run()
    {
        foreach ($_POST as $key => $val) {
            if ((is_string($val)) && (strpos($val, '[encrypt') !== false)) {
                require_code('password_censor');
                $_POST[$key] = _password_censor(post_param_string($key), PASSWORD_CENSOR__PRE_SCAN);
            }
        }
    }
}
