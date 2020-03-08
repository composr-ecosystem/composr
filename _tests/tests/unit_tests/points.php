<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class points_test_set extends cms_test_case
{
    protected $give_points_record;
    protected $system_gift_transfer;
    protected $negative_system_gift_transfer;
    protected $charge_member;
    protected $negative_charge_member;
    protected $topic_id;
    protected $post_id;

    public function setUp()
    {
        parent::setUp();

        if (!addon_installed('points')) {
            $this->assertTrue(false, 'Test only works with the points addon.');
            return;
        }

        require_code('points');
        require_code('points2');

        require_code('cns_topics');
        require_code('cns_posts');
        require_code('cns_forums');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_posts_action3');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        $this->establish_admin_session();
    }

    public function testGivePointsAndReverse()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_give = 10;

        $initial_points_used = get_gift_points_used(2);
        $initial_points_to_give = get_gift_points_to_give(2);
        $initial_points = available_points(3);

        // Test user 2 giving 10 points to user 1
        $this->give_points_record = give_points($points_to_give, 3, 2, 'Points unit test', true, false);

        $current_points_used = get_gift_points_used(2);
        $current_points_to_give = get_gift_points_to_give(2);
        $current_points = available_points(3);

        // User 2 should have used +10 points and have -10 points to give. User 1 should have +10 points to spend.
        $used_points_correct = ($current_points_used == ($initial_points_used + $points_to_give));
        $to_give_points_correct = ($current_points_to_give == ($initial_points_to_give - $points_to_give));
        $points_correct = ($current_points == ($initial_points + $points_to_give));

        // Now test reversal
        reverse_point_gift_transaction($this->give_points_record);

        $reversed_points_used = get_gift_points_used(2);
        $reversed_points_to_give = get_gift_points_to_give(2);
        $reversed_points = available_points(3);

        $reversed_correct = (($reversed_points_used == $initial_points_used) && ($reversed_points_to_give == $initial_points_to_give) && ($reversed_points == $initial_points));

        $this->assertTrue($used_points_correct, 'Used points did not increase as expected.');
        $this->assertTrue($to_give_points_correct, 'Points to give did not decrease as expected.');
        $this->assertTrue($points_correct, 'Points to spend did not increase as expected. It was at ' . $current_points . ' when it should have been at ' . ($initial_points + $points_to_give));
        $this->assertTrue($reversed_correct, 'Points did not reverse as expected for reverse transaction.');
    }

    public function testForumPoints() {
        if (get_forum_type() != 'cns' || !addon_installed('points')) {
            return;
        }

        $initial_points = available_points(2);

        $this->topic_id = cns_make_topic(db_get_first_id(), 'Test');
        $this->post_id = cns_make_post($this->topic_id, 'Welcome', 'Welcome to the posts', 0, true, null, 0, null, null, null, 2, null, null, null, true, true, null, true, '', null, false, false, false);

        $current_points = available_points(2);

        $points_to_earn = intval(get_option('points_posting'));

        $change = ($current_points - $initial_points);

        $this->assertTrue($change == $points_to_earn, 'Points to spend did not increase for a forum post as expected (' . strval($points_to_earn) . '). The change was ' . strval($change));

        // Tear down
        if (!cns_delete_posts_topic($this->topic_id, [$this->post_id], 'Nothing')) {
            cns_delete_topic($this->topic_id);
        }
    }

    public function testSystemGiftTransfer()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_give = 25;

        $initial_points = available_points(3);

        $this->system_gift_transfer = system_gift_transfer('Points unit test', $points_to_give, 3, true);

        $current_points = available_points(3);

        $this->assertTrue((($current_points - $initial_points) == $points_to_give), 'Points to spend did not increase with system gift transfer as expected.');

        // Tear down
        reverse_point_gift_transaction($this->system_gift_transfer);
    }

    public function testNegativeSystemGiftTransfer()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_give = -25;

        $initial_points = available_points(3);

        $this->negative_system_gift_transfer = system_gift_transfer('Points unit test', $points_to_give, 3, true);

        $current_points = available_points(3);

        $this->assertTrue((($current_points - $initial_points) == $points_to_give), 'Points to spend did not decrease as expected with negative system gift transfer.');

        // Tear down
        reverse_point_gift_transaction($this->negative_system_gift_transfer);
    }

    public function testChargeMember()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_charge = 50;

        $initial_points = available_points(3);

        $this->charge_member = charge_member(3, $points_to_charge, 'Points unit test');

        $current_points = available_points(3);

        $this->assertTrue((($initial_points - $current_points) == $points_to_charge), 'Points to spend did not decrease as expected for charge member.');

        // Tear down
        reverse_charge_transaction($this->charge_member);
    }

    public function testNegativeChargeMember()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_charge = -50;

        $initial_points = available_points(3);

        $this->negative_charge_member = charge_member(3, $points_to_charge, 'Points unit test');

        $current_points = available_points(3);

        $this->assertTrue((($initial_points - $current_points) == $points_to_charge), 'Points did not increase as expected for negative charge member.');

        // Tear down
        reverse_charge_transaction($this->negative_charge_member);
    }

    public function tearDown()
    {
        if (!addon_installed('points')) {
            return;
        }

        parent::tearDown();
    }
}
