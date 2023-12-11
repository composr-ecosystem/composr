<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class cms_merge_test_set extends cms_test_case
{
    public function testFullTableCoverage()
    {
        $c = file_get_contents(get_file_base() . '/sources/hooks/modules/admin_import/cms_merge.php');

        require_code('database_relations');

        $skip_flags = TABLE_PURPOSE__FLUSHABLE | TABLE_PURPOSE__NON_BUNDLED | TABLE_PURPOSE__AUTOGEN_STATIC | TABLE_PURPOSE__MISC_NO_MERGE | TABLE_PURPOSE__NOT_KNOWN;

        $tables = $GLOBALS['SITE_DB']->query_select('db_meta', array('DISTINCT m_table'));
        foreach ($tables as $table) {
            if (strpos($table['m_table'], 'catalogue_efv_') !== false) {
                continue; // These are imported, but the test can't detect it
            }

            if (!table_has_purpose_flag($table['m_table'], $skip_flags)) {
                $this->assertTrue(strpos($c, $table['m_table']) !== false, 'No import defined for ' . $table['m_table']);
            }
        }
    }
}
