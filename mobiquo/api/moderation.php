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

/*EXTRA FUNCTIONS: classTTConnection|CMS.*|member_acl*/

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function login_mod_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/moderation_read.php');

    $username = $params[0];
    $password = $params[1];

    require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
    $acl_object = new CMSMemberACL();
    $result = $acl_object->authenticate_credentials_and_set_auth($username, $password);

    if ($result <= 0) {
        warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
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
function m_stick_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);

    $moderation_object = new CMSModerationWrite();
    if ($params[1] == 1) {
        $result = $moderation_object->stick_topic($topic_id);
    } else {
        $result = $moderation_object->unstick_topic($topic_id);
    }

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_close_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);

    $moderation_object = new CMSModerationWrite();
    if ($params[1] == 1) {
        $result = $moderation_object->open_topic($topic_id);
    } else {
        $result = $moderation_object->close_topic($topic_id);
    }

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_delete_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);
    $reason = isset($params[2]) ? $params[2] : '';

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->delete_topic($topic_id, $reason);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_delete_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $post_id = intval($params[0]);
    $reason = isset($params[2]) ? $params[2] : '';

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->delete_post($post_id, $reason);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_undelete_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Soft deletion not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_undelete_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Soft deletion not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_move_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);
    $forum_id = intval($params[1]);

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->move_topic($topic_id, $forum_id);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_rename_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);
    $new_title = $params[1];

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->rename_topic($topic_id, $new_title);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_move_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $posts = array_map('intval', explode(',', $params[0]));
    $topic_id = isset($params[1]) ? intval($params[1]) : null;
    $new_topic_title = isset($params[2]) ? $params[2] : null;
    $forum_id = isset($params[3]) ? intval($params[3]) : null;

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->move_posts($posts, $topic_id, $new_topic_title, $forum_id);

    if ($result !== false) {
        if ($topic_id !== null) { // Moved to an existing topic
            $response = mobiquo_val([
                'result' => mobiquo_val(true, 'boolean'),
            ], 'struct');
        } else { // Moved to a new topic
            $response = mobiquo_val([
                'result' => mobiquo_val(true, 'boolean'),
                'topic_id' => mobiquo_val($result, 'string'),
            ], 'struct');
        }
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_merge_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_a = intval($params[0]);
    $topic_b = intval($params[1]);

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->merge_topics($topic_a, $topic_b);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_merge_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $source_post_ids = array_map('intval', explode(',', $params[0]));

    $target_post_id = intval($params[0]);

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->merge_posts($source_post_ids, $target_post_id);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_get_moderate_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/moderation_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 20);

    $moderation_object = new CMSModerationRead();
    $result = $moderation_object->get_topics_needing_moderation($start, $max);
    if ($result === false) {
        access_denied('AS_GUEST');
    }

    list($total_topic_num, $topics) = $result;

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
function m_get_moderate_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/moderation_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 20);

    $moderation_object = new CMSModerationRead();
    $result = $moderation_object->get_posts_needing_moderation($start, $max);
    if ($result === false) {
        access_denied('AS_GUEST');
    }

    list($total_post_num, $posts) = $result;

    $response = mobiquo_val([
        'total_post_num' => mobiquo_val($total_post_num, 'int'),
        'posts' => mobiquo_val($posts, 'array'),
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
function m_approve_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $topic_id = intval($params[0]);
    $approve = $params[1];

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->approve_topic($topic_id, $approve);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_approve_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $post_id = intval($params[0]);
    $approve = $params[1];

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->approve_post($post_id, $approve);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_ban_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $username = $params[0];
    $delete_all_posts = isset($params[1]) && $params[1] == 2;
    $reason = isset($params[2]) ? $params[2] : '';
    $expires = isset($params[3]) ? $params[3] : null;

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->ban_user($username, $delete_all_posts, $reason, $expires);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_unban_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $user_id = intval($params[0]);

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->unban_user($user_id);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_get_delete_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Soft deletion not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_get_delete_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Soft deletion not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_get_report_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Reported view not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function m_mark_as_spam_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/moderation_write.php');

    $user_id = intval($params[0]);

    $moderation_object = new CMSModerationWrite();
    $result = $moderation_object->mark_as_spam($user_id);

    if ($result) {
        $response = mobiquo_val([
            'result' => mobiquo_val(true, 'boolean'),
        ], 'struct');
    } else {
        $response = mobiquo_val([
            'result' => mobiquo_val(false, 'boolean'),
            'result_text' => mobiquo_val(do_lang('permissions:ACCESS_DENIED__NOT_AS_GUEST'), 'base64'),
            'is_login_mod' => mobiquo_val(is_guest(), 'boolean'),
        ], 'struct');
    }
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function m_close_report_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Reported view not supported');
}
