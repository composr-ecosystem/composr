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
class ___database_integrity_test_set extends cms_test_case
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
}
