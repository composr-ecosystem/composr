<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    user_simple_spreadsheet_sync
 */

function init__user_export()
{
    define('USER_EXPORT_ENABLED', false);
    define('USER_EXPORT_MINUTES', 60 * 24);

    define('USER_EXPORT_PATH', 'data_custom/modules/user_export/out.csv');

    define('USER_EXPORT_IPC_AUTO_REEXPORT', false);
    define('USER_EXPORT_IPC_URL_EDIT', null); // add or edit
    define('USER_EXPORT_IPC_URL_DELETE', null);
    define('USER_EXPORT_EMAIL', null);

    global $USER_EXPORT_WANTED;
    $USER_EXPORT_WANTED = [
        // LOCAL => REMOTE
        'id' => 'Composr member ID',
        'm_username' => 'Username',
        'm_email_address' => 'E-mail address',
    ];
}

function do_user_export($to_file = true)
{
    @header('X-Robots-Tag: noindex');

    cms_disable_time_limit();

    if (!$to_file) {
        if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
            access_denied('ADMIN_ONLY');
        }
    }

    $outfile_path = USER_EXPORT_PATH;
    require_code('files_spreadsheets_write');
    $sheet_writer = spreadsheet_open_write($outfile_path);

    global $USER_EXPORT_WANTED;
    $sheet_writer->write_row(array_values($USER_EXPORT_WANTED));

    require_code('cns_members');

    $start = 0;
    $max = 50;
    do {
        $rows = $GLOBALS['FORUM_DB']->query_select('f_members m JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields c ON m.id=c.mf_member_id', ['*'], [], 'ORDER BY m.id ASC', $max, $start);
        foreach ($rows as $row) {
            if (is_guest($row['id'])) {
                continue;
            }

            $row = cns_get_all_custom_fields_match_member($row['id']) + $row;

            $sheet_row = [];
            foreach (array_keys($USER_EXPORT_WANTED) as $i => $local_key) {
                $sheet_row[] = is_array($row[$local_key]) ? $row[$local_key]['RAW'] : $row[$local_key];
            }
            $sheet_writer->write_row($sheet_row);
        }
        $start += $max;
    } while (!empty($rows));

    if ($to_file) {
        $sheet_writer->close();

        // Move temporary file to final output path and sync etc
        require_code('files');
        make_missing_directory(get_custom_file_base() . '/' . dirname(USER_EXPORT_PATH));
        @unlink(get_custom_file_base() . '/' . USER_EXPORT_PATH);
        rename($outfile_path, get_custom_file_base() . '/' . USER_EXPORT_PATH);
        fix_permissions(get_custom_file_base() . '/' . USER_EXPORT_PATH);
        sync_file(get_custom_file_base() . '/' . USER_EXPORT_PATH);
    } else {
        $sheet_writer->output_and_exit(basename($outfile_path), true);
    }
}

function do_user_export__single_ipc($member_id, $delete = false)
{
    require_code('files');

    require_code('cns_members');

    if (USER_EXPORT_IPC_AUTO_REEXPORT) {
        do_user_export();
    }

    global $USER_EXPORT_WANTED;
    $rows = $GLOBALS['FORUM_DB']->query_select('f_members m JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields c ON m.id=c.mf_member_id', ['*'], ['id' => $member_id], '', 1);
    if (array_key_exists(0, $rows)) {
        $row = $rows[0];

        $row += cns_get_all_custom_fields_match_member($row['id']);

        $out = '';
        foreach ($USER_EXPORT_WANTED as $local_key => $url_key) {
            if ($out != '') {
                $out .= '&';
            }

            $val = is_array($row[$local_key]) ? $row[$local_key]['RAW'] : $row[$local_key];
            if (!is_string($val)) {
                $val = strval($val);
            }

            $out .= urlencode($url_key) . '=' . urlencode($val);
        }

        if ($delete) {
            if (USER_EXPORT_IPC_URL_DELETE !== null) {
                http_get_contents(USER_EXPORT_IPC_URL_DELETE . '?' . $out, ['trigger_error' => false]);
            }
        } else {
            if (USER_EXPORT_IPC_URL_EDIT !== null) {
                http_get_contents(USER_EXPORT_IPC_URL_EDIT . '?' . $out, ['trigger_error' => false]);
            }

            if (USER_EXPORT_EMAIL !== null) {
                $message_raw = 'This is an automated e-mail. A member record has been updated.' . "\n\n";
                foreach ($USER_EXPORT_WANTED as $local_key => $url_key) {
                    $val = is_array($row[$local_key]) ? $row[$local_key]['RAW'] : $row[$local_key];
                    if (!is_string($val)) {
                        $val = strval($val);
                    }

                    $message_raw .= $url_key . ' = ' . $val . "\n";
                }

                require_code('mail');
                dispatch_mail('Updated member record', $message_raw, '', [USER_EXPORT_EMAIL]);
            }
        }
    }
}
