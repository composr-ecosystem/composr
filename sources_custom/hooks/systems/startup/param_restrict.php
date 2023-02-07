<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    pagination_protection
 */

/**
 * Hook class.
 */
class Hook_startup_param_restrict
{
    public function run()
    {
        if (!addon_installed('pagination_protection')) {
            return;
        }

        if (running_script('hybridauth_admin_atom')) {
            return;
        }

        $max = 100;

        foreach ($_GET as $key => $val) {
            if (!is_string($key)) {
                $key = strval($key);
            }

            if ((strpos($key, 'max') !== false) && (is_numeric($val))) {
                if (intval($val) > $max) {
                    $_GET[$key] = strval($max);
                }
            }
        }
    }
}
