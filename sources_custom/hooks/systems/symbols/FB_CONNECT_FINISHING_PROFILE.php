<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class Hook_symbol_FB_CONNECT_FINISHING_PROFILE
{
    public function run($param)
    {
        if (!addon_installed('facebook_support')) {
            return '';
        }

        require_code('facebook_connect');

        if (isset($GLOBALS['FACEBOOK_FINISHING_PROFILE'])) {
            return '1';
        }
        return '0';
    }
}
