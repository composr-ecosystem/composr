<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    git_status
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('git_status');
require_code('files');

$type = get_param_string('type', 'browse');
if ($type == 'browse') {
    git_status__browse();
} elseif ($type == 'action') {
    $action = post_param_string('action');
    switch ($action) {
        case 'local_tar':
            git_status__local_tar(); // Exits
            break;

        case 'remote_tar':
            git_status__remote_tar(); // Exits
            break;

        case 'revert':
            git_status__revert();
            git_status__browse();
            break;

        case 'include':
            git_status__browse(true);
            break;

        case 'exclude':
            git_status__browse(false);
            break;
    }
} elseif ($type == 'local_diff') {
    git_status__local_diff();
} elseif ($type == 'remote_diff') {
    git_status__remote_diff();
}

function git_status__browse($include_ignored = null)
{
    $title = get_screen_title('Git status', false);

    if ($include_ignored === null) {
        $include_ignored = (get_param_integer('include_ignored', 0) == 1);
    }

    $sort = get_param_string('sort', 'path ASC');

    $branch = find_branch();
    if ($branch === null) {
        warn_exit('Could not find git branch, git may not be operating correctly (at least via the web server)');
    }

    $num_unsynched_local_commits = num_unsynched_local_commits();
    $num_unsynched_remote_commits = num_unsynched_remote_commits();

    $local_changes = get_local_changes(true, $include_ignored);
    $remote_changes = get_remote_changes(true);

    switch ($sort) {
        case 'path ASC':
        case 'path DESC':
            ksort($local_changes, SORT_NATURAL | SORT_FLAG_CASE);
            ksort($remote_changes, SORT_NATURAL | SORT_FLAG_CASE);
            break;

        case 'file_size ASC':
        case 'file_size DESC':
            sort_maps_by($local_changes, 'file_size');
            sort_maps_by($remote_changes, 'file_size');
            break;

        case 'mtime ASC':
        case 'mtime DESC':
            sort_maps_by($local_changes, 'mtime');
            sort_maps_by($remote_changes, 'mtime');
            break;

        case 'git_status ASC':
            sort_maps_by($local_changes, 'git_status');
            sort_maps_by($remote_changes, 'git_status');
            break;
    }
    if (substr($sort, -5) == ' DESC') {
        $local_changes = array_reverse($local_changes);
        $remote_changes = array_reverse($remote_changes);
    }

    $local_files = array();
    foreach ($local_changes as $path => $details) {
        $filename = basename($path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = '';
        }

        $local_files[] = array(
            'PATH_HASH' => md5($path),
            'PATH' => $path,
            'FILENAME' => $filename,
            'DIRECTORY' => $directory,
            'FILE_SIZE' => ($details['file_size'] === null) ? null : clean_file_size($details['file_size']),
            '_FILE_SIZE' => strval($details['file_size']),
            'MTIME' => ($details['mtime'] === null) ? null : get_timezoned_date($details['mtime']),
            '_MTIME' => strval($details['mtime']),
            'GIT_STATUS' => git_status_to_str($details['git_status']),
            '_GIT_STATUS' => strval($details['git_status']),
        );
    }

    $remote_files = array();
    foreach ($remote_changes as $path => $details) {
        $filename = basename($path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = '';
        }

        $remote_files[] = array(
            'PATH_HASH' => md5($path),
            'PATH' => $path,
            'FILENAME' => $filename,
            'DIRECTORY' => $directory,
            'FILE_SIZE' => ($details['file_size'] === null) ? null : clean_file_size($details['file_size']),
            '_FILE_SIZE' => strval($details['file_size']),
            'MTIME' => ($details['mtime'] === null) ? null : get_timezoned_date($details['mtime']),
            '_MTIME' => strval($details['mtime']),
            'GIT_STATUS' => git_status_to_str($details['git_status']),
            '_GIT_STATUS' => strval($details['git_status']),
            'EXISTS_LOCALLY' => is_file(get_git_file_base() . '/' . $path),
        );
    }

    $tpl = do_template('GIT_STATUS_SCREEN', array(
        '_GUID' => '953a05574225b72dc9bf4683ce49f788',
        'TITLE' => $title,
        'BRANCH' => $branch,
        'NUM_UNSYNCHED_LOCAL_COMMITS' => integer_format($num_unsynched_local_commits),
        '_NUM_UNSYNCHED_LOCAL_COMMITS' => strval($num_unsynched_local_commits),
        'NUM_UNSYNCHED_REMOTE_COMMITS' => integer_format($num_unsynched_remote_commits),
        '_NUM_UNSYNCHED_REMOTE_COMMITS' => strval($num_unsynched_remote_commits),
        'LOCAL_FILES' => $local_files,
        'HAS_LOCAL_FILES' => (count($local_files) > 0),
        'REMOTE_FILES' => $remote_files,
        'HAS_REMOTE_FILES' => (count($remote_files) > 0),
        'HAS_MAX_REMOTE_FILES' => (count($remote_files) == 50),
        'INCLUDE_IGNORED' => $include_ignored,
        'SORT' => $sort,
    ));
    $tpl->evaluate_echo();
}

function git_status__local_tar()
{
    _git_status__tar('local_select_', 'local-changes_' . get_site_name() . '_' . date('Y-m-d') . '.tar');
}

function git_status__remote_tar()
{
    _git_status__tar('remote_select_', 'local-backup_' . get_site_name() . '_' . date('Y-m-d') . '.tar');
}

function _git_status__tar($stub, $filename)
{
    $paths = git_status__paths($stub);

    header('Content-Type: application/octet-stream' . '; authoritative=true;');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    require_code('tar');
    $myfile = tar_open(null, 'wb');
    foreach ($paths as $path) {
        $_path = get_git_file_base() . '/' . $path;
        if (is_file($_path)) {
            tar_add_file($myfile, $path, $_path, 0644, filemtime($_path), true, false, true);
        }
    }
    tar_close($myfile);

    exit();
}

function git_status__revert()
{
    $local_changes = get_local_changes(false, true);

    $files_deleted = 0;
    $files_reverted = 0;
    $files_restored = 0;

    $paths = git_status__paths('local_select_');
    foreach ($paths as $path) {
        if (array_key_exists($path, $local_changes)) {
            switch ($local_changes[$path]['git_status']) {
                case GIT_STATUS__NEW:
                case GIT_STATUS__IGNORED:
                    // Delete
                    $_path = get_git_file_base() . '/' . $path;
                    unlink($_path);
                    $files_deleted++;
                    break;

                case GIT_STATUS__MODIFIED:
                    // Revert
                    git_revert($path);
                    $files_reverted++;
                    break;

                case GIT_STATUS__DELETED:
                    // Revert
                    git_revert($path);
                    $files_restored++;
                    break;
            }
        }
    }

    $msg = 'Deleted ' . integer_format($files_deleted) . ' ' . (($files_deleted == 1) ? 'file' : 'files') . ', ';
    $msg .= 'reverted ' . integer_format($files_reverted) . ' ' . (($files_reverted == 1) ? 'file' : 'files') . ', ';
    $msg .= 'restored ' . integer_format($files_restored) . ' ' . (($files_restored == 1) ? 'file' : 'files');
    attach_message($msg, 'inform');
}

function git_status__local_diff()
{
    $path = get_param_string('id', false, true);

    $diff = get_local_diff($path);

    _git_status__diff($diff);
}

function git_status__remote_diff()
{
    $path = get_param_string('id', false, true);

    $diff = get_remote_diff($path);

    _git_status__diff($diff);
}

function _git_status__diff($diff)
{
    $title = get_screen_title('Git diff', false);

    if (addon_installed('geshi')) {
        require_code('geshi');
        require_code('developer_tools');
        destrictify(false);
        $geshi = new GeSHi($diff, 'diff');
        $geshi->set_header_type(GESHI_HEADER_DIV);
        require_code('xhtml');
        $diff_nice = xhtmlise_html($geshi->parse_code());
        restrictify();
    } else {
        $diff_nice = nl2br(escape_html($diff));
    }

    $tpl = do_template('GIT_STATUS_DIFF_SCREEN', array(
        '_GUID' => '40799b14b6bf9dfb0a2f38e1b63e76af',
        'TITLE' => $title,
        'DIFF' => $diff_nice,
    ));
    $tpl->evaluate_echo();
}

function git_status__paths($stub)
{
    $paths = array();
    foreach (array_keys($_POST) as $key) {
        if (substr($key, 0, strlen($stub)) == $stub) {
            $paths[] = post_param_string($key);
        }
    }
    return $paths;
}
