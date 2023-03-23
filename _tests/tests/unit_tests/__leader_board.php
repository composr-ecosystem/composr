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

/**
 * Composr test case class (unit testing).
 */
class __leader_board_test_set extends cms_test_case
{
    protected $leaderboards;

    protected $points_a;
    protected $points_b;
    protected $old_voting_power;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            $this->assertTrue(false, 'Test only works with both the points and leader_board addons.');
            return;
        }

        $this->leaderboards = [];

        require_code('leader_board');
        require_code('leader_board2');

        // Award some points in case there are none
        require_code('points2');
        $members = $GLOBALS['FORUM_DRIVER']->get_next_members(null, 2);
        $this->points_a = points_credit_member($GLOBALS['FORUM_DRIVER']->mrow_member_id($members[0]), 'leader-board test', 1000000, 0, null, 0, 'unit_test', '', 'leader_board', (time() - 60));
        $this->points_b = points_credit_member($GLOBALS['FORUM_DRIVER']->mrow_member_id($members[1]), 'leader-board test', 100000, 0, null, 0, 'unit_test', '', 'leader_board', (time() - 60));
        points_flush_runtime_cache();

        // Turn on voting power for this test
        if (get_forum_type() == 'cns') {
            $this->old_voting_power = get_option('enable_poll_point_weighting');
            set_option('enable_poll_point_weighting', '1');
        }
    }

    public function testLeaderBoardRanks() {
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
        $this->leaderboards['rank'] = add_leader_board("Test rank", 'holders', 100, 'week', 1, 1, [], ((get_forum_type() == 'cns') ? 1 : 0));
        $this->assertTrue(is_integer($this->leaderboards['rank']), 'Failed to create rank leader-board');

        // Set up time
        $forced_period_start = strtotime("-1 week");

        // Ensure leader-board ranks are in the correct order according to points
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['rank']], '', 1);
        $process = calculate_leader_board($rows[0], time(), $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'rank: The leader-board did not generate a new result set when it should have in testLeaderBoardRanks().');
        } else {
            $results = get_leader_board($this->leaderboards['rank'], $process);
            $passed = true;
            if (empty($results)) {
                $this->assertTrue(false, 'rank: We expected at least one member in the result set, but we got none, in testLeaderBoardRanks().');
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
        $this->leaderboards['week_r'] = add_leader_board("Test week_r", 'holders', 10, 'week', 1, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['week_r']), 'Failed to create week_r leader-board');
        $this->leaderboards['month_r'] = add_leader_board("Test month_r", 'holders', 10, 'month', 1, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['month_r']), 'Failed to create month_r leader-board');
        $this->leaderboards['year_r'] = add_leader_board("Test year_r", 'holders', 10, 'year', 1, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['year_r']), 'Failed to create year_r leader-board');

        // Test 2: First, test to see that none of the leader-boards regenerate if the current time is the same as the most recent result set
        $forced_period_start = strtotime("January 1");
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 2.');
        }

        // Test 3: Next, test 6 days after the most recent result set. Again, nothing should re-generate.
        $forced_period_start = strtotime("January 1");
        $forced_time = strtotime("+6 days", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 3.');
        }

        // Test 4: Test one week after the most recent result set. Only the week leader-board should re-generate.
        $forced_period_start = strtotime("January 8");
        $forced_time = strtotime("+1 week", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 4.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 4.');
            }
        }

        // Test 5: Test one month after the most recent result set. The week and month leader-boards should re-generate
        $forced_period_start = strtotime("January 15");
        $forced_time = strtotime("+1 month", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week_r' || $key == 'month_r') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 5.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyRolling() test 5.');
            }
        }

        // Test 6: Finally, test one year after the result set. All leader-boards should re-generate.
        $forced_period_start = strtotime("February 15");
        $forced_time = strtotime("+1 year", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyRolling() test 6.');
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
        $this->leaderboards['week'] = add_leader_board("Test week", 'earners', 10, 'week', 0, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['week']), 'Failed to create week_r leader-board');
        $this->leaderboards['month'] = add_leader_board("Test month", 'earners', 10, 'month', 0, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['month']), 'Failed to create month_r leader-board');
        $this->leaderboards['year'] = add_leader_board("Test year", 'earners', 10, 'year', 0, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['year']), 'Failed to create year_r leader-board');

        // Test 2A: First, test to see that none of the leader-boards regenerate if the current time is the same as the most recent result set (Sunday)
        $forced_period_start = strtotime("January 1");
        $forced_period_start = strtotime("sunday", $forced_period_start);
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2A.');
        }

        // Test 2B: Same test but with Monday to test both cases of start of week for week leader-boards.
        $forced_period_start = strtotime("January 1");
        $forced_period_start = strtotime("monday", $forced_period_start);
        $forced_time = $forced_period_start;
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 2B.');
        }

        // Test 3: Next, if the result sets started on Tuesday and it is currently Saturday, make sure nothing re-generates again.
        $forced_period_start = strtotime("May 15");
        $forced_period_start = strtotime("tuesday", $forced_period_start);
        $forced_time = strtotime("saturday", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 3.');
        }

        // Test 4: If the recent result set was friday and it is now monday, still nothing should generate.
        $forced_period_start = strtotime("May 15");
        $forced_period_start = strtotime("friday", $forced_period_start);
        $forced_time = strtotime("monday", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 4.');
        }

        // Test 5: If the recent result set was first Sunday on a month and it is now subsequent Monday (over a week later), week should re-generate but nothing else.
        $forced_period_start = strtotime("october 17 2021");
        $forced_time = strtotime("monday +1 week", $forced_period_start);
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

        // Test 6: If the recent result set was January 15 and it is now February 1, week and month should re-generate.
        $forced_period_start = strtotime("January 15");
        $forced_time = strtotime("February 1", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            if ($key == 'week' || $key == 'month') {
                $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 6.');
            } else {
                $this->assertTrue($process === null, $key . ': The leader-board generated a new result set when it should not have in testLeaderBoardFrequencyNonRolling() test 6.');
            }
        }

        // Test 7: If the most recent result set is December 12 and it is now January 2, everything should re-generate
        $forced_period_start = strtotime("December 12");
        $forced_time = strtotime("January 2 +1 year", $forced_period_start);
        foreach ($this->leaderboards as $key => $value) {
            $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $value], '', 1);
            if (!isset($rows[0]) || empty($rows[0])) {
                $this->assertTrue(false, $key . ': The leader-board was not found in the database (id ' . strval($value) . '). This is unexpected.');
            }
            $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);

            $this->assertTrue($process !== null, $key . ': The leader-board did not generate a new result set when it should have in testLeaderBoardFrequencyNonRolling() test 7.');
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
        $this->leaderboards['one_member'] = add_leader_board("Test one_member", 'holders', 1, 'month', 0, 0, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['one_member']), 'Failed to create one_member leader-board');

        // The generated leader-board should only contain one member in the results (also test for a non voting power leader-board)
        $forced_period_start = strtotime("first day of this month");
        $forced_time = strtotime("+1 month", $forced_period_start);
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['one_member']], '', 1);
        $process = calculate_leader_board($rows[0], $forced_time, $forced_period_start);
        if ($process === null) {
            $this->assertTrue(false, 'one_member: The leader-board did not generate a new result set when it should have in testLeaderBoardOneMember().');
        } else {
            $results = get_leader_board($this->leaderboards['one_member'], $process);
            if (empty($results)) {
                $this->assertTrue(false, 'one_member: The leader-board did not return a result set when we expected one in testLeaderBoardOneMember().');
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
        $this->leaderboards['include_staff'] = add_leader_board("Test include_staff", 'holders', 10, 'month', 0, 1, [$groups[0]], 0);
        $this->assertTrue(is_integer($this->leaderboards['include_staff']), 'Failed to create include_staff leader-board');
        $this->leaderboards['no_staff'] = add_leader_board("Test no_staff", 'holders', 10, 'month', 0, 0, [$groups[0]], 0);
        $this->assertTrue(is_integer($this->leaderboards['no_staff']), 'Failed to create no_staff leader-board');

        // Set up time
        $forced_period_start = strtotime("first day of this month");
        $forced_time = strtotime("+1 month", $forced_period_start);

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
        $this->leaderboards['single_usergroup'] = add_leader_board("Test single_usergroup", 'holders', 100, 'month', 0, 1, [$groups[0]], 0);
        $this->assertTrue(is_integer($this->leaderboards['single_usergroup']), 'Failed to create single_usergroup leader-board');
        $this->leaderboards['multiple_usergroups'] = add_leader_board("Test multiple_usergroups", 'holders', 100, 'month', 0, 1, $groups, 0);
        $this->assertTrue(is_integer($this->leaderboards['multiple_usergroups']), 'Failed to create multiple_usergroups leader-board');

        // Set up time
        $forced_period_start = strtotime("first day of this month");
        $forced_time = strtotime("+1 month", $forced_period_start);

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
        $this->leaderboards['holders'] = add_leader_board("Test holders", 'holders', 10, 'week', 1, 1, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['holders']), 'Failed to create holders leader-board');

        $this->leaderboards['earners'] = add_leader_board("Test earners", 'earners', 10, 'week', 1, 1, [], 0);
        $this->assertTrue(is_integer($this->leaderboards['earners']), 'Failed to create earners leader-board');

        // Set up time
        $forced_period_start = strtotime("-1 week");

        // Determine our test member
        $members = $GLOBALS['FORUM_DRIVER']->get_next_members(null, 1);
        $member = $GLOBALS['FORUM_DRIVER']->mrow_member_id($members[0]);

        // Determine our current points for the member
        points_flush_runtime_cache();
        $current_points = points_lifetime($member, null, false);
        $past_points = points_lifetime($member, $forced_period_start, false);
        $earned_points = ($current_points - $past_points);

        // Process a dummy point transaction; amount should be absurdly high to ensure member is at the top of the results in our test
        $points_to_send = 100000000;
        $transfer = points_credit_member($member, 'unit test', $points_to_send, 0, null, 0, '', '', '', (time() - 60));

        points_flush_runtime_cache();

        // Test 1: Holders leader-board result for this member should equal $current_points + $points_to_send
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['holders']], '', 1);
        $process = calculate_leader_board($rows[0], time(), $forced_period_start);
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
                        $correct_points = $current_points + $points_to_send;
                        $this->assertTrue(($result['lb_points'] == $correct_points), 'holders: Expected total points to be ' . integer_format($correct_points) . ', but it was instead ' . integer_format($result['lb_points']) . ', in testLeaderBoardHoldersEarners() test 1.');
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'holders: Could not find the test member ' . strval($member) . ' in the leader-board result set, in testLeaderBoardHoldersEarners() test 1.');
            }
        }

        // Test 2: Earners leader-board result for this member should equal $points_to_send + $earned_points
        $rows = $GLOBALS['SITE_DB']->query_select('leader_boards', ['*'], ['id' => $this->leaderboards['earners']], '', 1);
        $process = calculate_leader_board($rows[0], time(), $forced_period_start);
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
                        $correct_points = $earned_points + $points_to_send;
                        $pass = ($result['lb_points'] == $correct_points);
                        $this->assertTrue($pass, 'earners: Expected points earned by #' . strval($member) . ' to be ' . integer_format($correct_points) . ', but it was instead ' . integer_format($result['lb_points']) . ', in testLeaderBoardHoldersEarners() test 2.');
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 'earners: Could not find the test member ' . strval($member) . ' in the leader-board result set, in testLeaderBoardHoldersEarners() test 2.');
            }
        }

        // Reverse the dummy transaction
        $data = points_transaction_reverse($transfer, false);

        // Do not keep unit tests in the ledger
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $transfer], '', 1);
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $data[0]], '', 1);
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (!addon_installed('points') || !addon_installed('leader_board')) {
            return;
        }

        foreach ($this->leaderboards as $key => $value) {
            delete_leader_board($value);
        }

        // Clean up point transactions
        require_code('points2');
        $a = points_transaction_reverse($this->points_a);
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_a], '', 1);
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $a[0]], '', 1);
        $b = points_transaction_reverse($this->points_b);
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_b], '', 1);
        $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $b[0]], '', 1);

        // Reset voting power for this test
        if (get_forum_type() == 'cns') {
            set_option('enable_poll_point_weighting', $this->old_voting_power);
        }

        parent::tearDown();
    }
}
