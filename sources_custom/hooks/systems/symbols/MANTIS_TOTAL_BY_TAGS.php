<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_symbol_MANTIS_TOTAL_BY_TAGS
{
    public function run($param)
    {
        if (!array_key_exists(0, $param)) {
            return '';
        }

        $in_clause = 'IN';
        if ($param[0] == '0') {
            $in_clause = 'NOT IN';
        }

        array_shift($param);

        $param = array_map('intval', $param);

        $cnt = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM mantis_bug_table WHERE status <80 AND EXISTS (SELECT 1 FROM mantis_bug_tag_table WHERE mantis_bug_tag_table.bug_id = mantis_bug_table.id AND mantis_bug_tag_table.tag_id ' . $in_clause . ' (' . implode(', ', $param) . '))');
        if ($cnt === null) {
            return '';
        }
        return integer_format($cnt);
    }
}
