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
class comment_test_set extends cms_test_case
{
    public $event_id;

    public function setUp()
    {
        parent::setUp();

        require_code('calendar2');
        require_code('feedback');
        require_code('cns_posts_action');
        require_code('cns_forum_driver_helper');
        require_lang('lang');

        $this->event_id = add_calendar_event(8, 'none', null, 0, 'test_event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, null, 1, null, 1, 1, 1, 1, '', null, 0, null, null, null);
        if ('test_event' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'e_title', array('id' => $this->event_id)))) {
            $map = array(
                'p_title' => 'test_comment1',
                'p_ip_address' => '127.0.0.1',
                'p_time' => time(),
                'p_poster' => 0,
                'p_poster_name_if_guest' => '',
                'p_validated' => 1,
                'p_topic_id' => 4,
                'p_is_emphasised' => 0,
                'p_cache_forum_id' => 4,
                'p_last_edit_time' => null,
                'p_last_edit_by' => null,
                'p_intended_solely_for' => null,
                'p_skip_sig' => 0,
                'p_parent_id' => null
            );
            $map += insert_lang_comcode('p_post', 'test_comment_desc_1', 4, $GLOBALS['FORUM_DB']);
            $this->post_id = $GLOBALS['FORUM_DB']->query_insert('f_posts', $map, true);
        }
        global $TABLE_LANG_FIELDS_CACHE;
        $lang_fields = $TABLE_LANG_FIELDS_CACHE['f_posts'];
        $rows = $GLOBALS['FORUM_DB']->query('SELECT p_title FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts p WHERE ' . $GLOBALS['FORUM_DB']->translate_field_ref('p_post') . ' NOT LIKE \'%' . db_encode_like(do_lang('SPACER_POST_MATCHER', '', '', '', get_site_default_lang()) . '%') . '\' AND (p.id=' . strval($this->post_id) . ') ORDER BY p.id', null, null, false, false, $lang_fields);
        $title = $rows[0]['p_title'];
        $this->assertTrue('test_comment1' == $title);
    }

    public function testEditComment()
    {
        edit_calendar_event($this->event_id, 8, '', null, 0, 'test_event1', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, 2010, 1, 19, 'day_of_month', 0, 0, get_users_timezone(), 1, null, '', '', 1, 1, 1, 1, '');
        $this->assertTrue('test_event1' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'e_title', array('id' => $this->event_id)), $GLOBALS['SITE_DB']));
    }

    public function tearDown()
    {
        delete_calendar_event($this->event_id);
        $GLOBALS['FORUM_DB']->query_delete('f_posts', array('id' => $this->post_id));

        parent::tearDown();
    }
}
