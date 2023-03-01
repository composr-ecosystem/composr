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
function report_pm_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pm_write.php');

    $msg_id = intval($params[0]);
    $reason = isset($params[1]) ? $params[1] : '';

    $post_object = new CMSPmWrite();
    $post_object->report_pm($msg_id, $reason);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function create_message_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pm_write.php');

    $pm_object = new CMSPmWrite();

    $user_name_list = $params[0];
    $subject = $params[1];
    $message = $params[2];
    $action = isset($params[3]) ? $params[3] : (CMSPmWrite::TAPATALK_MESSAGE_NEW);
    $post_id = @cms_empty_safe($params[4]) ? null : $params[4];

    $first_new_post_id = $pm_object->create_message($user_name_list, $subject, $message, $action, $post_id);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'msg_id' => mobiquo_val($first_new_post_id, 'string'),
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
function get_box_info_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pm_read.php');

    $pm_object = new CMSPmRead();
    $details = $pm_object->get_box_info();

    $list = [];

    $list[] = mobiquo_val([
        'box_id' => mobiquo_val(strval(TAPATALK_MESSAGE_BOX_INBOX), 'string'),
        'box_name' => mobiquo_val('INBOX', 'base64'),
        'msg_count' => mobiquo_val($details['inbox_total'], 'int'),
        'unread_count' => mobiquo_val($details['inbox_unread_total'], 'int'),
        'box_type' => mobiquo_val('INBOX', 'string'),
    ], 'struct');

    $list[] = mobiquo_val([
        'box_id' => mobiquo_val(strval(TAPATALK_MESSAGE_BOX_SENT), 'string'),
        'box_name' => mobiquo_val('SENT', 'base64'),
        'msg_count' => mobiquo_val($details['sent_total'], 'int'),
        'unread_count' => mobiquo_val($details['sent_unread_total'], 'int'),
        'box_type' => mobiquo_val('SENT', 'string'),
    ], 'struct');

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'list' => mobiquo_val($list, 'array'),
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
function get_box_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pm_read.php');

    list($start, $max) = get_pagination_positions($params, 1, 2, 20);
    $box_id = intval($params[0]);

    $pm_object = new CMSPmRead();
    $box = $pm_object->get_box($box_id, $start, $max);

    $posts = [];
    foreach ($box['posts'] as $post) {
        $posts[] = mobiquo_val([
            'msg_id' => mobiquo_val(strval($post['msg_id']), 'string'),
            'msg_state' => mobiquo_val($post['msg_state'], 'int'),
            'sent_date' => mobiquo_val($post['sent_date'], 'dateTime.iso8601'),
            'timestamp' => mobiquo_val(strval($post['sent_date']), 'string'),
            'msg_from_id' => mobiquo_val(strval($post['msg_from_id']), 'string'),
            'msg_from' => mobiquo_val($post['msg_from'], 'base64'),
            'icon_url' => mobiquo_val($post['icon_url'], 'string'),
            'msg_subject' => mobiquo_val($post['msg_subject'], 'base64'),
            'short_content' => mobiquo_val($post['short_content'], 'base64'),
            'is_online' => mobiquo_val($post['is_online'], 'boolean'),
        ], 'struct');
    }

    $msg_to = [];
    foreach ($box['msg_to'] as $msg) {
        $arr = [
            'user_id' => mobiquo_val(strval($msg['user_id']), 'string'),
            'username' => mobiquo_val($msg['username'], 'base64'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($msg['user_id'], true);
        if ($display_text != $msg['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $msg_to[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'total_message_count' => mobiquo_val($box['total_message_count'], 'int'),
        'total_unread_count' => mobiquo_val($box['total_unread_count'], 'int'),
        'list' => mobiquo_val($posts, 'array'),
        'msg_to' => mobiquo_val($msg_to, 'array'),
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
function get_message_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pm_read.php');

    $post_id = intval($params[0]);
    $return_html = isset($params[2]) && $params[2];

    $pm_object = new CMSPmRead();
    $details = $pm_object->get_message($post_id, $return_html);

    $msg_to = [];
    foreach ($details['msg_to'] as $_msg_to) {
        $arr = [
            'user_id' => mobiquo_val(strval($_msg_to['user_id']), 'string'),
            'username' => mobiquo_val($_msg_to['username'], 'base64'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($_msg_to['user_id'], true);
        if ($display_text != $_msg_to['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $msg_to[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'msg_from_id' => mobiquo_val(strval($details['msg_from_id']), 'string'),
        'msg_from' => mobiquo_val($details['msg_from'], 'base64'),
        'icon_url' => mobiquo_val($details['icon_url'], 'string'),
        'sent_date' => mobiquo_val($details['sent_date'], 'dateTime.iso8601'),
        'msg_subject' => mobiquo_val($details['msg_subject'], 'base64'),
        'text_body' => mobiquo_val($details['text_body'], 'base64'),
        'msg_to' => mobiquo_val($msg_to, 'array'),
        'attachments' => mobiquo_val(render_tapatalk_attachments($details['attachments']), 'array'),
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
function get_quote_pm_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pm_read.php');

    $post_id = intval($params[0]);

    $pm_object = new CMSPmRead();
    list($quote_title, $quote_content) = $pm_object->get_quote_pm($post_id);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'msg_id' => mobiquo_val(strval($post_id), 'string'),
        'msg_subject' => mobiquo_val($quote_title, 'base64'),
        'text_body' => mobiquo_val($quote_content, 'base64'),
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
function delete_message_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pm_write.php');

    $post_id = intval($params[0]);

    $pm_object = new CMSPmWrite();
    $pm_object->delete_message($post_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function mark_pm_unread_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pm_write.php');

    $message_ids = isset($params[0]) ? array_map('intval', explode(',', $params[0])) : null;

    $pm_object = new CMSPmWrite();
    $pm_object->mark_pm_unread($message_ids);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function mark_pm_read_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pm_write.php');

    $message_ids = isset($params[0]) ? array_map('intval', explode(',', $params[0])) : null;

    $pm_object = new CMSPmWrite();
    $pm_object->mark_pm_read($message_ids);

    return mobiquo_response_true();
}
