<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class xml_db_test_set extends cms_test_case
{
    protected $db;

    public function setUp()
    {
        parent::setUp();

        require_code('database/xml');
        $static = new Database_Static_xml('cms_');
        $this->db = new DatabaseConnector('test', 'localhost', 'root', '', 'cms_', false, $static);

        $this->db->drop_table_if_exists('db_meta');
        $this->db->create_table('db_meta', [
            'm_table' => '*ID_TEXT',
            'm_name' => '*ID_TEXT',
            'm_type' => 'ID_TEXT',
        ]);

        $this->db->drop_table_if_exists('test');
        $this->db->create_table('test', [
            'id' => '*AUTO',
            'line1' => 'SHORT_TEXT',
            'line2' => 'SHORT_TEXT',
        ]);
        $this->db->query_insert('test', [
            'line1' => 'test1',
            'line2' => 'test2',
        ]);
        $this->db->query_insert('test', [
            'line1' => 'test1',
            'line2' => 'test2',
        ]);
        $this->db->query_insert('test', [
            'line1' => 'test1',
            'line2' => 'test2x',
        ]);
    }

    public function testCompoundDistinct()
    {
        $rows = $this->db->query_select('test', ['DISTINCT line1,line2'], [], 'ORDER BY line1');
        $this->assertTrue(count($rows) == 2);
        if (array_key_exists(0, $rows)) {
            $this->assertTrue(array_keys($rows[0]) == ['line1', 'line2']);
        }
    }

    public function testWildcardDistinct()
    {
        $rows = $this->db->query_select('test', ['DISTINCT *'], [], 'ORDER BY line1');
        $this->assertTrue(count($rows) == 3);

        $rows = $this->db->query_select('test r', ['DISTINCT r.*'], [], 'ORDER BY line1');
        if (array_key_exists(0, $rows)) {
            $this->assertTrue(count($rows[0]) == 3);
        }
        $this->assertTrue(count($rows) == 3);
    }

    public function testAliasDistinct()
    {
        $rows = $this->db->query_select('test', ['DISTINCT line1 AS foo,line2 AS bar'], [], 'ORDER BY line1');
        $this->assertTrue(count($rows) == 2);
        if (array_key_exists(0, $rows)) {
            $this->assertTrue(array_keys($rows[0]) == ['foo', 'bar']);
        }
    }

    public function testOrderDistinctConstraint()
    {
        $rows = $this->db->query_select('test', ['DISTINCT id'], [], 'ORDER BY id');
        $this->assertTrue(count($rows) == 3);

        $rows = $this->db->query_select('test', ['DISTINCT line1'], [], 'ORDER BY id', null, 0, true);
        $this->assertTrue($rows === null);
    }

    public function testGroupByConstraint()
    {
        $rows = $this->db->query_select('test', ['id'], [], 'GROUP BY id');
        $this->assertTrue(count($rows) == 3);

        $rows = $this->db->query_select('test', ['line1'], [], 'GROUP BY id', null, 0, true);
        $this->assertTrue($rows === null);
    }

    public function testCount()
    {
        $rows = $this->db->query_select('test', ['COUNT(*) AS cnt']);
        $this->assertTrue($rows[0]['cnt'] == 3);
        $this->assertTrue(count($rows) == 1);

        $rows = $this->db->query_select('test', ['COUNT(DISTINCT line2) AS cnt']);
        $this->assertTrue($rows[0]['cnt'] == 2);

        $rows = $this->db->query_select('test r', ['COUNT(DISTINCT r.line2) AS cnt']);
        $this->assertTrue($rows[0]['cnt'] == 2);

        $rows = $this->db->query_select('test r', ['COUNT(DISTINCT *) AS cnt']);
        $this->assertTrue($rows[0]['cnt'] == 3);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
