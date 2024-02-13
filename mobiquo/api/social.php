<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
function thank_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/social_write.php');

    $post_id = intval($params[0]);

    $social_write_object = new CMSSocialWrite();
    $social_write_object->thank_post($post_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function follow_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/social_write.php');

    $user_id = intval($params[0]);

    $social_write_object = new CMSSocialWrite();
    $social_write_object->follow($user_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function unfollow_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/social_write.php');

    $user_id = intval($params[0]);

    $social_write_object = new CMSSocialWrite();
    $social_write_object->unfollow($user_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function like_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/social_write.php');

    $post_id = intval($params[0]);

    $social_write_object = new CMSSocialWrite();
    $social_write_object->like_post($post_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function unlike_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/social_write.php');

    $post_id = intval($params[0]);

    $social_write_object = new CMSSocialWrite();
    $social_write_object->unlike_post($post_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function get_dashboard_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Not for implementation');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function get_feed_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Not for implementation');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_following_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/social_read.php');

    $social_read_object = new CMSSocialRead();
    $_following = $social_read_object->get_following();
    $following = [];
    foreach ($_following as $f) {
        $arr = [
            'user_id' => mobiquo_val(strval($f['user_id']), 'string'),
            'username' => mobiquo_val($f['username'], 'base64'),
            'display_text' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_username($f['user_id'], true), 'base64'),
            'is_online' => mobiquo_val($f['is_online'], 'boolean'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($f['user_id'], true);
        if ($display_text != $f['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $following[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total_count' => mobiquo_val(count($following), 'int'),
        'list' => mobiquo_val($following, 'array'),
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
function get_follower_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/social_read.php');

    $social_read_object = new CMSSocialRead();
    $_followers = $social_read_object->get_followers();
    $followers = [];
    foreach ($_followers as $f) {
        $arr = [
            'user_id' => mobiquo_val(strval($f['user_id']), 'string'),
            'username' => mobiquo_val($f['username'], 'base64'),
            'is_online' => mobiquo_val($f['is_online'], 'boolean'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($f['user_id'], true);
        if ($display_text != $f['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $followers[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total_count' => mobiquo_val(count($followers), 'int'),
        'list' => mobiquo_val($followers, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function set_reputation_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Reputation feature not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_alert_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/social_read.php');

    list($start, $max) = get_pagination_positions__by_page($params, 0, 1, 20);

    $social_read_object = new CMSSocialRead();
    list($total, $_items) = $social_read_object->get_alerts($start, $max);

    $items = [];
    foreach ($_items as $item) {
        $arr = [
            'user_id' => mobiquo_val(strval($item['user_id']), 'string'),
            'username' => mobiquo_val($item['username'], 'base64'),
            'icon_url' => mobiquo_val($item['icon_url'], 'string'),
            'message' => mobiquo_val($item['message'], 'base64'),
            'timestamp' => mobiquo_val($item['timestamp'], 'string'),
            'content_type' => mobiquo_val($item['content_type'], 'string'),
            'content_id' => mobiquo_val($item['content_id'], 'string'),
            'unread' => mobiquo_val($item['unread'], 'boolean'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($item['user_id'], true);
        if ($display_text != $item['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }

        if (isset($item['topic_id'])) {
            $arr['topic_id'] = mobiquo_val(strval($item['topic_id']), 'string');
        }

        if (isset($item['position'])) {
            $arr['position'] = mobiquo_val($item['position'], 'int');
        }

        $items[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total' => mobiquo_val($total, 'int'),
        'items' => mobiquo_val($items, 'array'),
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
function get_activity_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/social_read.php');

    list($start, $max) = get_pagination_positions__by_page($params, 0, 1, 20);

    $social_read_object = new CMSSocialRead();
    list($total, $_items) = $social_read_object->get_activity($start, $max);

    $items = [];
    foreach ($_items as $item) {
        $arr = [
            'user_id' => mobiquo_val(strval($item['user_id']), 'string'),
            'username' => mobiquo_val($item['username'], 'base64'),
            'icon_url' => mobiquo_val($item['icon_url'], 'string'),
            'message' => mobiquo_val($item['message'], 'base64'),
            'timestamp' => mobiquo_val($item['timestamp'], 'string'),
            'content_type' => mobiquo_val($item['content_type'], 'string'),
            'content_id' => mobiquo_val($item['content_id'], 'string'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($item['user_id'], true);
        if ($display_text != $item['username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $items[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total' => mobiquo_val($total, 'int'),
        'items' => mobiquo_val($items, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}
