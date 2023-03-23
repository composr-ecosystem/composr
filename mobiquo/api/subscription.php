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
function get_subscribed_forum_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/subscription_read.php');

    $subscription_object = new CMSSubscriptionRead();

    $result = $subscription_object->get_subscribed_forums();

    $forum_list = [];
    foreach ($result as $forum) {
        $forum_list[] = mobiquo_val([
            'forum_id' => mobiquo_val(strval($forum['forum_id']), 'string'),
            'forum_name' => mobiquo_val($forum['forum_name'], 'base64'),
            'icon_url' => mobiquo_val($forum['icon_url'], 'string'),
            'new_post' => mobiquo_val($forum['new_post'], 'boolean'),
            'is_protected' => mobiquo_val($forum['is_protected'], 'boolean'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'total_forums_num' => mobiquo_val(count($forum_list), 'int'),
        'forums' => mobiquo_val($forum_list, 'array'),
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
function subscribe_forum_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/subscription_write.php');

    $subscription_write_object = new CMSSubscriptionWrite();
    $subscription_write_object->subscribe_forum(intval($params[0]));

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function unsubscribe_forum_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/subscription_write.php');

    $subscription_write_object = new CMSSubscriptionWrite();
    $subscription_write_object->unsubscribe_forum(($params[0] == 'ALL') ? null : intval($params[0]));

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_subscribed_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/subscription_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 50);

    $subscription_object = new CMSSubscriptionRead();
    list($total_topic_num, $topics) = $subscription_object->get_subscribed_topics($start, $max);

    $response = mobiquo_val([
        'total_topic_num' => mobiquo_val($total_topic_num, 'int'),
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
function subscribe_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/subscription_write.php');

    $subscription_write_object = new CMSSubscriptionWrite();
    $subscription_write_object->subscribe_topic(intval($params[0]));

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function unsubscribe_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/subscription_write.php');

    $subscription_write_object = new CMSSubscriptionWrite();
    $subscription_write_object->unsubscribe_topic(($params[0] == 'ALL') ? null : intval($params[0]));

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function user_subscription_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (!isset($params['code']) || !isset($params['uid'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];
    $uid = $params['uid'];

    $connection = new classTTConnection();
    $test = $connection->actionVerification($code, 'user_subscription');
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    require_once(COMMON_CLASS_PATH_READ . '/subscription_read.php');

    $account_object = new CMSSubscriptionRead();
    $forums = $account_object->get_member_forum_monitoring($uid);
    $topics = $account_object->get_member_topic_monitoring($uid);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'forums' => mobiquo_val($forums, 'array'),
        'topics' => mobiquo_val($topics, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}
