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
        /*
         * These checks ensure database fields are consistent with how database repair determines if fields in the database are the wrong CMS type
         *
         * Specific notes on field names:
         *
         * ip: The name is short and thus very likely to exist within the names of other non-ip fields, thus we enforce ip_address
         * usergroup: Already contains group, so no need to search specifically for this
         * grouping: Actually this is not a group but part of the forums; an exception is written out for this
         * until: Not contextually concise, but always refers to an expiration TIME
         * owner: Not contextually concise, but always refers to a MEMBER who owns the content
         * submitter: Not contextually concise, but always refers to a MEMBER who submitted the content
         * author: Refers to the author addon and does *not* refer to a MEMBER
         * times: There is no exclusion for this and will check against the TIME type; we should be using the term "count" instead
         * count: Overrides other types (including special types) and always expects INTEGER or UINTEGER
         */

        require_code('database_helper');

        $rows = $GLOBALS['SITE_DB']->query_select('db_meta', ['*'], []);
        foreach ($rows as $row) {
            $table = $row['m_table'];
            $type = $row['m_type'];
            $_type = str_replace(['*', '?', '#'], ['', '', ''], $row['m_type']);
            $name = $row['m_name'];

            if (strpos($name, 'count') !== false) {
                $this->assertTrue(($_type == 'INTEGER') || ($_type == 'UINTEGER'), $table . '/' . $name . ': Column is type ' . $type . ' but contains \'_count\' or \'count_\' in the name. You should fix the name or use type INTEGER or UINTEGER instead.');
            } else {
                switch ($_type) {
                    case 'IP':
                        $ok = (strpos($name, 'ip_address') !== false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'ip_address\' in the name (or might just contain \'ip\' which is too short). You should fix the name or use type MINIID_TEXT instead.');
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
                        $ok = ((strpos($name, 'group') !== false) && (strpos($name, 'grouping') === false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'group\' in the name. You should fix the name or the type accordingly.');
                        break;
                    case 'MEMBER':
                        $ok = ((strpos($name, 'member') !== false) || (strpos($name, 'user') !== false) || (strpos($name, 'submitter') !== false) || (strpos($name, 'owner') !== false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'member\', \'user\', \'owner\', nor \'submitter\' in the name. You should fix the name or the type accordingly.');

                        $ok = (strpos($name, 'author') === false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'author\' in the name. Author fields refer to authors from the addon and thus should use type AUTO_LINK instead. Please fix the name or the type.');
                        break;
                    case 'AUTO_LINK':
                        $ok = ((strpos($name, 'group') === false) || (strpos($name, 'grouping') !== false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'group\' in the name. You should fix the name or use type GROUP instead.');

                        $ok = ((strpos($name, 'usergroup') !== false) || ((strpos($name, 'member') === false) && (strpos($name, 'user') === false) && (strpos($name, 'submitter') === false) && (strpos($name, 'owner') === false)));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'member\', \'user\', \'owner\', or \'submitter\' in the name. You should fix the name or use type MEMBER instead.');

                        $ok = (strpos($name, '_id') !== false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but does not contain \'_id\' in the name. You should fix the name or use an integer-based type (ignore if changing to type MEMBER or GROUP).');
                        break;
                    case 'MINIID_TEXT':
                        $ok = (strpos($name, 'ip_address') === false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'ip_address\' in the name. you should fix the name or use type IP instead.');
                        break;
                    case 'SHORT_TEXT':
                        $ok = (strpos($name, 'url') === false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'url\' in the name. you should fix the name or use type URLPATH instead.');
                        break;
                    case 'INTEGER':
                    case 'AUTO':
                        $ok = ((strpos($name, 'group') === false) || (strpos($name, 'grouping') !== false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'group\' in the name. you should fix the name or use type GROUP instead.');

                        $ok = ((strpos($name, 'usergroup') !== false) || ((strpos($name, 'member') === false) && (strpos($name, 'user') === false) && (strpos($name, 'submitter') === false) && (strpos($name, 'owner') === false)));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'member\', \'user\', \'owner\', or \'submitter\' in the name. You should fix the name or use type MEMBER instead. Or, if counting members, use \'count\' in the name.');

                        $ok = (strpos($name, 'author') === false);
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'author\' in the name. Author fields refer to authors from the addon; you should fix the name or use type AUTO_LINK instead.');

                        $ok = ((strpos($name, '_id') === false) || ((strpos($name, 'group') !== false) && (strpos($name, 'grouping') === false)) || (strpos($name, 'user') !== false) || (strpos($name, 'member') !== false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'_id\' in the name. you should fix the name or use type AUTO_LINK instead.');
                    // No break; we should also check for the next conditions for these types
                    case 'LONG_TRANS':
                    case 'SHORT_TRANS':
                    case 'UINTEGER':
                        $ok = ((strpos($name, 'date') === false) && (strpos($name, 'time') === false) && (strpos($name, 'until') === false));
                        $this->assertTrue($ok, $table . '/' . $name . ': Column is type ' . $type . ' but contains \'date\', \'time\', or \'until\' in the name. You should fix the name or use type TIME instead. If counting something, you should use \'count\' in the name.');
                        break;
                }
            }
        }
    }
}
