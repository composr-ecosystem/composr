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
class forum_polls_test_set extends cms_test_case
{
    protected $topic_id;

    protected $post_id;

    protected $poll_id;

    public function setUp()
    {
        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        parent::setUp();

        require_code('cns_polls_action');
        require_code('cns_polls_action2');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');
        require_code('cns_topics');
        require_code('cns_forums');

        $this->establish_admin_session();

        $this->topic_id = cns_make_topic(db_get_first_id(), 'Test');

        $this->post_id = cns_make_post($this->topic_id, 'Who is this?', 'I have no clue', 0, true, 1);

        $this->poll_id = cns_make_poll($this->topic_id, 'Who are you ?', 0, 0, 2, 4, 0, ['a', 'b', 'c'], 0, 0, 0, 0, true);

        $this->assertTrue('Who are you ?' == $GLOBALS['FORUM_DB']->query_select_value('f_polls', 'po_question', ['id' => $this->poll_id]));

        cns_edit_poll($this->poll_id, 'Who am I?', 1, 1, 1, 4, 1, ['1', '2', '3'], 0, 0, 0, 0, 'nothing');

        $this->assertTrue('Who am I?' == $GLOBALS['FORUM_DB']->query_select_value('f_polls', 'po_question', ['id' => $this->poll_id]));
    }

    public function testVotingPowerEquation()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        if (!addon_installed('points')) {
            $this->assertTrue(false, 'Voting Power tests only work with the points addon installed');
            return;
        }

        $before_options = [
            get_option('topic_polls_weighting_ceiling'),
            get_option('topic_polls_weighting_offset'),
            get_option('topic_polls_weighting_multiplier'),
            get_option('topic_polls_weighting_base')
        ];

        set_option('topic_polls_weighting_ceiling', '100');
        set_option('topic_polls_weighting_offset', '1');
        set_option('topic_polls_weighting_multiplier', '2');
        set_option('topic_polls_weighting_base', '2');

        $with_1000_points = float_to_raw_string(cns_points_to_voting_power(1000), 2);
        $this->assertTrue($with_1000_points == '64.25', '$with_1000_points: Expected 64.25, got ' . $with_1000_points);

        $with_1_point = float_to_raw_string(cns_points_to_voting_power(1), 2);
        $this->assertTrue($with_1_point == '3.00', '$with_1_point: Expected 3.00, got ' . $with_1_point);

        $with_0_points = float_to_raw_string(cns_points_to_voting_power(0), 2);
        $this->assertTrue($with_0_points == '1.00', '$with_0_points: Expected 1.00, got ' . $with_0_points);

        $with_negative_1_points = float_to_raw_string(cns_points_to_voting_power(-1), 2);
        $this->assertTrue($with_negative_1_points == '0.00', '$with_negative_1_points: Expected 0.00, got ' . $with_negative_1_points);

        $with_maxint_points_ceiling_100 = float_to_raw_string(cns_points_to_voting_power(2147483647), 2);
        $this->assertTrue($with_maxint_points_ceiling_100 == '100.00', '$with_maxint_points_ceiling_100: Expected 100.00, got ' . $with_maxint_points_ceiling_100);

        set_option('topic_polls_weighting_ceiling', '');
        $with_maxint_points_noceiling = float_to_raw_string(cns_points_to_voting_power(2147483647), 2);
        $this->assertTrue($with_maxint_points_noceiling == '92682.90', '$with_maxint_points_noceiling: Expected 92682.90, got ' . $with_maxint_points_noceiling);

        set_option('topic_polls_weighting_ceiling', $before_options[0]);
        set_option('topic_polls_weighting_offset', $before_options[1]);
        set_option('topic_polls_weighting_multiplier', $before_options[2]);
        set_option('topic_polls_weighting_base', $before_options[3]);
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_poll($this->poll_id, 'Simple');
        cns_delete_topic($this->topic_id);

        parent::tearDown();
    }
}
