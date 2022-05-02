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
class Hook_symbol_FB_CONNECT_LOGGED_OUT
{
    public function run($param)
    {
        if (get_option('facebook_allow_signups') == '0') {
            return '0';
        }

        require_code('facebook_connect');

        if (isset($GLOBALS['FACEBOOK_LOGOUT'])) {
            return '1';
        }
        return '0';
    }
}
