<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    user_sync
 */

/*EXTRA FUNCTIONS: PDO*/

function init__user_sync()
{
    global $USER_SYNC_IMPORT_LIMIT;
    $USER_SYNC_IMPORT_LIMIT = null;

    global $DO_USER_SYNC_OFFSET;
    $DO_USER_SYNC_OFFSET = 0;

    global $DO_USER_SYNC;
    $DO_USER_SYNC = true;

    global $SYNC_DELETES;
    $SYNC_DELETES = false;

    global $DO_USER_ONLY_ID;
    $DO_USER_ONLY_ID = null;

    global $PROGRESS_UPDATE_GAP;
    $PROGRESS_UPDATE_GAP = 100;
}

/*
INBOUND
*/

function user_sync__inbound($since = null)
{
    global $USER_SYNC_IMPORT_LIMIT, $DO_USER_SYNC_OFFSET, $DO_USER_SYNC, $DO_USER_ONLY_ID, $PROGRESS_UPDATE_GAP;

    cms_disable_time_limit();

    require_code('cns_members');
    require_code('cns_members_action');
    require_code('cns_members_action2');
    require_code('cns_groups');
    require_code('crypt');
    require_code('site');
    require_code('site2');
    require_code('resource_fs');

    require_code('notifications');
    global $NOTIFICATIONS_ON;
    $NOTIFICATIONS_ON = false;
    require_code('global4');
    global $ADMIN_ACTIONLOG_ON;
    $ADMIN_ACTIONLOG_ON = false;

    @ignore_user_abort(false);

    resource_fs_logging__start('inform');

    // Load import scheme
    require_code('user_sync__customise');
    list(
        $db_type,
        $db_host,
        $db_name,
        $db_user,
        $db_password,
        $db_table,

        $db_field_delim,

        $username_fields,
        $username_fields_types,
        $time_field,

        $field_remap,

        $default_password,
        $temporary_password,
        ) = get_user_sync_env();

    if (!class_exists('PDO')) {
        warn_exit('PDO must be installed.', false, true);
    }

    if (strpos($db_type, 'mysql') !== false) {
        $db_type = 'mysql';
    }

    // Connect to the database
    $connect_string = $db_type . ':host=' . $db_host . ';dbname=' . $db_name;
    $dbh = new PDO($connect_string, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    if (get_charset() == 'utf-8') {
        $dbh->exec('set names utf8');
    }

    // Customised start code
    get_user_sync__begin($dbh, $since);

    if ($DO_USER_SYNC) {
        // Work out what fields there are
        $native_fields = user_sync_find_native_fields();

        $time_start = time();
        $i = $DO_USER_SYNC_OFFSET;
        $i2 = $i;

        // Run query to gather remote data
        $sql = 'SELECT * FROM ' . $db_table . ' WHERE 1=1';
        if (($time_field !== null) && ($since !== null)) {
            $sql .= ' AND ' . $db_field_delim . $time_field . $db_field_delim . '>=' . $dbh->quote(date('Y-m-d H:i:s', $since));
        }
        if ($DO_USER_ONLY_ID !== null) {
            foreach ($username_fields as $j => $username_field) {
                $sql .= ' AND ' . $username_field . '=' . $dbh->quote(is_array($DO_USER_ONLY_ID) ? $DO_USER_ONLY_ID[$j] : $DO_USER_ONLY_ID);
            }
        }
        $sth = $dbh->query($sql . ' LIMIT ' . strval($DO_USER_SYNC_OFFSET) . ', 18446744073709551615');

        // Handle each user
        while (($user = $sth->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (($USER_SYNC_IMPORT_LIMIT !== null) && ($i2 - $DO_USER_SYNC_OFFSET >= $USER_SYNC_IMPORT_LIMIT)) {
                resource_fs_logging('Partial, got to ' . strval($i2) . ' members', 'inform');
                break;
            }
            $i2++;

            if ($i != 0 && $i % $PROGRESS_UPDATE_GAP == 0) {
                resource_fs_logging('Progress update: imported ' . strval($i) . ' members', 'inform');
            }

            // Work out username
            $username = '';
            foreach ($username_fields as $j => $username_field) {
                if ($j != 0) {
                    $username .= ' ';
                }
                $username .= is_integer($user[$username_field]) ? strval($user[$username_field]) : $user[$username_field];
            }
            //cns_check_name_valid($username, null, null, null, null, true); // Not really needed
            if ($username == '') {
                resource_fs_logging('Blank username cannot be imported.', 'warn');
                continue;
            }

            $email_address = trim(user_sync_handle_field_remap('email_address', $field_remap['email_address'], $user, $dbh, null));

            // Bind to existing?
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if (($member_id === null) && (get_option('one_per_email_address') != '0') && ($email_address != '')) {
                $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email_address);
            }

            // Work out other data
            $user_data = [];
            foreach ($native_fields as $key) {
                $user_data[$key] = user_sync_handle_field_remap($key, null, $user, $dbh, $member_id);
            }
            foreach ($field_remap as $key => $remap_scheme) {
                $user_data[$key] = user_sync_handle_field_remap($key, $remap_scheme, $user, $dbh, $member_id);
            }
            $primary_group = $user_data['primary_group'];
            $groups = $user_data['groups'];
            $dob_day = $user_data['dob_day'];
            $dob_month = $user_data['dob_month'];
            $dob_year = $user_data['dob_year'];
            $language = $user_data['language'];
            $photo_url = $user_data['photo_url'];
            $reveal_age = $user_data['reveal_age'];
            $allow_emails = $user_data['allow_emails'];
            $allow_emails_from_staff = $user_data['allow_emails_from_staff'];
            $validated = $user_data['validated'];
            $is_perm_banned = @strval($user_data['is_perm_banned']);
            $join_time = $user_data['join_time'];

            // Collate CPFs
            $custom_fields = [];
            foreach ($user_data as $key => $value) {
                if (in_array($key, $native_fields)) {
                    continue;
                }

                // Try and find a match
                $cpfs = cns_get_all_custom_fields_match();
                $cpf_id = null;
                if (is_numeric($key)) {
                    $cpf_id = intval($key);
                }
                if ($cpf_id === null) {
                    foreach ($cpfs as $cpf) {
                        if ($cpf['trans_name'] == $key) {
                            $cpf_id = $cpf['id'];
                            break;
                        }
                    }
                }
                if ($cpf_id === null) {
                    foreach ($cpfs as $cpf) {
                        if ($cpf['trans_name'] == 'cms_' . $key) {
                            $cpf_id = $cpf['id'];
                            break;
                        }
                    }
                }

                // Set it
                if ($cpf_id !== null) {
                    $cpf_value = is_string($value) ? $value : strval($value);
                    $custom_fields[$cpf_id] = $cpf_value;
                } else {
                    resource_fs_logging('Could not bind ' . $key . ' to CPF.', 'warn');
                }
            }

            // Import to standard member record
            if ($member_id === null) {
                $user_data['pass_hash_salted'] = ($user_data['pass_hash_salted'] === null) ? $default_password : $user_data['pass_hash_salted'];
                $password = ($user_data['pass_hash_salted'] === null) ? get_secure_random_password(null, $username, $email_address) : $user_data['pass_hash_salted'];
                $password_compat_scheme = $temporary_password ? 'bcrypt_temporary' : (($user_data['pass_hash_salted'] === null) ? 'plain'/*so we can find it from the DB*/ : null);

                $member_id = cns_make_member(
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
                    null, // probation_expiration_time
                    $is_perm_banned, // is_perm_banned
                    false, // check_correctness
                    '', // ip_address
                    $password_compat_scheme, // password_compatibility_scheme
                    '', // salt
                    $join_time // join_time
                );
            } else {
                // Delete?
                if (function_exists('user_sync__handle_deletion')) {
                    global $SYNC_DELETES;
                    if ($SYNC_DELETES) {
                        if (user_sync__handle_deletion($dbh, $user_data, $member_id)) {
                            continue;
                        }
                    }
                }

                // Passwords will not be re-synched
                $password = null;
                $password_compat_scheme = null;
                $salt = null;

                $old_groups = $GLOBALS['CNS_DRIVER']->get_members_groups($member_id);
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
                    '', // theme
                    '', // title
                    $photo_url, // photo_url
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
                    null, // probation_expiration_time
                    $is_perm_banned, // is_perm_banned
                    false, // check_correctness
                    $password_compat_scheme, // password_compatibility_scheme
                    $salt, // salt
                    $join_time // join_time
                );

                require_code('cns_groups_action2');
                $members_groups = $GLOBALS['CNS_DRIVER']->get_members_groups($member_id);
                foreach ($groups as $group_id) {
                    if (!in_array($group_id, $members_groups)) {
                        cns_add_member_to_secondary_group($member_id, $group_id);
                    }
                }
                foreach ($members_groups as $group_id) {
                    if ((!in_array($group_id, $groups)) && ($group_id != $primary_group)) {
                        cns_member_leave_secondary_group($group_id, $member_id);
                    }
                }
                cns_update_group_approvals($member_id, get_member(), $old_groups);
            }

            $i++;
        }
        $time_end = time();
        if ($user === false) {
            resource_fs_logging('Imported ' . strval($i - $DO_USER_SYNC_OFFSET) . ' members in ' . strval($time_end - $time_start) . ' seconds', 'notice');
        }
    }

    // Customised end code
    get_user_sync__finish($dbh, $since);

    resource_fs_logging__end();
}

function user_sync_handle_field_remap($field_name, $remap_scheme, $remote_data, $dbh, $member_id)
{
    // Specified to use default/omitted (same effect)
    if ($remap_scheme === null) {
        return user_sync_get_field_default($field_name);
    }

    // Work out what to do...
    switch ($remap_scheme[0]) {
        case 'default': // Given a default
            return $remap_scheme[1];

        case 'field': // Direct field lookup
            if (!array_key_exists(1, $remap_scheme)) {
                $remap_scheme[1] = $field_name; // Identity map, by default
            }
            if (!array_key_exists($remap_scheme[1], $remote_data)) { // Not found!
                resource_fs_logging('Requested to import missing remote field, ' . $remap_scheme[1] . '.', 'warn');
                return user_sync_get_field_default($field_name);
            }
            $remote_value = $remote_data[$remap_scheme[1]];
            $data = [$remote_value];
            break;

        case 'callback': // Callback
            $data = call_user_func($remap_scheme[1], $field_name, $remote_data, $dbh, $member_id);
            if (!is_array($data)) {
                $data = [$data];
            }
            break;

        default: // ???
            resource_fs_logging('Unknown lookup type (' . $remap_scheme[0] . ').', 'warn');
            return user_sync_get_field_default($field_name);
    }

    // Any individual value remappings?
    if (array_key_exists(2, $remap_scheme)) {
        foreach ($data as $i => $_data) {
            if (array_key_exists($_data, $remap_scheme[2])) {
                $_data = $remap_scheme[2][$_data];
                $data[$i] = $_data;
            }
        }
    }

    // Return, but what field type?
    switch ($field_name) {
        // Groups
        case 'groups':
        case 'primary_group':
            foreach ($data as $i => $_data) {
                if ((!is_integer($_data)) && ($_data !== null)) {
                    if (is_numeric($_data)) {
                        $data[$i] = intval($_data);
                    } else { // By name
                        $resource_fs_ob = get_resource_commandr_fs_object('group');
                        $data[$i] = intval($resource_fs_ob->convert_label_to_id($_data, '', 'group')); // Will be created if it doesn't already exist
                    }
                }
            }
            if ($field_name == 'primary_group') {
                return $data[0];
            }
            return $data;
        // Integers
        case 'dob_day':
        case 'dob_month':
        case 'dob_year':
            if (is_integer($data[0])) {
                return $data[0];
            }
            return ($data[0] === null) ? null : intval($data[0]);
        // Ban value
        case 'is_perm_banned':
            if ($data[0] === null) {
                return null;
            }
            $data[0] = cms_strtolower_ascii(@strval($data[0]));
            if (($data[0] == '0') || ($data[0] == 'false') || ($data[0] == 'no') || ($data[0] == 'off')) {
                return '0';
            }
            if (($data[0] == '1') || ($data[0] == 'true') || ($data[0] == 'yes') || ($data[0] == 'on')) {
                return '1';
            }
            return $data[0];
        // Timestamps
        case 'join_time':
            if (is_integer($data[0])) {
                return $data[0];
            }
            return ($data[0] === null) ? null : (is_numeric($data[0]) ? intval($data[0]) : strtotime($data[0]));
        // Binary values
        case 'validated':
        case 'reveal_age':
        case 'allow_emails':
        case 'allow_emails_from_staff':
            if ($data[0] === null) {
                return null;
            }
            if (is_string($data[0])) {
                $data[0] = cms_strtolower_ascii($data[0]);
            }
            if (($data[0] === '1') || ($data[0] === 1) || ($data[0] === 'true') || ($data[0] === 'yes') || ($data[0] === 'on') || ($data[0] === true)) {
                return 1;
            }
            return 0;
    }
    if (!is_string($data[0])) {
        if ($data[0] === null) {
            $data[0] = '';
        } else {
            $data[0] = strval($data[0]);
        }
    }
    return $data[0]; // Default, string
}

function user_sync_get_field_default($field_name)
{
    switch ($field_name) {
        case 'pass_hash_salted':
            return null;
        case 'email_address':
            return '';
        case 'primary_group':
            return get_first_default_group();
        case 'groups':
            return [];
        case 'dob_day':
            return null;
        case 'dob_month':
            return null;
        case 'dob_year':
            return null;
        case 'language':
            return null;
        case 'photo_url':
            return '';
        case 'reveal_age':
            return 0;
        case 'validated':
            return 1;
        case 'is_perm_banned':
            return '0';
        case 'allow_emails':
            return 0;
        case 'allow_emails_from_staff':
            return 1;
        case 'join_time':
            return null;
    }
    resource_fs_logging('Requested to import unknown field. ' . $field_name . '.', 'warn');
    return null; // Should not get here
}

/*
OUTBOUND
*/

function user_sync__outbound($member_id)
{
    require_code('cns_members');

    require_code('user_sync__customise');
    list(
        $db_type,
        $db_host,
        $db_name,
        $db_user,
        $db_password,
        $db_table,

        $db_field_delim,

        $username_fields,
        $username_fields_types,
        $time_field,

        $field_remap,

        $default_password,
        $temporary_password,
        ) = get_user_sync_env();

    $native_fields = user_sync_find_native_fields();

    // Details of local member
    $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
    $record = $GLOBALS['FORUM_DRIVER']->get_member_row($member_id);
    $cpf_fields = cns_get_all_custom_fields_match_member($member_id);

    // Connect to the database
    if (!class_exists('PDO')) {
        warn_exit('PDO must be installed.', false, true);
    }

    if (strpos($db_type, 'mysql') !== false) {
        $db_type = 'mysql';
    }

    $dbh = new PDO($db_type . ':host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    // Try and fetch details of remote user
    $sql = 'SELECT * FROM ' . $db_table . ' WHERE ';
    $_username_parts = [];
    foreach ($username_fields as $i => $uf) {
        if ($i != 0) {
            $_username_parts[] = $dbh->quote(' ');
        }
        $_username_parts[] = $uf;
    }
    $sql .= db_function('CONCAT', $_username_parts);
    $sql .= '=' . $dbh->quote($record['m_username']);
    $sth = $dbh->query($sql);
    $user = $sth->fetch(PDO::FETCH_ASSOC);

    if ($user !== false) { // Does exist remotely
        $remote_member_id = $user['id'];
    } else { // Does not exist remotely yet: Insert empty user, just with the username field(s)
        // Username fields
        $insert_map = [];
        $username_parts = explode(' ', $username);
        foreach ($username_fields as $i => $uf) {
            $insert_map[$uf] = [
                array_key_exists($i, $username_parts) ? $username_parts[$i] : '',
                $username_fields_types[$i],
            ];
        }

        // Go through other fields
        foreach ($field_remap as $key => $remap_scheme) {
            if ($remap_scheme === null) {
                continue; // Not actually mapped
            }

            if ($remap_scheme[0] != 'field') {
                continue; // Not a direct mapping, we can't process backwards
            }

            if (in_array($key, $native_fields)) {
                $data = $record['m_' . $key];
            } else {
                $data = $cpf_fields[$key];
            }

            // Any individual value remappings?
            if (array_key_exists(3, $remap_scheme)) {
                if (array_key_exists($data, $remap_scheme[3])) {
                    $data = $remap_scheme[3][$data];
                }
            }

            // Ready for insertion
            $insert_map[$remap_scheme[1]] = [
                $data,
                array_key_exists(4, $remap_scheme) ? $remap_scheme[4] : (is_integer($data) ? 'INTEGER' : 'VARCHAR'),
            ];
        }

        // Build up SQL
        $sql = 'INSERT INTO ' . $db_table . ' (';
        foreach (array_keys($insert_map) as $i => $key) {
            if ($i != 0) {
                $sql .= ',';
            }
            $sql .= $db_field_delim . $uf . $db_field_delim;
        }
        $sql .= ') VALUES (';
        $val = null;
        foreach (array_values($insert_map) as $i => $_val) {
            if ($i != 0) {
                $sql .= ',';
            }

            list($val, $type) = $_val;

            switch ($type) {
                case 'INTEGER':
                    $sql .= strval($val);
                    break;

                case 'DATETIME':
                    $val = date('Y-m-d H:i:s', $val);
                    $sql .= $dbh->quote($val);
                    break;

                case 'VARCHAR':
                default:
                    $sql .= $dbh->quote($val);
                    break;
            }
        }
        $sql .= ')';

        // Execute SQL
        $dbh->query($sql);
        $remote_member_id = $dbh->lastInsertId();
    }
}

function user_sync__outbound_edit($member_id)
{
    // Not currently implemented
}

function user_sync__outbound_delete($member_id)
{
    // Not currently implemented
}

/*
UTILITY FUNCTIONS
*/

function user_sync_find_native_fields()
{
    /* Actually we don't support importing them all, as our code has to choose defaults
    $native_fields = [];
    $db_meta = $GLOBALS['SITE_DB']->query_select('db_meta', ['m_name'], ['m_table' => 'f_members']);
    foreach ($db_meta as $_db_meta) {
        if (substr($_db_meta['m_name'], 0, 2) == 'm_') {
            $native_fields[] = substr($_db_meta['m_name'], 2);
        }
    }
    return $native_fields;
    */

    return [
        'pass_hash_salted',
        'email_address',
        'groups',
        'dob_day',
        'dob_month',
        'dob_year',
        'timezone_offset',
        'primary_group',
        'validated',
        'is_perm_banned',
        'reveal_age',
        'photo_url',
        'language',
        'allow_emails',
        'allow_emails_from_staff',
        'join_time',

        'groups',
    ];
}
