<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class Hook_symbol_FB_CONNECT_ACCESS_TOKEN
{
    public function run($param)
    {
        if ($GLOBALS['GETTING_MEMBER']) {
            return ''; // Probably the Tempcode compiler doing some scanning, startup still happening, could cause crash
        }

        if (get_option('facebook_allow_signups') == '0') {
            return '';
        }

        $value = '';
        if (get_forum_type() == 'cns') {
            if (!is_guest()) { // A little crazy, but we need to do this as FB does not expire the cookie consistently, although oauth would have failed when creating a session against it
                require_code('facebook_connect');
                global $FACEBOOK_CONNECT;
                if (!is_null($FACEBOOK_CONNECT)) {
                    $value = strval($FACEBOOK_CONNECT->getAccessToken());
                    if ($value == '0') {
                        $value = '';
                    }
                }
            }
        }
        return $value;
    }
}
