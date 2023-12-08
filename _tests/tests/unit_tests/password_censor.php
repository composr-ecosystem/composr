<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class password_censor_test_set extends cms_test_case
{
    public function testCensorWorks()
    {
        require_code('cns_topics');
        require_code('cns_posts');
        require_code('cns_forums');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_posts_action3');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        $test_password = 'abcxxxx';

        $_forum = get_option('ticket_forum_name');
        if (is_numeric($_forum)) {
            $forum_id = intval($_forum);
        } else {
            $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name($_forum);
        }

        $topic_id = cns_make_topic($forum_id, 'Test');
        $post_id = cns_make_post($topic_id, '', 'Password: ' . $test_password, 0, false, null, 0, null, null, null, null, null, null, null, false, true, null, true, '', 0, null, false, false, false);

        require_code('password_censor');
        password_censor(true, false, 0);

        $GLOBALS['FORUM_DB']->text_lookup_original_cache = array();
        $GLOBALS['FORUM_DB']->text_lookup_cache = array();

        $text = get_translated_text($GLOBALS['FORUM_DB']->query_select_value('f_posts', 'p_post', array('id' => $post_id)), $GLOBALS['FORUM_DB']);

        $this->assertTrue(strpos($text, $test_password) === false);
    }
}
