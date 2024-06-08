<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _database_integrity_test_set extends cms_test_case
{
    // This test may fail if unexpected tables are present (e.g. from a prior install of another version)

    public function setUp()
    {
        parent::setUp();

        require_code('database_repair');
    }

    public function testIsMySQL()
    {
        $this->assertTrue(strpos(get_db_type(), 'mysql') !== false, 'Test can only run with MySQL');
    }

    public function testNoErrors()
    {
        if (strpos(get_db_type(), 'mysql') !== false) {
            $ob = new DatabaseRepair();
            list($phase, $sql) = $ob->search_for_database_issues();
            $this->assertTrue($phase == 2);
            if ($phase == 2) {
                $this->assertTrue($sql == '', $sql);
            }

            if ($this->debug) {
                @var_dump($phase);
                @var_dump($sql);
            }
        }
    }

    public function testCorrectNamingConventions()
    {
        // These checks ensure database fields are consistent with how database repair determines if fields in the database are the wrong CMS type
        require_code('database_helper');

        $rows = $GLOBALS['SITE_DB']->query_select('db_meta', ['*'], []);
        foreach ($rows as $row) {
            $table = $row['m_table'];
            $type = $row['m_type'];
            $name = $row['m_name'];

            switch (str_replace(['*', '?', '#'], ['', '', ''], $type)) {
                case 'IP':
                    $ok = (strpos($name, 'ip_address') !== false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'ip_address\' in the name (note that \'ip\' alone is too short). You should fix the name or use type MINIID_TEXT instead.');
                    break;
                case 'URLPATH':
                    $ok = (strpos($name, 'url') !== false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'url\' in the name. You should fix the name or use type SHORT_TEXT instead.');
                    break;
                case 'TIME':
                    $ok = ((strpos($name, 'date') !== false) || (strpos($name, 'time') !== false) || (strpos($name, 'until') !== false));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'date\', \'time\', or \'until\' in the name. You should fix the name or use an integer-based type instead.');
                    break;
                case 'GROUP':
                    $ok = (strpos($name, 'group') !== false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'group\' in the name. You should fix the name or the type accordingly.');
                    break;
                case 'MEMBER':
                    $ok = ((strpos($name, 'member') !== false) || (strpos($name, 'user') !== false) || (strpos($name, 'submitter') !== false));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'member\', \'user\', nor \'submitter\' in the name. You should fix the name or the type accordingly.');
                    break;
                case 'AUTO_LINK':
                    $ok = (strpos($name, '_id') !== false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'_id\' in the name. You should fix the name or use an integer-based type.');

                    $ok = (strpos($name, 'group') === false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'group\' in the name. You should fix the name or use type GROUP instead.');

                    $ok = ((strpos($name, 'usergroup') !== false) || ((strpos($name, 'member') === false) && (strpos($name, 'user') === false) && (strpos($name, 'submitter') === false)));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'member\', \'user\', or \'submitter\' in the name. You should fix the name or use type MEMBER instead.');
                    break;
                case 'MINIID_TEXT':
                    $ok = (strpos($name, 'ip_address') === false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'ip_address\' in the name. you should fix the name or use type IP instead.');
                    break;
                case 'SHORT_TEXT':
                    $ok = (strpos($name, 'url') === false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'url\' in the name. you should fix the name or use type URLPATH instead.');
                    break;
                case 'AUTO':
                case 'INTEGER':
                    $ok = (strpos($name, 'group') === false);
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'group\' in the name. you should fix the name or use type GROUP instead.');

                    $ok = ((strpos($name, 'usergroup') !== false) || ((strpos($name, 'member') === false) && (strpos($name, 'user') === false) && (strpos($name, 'submitter') === false)));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'member\', \'user\', or \'submitter\' in the name. You should fix the name or use type MEMBER instead.');

                    $ok = ((strpos($name, '_id') === false) || (strpos($name, 'group') !== false) || (strpos($name, 'user') !== false) || (strpos($name, 'member') !== false));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'_id\' in the name. you should fix the name or use type AUTO_LINK instead.');
                    // No break; we should also check for the next conditions for these types
                case 'LONG_TRANS':
                case 'SHORT_TRANS':
                case 'UINTEGER':
                    $ok = ((strpos($name, 'date') === false) && (strpos($name, 'time') === false) && (strpos($name, 'until') === false));
                    $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'date\', \'time\', or \'until\' in the name. You should fix the name or use type TIME instead.');
                    break;
            }
        }
    }
}
