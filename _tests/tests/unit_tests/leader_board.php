<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class leader_board_test_set extends cms_test_case
{

    protected $leaderboards;

    public function setUp()
    {
        parent::setUp();

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            $this->assertTrue(false, 'Test only works with the points and leader_board addons.');
            return;
        }

        $this->test_id = db_get_first_id() + 1;
        $this->leaderboards = [];

        require_code('leader_board');
        require_code('leader_board2');

        $this->establish_admin_session();
    }

    public function testLeaderBoardFrequencyRolling()
    {
        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Test 1: Set up leader-boards

        $this->leaderboards['week_r'] = add_leader_board("Test week_r", 'holders', 10, 'week', 1, 0, null);
        $this->assertTrue(isset($this->leaderboards['week_r']) && !empty($this->leaderboards['week_r']), 'Failed to create week_r leader-board');

        $this->leaderboards['month_r'] = add_leader_board("Test month_r", 'holders', 10, 'month', 1, 0, null);
        $this->assertTrue(isset($this->leaderboards['month_r']) && !empty($this->leaderboards['month_r']), 'Failed to create month_r leader-board');

        $this->leaderboards['year_r'] = add_leader_board("Test year_r", 'holders', 10, 'year', 1, 0, null);
        $this->assertTrue(isset($this->leaderboards['year_r']) && !empty($this->leaderboards['year_r']), 'Failed to create year_r leader-board');

        // Test 2: First, test to see that none of the leader-boards regenerate if the current time is the same as the most recent result set
        $forced_start = strtotime("January 1");
        $forced_time = $forced_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 2.');
        }

        // Test 3: Next, test 6 days after the most recent result set. Again, nothing should re-generate.
        $forced_start = strtotime("January 1");
        $forced_time = strtotime("+6 days", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 3.');
        }

        // Test 4: Test one week after the most recent result set. Only the week leader-board should re-generate.
        $forced_start = strtotime("January 8");
        $forced_time = strtotime("+1 week", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            if ($key == 'week_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 4.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 4.');
            }
        }

        // Test 5: Test one month after the most recent result set. The week and month leader-boards should re-generate
        $forced_start = strtotime("January 15");
        $forced_time = strtotime("+1 month", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            if ($key == 'week_r' || $key == 'month_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 5.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 5.');
            }
        }

        // Test 6: Finally, test one year after the result set. All leader-boards should re-generate.
        $forced_start = strtotime("February 15");
        $forced_time = strtotime("+1 year", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 6.');
        }
    }

    public function testLeaderBoardFrequencyNonRolling()
    {
        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Test 1: Set up leader-boards

        $this->leaderboards['week'] = add_leader_board("Test week", 'earners', 10, 'week', 0, 0, null);
        $this->assertTrue(isset($this->leaderboards['week']) && !empty($this->leaderboards['week']), 'Failed to create week_r leader-board');

        $this->leaderboards['month'] = add_leader_board("Test month", 'earners', 10, 'month', 0, 0, null);
        $this->assertTrue(isset($this->leaderboards['month']) && !empty($this->leaderboards['month']), 'Failed to create month_r leader-board');

        $this->leaderboards['year'] = add_leader_board("Test year", 'earners', 10, 'year', 0, 0, null);
        $this->assertTrue(isset($this->leaderboards['year']) && !empty($this->leaderboards['year']), 'Failed to create year_r leader-board');

        // Test 2A: First, test to see that none of the leader-boards regenerate if the current time is the same as the most recent result set (Sunday)
        $forced_start = strtotime("January 1");
        $forced_start = strtotime("sunday", $forced_start);
        $forced_time = $forced_start;
        $sep = '+++';
        var_dump($sep);
        var_dump($forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2A.');
        }

        // Test 2B: Same test but with Monday to test both cases of start of week for week leader-boards.
        $forced_start = strtotime("January 1");
        $forced_start = strtotime("monday", $forced_start);
        $forced_time = $forced_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2B.');
        }

        // Test 3: Next, if the result sets started on Tuesday and it is currently Saturday, make sure nothing re-generates again.
        $forced_start = strtotime("May 15");
        $forced_start = strtotime("tuesday", $forced_start);
        $forced_time = strtotime("saturday", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 3.');
        }

        // Test 4: If the recent result set was friday and it is now monday, week should re-generate but nothing else.
        $forced_start = strtotime("May 15");
        $forced_start = strtotime("friday", $forced_start);
        $forced_time = strtotime("monday", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            if ($key == 'week') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 4.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 4.');
            }
        }

        // Test 5: If the recent result set was January 15 and it is now February 1, week and month should re-generate.
        $forced_start = strtotime("January 15");
        $forced_time = strtotime("February 1", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            if ($key == 'week' || $key == 'month') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 5.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 5.');
            }
        }

        // Test 6: If the most recent result set is December 12 and it is now January 2, everything should re-generate
        $forced_start = strtotime("December 12");
        $forced_time = strtotime("January 2 +1 year", $forced_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 6.');
        }
    }

    public function tearDown()
    {
        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        foreach ($this->leaderboards as $key => $value) {
            // delete_leader_board($value);
        }

        parent::tearDown();
    }
}
