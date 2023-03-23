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
function login_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');

    $username = $params[0];
    $password = @cms_empty_safe($params[1]) ? '' : $params[1];
    $invisible = isset($params[2]) && $params[2];

    $user_object = new CMSMemberACL();
    $user_id = $user_object->authenticate_credentials_and_set_auth($username, $password, $invisible);

    if ($user_id === null) {
        if ($GLOBALS['FORUM_DRIVER']->get_member_from_username($username) === null) {
            $response = mobiquo_val([
                'result' => mobiquo_val(true, 'boolean'),
                'result_text' => mobiquo_val(do_lang('MEMBER_NO_EXIST'), 'base64'),
                'status' => mobiquo_val('2', 'string'),
            ], 'struct');
            return mobiquo_response($response);
        }

        warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
    }

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    $user_object = new CMSUserRead();
    $user_details = $user_object->get_user_details($user_id);

    $usergroups = [];
    foreach ($user_details['usergroup_id'] as $ugroup) {
        $usergroups[] = mobiquo_val([
            'usergroup_id' => mobiquo_val($ugroup, 'string'),
        ], 'struct');
    }

    $arr = [
        'result' => mobiquo_val(true, 'boolean'),
        'user_id' => mobiquo_val(strval($user_details['user_id']), 'string'),
        'login_name' => mobiquo_val($user_details['login_name'], 'base64'),
        'username' => mobiquo_val($user_details['username'], 'base64'),
        'usergroup_id' => mobiquo_val($usergroups, 'array'),
        'email' => mobiquo_val($user_details['email'], 'base64'),
        'icon_url' => mobiquo_val($user_details['icon_url'], 'string'),
        'post_count' => mobiquo_val($user_details['post_count'], 'int'),
        'user_type' => mobiquo_val($user_details['user_type'], 'base64'),
        'can_pm' => mobiquo_val($user_details['can_pm'], 'boolean'),
        'can_send_pm' => mobiquo_val($user_details['can_send_pm'], 'boolean'),
        'can_moderate' => mobiquo_val($user_details['can_moderate'], 'boolean'),
        'can_search' => mobiquo_val($user_details['can_search'], 'boolean'),
        'can_whosonline' => mobiquo_val($user_details['can_whosonline'], 'boolean'),
        'can_profile' => mobiquo_val($user_details['can_profile'], 'boolean'),
        'can_upload_avatar' => mobiquo_val($user_details['can_upload_avatar'], 'boolean'),
        'max_avatar_width' => mobiquo_val($user_details['max_avatar_width'], 'int'),
        'max_avatar_height' => mobiquo_val($user_details['max_avatar_height'], 'int'),
        'max_attachment' => mobiquo_val($user_details['max_attachment'], 'int'),
        'allowed_extensions' => mobiquo_val($user_details['allowed_extensions'], 'string'),
        'max_attachment_size' => mobiquo_val($user_details['max_attachment_size'], 'int'),
        'max_png_size' => mobiquo_val($user_details['max_png_size'], 'int'),
        'max_jpg_size' => mobiquo_val($user_details['max_jpg_size'], 'int'),
        'post_countdown' => mobiquo_val($user_details['post_countdown'], 'int'),
        'ignored_uids' => mobiquo_val($user_details['ignored_uids'], 'string'),
    ];

    if (isset($user_details['display_text'])) {
        $arr += [
            'display_text' => mobiquo_val($user_details['display_text'], 'base64'),
        ];
    }

    $response = mobiquo_val($arr, 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_inbox_stat_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    $user_object = new CMSUserRead();
    $user_details = $user_object->get_inbox_stats();

    $response = mobiquo_val([
        'inbox_unread_count' => mobiquo_val($user_details['inbox_unread_count'], 'int'),
        'subscribed_topic_unread_count' => mobiquo_val($user_details['subscribed_topic_unread_count'], 'int'),
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
function logout_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');

    $user_object = new CMSMemberACL();
    $user_object->logout_user();

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_online_users_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    list($start, $max) = get_pagination_positions__by_page($params, 0, 1, 20);

    if (isset($params[2])) {
        $id = $params[2];
        $area = (!isset($params[3])) ? 'forum' : $params[3];
    } else {
        $id = null;
        $area = 'forum';
    }

    $user_object = new CMSUserRead();
    $user_details = $user_object->get_online_users($start, $max, $id, $area);

    $users = [];
    foreach ($user_details['list'] as $user) {
        $arr = [
            'user_id' => mobiquo_val($user['user_id'], 'string'),
            'username' => mobiquo_val($user['username'], 'base64'),
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
        'member_count' => mobiquo_val($user_details['member_count'], 'int'),
        'guest_count' => mobiquo_val($user_details['guest_count'], 'int'),
        'list' => $users,
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
function get_user_info_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    if (!empty($params)) {
        if (!@cms_empty_safe($params[1])) {
            $user_id = intval($params[1]);
        } else {
            $user_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($params[0]);
        }
    } else {
        $user_id = get_member();
    }

    $user_object = new CMSUserRead();
    $user_details = $user_object->get_user_info($user_id);

    $arr = [
        'user_id' => mobiquo_val($user_details['user_id'], 'string'),
        'user_name' => mobiquo_val($user_details['username'], 'base64'),
        'post_count' => mobiquo_val($user_details['post_count'], 'int'),
        'reg_time' => mobiquo_val($user_details['reg_time'], 'dateTime.iso8601'),
        'last_activity_time' => mobiquo_val($user_details['last_activity_time'], 'dateTime.iso8601'),
        'timestamp' => mobiquo_val(strval($user_details['last_activity_time']), 'string'),
        'is_online' => mobiquo_val($user_details['is_online'], 'boolean'),
        'accept_pm' => mobiquo_val($user_details['accept_pm'], 'boolean'),
        'i_follow_u' => mobiquo_val($user_details['i_follow_u'], 'boolean'),
        'u_follow_me' => mobiquo_val($user_details['u_follow_me'], 'boolean'),
        'accept_follow' => mobiquo_val($user_details['accept_follow'], 'boolean'),
        'following_count' => mobiquo_val($user_details['following_count'], 'int'),
        'follower' => mobiquo_val($user_details['follower'], 'int'),
        'icon_url' => mobiquo_val($user_details['icon_url'], 'string'),
        'can_ban' => mobiquo_val($user_details['can_ban'], 'boolean'),
        'is_ban' => mobiquo_val($user_details['is_ban'], 'boolean'),
    ];
    $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user_details['user_id'], true);
    if ($display_text != $user_details['username']) {
        $arr += [
            'display_text' => mobiquo_val($display_text, 'base64'),
        ];
    }

    if (isset($arr['current_action'])) {
        $arr['current_action'] = mobiquo_val($user_details['current_action'], 'base64');
    }

    $user_details['custom_fields_list'];
    $custom_fields_list = [];
    foreach ($user_details['custom_fields_list'] as $name => $value) {
        $custom_fields_list[] = mobiquo_val([
            'name' => mobiquo_val($name, 'base64'),
            'value' => mobiquo_val($value, 'base64'),
        ], 'struct');
    }
    $arr += [
        'custom_fields_list' => mobiquo_val($custom_fields_list, 'array'),
    ];

    $response = mobiquo_val($arr, 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_user_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    if (!empty($params)) {
        if (isset($params[1])) {
            $user_id = intval($params[1]);
        } else {
            $user_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($params[0]);
        }
    } else {
        $user_id = get_member();
    }
    if (is_guest($user_id)) {
        access_denied('NOT_AS_GUEST');
    }

    $max = 50;

    $user_object = new CMSUserRead();
    $user_results = $user_object->get_user_topics($user_id, $max);

    $topics = [];
    foreach ($user_results as $user_details) {
        $topics[] = $user_details;
    }
    $response = mobiquo_val($topics, 'array');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_user_reply_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    if (!empty($params)) {
        if (isset($params[1])) {
            $user_id = intval($params[1]);
        } else {
            $user_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($params[0]);
        }
    } else {
        $user_id = get_member();
    }
    if (is_guest($user_id)) {
        access_denied('NOT_AS_GUEST');
    }

    $max = 50;

    $user_object = new CMSUserRead();
    $posts = $user_object->get_user_reply_posts($user_id, $max);

    $response = mobiquo_val($posts, 'array');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_recommended_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    list($start, $max) = get_pagination_positions($params, 0, 1, 20);
    //$mode = $params[2];   Not used, we don't differentiate Tapatalk vs non-Tapatalk users

    $user_object = new CMSUserRead();
    list($total, $_users) = $user_object->get_recommended_users($start, $max);

    $users = [];
    foreach ($_users as $user) {
        $username = $GLOBALS['FORUM_DRIVER']->get_username($user['member_liked'], false, USERNAME_DEFAULT_NULL);
        if ($username === null) {
            continue;
        }

        $arr = [
            'user_id' => mobiquo_val(strval($user['member_liked']), 'string'),
            'username' => mobiquo_val($username, 'base64'),
            'display_text' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_username($user['member_liked'], true), 'base64'),
            'icon_url' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($user['member_liked']), 'string'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user['member_liked'], true);
        if ($display_text != $username) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $users[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total' => mobiquo_val($total, 'int'),
        'list' => mobiquo_val($users, 'array'),
        'type' => mobiquo_val('contact', 'string'),
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
function search_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    $keywords = $params[0];
    list($start, $max) = get_pagination_positions__by_page($params, 1, 2, 20);

    $user_object = new CMSUserRead();
    list($total, $_users) = $user_object->get_search_users($keywords, $start, $max);

    $users = [];
    foreach ($_users as $user) {
        $arr = [
            'user_id' => mobiquo_val(strval($user['id']), 'string'),
            'username' => mobiquo_val($user['m_username'], 'base64'),
            'icon_url' => mobiquo_val($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($user['id']), 'string'),
        ];
        $display_text = $GLOBALS['FORUM_DRIVER']->get_username($user['id'], true);
        if ($display_text != $user['m_username']) {
            $arr += [
                'display_text' => mobiquo_val($display_text, 'base64'),
            ];
        }
        $users[] = mobiquo_val($arr, 'struct');
    }

    $response = mobiquo_val([
        'total' => mobiquo_val($total, 'int'),
        'list' => mobiquo_val($users, 'array'),
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
function ignore_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/user_write.php');

    $user_id = intval($params[0]);
    $adding = (!isset($params[1]) || $params[1] == 1);

    $user_object = new CMSUserWrite();
    $user_object->ignore_user($user_id, $adding);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function update_signature_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/user_write.php');

    $signature = $params[0];

    $user_object = new CMSUserWrite();
    $signature = $user_object->update_signature($signature);

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'signature' => mobiquo_val($signature, 'base64'),
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
function get_contact_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    if (!isset($params['code']) || !isset($params['user_id'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];
    $user_ids = array_map('intval', explode(',', $params['user_id']));

    $connection = new classTTConnection();
    $test = $connection->actionVerification($code, 'get_contact');
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    $user_object = new CMSUserRead();
    $_users = $user_object->get_contact($user_ids);

    $users = [];
    foreach ($_users as $user) {
        $users[] = mobiquo_val([
            'user_id' => mobiquo_val(strval($user['user_id']), 'string'),
            'display_name' => mobiquo_val($user['display_name'], 'base64'),
            'enc_email' => mobiquo_val($user['enc_email'], 'string'),
            'allow_email' => mobiquo_val($user['enc_email'], 'boolean'),
            'language' => mobiquo_val($user['enc_email'], 'string'),
            'activated' => mobiquo_val($user['enc_email'], 'boolean'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'users' => mobiquo_val($users, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}
