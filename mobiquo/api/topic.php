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
function mark_topic_read_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    $topic_ids = $params[0];
    foreach ($topic_ids as $topic_id) {
        cns_ping_topic_read(intval($topic_id));
    }

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_topic_status_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/topic_read.php');

    $topic_ids = array_map('intval', $params[0]);

    $topic_object = new CMSTopicRead();
    $topic_status = $topic_object->get_topic_statuses($topic_ids);

    $topics = [];
    foreach ($topic_status as $topic) {
        $topics[] = mobiquo_val([
            'topic_id' => mobiquo_val(strval($topic['topic_id']), 'string'),
            'is_subscribed' => mobiquo_val($topic['is_subscribed'], 'boolean'),
            'can_subscribe' => mobiquo_val($topic['can_subscribe'], 'boolean'),
            'is_closed' => mobiquo_val($topic['is_closed'], 'boolean'),
            'last_reply_time' => mobiquo_val($topic['last_reply_time'], 'dateTime.iso8601'),
            'timestamp' => mobiquo_val(strval($topic['last_reply_time']), 'string'),
            'new_post' => mobiquo_val($topic['new_post'], 'boolean'),
            'reply_number' => mobiquo_val($topic['reply_number'], 'int'),
            'view_number' => mobiquo_val($topic['view_number'], 'int'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'status' => mobiquo_val($topics, 'array'),
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
function new_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/topics_write.php');

    $forum_id = intval($params[0]);
    $title = $params[1];
    $post = $params[2];
    $attachment_ids = isset($params[4]) ? array_map('intval', $params[4]) : [];

    $topics_object = new CMSTopicWrite();
    list($new_topic_id, $validated) = $topics_object->new_topic($forum_id, $title, $post, $attachment_ids);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'topic_id' => mobiquo_val(strval($new_topic_id), 'string'),
        'state' => mobiquo_val(($validated == 1) ? TAPATALK_POST_LIVE : TAPATALK_POST_NEEDS_VALIDATION, 'int'),
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
function get_topic_func($raw_params) // Get topics in a forum
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/topic_read.php');

    $topic_list = [];
    $topic_object = new CMSTopicRead();

    $forum_id = intval($params[0]);

    $mode = isset($params[3]) ? $params[3] : '';

    list($start, $max) = get_pagination_positions($params, 1, 2, 20);

    member_tracking_update('forumview', '', strval($forum_id));

    list($total_topic_num, $_details, $forum_name, $unread_sticky_count, $unread_announce_count, $action_details) = $topic_object->get_num_topics($mode, $forum_id, $start, $max);

    foreach ($_details as $topic) {
        if ($topic['t_cache_first_member_id'] === null) {
            continue;
        }

        $topic_list[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic);
    }

    $response = mobiquo_val([
        'total_topic_num' => mobiquo_val($total_topic_num, 'int'),
        'forum_id' => mobiquo_val(strval($forum_id), 'string'),
        'forum_name' => mobiquo_val($forum_name, 'base64'),
        'can_post' => mobiquo_val($action_details['can_post'], 'boolean'),
        'can_upload' => mobiquo_val($action_details['can_upload'], 'boolean'),
        'unread_sticky_count' => mobiquo_val($unread_sticky_count, 'int'),
        'unread_announce_count' => mobiquo_val($unread_announce_count, 'int'),
        'can_subscribe' => mobiquo_val(!is_guest(), 'boolean'),
        'is_subscribed' => mobiquo_val(get_forum_subscription_status($forum_id), 'boolean'),
        'require_prefix' => mobiquo_val(false, 'boolean'),
        'prefixes' => mobiquo_val([], 'array'),
        'topics' => mobiquo_val($topic_list, 'array'),
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
function get_unread_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/topic_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 9);

    if (isset($params[3])) {
        $filters = $params[3];
    } else {
        $filters = [];
    }

    $topic_object = new CMSTopicRead();
    list($total_forum_topics, $_topics) = $topic_object->get_topics_advanced($start, $max, $filters, CMSTopicRead::GET_TOPICS_UNREAD_ONLY);

    $topics = [];
    foreach ($_topics as $topic) {
        $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_POST_KEY_NAME | RENDER_TOPIC_LAST_POSTER);
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'total_topic_num' => mobiquo_val($total_forum_topics, 'int'),
        'topics' => mobiquo_val($topics, 'array'),
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
function get_participated_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/topic_read.php');

    list($start, $max) = get_pagination_positions($params, 1, 2, 9);

    if (isset($params[4])) {
        $participant_id = intval($params[4]);
    } elseif (isset($params[0])) {
        $participant_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($params[0]);
    } else {
        $participant_id = get_member();
    }
    if (is_guest($participant_id)) {
        access_denied('NOT_AS_GUEST');
    }

    $topic_object = new CMSTopicRead();
    list($total_forum_topics, $_topics) = $topic_object->get_topics_advanced($start, $max, [], CMSTopicRead::GET_TOPICS_PARTICIPATED_ONLY, $participant_id);

    $unread_topic_count = 0;

    $topics = [];
    foreach ($_topics as $topic) {
        if (is_topic_unread($topic['topic_id'], null, $topic)) {
            $unread_topic_count++;
        }

        $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_POST_KEY_NAME);
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'total_topic_num' => mobiquo_val($total_forum_topics, 'int'),
        'total_unread_num' => mobiquo_val($unread_topic_count, 'int'),
        'topics' => mobiquo_val($topics, 'array'),
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
function get_latest_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/topic_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 9);

    if (isset($params[3])) {
        $filters = $params[3];
    } else {
        $filters = [];
    }

    if (isset($filters['only_in'])) {
        $filters['only_in'] = array_map('intval', $filters['only_in']);
    }
    if (isset($filters['not_in'])) {
        $filters['not_in'] = array_map('intval', $filters['not_in']);
    }
    if (isset($filters['excluded_topics'])) {
        $filters['excluded_topics'] = array_map('intval', $filters['excluded_topics']);
    }

    $topic_object = new CMSTopicRead();
    list($total_forum_topics, $_topics) = $topic_object->get_topics_advanced($start, $max, $filters);

    $topics = [];
    foreach ($_topics as $topic) {
        $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, null, null, $topic, RENDER_TOPIC_POST_KEY_NAME);
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'total_topic_num' => mobiquo_val($total_forum_topics, 'int'),
        'topics' => mobiquo_val($topics, 'array'),
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
function get_topic_participants_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    $topic_id = intval($params[0]);
    $max = isset($params[1]) ? $params[1] : 20;

    if (!cns_may_access_topic($topic_id)) {
        access_denied('I_ERROR');
    }

    $_users = get_topic_participants($topic_id, $max);

    $users = [];
    foreach ($_users as $user) {
        $arr = [
            'user_id' => mobiquo_val(strval($user['user_id']), 'string'),
            'username' => mobiquo_val($user['username'], 'base64'),
            'icon_url' => mobiquo_val($user['icon_url'], 'string'),
            'is_online' => mobiquo_val($user['is_online'], 'boolean'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user['user_id'], true);
        if ($display_text != $user['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $users[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'list' => mobiquo_val($users, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}
