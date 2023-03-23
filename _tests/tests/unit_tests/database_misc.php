<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*EXTRA FUNCTIONS: sleep*/

/**
 * Composr test case class (unit testing).
 */
class database_misc_test_set extends cms_test_case
{
    public function testCASE()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        // Simple syntax...

        $sql = "SELECT CASE 1 WHEN 1 THEN 'a' WHEN 2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'a');

        $sql = "SELECT CASE 2 WHEN 1 THEN 'a' WHEN 2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'b');

        $sql = "SELECT CASE 3 WHEN 1 THEN 'a' WHEN 2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === null);

        $sql = "SELECT CASE 3 WHEN 1 THEN 'a' WHEN 2 THEN 'b' ELSE 'c' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'c');

        // Expanded syntax...

        $sql = "SELECT CASE WHEN 1=1 THEN 'a' WHEN 1=2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'a');

        $sql = "SELECT CASE WHEN 2=1 THEN 'a' WHEN 2=2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'b');

        $sql = "SELECT CASE WHEN 3=1 THEN 'a' WHEN 3=2 THEN 'b' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === null);

        $sql = "SELECT CASE WHEN 3=1 THEN 'a' WHEN 4=2 THEN 'b' ELSE 'c' END";
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 'c');
    }

    public function testIFF()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('IFF', ['1=1', '2', '3']);
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 2);

        $sql = 'SELECT ' . db_function('IFF', ['1=2', '2', '3']);
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === 3);
    }

    public function testCONCAT()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('CONCAT', ['\'a\'', '\'b\'']);
        $expected_result = 'ab';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testREPLACE()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('REPLACE', ['\'ab\'', '\'a\'', '\'b\'']);
        $expected_result = 'bb';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testSUBSTR()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('SUBSTR', ['\'test\'', '1', '1']);
        $expected_result = 't';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testINSTR()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('INSTR', ['\'CORPORATE FLOOR\'', '\'OR\'']);
        $expected_result = 2;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT ' . db_function('INSTR', ['\'CORPORATE FLOOR\'', '\'FOOBAR\'']);
        $expected_result = 0; // Not found
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testUPPER()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('UPPER', ['\'test\'']);
        $expected_result = 'TEST';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testLOWER()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('LOWER', ['\'TEST\'']);
        $expected_result = 'test';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testLENGTH()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('LENGTH', ['\'test\'']);
        $expected_result = 4;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testRAND()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('RAND');
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue(is_numeric($result)/*NB: On MySQL it will come as a string and we have no way of changing that*/);
    }

    public function testCOALESCE()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('COALESCE', ['\'a\'', '\'b\'']);
        $expected_result = 'a';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT ' . db_function('COALESCE', ['NULL', '\'b\'']);
        $expected_result = 'b';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT ' . db_function('COALESCE', ['NULL', 'NULL']);
        $expected_result = null;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result === $expected_result);
    }

    public function testLEAST()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('LEAST', ['1', '2', '3']);
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testGREATEST()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('GREATEST', ['1', '2', '3']);
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testMOD()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('MOD', ['4', '2']);
        $expected_result = 0;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT ' . db_function('MOD', ['5', '2']);
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testCOUNT()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT COUNT(*) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT COUNT(1) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testSUM()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT SUM(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 6;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testAVG()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT AVG(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 2.0;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        if (is_integer($result)) {
            $result = floatval($result);
        }
        $this->assertTrue($result == $expected_result);
    }

    public function testMAX()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT MAX(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testMIN()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT MIN(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testGROUP_CONCAT()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('GROUP_CONCAT', ['x', '(SELECT \'a\' AS x UNION SELECT \'b\' AS x) x']);
        $expected_result = 'a,b';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testX_ORDER_BY_BOOLEAN()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT x FROM (SELECT 1 AS x UNION SELECT 2 AS x) y ORDER BY ' . db_function('X_ORDER_BY_BOOLEAN', ['x=1']);
        $expected_result = 2;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testREVERSE()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT ' . db_function('REVERSE', ['\'abca\'']);
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == 'acba');
    }

    public function testOperations()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT 1+2';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT 2-1';
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT 2*2';
        $expected_result = 4;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT 6/2';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testInequalities()
    {
        if (($this->only !== null) && ($this->only != 'sql')) {
            return;
        }

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1>2';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(empty($result));

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 2>1';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(count($result) == 1);

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1<2';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(count($result) == 1);

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 2<1';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(empty($result));

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1>=2';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(empty($result));

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 2>=1';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(count($result) == 1);

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1<=2';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(count($result) == 1);

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 2<=1';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(empty($result));

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1=1';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(count($result) == 1);

        $sql = 'SELECT 1 FROM (SELECT 1 AS x) x WHERE 1=0';
        $result = $GLOBALS['SITE_DB']->query($sql);
        $this->assertTrue(empty($result));
    }

    public function testDDL()
    {
        if (($this->only !== null) && ($this->only != 'ddl')) {
            return;
        }

        $db = $GLOBALS['SITE_DB'];

        // In case test had crashed previously
        $db->drop_table_if_exists('dbmisc_test');
        $db->drop_table_if_exists('dbmisc_renamed');

        // Tables and fields
        $db->create_table('dbmisc_test', [
            'id' => '*INTEGER',
            'blah' => 'INTEGER',
        ]);
        $db->query_insert('dbmisc_test', ['id' => 1, 'blah' => 123]);
        $db->change_primary_key('dbmisc_test', ['blah']);
        $db->delete_table_field('dbmisc_test', 'id');
        $db->add_auto_key('dbmisc_test', 'id');
        $db->add_table_field('dbmisc_test', 'whatever', 'REAL', 0.0);
        $db->add_table_field('dbmisc_test', 'whatever_nullable', '?REAL');
        $db->add_table_field('dbmisc_test', 'long_text', 'LONG_TEXT', 'test');
        $db->add_table_field('dbmisc_test', 'long_text_no_default', 'LONG_TEXT');
        $db->rename_table('dbmisc_test', 'dbmisc_renamed');
        $db->drop_table_if_exists('dbmisc_renamed');

        // Indexes
        $db->create_table('dbmisc_test', [
            'id' => '*AUTO',
            'blah' => 'INTEGER',
            'some_text' => 'SHORT_TEXT',
        ]);
        $db->create_index('dbmisc_test', 'test_index', ['blah']);
        $db->create_index('dbmisc_test', 'test_fulltext_index', ['some_text']);
        $db->delete_index_if_exists('dbmisc_test', 'test_index');
        $db->drop_table_if_exists('dbmisc_test');
    }

    public function testEmoji()
    {
        if (($this->only !== null) && ($this->only != 'utf')) {
            return;
        }

        $emoji = "\u{1F601}";
        set_value('emoji_test', $emoji);
        $this->assertTrue($emoji == get_value('emoji_test'));
        delete_value('emoji_test');
    }

    public function testSmoothUtf8()
    {
        if (($this->only !== null) && ($this->only != 'utf')) {
            return;
        }

        // Really you should also manually check the DB is storing utf-8, not just working as a byte-bucket

        $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test_2');
        $GLOBALS['SITE_DB']->create_table('testy_test_test_2', [
            'id' => '*AUTO',
            'test_data_1' => 'LONG_TEXT',
            'test_data_2' => 'SHORT_TEXT',
        ]);

        $data = "\u{203E}";

        $GLOBALS['SITE_DB']->query_insert('testy_test_test_2', [
            'test_data_1' => $data,
            'test_data_2' => $data,
        ]);

        $this->assertTrue($GLOBALS['SITE_DB']->query_select_value('testy_test_test_2', 'test_data_1') == $data);
        $this->assertTrue($GLOBALS['SITE_DB']->query_select_value('testy_test_test_2', 'test_data_2') == $data);

        $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test_2');
    }

    public function testCountApprox()
    {
        if (($this->only !== null) && ($this->only != 'count')) {
            return;
        }

        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', [], null) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', ['id' => db_get_first_id()], null) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', [], 'id=' . strval(db_get_first_id())) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', ['id' => db_get_first_id()], 'id=' . strval(db_get_first_id())) > 0);
    }

    public function testFullTextSearch()
    {
        if (($this->only !== null) && ($this->only != 'fulltext')) {
            return;
        }

        require_code('database_search');

        $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test');
        $GLOBALS['SITE_DB']->create_table('testy_test_test', [
            'id' => '*AUTO',
            'test_data_1' => 'LONG_TEXT',
            'test_data_2' => 'SHORT_TEXT',
        ]);
        $GLOBALS['SITE_DB']->create_index('testy_test_test', '#testx', ['test_data_1']);

        $total = 20;

        for ($i = 0; $i < $total; $i++) {
            $id = $GLOBALS['SITE_DB']->query_insert('testy_test_test', [
                'test_data_1' => ($i == 0) ? 'abacus, this is a test' : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                'test_data_2' => 'cheese',
            ], true);
            require_code('content2');
            seo_meta_set_for_explicit('test', strval($id), 'sample', '');
        }

        sleep(2); // Some databases may take some time to fill up the index, via a background process

        $searches = [
            // By keyword
            'by_keyword' => [
                /*$content = */'sample',
                /*$expected = */$total,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],

            // Fulltext
            'boolean_yes__success' => [
                /*$content = */'+abacus',
                /*$expected = */1,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_no__success' => [ // If this is failing on SQL Server, try resetting the SQL Server process (auto-indexing may be buggy or delayed, but nothing we can do)
                /*$content = */'-foobar abacus',
                /*$expected = */1,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_yes__fail' => [
                /*$content = */'+foobar',
                /*$expected = */0,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_no__fail' => [
                /*$content = */'-abacus foobar',
                /*$expected = */0,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
        ];

        foreach ($searches as $test_codename => $bits) {
            if (($this->only !== null) && ($this->only != $test_codename)) {
                continue;
            }

            list($content, $expected, $fields, $raw_fields) = $bits;
            list($content_where) = build_content_where($content);
            $order = '';
            $rows = get_search_rows(
                'test',
                'id',
                $content,
                $content_where,
                '',
                false,
                false,
                1000,
                0,
                $order,
                'ASC',
                'testy_test_test r',
                'r.id',
                $fields,
                $raw_fields
            );
            $this->assertTrue((count($rows) == $expected), $test_codename . ' failed, got ' . integer_format(count($rows)) . ' rows but expected ' . integer_format($expected) . ' rows.');
        }

        if ($this->only === null) {
            $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test');
        }
        // Otherwise we're probably testing via manual queries too, so need the table
    }
}
