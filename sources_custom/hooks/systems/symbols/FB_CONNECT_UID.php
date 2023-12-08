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
class Hook_symbol_FB_CONNECT_UID
{
    public function run($param)
    {
        // The symbol connects direct to the Facebook API, which won't be running if the base user is not running on Facebook. The results using SU are going to be unreliable, because it's always going to get the result from the Facebook API (if running) and not the Composr database.

        if (get_option('facebook_allow_signups') == '0') {
            return '';
        }

        if ((is_guest()) && (!$GLOBALS['IS_ACTUALLY_ADMIN'])) {
            return ''; // Theoretically unneeded, but if FB cookie is invalid then we need to assume getUser may be wrong (if Guest, and not SU, it implies we found it was invalid in facebook_connect.php)
        }

        $value = '';
        if (get_forum_type() == 'cns') {
            require_code('facebook_connect');
            global $FACEBOOK_CONNECT;
            if (!is_null($FACEBOOK_CONNECT)) {
                safe_ini_set('ocproducts.type_strictness', '0');
                $value = strval($FACEBOOK_CONNECT->getUser());
                //safe_ini_set('ocproducts.type_strictness', '1');
                if ($value == '0') {
                    $value = '';
                }
            }
        }
        return $value;
    }
}
