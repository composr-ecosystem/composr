<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class Hook_symbol_MANTIS_TOTAL
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

        $cnt = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM mantis_bug_table WHERE status<80');
        return strval($cnt);
    }
}
