<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    external_db_login
 */

/*
This addon syncs/logs-Composr-in when you log-in locally for the first time, or when a remote session already exists.
It also re-syncs whenever a new Composr session is created, just in case the synched data got out of date.
Only e-mail/username/password/DOB are synched.

Lots of settings need making in the database via Commandr:

:
set_value('external_db_login__db_type', '?', true);
set_value('external_db_login__db_name', '?', true);
set_value('external_db_login__db_host', '?', true);
set_value('external_db_login__db_user', '?', true);
set_value('external_db_login__db_password', '?', true);
set_value('external_db_login__table', '?', true);
set_value('external_db_login__username_field', '?', true);
set_value('external_db_login__password_field', '?', true);
set_value('external_db_login__email_address_field', '?', true);
set_value('external_db_login__dob_day_field', '?', true); // Optional
set_value('external_db_login__dob_month_field', '?', true); // Optional
set_value('external_db_login__dob_year_field', '?', true); // Optional
set_value('external_db_login__dob_field', '?', true); // Optional
set_value('external_db_login__session_name', '?', true); // Optional (only set if you've coded up the external_db_user_from_session function)
set_value('external_join_url', '?', true); // Optional
set_value('external_lost_password_url', '?', true); // Optional
set_value('external_login_url', '?', true); // Optional
*/

/**
 * Get a connection to the external user database.
 *
 * @return ?object Database driver (null: none)
 */
function external_db() : ?object
{
    $db_type = get_value('external_db_login__db_type', null, true);
    if ($db_type === null) {
        return null;
    }
    $db_name = get_value('external_db_login__db_name', null, true);
    $db_host = get_value('external_db_login__db_host', null, true);
    $db_user = get_value('external_db_login__db_user', null, true);
    $db_password = get_value('external_db_login__db_password', null, true);

    require_code('database/' . filter_naughty_harsh($db_type));
    $db = new DatabaseConnector($db_name, $db_host, $db_user, $db_password, '', false, object_factory('Database_Static_' . filter_naughty_harsh($db_type), false, ['']));

    return $db;
}

/**
 * Find who is logged into the remote system.
 * THIS FUNCTION NEEDS CUSTOMISING. It is currently written for one particular ASP.net system.
 *
 * @return ?array User record for session (null: none)
 */
function external_db_user_from_session() : ?array
{
    $coookie_session_name = get_value('external_db_login__session_name', null, true);
    if (@cms_empty_safe($_COOKIE[$coookie_session_name])) {
        return null; // No session
    }
    $cookie = $_COOKIE[$coookie_session_name];

    $db = external_db();

    // Look for existing session. The particular system we are integrating has a ASP.net cookie session, and the ASP.net session contains the database session ID (yes, over-complex)
    cms_ini_set('allow_url_open', '1');
    $opts = [
        'http' => [
            'method' => "GET",
            'header' =>
                "Accept-language: en\r\n" .
                "Cookie: ASP.NET_SessionId=" . $cookie . "\r\n",
        ]
    ];
    $context = stream_context_create($opts);
    $url = 'https://' . $_SERVER['HTTP_HOST'] . '/DumpSession.aspx'; // Call a script we made in ASP.net, grabbing the DB session ID
    $session_id = file_get_contents($url, false, $context);
    if ($session_id == '') {
        return null;
    }
    $sql = 'SELECT u.* FROM tblUserSession s JOIN tbllogin u ON s.IDUser=u.Userid WHERE ' . db_string_equal_to('s.IDSession', $session_id);
    $records = $db->query($sql);
    return isset($records[0]) ? $records[0] : null; // If not set it's odd, remote session for a non-existent remote user
}

/**
 * Synchronise an external user.
 *
 * @param  MEMBER $member_id Authorised member
 * @param  array $record User record to sync
 */
function external_db_user_sync(int $member_id, array $record)
{
    $username_field = get_value('external_db_login__username_field', null, true);
    $password_field = get_value('external_db_login__password_field', null, true);
    $email_address_field = get_value('external_db_login__email_address_field', null, true);

    require_code('crypt');
    $salt = get_secure_random_string(32, CRYPT_BASE64);
    $new = ratchet_hash($record[$password_field], $salt);

    $update_map = [
        'm_email_address' => $record[$email_address_field],
        'm_validated_email_confirm_code' => '',
        'm_password_compat_scheme' => 'bcrypt',
        'm_password_change_code' => '',
        'm_password_change_code_time' => null,
        'm_pass_hash_salted' => $new,
        'm_pass_salt' => $salt,
    ];

    $username = $record[$username_field];
    if ($username != '') {
        $update_map['m_username'] = $username; // Ok, it's valid, so we can sync it
    }

    // Re-sync e-mail address and password, just in case it was changed in the other system
    //  NB: We have no way of synching local changes back. You could consider blocking off the members module
    //      This code has been originally written with the intent of providing a stepping stone, so we are not all that concerned about synching stuff back
    //      You could of course edit the other system to re-sync with Composr upon login

    require_code('users_active_actions');
    handle_active_logout__login_providers($member_id);

    $GLOBALS['FORUM_DB']->query_update('f_members', $update_map, ['id' => $member_id], '', 1);
}

/**
 * Import an external user.
 *
 * @param  array $record User record to import
 * @return MEMBER Authorised member
 */
function external_db_user_add(array $record) : int
{
    require_code('cns_members_action2');

    $username_field = get_value('external_db_login__username_field', null, true);
    $password_field = get_value('external_db_login__password_field', null, true);
    $email_address_field = get_value('external_db_login__email_address_field', null, true);

    $username = $record[$username_field];
    $username = process_username_discriminator($username);

    $password = $record[$password_field];
    $email_address = $record[$email_address_field];

    $dob_day_field = get_value('external_db_login__dob_day_field', null, true);
    $dob_day = null;
    $dob_month = null;
    $dob_year = null;
    if (!empty($dob_day_field)) {
        $dob_month_field = get_value('external_db_login__dob_month_field', null, true);
        $dob_year_field = get_value('external_db_login__dob_year_field', null, true);
        $dob_day = $record[$dob_day_field];
        $dob_month = $record[$dob_month_field];
        $dob_year = $record[$dob_year_field];
    } else {
        $dob_field = get_value('external_db_login__dob_field', null, true);
        if (!empty($dob_field)) {
            if ($record[$dob_field] != '') {
                list($dob_year, $dob_month, $dob_day) = explode('-', $record[$dob_field]);
            }
        }
    }

    // Ask Composr to finish off the profile
    require_lang('cns');
    require_code('cns_members');
    require_code('cns_groups');
    require_code('cns_members2');
    require_code('cns_members_action');
    $member_id = cns_member_external_linker('', $username, $password, false, $email_address, $dob_day, $dob_month, $dob_year);

    return $member_id;
}
