<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_symbol_MANTIS_SPONSOR_WEEK_ACTIVITY
{
    public function run($param)
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return '';
        }

        if (!addon_installed('tickets')) {
            return '';
        }
        if (!addon_installed('stats')) {
            return '';
        }
        if (!addon_installed('ecommerce')) {
            return '';
        }
        if (!addon_installed('points')) {
            return '';
        }

        if (get_forum_type() != 'cns') {
            return '';
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return '';
        }

        $cnt_in_last_week = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM mantis_sponsorship_table WHERE date_submitted>' . strval(time() - 60 * 60 * 24 * 7));
        return strval($cnt_in_last_week);
    }
}
