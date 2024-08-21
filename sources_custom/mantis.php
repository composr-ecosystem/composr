<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

function get_tracker_issues($ids, $version = null, $previous_version = null)
{
    if ((empty($ids)) && ($version === null) && ($previous_version === null)) {
        return [];
    }

    $sql = 'SELECT id,summary,view_state,(SELECT username FROM mantis_user_table u WHERE u.id=m.reporter_id) AS reporter,(SELECT username FROM mantis_user_table u WHERE u.id=m.handler_id) AS handler,(SELECT name FROM mantis_category_table c WHERE c.id=m.category_id) AS category FROM mantis_bug_table m WHERE ';

    if (empty($ids)) {
        $sql .= '1=0';
    } else {
        $_ids = @implode(',', $ids);
        $where_ids = 'id IN (' . $_ids . ')';
        $sql .= $where_ids;
    }

    if ($version !== null) {
        $where_version = 'status=80 AND ' . db_string_equal_to('fixed_in_version', $version);
        $sql .= ' OR ' . $where_version;
    }

    if ($previous_version !== null) {
        $where_version = 'status=80 AND ' . db_string_equal_to('version', $previous_version) . ' AND ' . db_string_equal_to('fixed_in_version', '') . ' AND severity<>10';
        $sql .= ' OR ' . $where_version;
    }

    $issue_titles = [];
    $issues = $GLOBALS['SITE_DB']->query($sql);
    foreach ($issues as $issue) {
        $summary = $issue['summary'];
        $summary .= ' [' . $issue['category'] . ']';
        if ($issue['view_state'] != 10) {
            $summary .= ' [*private issue*]';
        }
        if (!array_key_exists($issue['category'], $issue_titles)) {
            $issue_titles[$issue['category']] = [];
        }
        $reporter = $issue['reporter'];
        $handler = $issue['handler'];
        $issue_titles[$issue['category']][$issue['id']] = [$summary, $reporter, $handler];

    }
    ksort($issue_titles);

    $_issue_titles = [];
    foreach ($issue_titles as $category_id => $issues) {
        foreach ($issues as $issue_id => $data) {
            $_issue_titles['_' . strval($issue_id)] = $data;
        }
    }

    return $_issue_titles;
}

function create_tracker_issue($version, $tracker_title, $tracker_message, $tracker_additional, $tracker_severity, $tracker_category, $tracker_project = '1', $handler_id = null, $steps_to_reproduce = '', $reproducibility = '10'/*always*/, $status = '80'/*resolved*/, $resolution = '20'/*fixed*/, $view_state = '10'/*public*/)
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
            '" . db_escape_string($steps_to_reproduce) . "',
            '" . db_escape_string($tracker_additional) . "'
        )
    ";
    $text_id = $GLOBALS['SITE_DB']->_query(trim($query), null, null, false, true, null, '', false);

    ensure_version_exists_in_tracker($version);

    if ($handler_id === null) {
        $handler_id = strval(get_member());
    }

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
            '" . db_escape_string($handler_id) . "',
            '0',
            '40', /* High priority */
            '" . db_escape_string($tracker_severity) . "',
            '" . db_escape_string($reproducibility) . "',
            '" . db_escape_string($status) . "',
            '" . db_escape_string($resolution) . "',
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
            '" . db_escape_string($view_state) . "',
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
    $ret = $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, true, null, '', false);

    // We need to send out our own e-mail notifications for the issue because Mantis won't do that (we're using the database directly)
    require_code('notifications');
    dispatch_notification(
        'tracker_issue_added',
        null,
        'New tracker issue: ' . $tracker_title,
        'A new tracker issue has been reported through the report issue wizard, [url="issue #' . strval($ret) . '"]' . get_base_url() . '/tracker/view.php?id=' . strval($ret) . '[/url]' . "\n\n" . 'Subject: ' . comcode_escape($tracker_title) . "\n\n" . 'Message: ' . comcode_escape($tracker_message)
    );

    return $ret;
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
    $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, false, null, '', false);
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
        $GLOBALS['SITE_DB']->_query($query, null, 0, true);
    }
}

function upload_to_tracker_issue($tracker_id, $upload)
{
    $out = new Tempcode();
    if (!addon_installed__messaged('cms_homesite', $out)) {
        warn_exit($out);
    }

    require_code('cms_homesite');

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

    return $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, true, null, '', false);
}

function create_tracker_post($tracker_id, $tracker_comment_message)
{
    $out = new Tempcode();
    if (!addon_installed__messaged('cms_homesite', $out)) {
        warn_exit($out);
    }

    require_code('cms_homesite');

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
    $text_id = $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, true, null, '', false);

    $monitors = $GLOBALS['SITE_DB']->query('SELECT user_id FROM mantis_bug_monitor_table WHERE bug_id=' . strval($tracker_id));
    foreach ($monitors as $m) {
        $to_name = $GLOBALS['FORUM_DRIVER']->get_username($m['user_id'], true, USERNAME_DEFAULT_NULL);
        if ($to_name !== null) {
            $to_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($m['user_id']);

            $join_time = $GLOBALS['FORUM_DRIVER']->get_member_row_field($m['user_id'], 'm_join_time');

            require_code('mail');
            dispatch_mail('Tracker issue updated', 'A tracker issue you are monitoring has been updated (' . get_base_url() . '/tracker/view.php?id=' . strval($tracker_id) . ').', [$to_email], $to_name, '', '', ['require_recipient_valid_since' => $join_time]);
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
    return $GLOBALS['SITE_DB']->_query($query, null, 0, false, true, null, '', false);
}

function resolve_tracker_issue($tracker_id, $handler = null)
{
    if ($handler === null) {
        $handler = get_member();
    }

    $GLOBALS['SITE_DB']->query('UPDATE mantis_bug_table SET resolution=20, status=80, handler_id=' . strval($handler) . ' WHERE id=' . strval($tracker_id));

    if (addon_installed('points')) {
        require_code('points_escrow__sponsorship');
        escrow_complete_all_sponsorships($tracker_id, $handler);

        $reporter = $GLOBALS['SITE_DB']->query_value_if_there('SELECT reporter_id FROM mantis_bug_table WHERE id=' . strval($tracker_id));
        if ($reporter !== null) {
            award_tracker_points($tracker_id, $reporter, $handler);
        }
    }
}

/**
 * Award points for a resolved tracker issue.
 * This will undo previous transactions for the same issue if they exist (treated as an edit).
 *
 * @param  AUTO_LINK $bug_id The issue that was resolved
 * @param  MEMBER $reporter The member who reported the issue
 * @param  MEMBER $handler The member who resolved the issue
 * @return boolean Whether the operation was successful
 */
function award_tracker_points(int $bug_id, int $reporter, int $handler) : bool
{
    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }

    require_code('points2');

    $ret = true;
    $points = 25; // FUDGE

    points_transactions_reverse_all(true, null, null, 'tracker_issue', '', strval($bug_id));

    $id = points_credit_member($handler, 'Resolved tracker issue #' . strval($bug_id), $points, 0, true, 0, 'tracker_issue', 'resolve', strval($bug_id));
    if ($id === null) {
        $ret = false;
    }

    if (($reporter > 0) && !is_guest($reporter)) {
        $id = points_credit_member($reporter, 'Reported resolved tracker issue #' . strval($bug_id), $points, 0, true, 0, 'tracker_issue', 'report_resolved', strval($bug_id));
        if ($id === null) {
            $ret = false;
        }
    }

    return $ret;
}

/**
 * Undo points awarded on a tracker issue.
 * This does not undo sponsorships.
 *
 * @param  AUTO_LINK $bug_id The tracker issue
 */
function reverse_tracker_points(int $bug_id)
{
    if (!addon_installed('points')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
    }

    require_code('points2');

    points_transactions_reverse_all(true, null, null, 'tracker_issue', '', strval($bug_id));
}
