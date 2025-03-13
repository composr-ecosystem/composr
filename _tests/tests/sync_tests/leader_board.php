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

// This test supports debug

/**
 * Composr test case class (unit testing).
 */
class leader_board_test_set extends cms_test_case
{
    protected $leaderboards;

    protected $points_a;
    protected $points_b;
    protected $old_voting_power;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            $this->assertTrue(false, 'Test only works with both the points and leader_board addons.');
            return;
        }

        $epoch = cms_mktime(0, 0, 0, 1, 1, 1971);

        $has_records = $GLOBALS['SITE_DB']->query_select_value('points_ledger', 'COUNT(*)', [], ' AND date_and_time<=' . strval($epoch));
        if ($has_records > 0) {
            $this->assertTrue(false, 'You need to empty the points_ledger of all records before and including the date_and_time of ' . strval($epoch));
            return;
        }

        $this->leaderboards = [];

        require_code('temporal');
        require_code('leader_board');
        require_code('leader_board2');

        // Award some points in case there are none (we add a GUID in the meta just to stop the infinite loop check from triggering)
        require_code('points2');
        require_code('global4');
        $members = $GLOBALS['FORUM_DRIVER']->get_next_members(null, 2);
        $this->points_a = points_credit_member($GLOBALS['FORUM_DRIVER']->mrow_member_id($members[0]), 'leader-board test', 123, 0, null, 0, 'unit_test', generate_guid(), 'leader_board', $epoch);
        $this->points_b = points_credit_member($GLOBALS['FORUM_DRIVER']->mrow_member_id($members[1]), 'leader-board test', 456, 0, null, 0, 'unit_test', generate_guid(), 'leader_board', $epoch);
        points_flush_runtime_cache();

        // Turn on voting power for this test
        if (get_forum_type() == 'cns') {
            $this->old_voting_power = get_option('enable_poll_point_weighting');
            set_option('enable_poll_point_weighting', '1');
        }
    }

    public function testLeaderBoardRanks()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardRanks')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Set up leader-board
        $epoch = cms_mktime(0, 0, 0, 1, 8, 1971);
        $pre_epoch = cms_mktime(0, 0, 0, 1, 1, 1971);
        $this->leaderboards['rank'] = add_leader_board('Test rank', 'holders', 100, 'week', 1, 1, [], ((get_forum_type() == 'cns') ? 1 : 0), $epoch);
        $this->assertTrue(is_integer($this->leaderboards['rank']), 'Failed to create rank leader-board');

        // Ensure leader-board ranks are in the correct order according to points
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['rank']], '', 1);
        $process = calculate_leader_board($rows[0], $epoch, $pre_epoch);
        if ($process === null) {
            $this->assertTrue(false, 'rank: The leader-board did not generate a new result set when it should have in testLeaderBoardRanks().');
        } else {
            $results = get_leader_board($this->leaderboards['rank'], $process);
            if (empty($results)) {
                $this->assertTrue(false, 'rank: We expected at least one member in the result set, but we got none, in testLeaderBoardRanks().');
                if ($this->debug) {
                    $this->dump($this->leaderboards['rank'], 'id');
                    $this->dump($process, 'process');
                }
            } else {
                // Sort the results according to specified rank
                sort_maps_by($results, 'lb_rank');

                // As we go down the rank, the number of points should decrease or stay the same. Fail if this does not happen. Also checks to ensure we have voting power results.
                $passed = true;
                $voting_power_passed = true;
                $prev_points = null;
                foreach ($results as $result) {
                    if ($prev_points === null) {
                        $prev_points = $result['lb_points'];
                    } elseif ($result['lb_points'] > $prev_points) {
                        $passed = false;
                    }
                    $prev_points = $result['lb_points'];

                    if (($result['lb_voting_power'] === null) && (get_forum_type() == 'cns')) {
                        $voting_power_passed = false;
                    }
                }
                $this->assertTrue($passed, 'rank: The leader-board assigned ranks in the incorrect order; we expected ranks to be in order from most points to least, in testLeaderBoardRanks().');
                $this->assertTrue($voting_power_passed, 'rank: Expected the leader-board to also include voting power, but it did not, in testLeaderBoardRanks().');
            }
        }
    }

    public function testLeaderBoardFrequencyRolling()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardFrequencyRolling')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Test 1: Set up leader-boards
        $epoch = cms_gmmktime(0, 0, 0, 1, 2, 1971); // Saturday
        $this->leaderboards['week_r'] = add_leader_board('Test week_r', 'holders', 10, 'week', 1, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['week_r']), 'Failed to create week_r leader-board');
        $this->leaderboards['month_r'] = add_leader_board('Test month_r', 'holders', 10, 'month', 1, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['month_r']), 'Failed to create month_r leader-board');
        $this->leaderboards['year_r'] = add_leader_board('Test year_r', 'holders', 10, 'year', 1, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['year_r']), 'Failed to create year_r leader-board');

        // Test 2A: Test to see that none of the leader-boards regenerate if we pretend the current time is the same as the most recent generation
        $forced_period_start = $epoch;
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 2A.');
        }

        // Test 2B: ...but make sure they all *do* generate if no results have been created yet (and we are not pretending one has)
        $forced_period_start = null;
        $forced_time = $epoch;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 2B.');
        }

        // Test 3: Make sure the week leader-board does not regenerate on a Sunday / Monday because it's rolling (it should generate on Saturdays)
        $forced_period_start = $epoch;
        $forced_time = cms_gmmktime(0, 0, 0, 1, 4, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 3.');
        }

        // Test 4: Test 6 days after a Saturday generation. Nothing should generate (because leader-boards were created Saturday, not Sunday/Monday).
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 9, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 15, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 4.');
        }

        // Test 5: Test a one-week span between result sets in the middle of the month. Only the week leader-board should re-generate.
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 15, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 22, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 5.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 5.');
            }
        }

        // Test 6: Test the result set was generated on a Friday and it is now Saturday. The week leader-board should regenerate to get back on track with the board's creation time.
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 22, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 23, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 6.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 6.');
            }
        }

        // Test 7: Test one month between generations. The week and month leader-boards should re-generate
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 30, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 2, 24, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if (($key == 'week_r') || ($key == 'month_r')) {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 7.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 7.');
            }
        }

        // Test 8: Test March 31 (Wednesday) -> April 1 (Thursday). Nothing should generate; the month leader-board should be generated on the 2nd.
        $forced_period_start = cms_gmmktime(0, 0, 0, 3, 31, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 4, 1, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 8.');
        }

        // Test 9: Test May 1 -> May 2. Only the month leader-board should generate since the leader-board was created on the 2nd.
        $forced_period_start = cms_gmmktime(0, 0, 0, 5, 1, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 5, 2, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'month_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 9.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 9.');
            }
        }

        // Test 10: Test June -> January 2 span between generations. All leader-boards should re-generate.
        $forced_period_start = cms_gmmktime(0, 0, 0, 6, 1, 1971);
        $forced_time = cms_gmmktime(2, 0, 0, 1, 2, 1972); // Have to add a couple hours in case of DST
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 10.');
        }
    }

    public function testLeaderBoardFrequencyNonRolling()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardFrequencyNonRolling')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Test 1: Set up leader-boards
        $epoch = cms_gmmktime(0, 0, 0, 1, 2, 1971); // Saturday
        $this->leaderboards['week'] = add_leader_board('Test week', 'earners', 10, 'week', 0, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['week']), 'Failed to create week_r leader-board');
        $this->leaderboards['month'] = add_leader_board('Test month', 'earners', 10, 'month', 0, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['month']), 'Failed to create month_r leader-board');
        $this->leaderboards['year'] = add_leader_board('Test year', 'earners', 10, 'year', 0, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['year']), 'Failed to create year_r leader-board');

        // Test 2A: First, test to see that none of the leader-boards regenerate if the current time is the same as when they were created
        $forced_period_start = $epoch;
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2A.');
        }

        // Test 2B: Same test but with Sunday as both the current time and most recent results time
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 3, 1971);
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2B.');
        }

        // Test 2C: Same test but with Monday as both the current time and most recent results time
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 4, 1971);
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2C.');
        }

        // Test 2D: ...but make sure they all *do* generate if no results have been created yet (and we are not pretending one has)
        $forced_period_start = null;
        $forced_time = $epoch;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 2D.');
        }

        // Test 3: If the result sets started on Monday and it is currently Saturday, make sure nothing re-generates (leader-boards created Friday, but this is non-rolling)
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 11, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 16, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 3.');
        }

        // Test 4: If the recent result set was Saturday and it is now Monday, week should re-generate to correct itself so it's back on the start of the week again.
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 16, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 18, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 4.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 4.');
            }
        }

        // Test 5: If the recent result set was first Sunday on a month and it is now subsequent Monday (over a week later), week should re-generate but nothing else.
        $forced_period_start = cms_gmmktime(0, 0, 0, 2, 7, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 2, 15, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 5.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 5.');
            }
        }

        // Test 6: If the recent result set was February 15 and it is now March 1, week and month should re-generate.
        $forced_period_start = cms_gmmktime(0, 0, 0, 2, 15, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 3, 1, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if (($key == 'week') || ($key == 'month')) {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 6.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 6.');
            }
        }

        // Test 7: If the recent result set was March 31 (Wednesday) and it is now April 1 (Thursday), only month should regenerate.
        $forced_period_start = cms_gmmktime(0, 0, 0, 3, 31, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 4, 1, 1971);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'month') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 7.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 7.');
            }
        }

        // Test 8: If the most recent result set is December 26 and it is now January 2, everything should re-generate
        $forced_period_start = cms_gmmktime(0, 0, 0, 12, 26, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 1, 2, 1972);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 8.');
        }
    }

    public function testLeaderBoardOneMember()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardOneMember')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Set up leader-boards
        $epoch = cms_gmmktime(0, 0, 0, 1, 1, 1971); // Friday
        $this->leaderboards['one_member'] = add_leader_board('Test one_member', 'holders', 1, 'month', 0, 0, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['one_member']), 'Failed to create one_member leader-board');

        // The generated leader-board should only contain one member in the results (also test for a non voting power leader-board)
        $forced_period_start = cms_mktime(0, 0, 0, 1, 1, 1971);
        $forced_time = cms_mktime(0, 0, 0, 2, 1, 1971);
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['one_member']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'one_member: The leader-board did not generate a new result set when it should have in testLeaderBoardOneMember().');
            if ($this->debug) {
                $this->dump($forced_period_start, 'forced_period_start');
                $this->dump($forced_time, 'forced_time');
                $this->dump($rows[0], 'row');
            }
        } else {
            $results = get_leader_board($this->leaderboards['one_member'], $process);
            if (empty($results)) {
                $this->assertTrue(false, 'one_member: The leader-board did not return a result set when we expected one in testLeaderBoardOneMember().');
                if ($this->debug) {
                    $this->dump($forced_period_start, 'forced_period_start');
                    $this->dump($forced_time, 'forced_time');
                    $this->dump($rows[0], 'row');
                }
                return;
            }
            $count = count($results);
            $this->assertTrue(($count == 1), 'one_member: The leader-board returned ' . strval($count) . ' members when we expected 1. testLeaderBoardOneMember().');
            $this->assertTrue((($count < 1) || ($results[0]['lb_voting_power'] === null)), 'one_member: Expected the leader-board not to calculate / return voting power, but it did. testLeaderBoardOneMember().');
        }
    }

    public function testLeaderBoardIncludeStaff()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardIncludeStaff')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Grab staff usergroups (for accuracy, we want to filter to a staff usergroup so we do not have a case where no staff ranked)
        $groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
        if (empty($groups)) {
            $this->assertTrue(false, 'Failed to get super admin groups in testLeaderBoardIncludeStaff().');
        }

        // Set up leader-boards
        $epoch = cms_gmmktime(0, 0, 0, 1, 1, 1971); // Friday
        $this->leaderboards['include_staff'] = add_leader_board('Test include_staff', 'holders', 10, 'month', 0, 1, [$groups[0]], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['include_staff']), 'Failed to create include_staff leader-board');
        $this->leaderboards['no_staff'] = add_leader_board('Test no_staff', 'holders', 10, 'month', 0, 0, [$groups[0]], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['no_staff']), 'Failed to create no_staff leader-board');

        // Set up time
        $forced_period_start = cms_mktime(0, 0, 0, 1, 1, 1971);
        $forced_time = cms_mktime(0, 0, 0, 2, 1, 1971);

        // Test 1: First, test for staff
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['include_staff']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'include_staff: The leader-board did not generate a new result set when it should have in testLeaderBoardIncludeStaff() test 1.');
        } else {
            $results = get_leader_board($this->leaderboards['include_staff'], $process);
            $this->assertTrue(!empty($results), 'include_staff: The leader-board returned an empty result set when we expected at least one member in testLeaderBoardIncludeStaff() test 1.');
        }

        // Test 2: test for no staff
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['no_staff']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'no_staff: The leader-board did not generate a new result set when it should have in testLeaderBoardIncludeStaff() test 2.');
        } else {
            $results = get_leader_board($this->leaderboards['no_staff'], $process);
            $this->assertTrue(empty($results), 'no_staff: The leader-board returned a result set with members when we expected no members in testLeaderBoardIncludeStaff() test 2.');
        }
    }

    public function testLeaderBoardUsergroup()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardUsergroup')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        // Test using the primary usergroup of the first member; we know there will always be at least one member in the leader-board by doing so
        $current_id = null;
        $groups = [];
        do {
            $members = $GLOBALS['FORUM_DRIVER']->get_next_members($current_id, 1);
            if (empty($members)) {
                $this->assertTrue(false, 'testLeaderBoardUsergroup(): Needs 2 members with unique primary groups to work.');
                return;
            }
            $group = $GLOBALS['FORUM_DRIVER']->mrow_primary_group($members[0]);
            if (!in_array($group, $groups)) {
                $groups[] = $group;
            }
            $current_id = $GLOBALS['FORUM_DRIVER']->mrow_member_id($members[0]);
        } while (count($groups) < 2);

        // Set up leader-boards
        $epoch = cms_gmmktime(0, 0, 0, 1, 1, 1971); // Friday
        $this->leaderboards['single_usergroup'] = add_leader_board('Test single_usergroup', 'holders', 100, 'month', 0, 1, [$groups[0]], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['single_usergroup']), 'Failed to create single_usergroup leader-board');
        $this->leaderboards['multiple_usergroups'] = add_leader_board('Test multiple_usergroups', 'holders', 100, 'month', 0, 1, $groups, 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['multiple_usergroups']), 'Failed to create multiple_usergroups leader-board');

        // Set up time
        $forced_period_start = cms_gmmktime(0, 0, 0, 1, 1, 1971);
        $forced_time = cms_gmmktime(0, 0, 0, 2, 1, 1971);

        // Test 1: Test to make sure all members in the result set are in the tested usergroup for single_usergroup
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['single_usergroup']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'single_usergroup: The leader-board did not generate a new result set when it should have in testLeaderBoardUsergroup() test 1.');
        } else {
            $results = get_leader_board($this->leaderboards['single_usergroup'], $process);
            $passed = true;
            if (empty($results)) {
                $this->assertTrue(false, 'single_usergroup: We expected at least one member in the result set, but we got none, in testLeaderBoardUsergroup() test 1.');
            } else {
                foreach ($results as $result) {
                    $mgroups = $GLOBALS['FORUM_DRIVER']->get_members_groups($result['lb_member']);
                    if (!in_array($groups[0], $mgroups)) {
                        $passed = false;
                    }
                }
                $this->assertTrue($passed, 'single_usergroup: We expected all members of the result set to be in usergroup ID ' . strval($group) . ', but that was not the case, in testLeaderBoardUsergroup() test 1.');
            }
        }

        // Test 2: Test to make sure all members in the result set are in one of the tested usergroups for multiple_usergroups
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['multiple_usergroups']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'multiple_usergroups: The leader-board did not generate a new result set when it should have in testLeaderBoardUsergroup() test 2.');
        } else {
            $results = get_leader_board($this->leaderboards['multiple_usergroups'], $process);
            $passed = true;
            if (empty($results)) {
                $this->assertTrue(false, 'multiple_usergroups: We expected at least one member in the result set, but we got none, in testLeaderBoardUsergroup() test 2.');
            } else {
                foreach ($results as $result) {
                    $mgroups = $GLOBALS['FORUM_DRIVER']->get_members_groups($result['lb_member']);
                    if (count(array_intersect($groups, $mgroups)) == 0) {
                        $passed = false;
                    }
                }
                $this->assertTrue($passed, 'multiple_usergroups: We expected all members of the result set to be in one of the 2 tested usergroups, but that was not the case, in testLeaderBoardUsergroup() test 2.');
            }
        }
    }

    public function testLeaderBoardHoldersEarners()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (($this->only !== null) && ($this->only !== 'testLeaderBoardHoldersEarners')) {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        require_code('points');
        require_code('points2');

        // Set up leader-boards (make these rolling for easier calculation)
        $epoch = cms_gmmktime(0, 0, 2, 1, 1, 1971); // Friday
        $this->leaderboards['holders'] = add_leader_board('Test holders', 'holders', 10, 'week', 1, 1, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['holders']), 'Failed to create holders leader-board');

        $this->leaderboards['earners'] = add_leader_board('Test earners', 'earners', 10, 'week', 1, 1, [], 0, $epoch);
        $this->assertTrue(is_integer($this->leaderboards['earners']), 'Failed to create earners leader-board');

        // Set up time
        $forced_period_start = cms_gmmktime(0, 0, 3, 1, 1, 1971);
        $forced_time = cms_gmmktime(0, 0, 3, 1, 8, 1971);

        // Determine our test member
        $members = $GLOBALS['FORUM_DRIVER']->get_next_members(null, 1);
        $member = $GLOBALS['FORUM_DRIVER']->mrow_member_id($members[0]);

        // Process a dummy point transaction
        $points_to_send = 1000;
        $transfer = points_credit_member($member, 'unit test', $points_to_send, 0, null, 0, '', '', '', $forced_period_start);
        points_flush_runtime_cache();

        // Determine our points for the member
        $past_points = points_rank($member, ($forced_period_start - 1), false);
        points_flush_runtime_cache();

        // Test 1: Holders leader-board result for this member should equal $past_points + $points_to_send
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['holders']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'holders: The leader-board did not generate a new result set when it should have in testLeaderBoardHoldersEarners() test 1.');
        } else {
            $results = get_leader_board($this->leaderboards['holders'], $process);
            if (empty($results)) {
                $this->assertTrue(false, 'holders: We expected at least one member in the result set, but we got none, in testLeaderBoardHoldersEarners() test 1.');
            } else {
                $found = false;
                foreach ($results as $result) {
                    if ($result['lb_member'] == $member) {
                        $correct_points = $past_points + $points_to_send;
                        $this->assertTrue(($result['lb_points'] == $correct_points), 'holders: Expected total points to be ' . integer_format($correct_points) . ', but it was instead ' . integer_format($result['lb_points']) . ', in testLeaderBoardHoldersEarners() test 1.');
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'holders: Could not find the test member ' . strval($member) . ' in the leader-board result set, in testLeaderBoardHoldersEarners() test 1.');
            }
        }

        // Test 2: Earners leader-board result for this member should equal $points_to_send
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['earners']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'earners: The leader-board did not generate a new result set when it should have in testLeaderBoardHoldersEarners() test 2.');
        } else {
            $results = get_leader_board($this->leaderboards['earners'], $process);
            if (empty($results)) {
                $this->assertTrue(false, 'earners: We expected at least one member in the result set, but we got none, in testLeaderBoardHoldersEarners() test 2.');
            } else {
                $found = false;
                foreach ($results as $result) {
                    if ($result['lb_member'] == $member) {
                        $correct_points = $points_to_send;
                        $pass = ($result['lb_points'] == $correct_points);
                        $this->assertTrue($pass, 'earners: Expected points earned by #' . strval($member) . ' to be ' . integer_format($correct_points) . ', but it was instead ' . integer_format($result['lb_points']) . ', in testLeaderBoardHoldersEarners() test 2.');
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'earners: Could not find the test member ' . strval($member) . ' in the leader-board result set, in testLeaderBoardHoldersEarners() test 2.');
            }
        }

        if (!$this->debug) {
            // Reverse the dummy transaction
            $data = points_transaction_reverse($transfer, false);

            // Do not keep unit tests in the ledger
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $transfer], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $data[0]], '', 1);
            points_flush_runtime_cache();
        }
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        if (!$this->debug) {
            foreach ($this->leaderboards as $key => $value) {
                delete_leader_board($value);
            }

            // Clean up point transactions
            require_code('points');
            require_code('points2');
            $a = points_transaction_reverse($this->points_a);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_a], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $a[0]], '', 1);
            $b = points_transaction_reverse($this->points_b);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_b], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $b[0]], '', 1);

            points_flush_runtime_cache();
        }

        // Reset voting power for this test
        if (get_forum_type() == 'cns') {
            set_option('enable_poll_point_weighting', $this->old_voting_power);
        }

        parent::tearDown();
    }
}
