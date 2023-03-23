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
function new_conversation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $user_name_list = $params[0];
    $subject = $params[1];
    $body = $params[2];
    $attachment_ids = isset($params[3]) ? array_map('intval', $params[3]) : [];

    $pt_object = new CMSPtWrite();
    $new_topic_id = $pt_object->new_private_topic($user_name_list, $subject, $body, $attachment_ids);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'conv_id' => mobiquo_val(strval($new_topic_id), 'string'),
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
function reply_conversation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $topic_id = intval($params[0]);
    $subject = isset($params[2]) ? $params[2] : '';
    $body = $params[1];
    $attachment_ids = isset($params[3]) ? array_map('intval', $params[3]) : [];

    $pt_object = new CMSPtWrite();
    $new_post_id = $pt_object->reply_private_topic($topic_id, $subject, $body, $attachment_ids);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'msg_id' => mobiquo_val(strval($new_post_id), 'string'),
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
function invite_participant_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $user_name_list = $params[0];
    $topic_id = intval($params[1]);
    $reason = isset($params[2]) ? $params[2] : '';

    $pt_object = new CMSPtWrite();
    $pt_object->invite_participants($user_name_list, $topic_id, $reason);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_conversations_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pt_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 20);

    $pt_object = new CMSPtRead();
    $result = $pt_object->get_private_topics($start, $max);

    $topics = [];
    foreach ($result['topics'] as $topic) {
        $participants = [];
        foreach ($topic['participants'] as $participant) {
            $arr = [
                'user_id' => mobiquo_val(strval($participant['user_id']), 'string'),
                'username' => mobiquo_val($participant['username'], 'base64'),
                'icon_url' => mobiquo_val($participant['icon_url'], 'string'),
                'is_online' => mobiquo_val($participant['is_online'], 'boolean'),
            ];
            $display_text = $GLOBALS['FORUM_DRIVER']->get_username($participant['user_id'], true);
            if ($display_text != $participant['username']) {
                $arr += [
                    'display_text' => mobiquo_val($display_text, 'base64'),
                ];
            }
            $participants[$participant['user_id']] = mobiquo_val($arr, 'struct');
        }

        $topics[] = mobiquo_val([
            'conv_id' => mobiquo_val(strval($topic['topic_id']), 'string'),
            'total_message_num' => mobiquo_val($topic['total_posts'], 'int'),
            'reply_count' => mobiquo_val($topic['total_posts'] - 1, 'int'),
            'participant_count' => mobiquo_val($topic['participant_count'], 'int'),
            'start_user_id' => mobiquo_val(strval($topic['start_user_id']), 'string'),
            'start_conv_time' => mobiquo_val($topic['start_conv_time'], 'dateTime.iso8601'),
            'last_user_id' => mobiquo_val(strval($topic['last_user_id']), 'string'),
            'timestamp' => mobiquo_val($topic['last_conv_time'], 'string'),
            'last_conv_time' => mobiquo_val($topic['last_conv_time'], 'dateTime.iso8601'),
            'conv_subject' => mobiquo_val($topic['conv_subject'], 'base64'),
            'conv_title' => mobiquo_val($topic['conv_subject'], 'base64'),
            'new_post' => mobiquo_val($topic['new_post'], 'boolean'),
            'unread_num' => mobiquo_val($topic['unread_num'], 'int'),
            'can_invite' => mobiquo_val($topic['can_invite'], 'boolean'),
            'can_edit' => mobiquo_val($topic['can_edit'], 'boolean'),
            'can_close' => mobiquo_val($topic['can_close'], 'boolean'),
            'can_upload' => mobiquo_val($topic['can_upload'], 'boolean'),
            'is_closed' => mobiquo_val($topic['is_closed'], 'boolean'),
            'delete_mode' => mobiquo_val($topic['delete_mode'], 'int'),
            'participants' => mobiquo_val($participants, 'struct'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'conversation_count' => mobiquo_val($result['topics_count'], 'int'),
        'unread_count' => mobiquo_val($result['unread_count'], 'int'),
        'can_upload' => mobiquo_val($result['can_upload'], 'boolean'),
        'list' => mobiquo_val($topics, 'array'),
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
function get_conversation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pt_read.php');

    $topic_id = intval($params[0]);
    list($start, $max) = get_pagination_positions($params, 1, 2, 8);
    $return_html = isset($params[3]) && $params[3];

    $pt_object = new CMSPtRead();
    $result = $pt_object->get_private_topic($topic_id, $start, $max, $return_html);

    $participants = [];
    foreach ($result['participants'] as $participant) {
        $arr = [
            'user_id' => mobiquo_val(strval($participant['user_id']), 'string'),
            'username' => mobiquo_val($participant['username'], 'base64'),
            'icon_url' => mobiquo_val($participant['icon_url'], 'string'),
            'is_online' => mobiquo_val($participant['is_online'], 'boolean'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($participant['user_id'], true);
        if ($display_text != $participant['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $participants[$participant['user_id']] = mobiquo_val($arr, 'struct');
    }

    $posts = [];
    foreach ($result['posts'] as $post) {
        $posts[] = mobiquo_val([
            'msg_id' => mobiquo_val(strval($post['msg_id']), 'string'),
            'msg_content' => mobiquo_val($post['msg_content'], 'base64'),
            'msg_author_id' => mobiquo_val(strval($post['msg_author_id']), 'string'),
            'is_unread' => mobiquo_val($post['is_unread'], 'boolean'),
            'is_online' => mobiquo_val($post['is_online'], 'boolean'),
            'has_left' => mobiquo_val($post['has_left'], 'boolean'),
            'post_time' => mobiquo_val($post['post_time'], 'dateTime.iso8601'),
            'timestamp' => mobiquo_val(strval($post['post_time']), 'string'),
            'new_post' => mobiquo_val($post['new_post'], 'boolean'),
            'attachments' => mobiquo_val(render_tapatalk_attachments($post['attachments']), 'array'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'conv_id' => mobiquo_val(strval($result['topic_id']), 'string'),
        'conv_subject' => mobiquo_val($result['conv_title'], 'base64'),
        'conv_title' => mobiquo_val($result['conv_title'], 'base64'),
        'participant_count' => mobiquo_val($result['participant_count'], 'int'),
        'start_user_id' => mobiquo_val($result['start_user_id'], 'string'),
        'start_conv_time' => mobiquo_val($result['start_conv_time'], 'dateTime.iso8601'),
        'last_user_id' => mobiquo_val($result['last_user_id'], 'string'),
        'last_conv_time' => mobiquo_val($result['last_conv_time'], 'dateTime.iso8601'),
        'total_message_num' => mobiquo_val($result['total_posts'], 'int'),
        'reply_count' => mobiquo_val($result['total_posts'] - 1, 'int'),
        'new_post' => mobiquo_val($result['new_post'], 'boolean'),
        'can_invite' => mobiquo_val($result['can_invite'], 'boolean'),
        'can_edit' => mobiquo_val($result['can_edit'], 'boolean'),
        'can_close' => mobiquo_val($result['can_close'], 'boolean'),
        'can_upload' => mobiquo_val($result['can_upload'], 'boolean'),
        'is_closed' => mobiquo_val($result['is_closed'], 'boolean'),
        'delete_mode' => mobiquo_val($result['delete_mode'], 'int'),
        'participants' => mobiquo_val($participants, 'struct'),
        'list' => mobiquo_val($posts, 'array'),
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
function get_quote_conversation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/pt_read.php');

    $post_id = intval($params[1]);

    $pt_object = new CMSPtRead();
    $text_body = $pt_object->get_quote_for_private_topic($post_id);

    $response = mobiquo_val([
        'text_body' => mobiquo_val($text_body, 'base64'),
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
function delete_conversation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $topic_id = intval($params[0]);

    $pt_object = new CMSPtWrite();
    $pt_object->delete_private_topic($topic_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function mark_conversation_unread_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $topic_id = intval($params[0]);

    $pt_object = new CMSPtWrite();
    $pt_object->mark_private_topic_unread($topic_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function mark_conversation_read_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/pt_write.php');

    $topic_id = isset($params[0]) ? intval($params[0]) : null;

    $pt_object = new CMSPtWrite();
    $pt_object->mark_private_topic_read($topic_id);

    return mobiquo_response_true();
}
