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
 * Hook class.
 */
class Hook_symbol_FB_CONNECT_UID
{
    public function run($param)
    {
        // The symbol connects direct to the Facebook API, which won't be running if the base user is not running on Facebook. The results using SU are going to be unreliable, because it's always going to get the result from the Facebook API (if running) and not the Composr database.

        if (!addon_installed('facebook_support')) {
            return '';
        }

        $value = '';
        if (get_forum_type() == 'cns') {
            require_code('facebook_connect');
            $errormsg = null;
            $value = facebook_get_current_user_id(null, $errormsg);
            if ($value === null) {
                $value = ($errormsg === null) ? '' : '-1'/*Signal there's an error to JS*/;
            } else {
                // Invalidate if we're not actually logged in
                if ((is_guest()) && (!$GLOBALS['IS_ACTUALLY_ADMIN'])) {
                    $value = ''; // Theoretically unneeded, but if FB cookie is invalid then we need to assume getUser may be wrong (if Guest, and not SU, it implies we found it was invalid in facebook_connect.php)
                }
            }
        }
        return $value;
    }
}
