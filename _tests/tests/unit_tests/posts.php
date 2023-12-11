<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class posts_test_set extends cms_test_case
{
    public $post_id;
    public $topic_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_topics');
        require_code('cns_posts');
        require_code('cns_forums');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_posts_action3');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        $this->establish_admin_session();

        $this->topic_id = cns_make_topic(db_get_first_id(), 'Test');

        $this->post_id = cns_make_post($this->topic_id, 'welcome', 'welcome to the posts', 0, false, null, 0, null, null, null, null, null, null, null, true, true, null, true, '', 0, null, false, false, false);

        $this->assertTrue('welcome' == $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_title', array('id' => $this->post_id)));
    }

    public function testEditPosts()
    {
        $this->establish_admin_session();

        cns_edit_post($this->post_id, 1, 'take care', 'the post editing', 0, 0, null, true, false, 'Nothing');

        $this->assertTrue('take care' == $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_title', array('id' => $this->post_id)));
    }

    public function tearDown()
    {
        if (!cns_delete_posts_topic($this->topic_id, array($this->post_id), 'Nothing')) {
            cns_delete_topic($this->topic_id);
        }
        parent::tearDown();
    }
}
