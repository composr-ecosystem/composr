<?php /*

 Composr
 Copyright (c) ocProducts/Tapatalk, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: classTTConnection|CMS.**/

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function report_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/post_write.php');

    $post_id = intval($params[0]);
    $reason = isset($params[1]) ? $params[1] : '';

    $post_object = new CMSPostWrite();
    $post_object->report_post($post_id, $reason);
    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function reply_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/post_write.php');

    $forum_id = intval($params[0]);
    $topic_id = intval($params[1]);
    $title = $params[2];
    $post = $params[3];
    $attachment_ids = isset($params[4]) ? array_map('intval', $params[4]) : [];
    $return_html = isset($params[6]) && $params[6];

    $post_object = new CMSPostWrite();
    $response = $post_object->reply_post($forum_id, $topic_id, $title, $post, $attachment_ids, $return_html);

    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_quote_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

    if (empty($params[0])) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }
    $post_ids = array_map('intval', explode('-', $params[0]));

    $post_object = new CMSPostRead();
    list($quote_title, $quote_content) = $post_object->get_quote_post($post_ids);

    if ($quote_content == '') {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($params[0]), 'post'));
    }

    $response = mobiquo_val([
        'post_id' => mobiquo_val($params[0], 'string'),
        'post_title' => mobiquo_val($quote_title, 'base64'),
        'post_content' => mobiquo_val($quote_content, 'base64'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_raw_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

    $post_id = intval($params[0]);

    $post_object = new CMSPostRead();
    $post_details = $post_object->get_raw_post($post_id);

    $response = mobiquo_val([
        'post_id' => mobiquo_val($post_details['post_id'], 'string'),
        'post_title' => mobiquo_val($post_details['post_title'], 'base64'),
        'post_content' => mobiquo_val($post_details['post_content'], 'base64'),
        'show_reason' => mobiquo_val(true, 'boolean'),
        'edit_reason' => mobiquo_val($post_details['post_content'], 'base64'),
        'attachments' => mobiquo_val(render_tapatalk_attachments($post_details['attachments']), 'array'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function save_raw_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/post_write.php');

    $post_id = intval($params[0]);
    $title = $params[1];
    $post = $params[2];
    $return_html = isset($params[3]) && $params[3];
    $attachment_ids = isset($params[4]) ? array_map('intval', $params[4]) : [];
    $reason = empty($params[6]) ? do_lang('REASON_TAPATALK_EDITING_POST') : $params[6];

    $post_object = new CMSPostWrite();
    $response = $post_object->edit_post($post_id, $title, $post, $attachment_ids, $return_html, $reason);

    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_thread_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

    $topic_id = intval($params[0]);
    list($start, $max) = get_pagination_positions($params, 1, 2, 20);
    $return_html = isset($params[3]) && $params[3];

    member_tracking_update('topicview', '', strval($topic_id));

    $post_object = new CMSPostRead();
    $response = $post_object->get_topic($topic_id, $start, $max, $return_html);
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_thread_by_unread_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

    $topic_id = intval($params[0]);
    $max = isset($params[1]) ? $params[1] : 20;
    $return_html = isset($params[2]) && $params[2];

    $last_read_time = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_read_logs', 'l_time', ['l_member_id' => get_member(), 'l_topic_id' => $topic_id]);
    if ($last_read_time === null) {
        // Assumes that everything made in the last two weeks has not been read
        $unread_details = $GLOBALS['FORUM_DB']->query('SELECT id,p_time FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts WHERE p_topic_id=' . strval($topic_id) . ' AND p_time>' . strval(time() - 60 * 60 * 24 * intval(get_option('post_read_history_days'))) . ' ORDER BY p_time', 1);
        if (array_key_exists(0, $unread_details)) {
            $last_read_time = $unread_details[0]['p_time'] - 1;
        } else {
            $last_read_time = 0;
        }
    }
    $first_unread_id_p_time = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT p_time FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts WHERE p_topic_id=' . strval($topic_id) . ' AND p_time>' . strval($last_read_time) . ' ORDER BY p_time');
    if ($first_unread_id_p_time !== null) {
        // What page is it on?
        $before = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts WHERE p_time<' . strval($first_unread_id_p_time) . ' AND ' . tapatalk_get_topic_where($topic_id));
        $start = intval(floor(floatval($before) / floatval($max))) * $max;
    } else {
        // What page is it on?
        $before = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts WHERE ' . tapatalk_get_topic_where($topic_id));
        $start = intval(floor(floatval($before) / floatval($max))) * $max;
        if ($start == $before) {
            $start = max(0, $before - $max);
        }
    }
    $position = $before + 1;

    member_tracking_update('topicview', '', strval($topic_id));

    $post_object = new CMSPostRead();
    $response = $post_object->get_topic($topic_id, $start, $max, $return_html, $position);
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_thread_by_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/post_read.php');

    $post_id = intval($params[0]);
    $max = isset($params[1]) ? $params[1] : 20;
    $return_html = isset($params[2]) && $params[2];

    $post_details = $GLOBALS['FORUM_DB']->query_select('f_posts', ['p_topic_id', 'p_time'], ['id' => $post_id]);
    if (empty($post_details)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
    }

    $post_time = $post_details[0]['p_time'];
    $topic_id = $post_details[0]['p_topic_id'];

    // What page is it on?
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts WHERE p_time<=' . strval($post_time) . ' AND id<>' . strval($post_id) . ' AND ' . tapatalk_get_topic_where($topic_id);
    $before = $GLOBALS['FORUM_DB']->query_value_if_there($sql);
    $start = intval(floor(floatval($before) / floatval($max))) * $max;
    $position = $before + 1;

    member_tracking_update('topicview', '', strval($topic_id));

    $post_object = new CMSPostRead();
    $response = $post_object->get_topic($topic_id, $start, $max, $return_html, $position);
    return mobiquo_response($response);
}
