<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

/**
 * Hook class.
 */
class Hook_symbol_MANTIS_TOTAL
{
    public function run($param)
    {
        if (!addon_installed('cms_homesite_tracker')) {
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
