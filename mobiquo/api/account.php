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
function sign_in_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/account_write.php');
    require_once(COMMON_CLASS_PATH_READ . '/account_read.php');

    $token = $params[0];
    $code = $params[1];
    $email = isset($params[2]) ? $params[2] : '';
    $username = isset($params[3]) ? $params[3] : '';
    $password = isset($params[4]) ? $params[4] : null;

    $account_read_object = new CMSAccountRead();
    $custom_fields = [];
    $_custom_fields = isset($params[5]) ? $params[5] : [];
    $_custom_register_fields = $account_read_object->get_custom_register_fields();
    foreach ($_custom_register_fields as $_custom_register_field) {
        if (isset($_custom_fields[$_custom_register_field['name']])) {
            $custom_fields[$_custom_register_field['id']] = $_custom_fields[$_custom_register_field['name']];
        }
    }

    $account_object = new CMSAccountWrite();
    $results = $account_object->sign_in($token, $code, $email, $username, $password, $custom_fields);

    // Error
    if ($results['member_id'] === null) {
        $arr = [
            'status' => mobiquo_val($results['status'], 'string'),
        ];
        if (!empty($results['result_text'])) {
            $arr['result_text'] = mobiquo_val($results['result_text'], 'base64');
        }
        $response = mobiquo_val($arr, 'struct');
        return mobiquo_response($response);
    }

    require_once(COMMON_CLASS_PATH_READ . '/user_read.php');

    $user_object = new CMSUserRead();
    $user_details = $user_object->get_user_details($results['member_id']);
    $user_group = [];
    foreach ($user_details['usergroup_id'] as $ugroup) {
        $user_group[] = mobiquo_val([
            'usergroup_id' => mobiquo_val($ugroup, 'string'),
        ], 'struct');
    }
    $arr = [
        'result' => mobiquo_val(true, 'boolean'),
        'status' => mobiquo_val($results['status'], 'string'),
        'register' => mobiquo_val($results['register'], 'boolean'),
        'user_id' => mobiquo_val($results['member_id'], 'string'),
        'login_name' => mobiquo_val($user_details['login_name'], 'base64'),
        'username' => mobiquo_val($user_details['username'], 'base64'),
        'usergroup_id' => mobiquo_val($user_group, 'array'),
        'email' => mobiquo_val($user_details['email'], 'base64'),
        'icon_url' => mobiquo_val($user_details['icon_url'], 'string'),
        'post_count' => mobiquo_val($user_details['post_count'], 'int'),
        'user_type' => mobiquo_val($user_details['user_type'], 'base64'),
        'can_pm' => mobiquo_val($user_details['can_pm'], 'boolean'),
        'can_send_pm' => mobiquo_val($user_details['can_send_pm'], 'boolean'),
        'can_moderate' => mobiquo_val($user_details['can_moderate'], 'boolean'),
        'can_search' => mobiquo_val($user_details['can_search'], 'boolean'),
        'can_profile' => mobiquo_val($user_details['can_profile'], 'boolean'),
        'can_upload_avatar' => mobiquo_val($user_details['can_upload_avatar'], 'boolean'),
        'max_avatar_width' => mobiquo_val($user_details['max_avatar_width'], 'int'),
        'max_attachment' => mobiquo_val($user_details['max_attachment'], 'int'),
        'allowed_extensions' => mobiquo_val($user_details['allowed_extensions'], 'string'),
        'max_attachment_size' => mobiquo_val($user_details['max_attachment_size'], 'int'),
        'max_png_size' => mobiquo_val($user_details['max_png_size'], 'int'),
        'max_jpg_size' => mobiquo_val($user_details['max_jpg_size'], 'int'),
        'post_countdown' => mobiquo_val($user_details['post_countdown'], 'int'),
    ];
    if (isset($user_details['display_text'])) {
        $arr += [
            'display_text' => mobiquo_val($user_details['display_text'], 'base64'),
        ];
    }
    if (isset($results['preview_topic_id'])) {
        $arr['preview_topic_id'] = mobiquo_val(strval($results['preview_topic_id']), 'string');
    }
    if (!empty($results['result_text'])) {
        $arr['result_text'] = mobiquo_val($results['result_text'], 'base64');
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
function forget_password_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/account_write.php');

    $username = $params[0];
    $token = isset($params[1]) ? $params[1] : '';
    $code = isset($params[2]) ? $params[2] : '';

    $account_object = new CMSAccountWrite();
    $results = $account_object->forget_password($username, $token, $code);

    $arr = [
        'result' => mobiquo_val($results['result'], 'boolean'),
        'verified' => mobiquo_val($results['verified'], 'boolean'),
    ];
    if (!empty($results['result_text'])) {
        $arr['result_text'] = mobiquo_val($results['result_text'], 'base64');
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
function update_password_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/account_write.php');

    $account_object = new CMSAccountWrite();
    if (isset($params[2])) {
        $account_object->update_password__for_session($params[0], $params[1], $params[2]);
    } else {
        $account_object->update_password__old_to_new($params[0], $params[1]);
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
function update_email_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/account_write.php');

    $password = $params[0];
    $new_email = $params[1];

    $account_object = new CMSAccountWrite();
    $account_object->update_email($password, $new_email);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function register_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/account_write.php');

    $username = $params[0];
    $password = $params[1];
    $email = $params[2];
    $token = isset($params[3]) ? $params[3] : null;
    $code = isset($params[4]) ? $params[4] : null;
    //$custom_fields = isset($params[5]) ? $params[5] : [];    Register is old endpoint, doesn't support custom fields

    $account_object = new CMSAccountWrite();
    $results = $account_object->register($username, $password, $email, $token, $code, []);

    $arr = [
        'result' => mobiquo_val(true, 'boolean'),
    ];
    if ($results['preview_topic_id'] !== null) {
        $arr['preview_topic_id'] = mobiquo_val(strval($results['preview_topic_id']), 'string');
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
function prefetch_account_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/account_read.php');

    if (!empty($params[0])) {
        $email = $params[0];

        $account_object = new CMSAccountRead();
        $member = $account_object->prefetch_account($email);
    } else {
        $member = null;
    }

    $member_exists = $member !== null;

    $_custom_register_fields = $account_object->get_custom_register_fields();
    $custom_register_fields = [];
    foreach ($_custom_register_fields as $_custom_register_field) {
        $custom_register_fields[] = mobiquo_val([
            'name' => mobiquo_val($_custom_register_field['name'], 'base64'),
            'description' => mobiquo_val($_custom_register_field['description'], 'base64'),
            'key' => mobiquo_val($_custom_register_field['key'], 'string'),
            'default' => mobiquo_val($_custom_register_field['default'], 'string'),
            'type' => mobiquo_val($_custom_register_field['type'], 'string'),
            'options' => mobiquo_val($_custom_register_field['options'], 'string'),
        ], 'struct');
    }

    $arr = [
        'result' => mobiquo_val($member_exists, 'boolean'),
        'custom_register_fields' => mobiquo_val($custom_register_fields, 'array'),
    ];
    if ($member_exists) {
        $arr += [
            'user_id' => mobiquo_val($member['user_id'], 'string'),
            'login_name' => mobiquo_val($member['login_name'], 'base64'),
            'display_name' => mobiquo_val($member['display_name'], 'base64'),
            'avatar' => mobiquo_val($member['avatar_url'], 'string'),
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
function sync_user_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (mobiquo_input_protocol() != 'post') {
        warn_exit('Only supported for POST method');
    }

    if (!isset($params['code'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];

    $start = 0;
    $max = 1000;
    if (isset($params['start'])) {
        $start = intval($params['start']);
    }
    if (isset($params['limit'])) {
        $max = intval($params['limit']);
    }

    $connection = new classTTConnection();
    $test = $connection->actionVerification($code, 'sync_user');
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    $users = [];
    if (get_option('tapatalk_enable_sync_user') == '1') {
        require_once(COMMON_CLASS_PATH_READ . '/account_read.php');

        $account_object = new CMSAccountRead();
        $users = $account_object->sync_members($start, $max);
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'new_encrypt' => mobiquo_val(true, 'boolean'),
        'users' => mobiquo_val($users, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}
