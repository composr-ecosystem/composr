<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class database_relations_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('database_relations');
    }

    public function testTablePurposesDefined()
    {
        $table_purposes = get_table_purpose_flags();

        $all_tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table']);
        foreach ($all_tables as $_table) {
            $table = $_table['m_table'];

            if (in_array($table, ['testy_test_test', 'testy_test_test_2', 'temp_test', 'temp_test_linked'])) {
                continue;
            }

            $this->assertTrue(array_key_exists($table, $table_purposes), 'Table purposes not described: ' . $table);
        }
    }

    /* We actually don't define descriptions for all tables, only important ones that aren't necessarily obvious. Sometimes it is good to uncomment this test just to see if anything stands out (i.e. new complex tables that accidentally weren't documented).
    public function testTableDescriptionsDefined()
    {
        $table_descriptions = get_table_descriptions();

        $all_tables = $GLOBALS['SITE_DB']->query_select('db_meta', array('DISTINCT m_table'));
        foreach ($all_tables as $table) {
            if (!table_has_purpose_flag($table['m_table'], TABLE_PURPOSE__NON_BUNDLED)) {
                $this->assertTrue(array_key_exists($table['m_table'], $table_descriptions), 'Table description not provided: ' . $table['m_table']);
            }
        }
    }
    */

    public function testRelationsDefined()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $all_links = $GLOBALS['SITE_DB']->query('SELECT m_table,m_name FROM ' . get_table_prefix() . 'db_meta WHERE m_type LIKE \'' . db_encode_like('%AUTO\_LINK%') . '\' ORDER BY m_table');
        $links = get_relation_map();

        foreach ($all_links as $l) {
            if (!table_has_purpose_flag($l['m_table'], TABLE_PURPOSE__NON_BUNDLED)) {
                $_l = $l['m_table'] . '.' . $l['m_name'];
                $this->assertTrue(array_key_exists($_l, $links), 'Link not described: ' . $_l);
            }
        }
    }

    public function testRelationsAccurate()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $links = get_relation_map();
        foreach ($links as $from => $to) {
            if ($from !== null) {
                list($from_table, $from_field) = explode('.', $from, 2);

                if (substr($from_table, 0, 2) == 'f_') {
                    if (get_forum_type() != 'cns') {
                        continue;
                    }
                }
                $db = get_db_for($from_table);

                $db->query_select_value_if_there($from_table, $from_field);
            }

            if ($to !== null) {
                list($to_table, $to_field) = explode('.', $to, 2);

                if (substr($to_table, 0, 2) == 'f_') {
                    if (get_forum_type() != 'cns') {
                        continue;
                    }
                }
                $db = get_db_for($to_table);

                $db->query_select_value_if_there($to_table, $to_field);
            }
        }
    }

    public function testMetaAwareDefined() // Composr's equivalent of "entities"
    {
        $tables_in_hooks = [];

        require_code('content');

        $meta_aware = find_all_hooks('systems', 'content_meta_aware') + find_all_hooks('systems', 'resource_meta_aware');
        foreach (array_keys($meta_aware) as $hook) {
            $resource_fs_hook = convert_composr_type_codes('content_type', $hook, 'commandr_filesystem_hook');
            $table = convert_composr_type_codes('content_type', $hook, 'table');
            if ($table !== null) {
                $tables_in_hooks[$table] = $resource_fs_hook;
            }
        }

        $skip_flags = TABLE_PURPOSE__NON_BUNDLED | TABLE_PURPOSE__FLUSHABLE | TABLE_PURPOSE__NO_STAGING_COPY | TABLE_PURPOSE__MISC_NO_MERGE | TABLE_PURPOSE__AUTOGEN_STATIC | TABLE_PURPOSE__SUBDATA | TABLE_PURPOSE__AS_COMMANDER_FS_EXTENDED_CONFIG;

        $all_tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table']);
        foreach ($all_tables as $_table) {
            $table = $_table['m_table'];

            if (in_array($table, ['testy_test_test', 'testy_test_test_2', 'temp_test', 'temp_test_linked'])) {
                continue;
            }

            if (get_forum_type() != 'cns') {
                if (substr($table, 0, 2) == 'f_') {
                    continue;
                }
            }

            if (!table_has_purpose_flag($table, $skip_flags)) {
                $this->assertTrue(isset($tables_in_hooks[$table]), 'Table not in a content or resource hook: ' . $table);
            }
        }
    }
}
