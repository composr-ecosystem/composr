<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_startup_referrals
{
    public function run()
    {
        // Store referrer in cookie
        $by_url = get_param_string('keep_referrer', '');
        if ($by_url != '') {
            $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);
            if ((!isset($ini_file['referrer_cookies'])) || ($ini_file['referrer_cookies'] == '1')) {
                require_code('users_active_actions');
                cms_setcookie('referrer', $by_url);
            }
        }
    }
}
