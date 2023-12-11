<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        $max = 100;

        foreach ($_GET as $key => $val) {
            if (!is_string($key)) {
                $key = strval($key);
            }

            if ((strpos($key, 'max') !== false) && (is_string($val)) && (is_numeric($val))) {
                if (intval($val) > $max) {
                    $_GET[$key] = strval($max);
                }
            }
        }
    }
}
