<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

function get_tracker_issue_titles($ids, $version = null, $previous_version = null)
{
    if ((count($ids) == 0) && ($version === null) && ($previous_version === null)) {
        return array();
    }

    $sql = 'SELECT id,summary,view_state,(SELECT name FROM mantis_category_table c WHERE c.id=m.category_id) AS category FROM mantis_bug_table m WHERE ';

    if (empty($ids)) {
        $sql .= '1=0';
    } else {
        $_ids = @implode(',', $ids);
        $where_ids = 'id IN (' . $_ids . ')';
        $sql .= $where_ids;
    }

    if ($version !== null) {
        $where_version = 'status=80 AND fixed_in_version=\'' . db_escape_string($version) . '\'';
        $sql .= ' OR ' . $where_version;
    }

    if ($previous_version !== null) {
        $where_version = 'status=80 AND version=\'' . db_escape_string($previous_version) . '\' AND fixed_in_version=\'\' AND severity<>10';
        $sql .= ' OR ' . $where_version;
    }

    $issue_titles = array();
    $issues = $GLOBALS['SITE_DB']->query($sql);
    foreach ($issues as $issue) {
        $summary = $issue['summary'];
        $summary .= ' [' . $issue['category'] . ']';
        if ($issue['view_state'] != 10) {
            $summary .= ' [*private issue*]';
        }
        if (!array_key_exists($issue['category'], $issue_titles)) {
            $issue_titles[$issue['category']] = array();
        }
        $issue_titles[$issue['category']][$issue['id']] = $summary;
    }
    ksort($issue_titles);

    $_issue_titles = array();
    foreach ($issue_titles as $category_id => $issues) {
        foreach ($issues as $issue_id => $summary) {
            $_issue_titles['_' . strval($issue_id)] = $summary;
        }
    }

    return $_issue_titles;
}

function create_tracker_issue($version, $tracker_title, $tracker_message, $tracker_additional, $tracker_severity, $tracker_category, $tracker_project = '1')
{
    $query = "
        INSERT INTO
        `mantis_bug_text_table`
        (
            `description`,
            `steps_to_reproduce`,
            `additional_information`
        )
        VALUES
        (
            '" . db_escape_string($tracker_message) . "',
            '',
            '" . db_escape_string($tracker_additional) . "'
        )
    ";
    $text_id = $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, true, null, '', false);

    ensure_version_exists_in_tracker($version);

    $query = "
        INSERT INTO
        `mantis_bug_table`
        (
            `project_id`,
            `reporter_id`,
            `handler_id`,
            `duplicate_id`,
            `priority`,
            `severity`,
            `reproducibility`,
            `status`,
            `resolution`,
            `projection`,
            `eta`,
            `bug_text_id`,
            `os`,
            `os_build`,
            `platform`,
            `version`,
            `fixed_in_version`,
            `build`,
            `profile_id`,
            `view_state`,
            `summary`,
            `sponsorship_total`,
            `sticky`,
            `target_version`,
            `category_id`,
            `date_submitted`,
            `due_date`,
            `last_updated`
        )
        VALUES
        (
            '" . db_escape_string($tracker_project) . "',
            '" . strval(get_member()) . "',
            '" . strval(get_member()) . "',
            '0',
            '40', /* High priority */
            '" . db_escape_string($tracker_severity) . "',
            '10', /* Always reproducible */
            '80', /* Status: Resolved */
            '20', /* Resolution: Fixed */
            '10',
            '10',
            '" . strval($text_id) . "',
            '',
            '',
            '',
            '" . db_escape_string($version) . "',
            '',
            '',
            '0',
            '10',
            '" . db_escape_string($tracker_title) . "',
            '0',
            '0',
            '" . db_escape_string($version) . "',
            '" . db_escape_string($tracker_category) . "',
            '" . strval(time()) . "',
            '1',
            '" . strval(time()) . "'
        )
    ";
    return $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, true, null, '', false);
}

function update_tracker_issue($tracker_id, $version = null, $tracker_severity = null, $tracker_category = null, $tracker_project = null)
{
    ensure_version_exists_in_tracker($version);

    $query = "
        UPDATE
        `mantis_bug_table`
        SET
    ";
    if ($tracker_project !== null) {
        $query .= "
            `project_id`='" . db_escape_string($tracker_project) . "',
        ";
    }
    if (true) {
        $query .= "
            `handler_id`='" . strval(get_member()) . "',
        ";
    }
    if ($tracker_severity !== null) {
        $query .= "
            `severity`='" . db_escape_string($tracker_severity) . "',
        ";
    }
    if ($version !== null) {
        $query .= "
            `version`='" . db_escape_string($version) . "',
        ";
    }
    if ($tracker_category !== null) {
        $query .= "
            `category_id`='" . db_escape_string($tracker_category) . "',
        ";
    }
    $query .= "
            `last_updated`='" . strval(time()) . "'
        WHERE
            id=" . strval($tracker_id);
    $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, false, null, '', false);
}

function ensure_version_exists_in_tracker($version)
{
    if ($version === null) {
        return;
    }

    if ($GLOBALS['SITE_DB']->query_value_if_there('SELECT version FROM mantis_project_version_table WHERE ' . db_string_equal_to('version', $version)) === null) {
        $query = "
            INSERT INTO
            `mantis_project_version_table`
            (
                `project_id`,
                `version`,
                `description`,
                `released`,
                `obsolete`,
                `date_order`
            )
            VALUES
            (
                    1,
                    '" . db_escape_string($version) . "',
                    '',
                    1,
                    0,
                    " . strval(time()) . "
            )
        ";
        $GLOBALS['SITE_DB']->_query($query, null, null, true);
    }
}

function upload_to_tracker_issue($tracker_id, $upload)
{
    $disk_filename = md5(serialize($upload));
    $save_path = get_custom_file_base() . '/tracker/uploads/' . $disk_filename;
    move_uploaded_file($upload['tmp_name'], $save_path);
    fix_permissions($save_path);
    sync_file($save_path);

    $query = "
        INSERT INTO
        `mantis_bug_file_table`
        (
          `bug_id`,
          `title`,
          `description`,
          `diskfile`,
          `filename`,
          `folder`,
          `filesize`,
          `file_type`,
          `content`,
          `date_added`,
          `user_id`
        )
        VALUES
        (
            '" . strval($tracker_id) . "',
            '',
            '',
            '" . $disk_filename . "',
            '" . db_escape_string($upload['name']) . "',
            '" . get_custom_file_base() . "/tracker/uploads/',
            '" . strval($upload['size']) . "',
            'application/octet-stream',
            '',
            '" . strval(time()) . "',
            '" . strval(LEAD_DEVELOPER_MEMBER_ID) . "'
        )
    ";

    return $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, true, null, '', false);
}

function create_tracker_post($tracker_id, $tracker_comment_message)
{
    $query = "
        INSERT INTO
        `mantis_bugnote_text_table`
        (
          `note`
        )
        VALUES
        (
            '" . db_escape_string($tracker_comment_message) . "'
        )
    ";
    $text_id = $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, true, null, '', false);

    $monitors = $GLOBALS['SITE_DB']->query('SELECT user_id FROM mantis_bug_monitor_table WHERE bug_id=' . strval($tracker_id));
    foreach ($monitors as $m) {
        $to_name = $GLOBALS['FORUM_DRIVER']->get_username($m['user_id'], true);
        if (!is_null($to_name)) {
            $to_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($m['user_id']);

            $join_time = $GLOBALS['FORUM_DRIVER']->get_member_row_field($m['user_id'], 'm_join_time');

            require_code('mail');
            mail_wrap('Tracker issue updated', 'A tracker issue you are monitoring has been updated (' . get_base_url() . '/tracker/view.php?id=' . strval($tracker_id) . ').', array($to_email), $to_name, '', '', 3, null, false, null, false, false, false, 'MAIL', false, null, null, $join_time);
        }
    }

    $query = "
        INSERT INTO
        `mantis_bugnote_table`
        (
          `bug_id`,
          `reporter_id`,
          `bugnote_text_id`,
          `view_state`,
          `note_type`,
          `note_attr`,
          `time_tracking`,
          `last_modified`,
          `date_submitted`
        )
        VALUES
        (
            '" . strval($tracker_id) . "',
            '" . strval(LEAD_DEVELOPER_MEMBER_ID) . "',
            '" . strval($text_id) . "',
            '10', /* Public */
            '0',
            '',
            '0',
            '" . strval(time()) . "',
            '" . strval(time()) . "'
        )
    ";
    return $GLOBALS['SITE_DB']->_query($query, null, null, false, true, null, '', false);
}

function close_tracker_issue($tracker_id)
{
    $GLOBALS['SITE_DB']->query('UPDATE mantis_bug_table SET resolution=20, status=80 WHERE id=' . strval($tracker_id));
}

function get_user_currency()
{
    require_code('users');
    $return_default = false;
    $safe_currency = 'USD';
    $the_id = intval(get_member());
    $member_id = is_guest($the_id) ? mixed() : $the_id;
    if (!is_null($member_id)) {
        $cpf_id = get_credits_profile_field_id('cms_currency');
        if (!is_null($cpf_id)) {
            require_code('cns_members_action2');
            $_fields = cns_get_custom_field_mappings($member_id);
            $result = $_fields['field_' . strval($cpf_id)];
            $user_currency = !is_null($result) ? $result : null;
            $return_default = is_null($user_currency);
            if ($return_default === false) {
                if (preg_match('/^[a-zA-Z]$/', $user_currency) == 0) {
                    log_hack_attack_and_exit('HACK_ATTACK');
                }
            }
        } else {
            $return_default = true;
        }
    } else {
        $return_default = true;
    }
    $_system_currency = get_option('currency', true);
    $system_currency = is_null($_system_currency) ? $safe_currency : $_system_currency;
    return $return_default ? $system_currency : $user_currency;
}

function get_credits_profile_field_id($field_name = 'cms_support_credits')
{
    require_code('cns_members');
    if (preg_match('/\W/', $field_name)) {
        log_hack_attack_and_exit('HACK_ATTACK');
    }
    $fields = cns_get_all_custom_fields_match(
        null, // groups
        null, // public view
        null, // owner view
        null, // owner set
        null, // required
        null, // show in posts
        null, // show in post previews
        1 // special start
    );
    $field_id = null;
    foreach ($fields as $field) {
        if ($field['trans_name'] == $field_name) {
            $field_id = $field['id'];
            break;
        }
    }
    return $field_id;
}
