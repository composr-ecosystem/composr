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
class Hook_symbol_FB_CONNECT_ACCESS_TOKEN
{
    public function run($param)
    {
        if (!addon_installed('facebook_support')) {
            return '';
        }

        if ($GLOBALS['GETTING_MEMBER']) {
            return ''; // Probably the Tempcode compiler doing some scanning, startup still happening, could cause crash
        }

        $value = '';
        if (get_forum_type() == 'cns') {
            if (!is_guest()) { // A little crazy, but we need to do this as FB does not expire the cookie consistently, although oauth would have failed when creating a session against it
                require_code('facebook_connect');
                $value = facebook_get_access_token_from_js_sdk();
                if ($value === null) {
                    $value = '';
                }
            }
        }
        return $value;
    }
}
