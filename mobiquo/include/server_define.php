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

/*CQC: No check*/

if (!defined('IN_MOBIQUO') && !function_exists('get_base_url')) {
    exit('May not call this directly');
}

global $SERVER_DEFINE;

global $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

/*
Standard file taken from other implementation.

Modified so that old endpoints are commented out (because our mobiquo_common code checks it all).

Added some ones which were missing at the bottom.

Fixed method signatures, which had many issues.

Removed docstirng lines (didn't want to make them consistent).

Reformatted code in PhpStorm.
*/

/*
Info...

A signature is a description of a method's return type and its parameter types. A method may have more than one signature.

Within a server's dispatch map, each method has an array of possible signatures. Each signature is an array of types. The first entry is the return type. For instance, the method
*/

$SERVER_DEFINE = [
    'login' => [
        'function' => 'login_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean, $xmlrpcString], // 4th parameter is unused but passed by client
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'sign_in' => [
        'function' => 'sign_in_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'register' => [
        'function' => 'register_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64]],
    ],

    'ignore_user' => [
        'function' => 'ignore_user_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],

    'forget_password' => [
        'function' => 'forget_password_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'update_password' => [
        'function' => 'update_password_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcString, $xmlrpcString]],
    ],

    'update_email' => [
        'function' => 'update_email_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64]],
    ],

    'get_forum' => [
        'function' => 'get_forum_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcBoolean, $xmlrpcString]],
    ],

    'get_board_stat' => [
        'function' => 'get_board_stat_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_topic' => [
        'function' => 'get_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],

    'get_thread' => [
        'function' => 'get_thread_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],
    'get_thread_by_unread' => [
        'function' => 'get_thread_by_unread_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],
    'get_thread_by_post' => [
        'function' => 'get_thread_by_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],

    'get_recommended_user' => [
        'function' => 'get_recommended_user_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct]],
    ],

    'search_user' => [
        'function' => 'search_user_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'mark_conversation_unread' => [
        'function' => 'mark_conversation_unread_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],
    'mark_conversation_read' => [
        'function' => 'mark_conversation_read_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct]],
    ],

    'get_raw_post' => [
        'function' => 'get_raw_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'save_raw_post' => [
        'function' => 'save_raw_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean, $xmlrpcArray, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBoolean, $xmlrpcArray, $xmlrpcString, $xmlrpcBase64]],
    ],

    'get_quote_post' => [
        'function' => 'get_quote_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'get_user_topic' => [
        'function' => 'get_user_topic_func',
        'signature' => [[$xmlrpcArray, $xmlrpcBase64],
                             [$xmlrpcArray, $xmlrpcBase64, $xmlrpcString]],
    ],

    'get_user_reply_post' => [
        'function' => 'get_user_reply_post_func',
        'signature' => [[$xmlrpcArray, $xmlrpcBase64],
                             [$xmlrpcArray, $xmlrpcBase64, $xmlrpcString]],
    ],

    /*'get_new_topic' => array( NO LONGER EXISTS
        'function' => 'get_new_topic_func',
        'signature' => [array($xmlrpcStruct],
                                [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]),
    ),*/

    'get_latest_topic' => [
        'function' => 'get_latest_topic_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString, $xmlrpcStruct]],
    ],

    'get_unread_topic' => [
        'function' => 'get_unread_topic_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString, $xmlrpcArray]],
    ],

    'get_subscribed_topic' => [
        'function' => 'get_subscribed_topic_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],
    'get_subscribed_forum' => [
        'function' => 'get_subscribed_forum_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_user_info' => [
        'function' => 'get_user_info_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcString]],
    ],

    'get_config' => [
        'function' => 'get_config_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'logout_user' => [
        'function' => 'logout_user_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'new_topic' => [
        'function' => 'new_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString, $xmlrpcArray, $xmlrpcString]],
    ],

    'reply_post' => [
        'function' => 'reply_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString, $xmlrpcBoolean]],
    ],

    /*'reply_topic' => array(   NO LONGER EXISTS
        'function' => 'reply_topic_func',
            'signature' => [array($xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcString],
                               [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcBase64],
                               [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                               [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                               [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString],
                               [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString, $xmlrpcBoolean]),
    ),*/

    'subscribe_topic' => [
        'function' => 'subscribe_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],
    'unsubscribe_topic' => [
        'function' => 'unsubscribe_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'subscribe_forum' => [
        'function' => 'subscribe_forum_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],
    'unsubscribe_forum' => [
        'function' => 'unsubscribe_forum_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'get_inbox_stat' => [
        'function' => 'get_inbox_stat_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_conversations' => [
        'function' => 'get_conversations_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],

    'get_conversation' => [
        'function' => 'get_conversation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt, $xmlrpcBoolean]],
    ],

    'get_online_users' => [
        'function' => 'get_online_users_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt, $xmlrpcString, $xmlrpcString]],
    ],

    'mark_all_as_read' => [
        'function' => 'mark_all_as_read_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],

    'search' => [
        'function' => 'search_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcStruct]],
    ],
    'search_topic' => [
        'function' => 'search_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],
    'search_post' => [
        'function' => 'search_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'get_participated_topic' => [
        'function' => 'get_participated_topic_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcInt, $xmlrpcString, $xmlrpcString]],
    ],

    'login_forum' => [
        'function' => 'login_forum_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64]],
    ],

    'invite_participant' => [
        'function' => 'invite_participant_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcArray, $xmlrpcString, $xmlrpcBase64]],
    ],

    'new_conversation' => [
        'function' => 'new_conversation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString]],
    ],

    'reply_conversation' => [
        'function' => 'reply_conversation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcArray, $xmlrpcString]],
    ],

    'get_quote_conversation' => [
        'function' => 'get_quote_conversation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'delete_conversation' => [
        'function' => 'delete_conversation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],

    'report_post' => [
        'function' => 'report_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64],
        ],
    ],
    'report_pm' => [
        'function' => 'report_pm_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64],
        ],
    ],

    'upload_attach' => [
        'function' => 'upload_attach_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    /*'set_avatar' => array(    NO LONGER EXISTS
        'function' => 'upload_avatar_func',
        'signature' => [array($xmlrpcStruct]),
    ),*/

    'upload_avatar' => [
        'function' => 'upload_avatar_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_id_by_url' => [
        'function' => 'get_id_by_url_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    /*'authorize_user' => array(    NO LONGER EXISTS
        'function' =>'authorize_user_func',
        'signature' => [array($xmlrpcStruct,$xmlrpcBase64,$xmlrpcString],
                                 [$xmlrpcStruct,$xmlrpcBase64,$xmlrpcBase64],
        ),
    ),*/

    'remove_attachment' => [
        'function' => 'remove_attachment_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString]],
    ],

    'm_stick_topic' => [
        'function' => 'm_stick_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],

    'm_close_topic' => [
        'function' => 'm_close_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],

    'm_delete_topic' => [
        'function' => 'm_delete_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],
    'm_delete_post' => [
        'function' => 'm_delete_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcString]],
    ],

    'm_undelete_topic' => [
        'function' => 'm_undelete_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64],
        ],
    ],
    'm_undelete_post' => [
        'function' => 'm_undelete_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64],
        ],
    ],

    /*'m_delete_post_by_user' => array( NO LONGER EXISTS
        'function' => 'm_delete_post_by_user_func',
        'signature' => [array($xmlrpcStruct,$xmlrpcString,$xmlrpcBase64]),
    ),*/

    'm_move_topic' => [
        'function' => 'm_move_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean],
        ],
    ],

    'm_rename_topic' => [
        'function' => 'm_rename_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcBase64]],
    ],

    'm_move_post' => [
        'function' => 'm_move_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBase64, $xmlrpcString],
        ],
    ],

    'm_merge_topic' => [
        'function' => 'm_merge_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean]],
    ],

    'm_get_moderate_topic' => [
        'function' => 'm_get_moderate_topic_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],
    'm_get_moderate_post' => [
        'function' => 'm_get_moderate_post_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],

    'm_approve_topic' => [
        'function' => 'm_approve_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],
    'm_approve_post' => [
        'function' => 'm_approve_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],

    'm_ban_user' => [
        'function' => 'm_ban_user_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcBase64, $xmlrpcInt, $xmlrpcBase64, $xmlrpcInt]],
    ],

    'm_get_report_post' => [
        'function' => 'm_get_report_post_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],

    'reset_push_slug' => [
        'function' => 'reset_push_slug_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcStruct, $xmlrpcBase64, $xmlrpcBase64]],
    ],

    'get_alert' => [
        'function' => 'get_alert_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcInt],
                             [$xmlrpcStruct]],
    ],

    'prefetch_account' => [
        'function' => 'prefetch_account_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'm_unban_user' => [
        'function' => 'm_unban_user_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'm_close_report' => [
        'function' => 'm_close_report_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'update_signature' => [
        'function' => 'update_signature_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcBase64]],
    ],

    'get_topic_participants' => [
        'function' => 'get_topic_participants_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcInt]],
    ],

    /*'activate_account' => array(  NO LONGER EXISTS
          'function' => 'activate_account_func',
          'signature' => [[$xmlrpcStruct, $xmlrpcBase64, $xmlrpcString, $xmlrpcString]],
     ),*/

    'set_api_key' => [
        'function' => 'set_api_key_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'verify_connection' => [
        'function' => 'verify_connection_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'sync_user' => [
        'function' => 'sync_user_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_contact' => [
        'function' => 'get_contact_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'user_subscription' => [
        'function' => 'user_subscription_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'push_content_check' => [
        'function' => 'push_content_check_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    // Added missing ones...

    'get_participated_forum' => [
        'function' => 'get_participated_forum_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_forum_status' => [
        'function' => 'get_forum_status_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray]],
    ],

    'get_smilies' => [
        'function' => 'get_smilies_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'login_mod' => [
        'function' => 'login_mod_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'm_merge_post' => [
        'function' => 'm_merge_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'm_get_delete_topic' => [
        'function' => 'm_get_delete_topic_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],
    'm_get_delete_post' => [
        'function' => 'm_get_delete_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt]],
    ],

    'm_mark_as_spam' => [
        'function' => 'm_mark_as_spam_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'create_message' => [
        'function' => 'create_message_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64],
                             [$xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcInt],
                             [$xmlrpcStruct, $xmlrpcArray, $xmlrpcBase64, $xmlrpcBase64, $xmlrpcInt, $xmlrpcString],
        ],
    ],

    'get_box_info' => [
        'function' => 'get_box_info_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'get_box' => [
        'function' => 'get_box_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString],
                             [$xmlrpcStruct, $xmlrpcString, $xmlrpcInt, $xmlrpcInt],
        ],
    ],

    'get_message' => [
        'function' => 'get_message_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean]],
    ],

    'get_quote_pm' => [
        'function' => 'get_quote_pm_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'delete_message' => [
        'function' => 'delete_message_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'mark_pm_unread' => [
        'function' => 'mark_pm_unread_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcString],
        ],
    ],
    'mark_pm_read' => [
        'function' => 'mark_pm_read_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcString],
        ],
    ],

    'thank_post' => [
        'function' => 'thank_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'follow' => [
        'function' => 'follow_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],
    'unfollow' => [
        'function' => 'unfollow_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'like_post' => [
        'function' => 'like_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],
    'unlike_post' => [
        'function' => 'unlike_post_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString]],
    ],

    'get_dashboard' => [
        'function' => 'get_dashboard_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcBoolean],
        ],
    ],

    'get_feed' => [
        'function' => 'get_feed_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcString],
        ],
    ],

    'get_following' => [
        'function' => 'get_following_func',
        'signature' => [[$xmlrpcStruct]],
    ],
    'get_follower' => [
        'function' => 'get_follower_func',
        'signature' => [[$xmlrpcStruct]],
    ],

    'set_reputation' => [
        'function' => 'set_reputation_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],

    'get_activity' => [
        'function' => 'get_activity_func',
        'signature' => [[$xmlrpcStruct],
                             [$xmlrpcStruct, $xmlrpcInt, $xmlrpcInt],
        ],
    ],

    'mark_topic_read' => [
        'function' => 'mark_topic_read_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray]],
    ],

    'get_topic_status' => [
        'function' => 'get_topic_status_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcArray]],
    ],

    // Added by us, as will be needed in future by official client. Wanted to give a good starting example

    'get_real_url' => [
        'function' => 'get_real_url_func',
        'signature' => [[$xmlrpcStruct, $xmlrpcString, $xmlrpcString]],
    ],
];

// We don't want to define signatures outside dev-mode, because parameters may be added in the mobile clients over time
if (!$GLOBALS['DEV_MODE']) {
    foreach ($SERVER_DEFINE as $endpoint => $details) {
        unset($details['signature']);
    }
}
