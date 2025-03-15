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

/**
 * Get rows of tracker issues from Mantis.
 *
 * @param  array $ids Array of tracker IDs to get
 * @param  ?ID_TEXT $version Limit to issues fixed in this version (null: do not limit)
 * @param  ?ID_TEXT $previous_version Limit to issues reported in the given version (null: do not limit)
 * @return array Array of issues found
 */
function get_tracker_issues(array $ids, ?string $version = null, ?string $previous_version = null) : array
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

/**
 * Create a new tracker issue in Mantis.
 *
 * @param  ID_TEXT $version The version in which this issue occurs
 * @param  SHORT_TEXT $tracker_title The title for this issue
 * @param  LONG_TEXT $tracker_message The description of the issue
 * @param  LONG_TEXT $tracker_additional Additional information
 * @param  integer $tracker_severity The severity identifier
 * @param  AUTO_LINK $tracker_category The issue category ID, usually an addon
 * @param  AUTO_LINK $tracker_project The project in which to file this issue
 * @param  ?MEMBER $handler_id The member handling this issue (null: current member)
 * @param  LONG_TEXT $steps_to_reproduce Steps to reproduce this issue
 * @param  integer $reproducibility Reproducibility identifier
 * @param  integer $status Issue status identifier
 * @param  integer $resolution Issue resolution identifier
 * @param  integer $view_state The identifier determining how this issue can be viewed
 * @return AUTO The issue ID
 */
function create_tracker_issue(string $version, string $tracker_title, string $tracker_message, string $tracker_additional, int $tracker_severity, int $tracker_category, int $tracker_project = 1, ?int $handler_id = null, string $steps_to_reproduce = '', int $reproducibility = 10/*always*/, int $status = 80/*resolved*/, int $resolution = 20/*fixed*/, int $view_state = 10/*public*/) : int
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
    $text_id = $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, true);

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
            '" . db_escape_string(strval($tracker_project)) . "',
            '" . strval(get_member()) . "',
            '" . db_escape_string(strval($handler_id)) . "',
            '0',
            '40', /* High priority */
            '" . db_escape_string(strval($tracker_severity)) . "',
            '" . db_escape_string(strval($reproducibility)) . "',
            '" . db_escape_string(strval($status)) . "',
            '" . db_escape_string(strval($resolution)) . "',
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
            '" . db_escape_string(strval($tracker_category)) . "',
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

/**
 * Update details on a Mantis tracker issue (and set the handler to the current member).
 *
 * @param  AUTO_LINK $tracker_id The tracker issue we are editing
 * @param  ?ID_TEXT $version The issue reported version (null: do not change)
 * @param  ?integer $tracker_severity The severity identifier (null: do not change)
 * @param  ?AUTO_LINK $tracker_category The category / addon (null: do not change)
 * @param  ?AUTO_LINK $tracker_project The issue project (null: do not change)
 */
function update_tracker_issue(int $tracker_id, ?string $version = null, ?int $tracker_severity = null, ?int $tracker_category = null, ?int $tracker_project = null)
{
    ensure_version_exists_in_tracker($version);

    $query = "
        UPDATE
        `mantis_bug_table`
        SET
    ";
    if ($tracker_project !== null) {
        $query .= "
            `project_id`='" . db_escape_string(strval($tracker_project)) . "',
        ";
    }
    if (true) {
        $query .= "
            `handler_id`='" . strval(get_member()) . "',
        ";
    }
    if ($tracker_severity !== null) {
        $query .= "
            `severity`='" . db_escape_string(strval($tracker_severity)) . "',
        ";
    }
    if ($version !== null) {
        $query .= "
            `version`='" . db_escape_string($version) . "',
        ";
    }
    if ($tracker_category !== null) {
        $query .= "
            `category_id`='" . db_escape_string(strval($tracker_category)) . "',
        ";
    }
    $query .= "
            `last_updated`='" . strval(time()) . "'
        WHERE
            id=" . strval($tracker_id);
    $GLOBALS['SITE_DB']->_query(trim($query), null, 0, false, false, null, '', false);
}

/**
 * Make sure the given version exists in Mantis.
 *
 * @param  ?ID_TEXT $version The version to check and create if it does not exist (null: do not check anything)
 */
function ensure_version_exists_in_tracker(?string $version)
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

/**
 * Upload a file to a tracker issue in Mantis.
 *
 * @param  AUTO_LINK $tracker_id The tracker ID on which to upload a file
 * @param  mixed $upload The file resource to upload
 * @return AUTO The file ID
 */
function upload_to_tracker_issue(int $tracker_id, $upload) : int
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

/**
 * Create a bug note on a Mantis tracker issue.
 *
 * @param  AUTO_LINK $tracker_id The tracker ID on which to post the note
 * @param  LONG_TEXT $tracker_comment_message The message to post
 * @return AUTO The bugnote ID
 */
function create_tracker_post(int $tracker_id, string $tracker_comment_message) : int
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
            dispatch_mail('Tracker issue updated', 'A tracker issue you are monitoring has been updated (' . get_base_url() . '/tracker/view.php?id=' . strval($tracker_id) . ').', '', [$to_email], $to_name, '', '', ['require_recipient_valid_since' => $join_time]);
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

/**
 * Mark a Mantis tracker issue as resolved, and award points where applicable.
 *
 * @param  AUTO_LINK $tracker_id The tracker issue to resolve
 * @param  ?MEMBER $handler The member who resolved the issue (null: current member)
 */
function resolve_tracker_issue(int $tracker_id, ?int $handler = null)
{
    if ($handler === null) {
        $handler = get_member();
    }

    $GLOBALS['SITE_DB']->query('UPDATE mantis_bug_table SET resolution=20, status=80, handler_id=' . strval($handler) . ' WHERE id=' . strval($tracker_id));

    if (addon_installed('points')) {
        if (addon_installed('cms_homesite')) {
            require_code('points_escrow__sponsorship');
            escrow_complete_all_sponsorships($tracker_id, $handler);
        }

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
