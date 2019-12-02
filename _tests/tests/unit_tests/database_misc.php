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

/*EXTRA FUNCTIONS: sleep*/

/**
 * Composr test case class (unit testing).
 */
class database_misc_test_set extends cms_test_case
{
    public function testCONCAT()
    {
        $sql = 'SELECT ' . db_function('CONCAT', ['\'a\'', '\'b\'']);
        $expected_result = 'ab';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testREPLACE()
    {
        $sql = 'SELECT ' . db_function('REPLACE', ['\'ab\'', '\'a\'', '\'b\'']);
        $expected_result = 'bb';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testSUBSTR()
    {
        $sql = 'SELECT ' . db_function('SUBSTR', ['\'test\'', '1', '1']);
        $expected_result = 't';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testLENGTH()
    {
        $sql = 'SELECT ' . db_function('LENGTH', ['\'test\'']);
        $expected_result = 4;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testRAND()
    {
        $sql = 'SELECT ' . db_function('RAND');
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue(is_numeric($result)/*NB: On MySQL it will come as a string and we have no way of changing that*/);
    }

    public function testCOALESCE()
    {
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
        $sql = 'SELECT ' . db_function('LEAST', ['1', '2', '3']);
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testGREATEST()
    {
        $sql = 'SELECT ' . db_function('GREATEST', ['1', '2', '3']);
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testMOD()
    {
        $sql = 'SELECT ' . db_function('MOD', ['4', '2']);
        $expected_result = 0;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);

        $sql = 'SELECT ' . db_function('MOD', ['5', '2']);
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assertTrue($result == $expected_result);
    }

    public function testGROUP_CONCAT()
    {
        $sql = 'SELECT ' . db_function('GROUP_CONCAT', ['x', '(SELECT \'a\' AS x UNION SELECT \'b\' AS x) x']);
        $expected_result = 'a,b';
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testCOUNT()
    {
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
        $sql = 'SELECT SUM(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 6;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testAVG()
    {
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
        $sql = 'SELECT MAX(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 3;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testMIN()
    {
        $sql = 'SELECT MIN(x) FROM (SELECT 1 AS x UNION SELECT 2 AS x UNION SELECT 3 AS x) x';
        $expected_result = 1;
        $result = $GLOBALS['SITE_DB']->query_value_if_there($sql, false, true);
        $this->assertTrue($result == $expected_result);
    }

    public function testOperations()
    {
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

    public function testEmoji()
    {
        $emoji = hex2bin('f09f9881');
        set_value('emoji_test', $emoji);
        $this->assertTrue($emoji == get_value('emoji_test'));
        delete_value('emoji_test');
    }

    public function testCountApprox()
    {
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', [], null) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', ['id' => db_get_first_id()], null) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', [], 'id=' . strval(db_get_first_id())) > 0);
        $this->assertTrue($GLOBALS['SITE_DB']->get_table_count_approx('download_categories', ['id' => db_get_first_id()], 'id=' . strval(db_get_first_id())) > 0);
    }

    public function testSmoothUtf8()
    {
        // Really you should also manually check the DB is storing utf-8, not just working as a byte-bucket

        $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test_2');
        $GLOBALS['SITE_DB']->create_table('testy_test_test_2', [
            'id' => '*AUTO',
            'test_data_1' => 'LONG_TEXT',
            'test_data_2' => 'SHORT_TEXT',
        ]);

        $data = hex2bin('e280be');

        $GLOBALS['SITE_DB']->query_insert('testy_test_test_2', [
            'test_data_1' => $data,
            'test_data_2' => $data,
        ]);

        $this->assertTrue($GLOBALS['SITE_DB']->query_select_value('testy_test_test_2', 'test_data_1') == $data);
        $this->assertTrue($GLOBALS['SITE_DB']->query_select_value('testy_test_test_2', 'test_data_2') == $data);

        $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test_2');
    }

    public function testFullTextSearch()
    {
        require_code('database_search');
        $boolean_operator = 'AND';

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
                /*$boolean_search = */true,
                /*$expected = */$total,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],

            // Fulltext
            'boolean_yes__success' => [
                /*$content = */'abacus',
                /*$boolean_search = */true,
                /*$expected = */1,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_no__success' => [ // If this is failing on SQL Server, try resetting the SQL Server process (auto-indexing may be buggy or delayed, but nothing we can do)
                /*$content = */'abacus',
                /*$boolean_search = */false,
                /*$expected = */1,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_yes__fail' => [
                /*$content = */'foobar',
                /*$boolean_search = */true,
                /*$expected = */0,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
            'boolean_no__fail' => [
                /*$content = */'foobar',
                /*$boolean_search = */false,
                /*$expected = */0,
                /*$fields = */[],
                /*$raw_fields = */['r.test_data_1'],
            ],
        ];

        foreach ($searches as $test_codename => $bits) {
            if (($this->only !== null) && ($this->only != $test_codename)) {
                continue;
            }

            list($content, $boolean_search, $expected, $fields, $raw_fields) = $bits;
            list($content_where) = build_content_where($content, $boolean_search, $boolean_operator);
            $order = '';
            $rows = get_search_rows(
                'test',
                'id',
                $content,
                $boolean_search,
                $boolean_operator,
                false,
                'ASC',
                1000,
                0,
                false,
                'testy_test_test r',
                $fields,
                '',
                $content_where,
                $order,
                'r.id',
                $raw_fields
            );
            $this->assertTrue(count($rows) == $expected, $test_codename . ' failed, got ' . integer_format(count($rows)) . ' rows but expected ' . integer_format($expected) . ' rows');
        }

        if ($this->only === null) {
            $GLOBALS['SITE_DB']->drop_table_if_exists('testy_test_test');
        }
        // Otherwise we're probably testing via manual queries too, so need the table
    }
}
