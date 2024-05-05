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
function get_config_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_code('database_search');

    $banners_disabled = [];
    $_banners_disabled = $GLOBALS['SITE_DB']->query_select('group_privileges', ['group_id'], ['the_page' => '', 'privilege' => 'banner_free']);
    foreach ($_banners_disabled as $b) {
        $banners_disabled[] = strval($b['group_id']);
    }

    switch (get_option('one_per_email_address')) {
        case '1':
            $login_type = 'both';
            break;

        case '2':
            $login_type = 'email';
            break;

        case '0':
        default:
            $login_type = 'username';
            break;
    }

    $brand_name = get_value('rebrand_name');
    if ($brand_name === null) {
        $brand_name = DEFAULT_BRAND_NAME;
    }

    require_code('database_search');

    $_config = [
        'api_level' => '4',
        'is_open' => strval(1 - intval(get_option('site_closed'))),
        'offline' => strval(intval(get_option('site_closed'))),
        'guest_okay' => (has_actual_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'topicview') && get_option('enable_guest_access') == '1') ? '1' : '0',
        'private' => (has_actual_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'topicview') && get_option('enable_guest_access') == '1') ? '0' : '1',
        'report_post' => (addon_installed('tickets') && has_privilege(get_member(), 'may_report_content')) ? '1' : '0',
        'report_pm' => (addon_installed('tickets') && has_privilege(get_member(), 'may_report_content')) ? '1' : '0',
        'mark_read' => '1',
        'subscribe_forum' => '1',
        'get_latest_topic' => '1',
        'get_id_by_url' => '1',
        'delete_reason' => '1',
        'mod_approve' => addon_installed('validation') ? '1' : '0',
        'mod_delete' => '0', // No "soft delete" feature
        'mod_report' => '0', // No centralised view of reports, it's just another forum
        'guest_search' => '1',
        'anonymous' => get_option('is_on_invisibility'),
        'guest_whosonline' => has_actual_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'users_online') ? '1' : '0',
        'searchid' => '0',
        'avatar' => addon_installed('cns_member_avatars') ? '1' : '0',
        'pm_load' => '1',
        'subscribe_load' => '1',
        'min_search_length' => strval($GLOBALS['SITE_DB']->get_minimum_search_length()),
        'inbox_stat' => '1',
        'multi_quote' => '1',
        'default_smilies' => '1',
        'can_unread' => '1',
        'announcement' => '1',
        'emoji_support' => '1', // Can't be disabled, on iOS just results in Unicode emoji if disabled (which is problematic for us)
        'support_md5' => '0',
        'support_sha1' => '0',
        'conversation' => '1',
        'get_forum' => '1',
        'get_topic_status' => '1',
        'get_participated_forum' => '1',
        'get_forum_status' => '1',
        'get_smilies' => '1',
        'advanced_online_users' => '1',
        'mark_pm_read' => '1',
        'mark_pm_unread' => '1',
        'advanced_search' => '1',
        'mass_subscribe' => '1',
        'user_id' => '1',
        'advanced_delete' => '1',
        'mark_topic_read' => '1',
        'first_unread' => '1',
        'alert' => '1',
        'get_activity' => addon_installed('activity_feed') ? '1' : '0',
        'direct_unsubscribe' => '0', // '0' means we work with subscribing/unsubscribing from forum IDs, not any special subscription IDs
        'prefix_edit' => '0',
        'ban_delete_type' => 'soft_delete',
        'anonymous_login' => '1',
        'advanced_edit' => '1',
        'search_user' => '1',
        'user_recommended' => addon_installed('chat') ? '1' : '0',
        'inappreg' => get_option('tapatalk_joining'),
        'sign_in' => '1',
        'sso_login' => '1',
        'sso_signin' => '1',
        'sso_register' => '1',
        'native_register' => '1',
        'reg_url' => static_evaluate_tempcode(build_url(['page' => 'join'], '', [], false, false, true)),
        'ignore_user' => addon_installed('chat') ? '1' : '0',
        'advanced_merge' => '1',
        'advanced_move' => '1',
        'advanced_reg' => '1',
        'ban_expires' => '1',
        'close_report' => '0', // No centralised view of reports, it's just another forum
        'get_contact' => '1',
        'ads_disabled_group' => implode(',', $banners_disabled),
        'guest_group_id' => strval(db_get_first_id()),
        'login_type' => $login_type,
        'get_topic_participants' => '1',
        'upload_avatar' => '1',
        'goto_post' => '1',
        'goto_unread' => '1',
        'mark_forum' => '1',
        'no_refresh_on_post' => '1',
        'soft_delete' => '0',
        'system' => $brand_name,
        'charset' => 'utf-8', // Keep it simple and compatible with different clients. Will always convert to utf-8
        'timezone' => get_site_timezone(),
        'disable_bbcode' => '0',

        //'json_support' => '1',    Enable once JSON is tested. Test it if Tapatalk has stopped supporting XML-RPC.

        'push' => '1',
        'push_type' => 'pm,sub,quote,newtopic,tag',
    ];

    $is_server_call = false;
    $headers = apache_request_headers();
    if (isset($headers['X-TT'])) {
        $code = trim($headers['X-TT']);
        $connection = new classTTConnection();
        $response = $connection->actionVerification($code, 'get_config');
        if ($response !== false) {
            $is_server_call = true;
        }
    }

    if ($is_server_call) {
        $_config += [
            'sys_version' => float_to_raw_string(cms_version_number()),
            'version' => 'cms' . float_to_raw_string(cms_version_number()),
        ];
    } else {
        $_config += [
            'version' => 'cms',
        ];
    }

    $config = [];
    foreach ($_config as $config_setting => $config_value) {
        switch ($config_setting) {
            case 'is_open':
            case 'guest_okay':
                $config[$config_setting] = mobiquo_val($config_value, 'boolean');
                break;

            case 'min_search_length':
                $config[$config_setting] = mobiquo_val($config_value, 'int');
                break;

            default:
                $config[$config_setting] = mobiquo_val($config_value, 'string');
                break;
        }
    }

    $response = mobiquo_val($config, 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_forum_func($raw_params) // Get forum tree structure
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/forum_read.php');

    if (array_key_exists(0, $params)) {
        $return_description = $params[0];
        if (array_key_exists(1, $params)) {
            $forum_id = intval($params[1]);
            if ($forum_id == 0) {
                $forum_id = db_get_first_id();
            }
        } else {
            $forum_id = db_get_first_id();
        }
        $full_tree = false;
    } else {
        $forum_id = db_get_first_id();
        $return_description = true;
        $full_tree = true;
    }

    $forum_object = new CMSForumRead();
    $forums = $forum_object->forum_recursive_load($forum_id, $full_tree, $return_description);

    return mobiquo_response($forums);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_participated_forum_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/forum_read.php');

    $forum_object = new CMSForumRead();

    list($total_forums_num, $_forums) = $forum_object->get_participated_forums();
    $forums = [];
    foreach ($_forums as $pf) {
        $forums[] = mobiquo_val([
            'forum_id' => mobiquo_val(strval($pf['forum_id']), 'string'),
            'forum_name' => mobiquo_val($pf['forum_name'], 'base64'),
            'logo_url' => mobiquo_val('', 'string'),
            'is_protected' => mobiquo_val(false, 'boolean'),
            'new_post' => mobiquo_val($pf['new_post'], 'boolean'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'total_forums_num' => mobiquo_val($total_forums_num, 'int'),
        'forums' => mobiquo_val($forums, 'array'),
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
function mark_all_as_read_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/forum_write.php');

    if (!isset($params[0])) {
        $forum_id = db_get_first_id();
    } else {
        $forum_id = intval($params[0]);
    }

    $forum_write_object = new CMSForumWrite();
    $forum_write_object->mark_forum_as_read($forum_id);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 */
function login_forum_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    warn_exit('Password protected forums not supported');
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function get_id_by_url_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    $arr = get_id_by_url($params[0]);

    if ($arr === null) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $response = mobiquo_val([
        'forum_id' => mobiquo_val(strval($arr['forum_id']), 'string'),
        'topic_id' => mobiquo_val(strval($arr['topic_id']), 'string'),
        'post_id' => mobiquo_val(strval($arr['post_id']), 'string'),
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
function get_board_stat_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/board_stats.php');

    $board_stats_object = new CMSBoardStats();
    $board_stats_result = $board_stats_object->get_board_stat();

    $response = mobiquo_val([
        'total_threads' => mobiquo_val($board_stats_result['total_threads'], 'int'),
        'total_posts' => mobiquo_val($board_stats_result['total_posts'], 'int'),
        'total_members' => mobiquo_val($board_stats_result['total_members'], 'int'),
        'active_members' => mobiquo_val($board_stats_result['active_members'], 'int'),
        'guest_online' => mobiquo_val($board_stats_result['guest_online'], 'int'),
        'total_online' => mobiquo_val($board_stats_result['total_online'], 'int'),
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
function get_forum_status_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/forum_read.php');

    $forum_ids = [];
    foreach ($params[0] as $_forum_id) {
        $forum_ids[] = intval($_forum_id);
    }

    $forum_object = new CMSForumRead();
    $_forums = $forum_object->get_forum_status($forum_ids);

    $forums = [];
    foreach ($_forums as $key => $val) {
        $forums[] = mobiquo_val([
            'forum_id' => mobiquo_val(strval($val['forum_id']), 'string'),
            'forum_name' => mobiquo_val($val['forum_name'], 'base64'),
            'logo_url' => mobiquo_val('', 'string'),
            'is_protected' => mobiquo_val(false, 'boolean'),
            'new_post' => mobiquo_val($val['new_post'], 'boolean'),
        ], 'struct');
    }

    $response = mobiquo_val([
        'forums' => mobiquo_val($forums, 'array'),
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
function get_smilies_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/forum_read.php');

    $forum_object = new CMSForumRead();
    $_smiley_categories = $forum_object->get_smilies();

    $smiley_categories = [];
    foreach ($_smiley_categories as $smiley_category => $_smilies) {
        $smilies = [];
        foreach ($_smilies as $smiley) {
            $smilies[] = mobiquo_val([
                'code' => mobiquo_val($smiley['code'], 'base64'),
                'url' => mobiquo_val($smiley['url'], 'string'),
            ], 'struct');
        }
        $smiley_categories[$smiley_category] = mobiquo_val($smilies, 'array');
    }

    $response = mobiquo_val([
        'list' => mobiquo_val($smiley_categories, 'struct'),
    ], 'struct');
    return mobiquo_response($response);
}
