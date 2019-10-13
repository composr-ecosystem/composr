<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    user_simple_csv_sync
 */

function init__user_import()
{
    define('USER_IMPORT_ENABLED', false);
    define('USER_IMPORT_MINUTES', 60 * 24);

    define('USER_IMPORT_TEST_MODE', false);

    define('USER_IMPORT_DELIM', ',');

    define('USER_IMPORT_MATCH_KEY', 'id'); // defined in terms of the local key

    define('USER_IMPORT_URL', get_base_url() . '/data_custom/modules/user_export/in.csv'); // Can be remote, we do an HTTP download to the path below (even if local)...
    define('USER_IMPORT_TEMP_PATH', 'data_custom/modules/user_export/in.csv');

    global $USER_IMPORT_WANTED;
    $USER_IMPORT_WANTED = array(
        // LOCAL => REMOTE
        'id' => 'Composr member ID',
        'm_username' => 'Username',
        'm_email_address' => 'E-mail address',
    );
}

function do_user_import()
{
    header('X-Robots-Tag: noindex');

    cms_disable_time_limit();

    if (!USER_IMPORT_TEST_MODE) {
        require_code('files');
        cms_ini_set('auto_detect_line_endings', '1'); // TODO: Remove with #3032
        $infile = cms_fopen_wb_bom(get_custom_file_base() . '/' . USER_IMPORT_TEMP_PATH);
        $test = http_get_contents(USER_IMPORT_URL, array('trigger_error' => false, 'write_to_file' => $infile)); // TODO #3467
        fclose($infile);
        if ($test === null) {
            return;
        }
        $infile = fopen(get_custom_file_base() . '/' . USER_IMPORT_TEMP_PATH, 'rb');
        // TODO: #3467
        // TODO: #3032
    } else {
        cms_ini_set('auto_detect_line_endings', '1'); // TODO: Remove with #3032
        $infile = fopen(get_custom_file_base() . '/' . USER_IMPORT_TEMP_PATH, 'rb');
        // TODO: #3467
        // TODO: #3032
    }

    require_code('cns_members_action');
    require_code('cns_members_action2');
    require_code('cns_members');

    global $USER_IMPORT_WANTED;
    $header_row = fgetcsv($infile, 0, USER_IMPORT_DELIM);
    foreach ($USER_IMPORT_WANTED as $local_key => $remote_key) {
        $remote_index = array_search($remote_key, $header_row);
        if ($remote_index !== false) {
            $USER_IMPORT_WANTED[$local_key] = $remote_index;
        } else {
            fatal_exit('Could not find the ' . $remote_key . ' field.');
        }
    }

    $cpf_ids = array();
    $fields_to_show = cns_get_all_custom_fields_match();
    foreach ($fields_to_show as $field_to_show) {
        $cpf_ids[$field_to_show['trans_name']] = $field_to_show['id'];
    }

    do {
        $row = fgetcsv($infile, 0, USER_IMPORT_DELIM);
        if ($row !== false) {
            // Match to ID
            $remote_match_key_value = $row[$USER_IMPORT_WANTED[USER_IMPORT_MATCH_KEY]];
            if ($remote_match_key_value == '') {
                continue; // No key, and it's not a good idea for us to try to match to a blank value
            }
            if ((substr(USER_IMPORT_MATCH_KEY, 0, 2) != 'm_') && (USER_IMPORT_MATCH_KEY != 'id')) {
                $cpf_id = $cpf_ids[USER_IMPORT_MATCH_KEY];
                $member_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_member_custom_fields', 'mf_member_id', array('field_' . strval($cpf_id) => $remote_match_key_value));
            } else {
                $member_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array(USER_IMPORT_MATCH_KEY => $remote_match_key_value));
            }

            // Find data
            $username = isset($USER_IMPORT_WANTED['m_username']) ? $row[$USER_IMPORT_WANTED['m_username']] : null;
            $password = isset($USER_IMPORT_WANTED['m_password']) ? $row[$USER_IMPORT_WANTED['m_password']] : null;
            $email_address = isset($USER_IMPORT_WANTED['m_email_address']) ? $row[$USER_IMPORT_WANTED['m_email_address']] : null;
            $primary_group = isset($USER_IMPORT_WANTED['m_primary_group']) ? $row[$USER_IMPORT_WANTED['m_primary_group']] : null;
            $groups = isset($USER_IMPORT_WANTED['groups']) ? array_map('intval', explode(',', $row[$USER_IMPORT_WANTED['groups']])) : null;
            $dob_day = isset($USER_IMPORT_WANTED['m_dob_day']) ? $row[$USER_IMPORT_WANTED['m_dob_day']] : null;
            $dob_month = isset($USER_IMPORT_WANTED['m_dob_month']) ? $row[$USER_IMPORT_WANTED['m_dob_month']] : null;
            $dob_year = isset($USER_IMPORT_WANTED['m_dob_year']) ? $row[$USER_IMPORT_WANTED['m_dob_year']] : null;
            $custom_fields = array();
            foreach ($USER_IMPORT_WANTED as $local_key => $remote_index) {
                if ((substr($local_key, 0, 2) != 'm_') && ($local_key != 'id')) {
                    $custom_fields[$cpf_ids[$local_key]] = $row[$remote_index];
                }
            }
            $language = isset($USER_IMPORT_WANTED['m_language']) ? $row[$USER_IMPORT_WANTED['m_language']] : null;
            $photo_url = isset($USER_IMPORT_WANTED['m_photo_url']) ? $row[$USER_IMPORT_WANTED['m_photo_url']] : null;
            $reveal_age = isset($USER_IMPORT_WANTED['m_reveal_age']) ? $row[$USER_IMPORT_WANTED['m_reveal_age']] : 1;
            $allow_emails = isset($USER_IMPORT_WANTED['m_allow_emails']) ? $row[$USER_IMPORT_WANTED['m_allow_emails']] : 1;
            $allow_emails_from_staff = isset($USER_IMPORT_WANTED['m_allow_emails_from_staff']) ? $row[$USER_IMPORT_WANTED['m_allow_emails_from_staff']] : 1;
            $validated = isset($USER_IMPORT_WANTED['m_validated']) ? $row[$USER_IMPORT_WANTED['m_validated']] : 1;
            $is_perm_banned = isset($USER_IMPORT_WANTED['m_is_perm_banned']) ? $row[$USER_IMPORT_WANTED['m_is_perm_banned']] : 0;

            if ($member_id === null) {
                if ($username !== null) {
                    // Add
                    if ($password === null) {
                        require_code('crypt');
                        $password = get_secure_random_string();
                    }
                    cns_make_member(
                        $username, // username
                        $password, // password
                        $email_address, // email_address
                        $primary_group, // primary_group
                        $groups, // secondary_groups
                        $dob_day, // dob_day
                        $dob_month, // dob_month
                        $dob_year, // dob_year
                        $custom_fields, // custom_fields
                        null, // timezone
                        $language, // language
                        '', // theme
                        '', // title
                        $photo_url, // photo_url
                        '', // photo_thumb_url
                        null, // avatar_url
                        '', // signature
                        null, // preview_posts
                        $reveal_age, // reveal_age
                        1, // views_signatures
                        null, // auto_monitor_contrib_content
                        null, // smart_topic_notification
                        null, // mailing_list_style
                        1, // auto_mark_read
                        null, // sound_enabled
                        $allow_emails, // allow_emails
                        $allow_emails_from_staff, // allow_emails_from_staff
                        0, // highlighted_name
                        '*', // pt_allow
                        '', // pt_rules_text
                        $validated, // validated
                        '', // validated_email_confirm_code
                        null, // on_probation_until
                        $is_perm_banned, // is_perm_banned
                        false, // check_correctness
                        '', // ip_address
                        'plain', // password_compatibility_scheme
                        '', // salt
                        null // join_time
                    );
                }
            } else {
                // Edit
                cns_edit_member(
                    $member_id, // member_id
                    $username, // username
                    $password, // password
                    $email_address, // email_address
                    $primary_group, // primary_group
                    $dob_day, // dob_day
                    $dob_month, // dob_month
                    $dob_year, // dob_year
                    $custom_fields, // custom_fields
                    null, // timezone
                    $language, // language
                    null, // theme
                    null, // title
                    $photo_url, // photo_url
                    null, // photo_thumb_url
                    null, // avatar_url
                    null, // signature
                    null, // preview_posts
                    $reveal_age, // reveal_age
                    null, // views_signatures
                    null, // auto_monitor_contrib_content
                    null, // smart_topic_notification
                    null, // mailing_list_style
                    null, // auto_mark_read
                    null, // sound_enabled
                    $allow_emails, // allow_emails
                    $allow_emails_from_staff, // allow_emails_from_staff
                    null, // highlighted_name
                    null, // pt_allow
                    null, // pt_rules_text
                    $validated, // validated
                    null, // on_probation_until
                    $is_perm_banned, // is_perm_banned
                    false // check_correctness
                );
                require_code('cns_groups_action2');
                if ($groups !== null) {
                    $members_groups = $GLOBALS['CNS_DRIVER']->get_members_groups($member_id);
                    foreach ($groups as $group_id) {
                        if (!in_array($group_id, $members_groups)) {
                            cns_add_member_to_group($member_id, $group_id);
                        }
                    }
                    foreach ($members_groups as $group_id) {
                        if (!in_array($group_id, $groups)) {
                            cns_member_leave_group($group_id, $member_id);
                        }
                    }
                }
            }
        }
    } while ($row !== false);

    fclose($infile);
}
