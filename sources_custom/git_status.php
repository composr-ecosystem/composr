<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    git_status
 */

/*EXTRA FUNCTIONS: shell_exec*/

function init__git_status()
{
    define('GIT_STATUS__IGNORED', 0);
    define('GIT_STATUS__NEW', 1);
    define('GIT_STATUS__MODIFIED', 2);
    define('GIT_STATUS__DELETED', 3);
}

function git_status_to_str($git_status)
{
    switch ($git_status) {
        case GIT_STATUS__IGNORED:
            $git_status_str = 'Ignored';
            break;
        case GIT_STATUS__NEW:
            $git_status_str = 'New';
            break;
        case GIT_STATUS__MODIFIED:
            $git_status_str = 'Modified';
            break;
        case GIT_STATUS__DELETED:
            $git_status_str = 'Deleted';
            break;

        default:
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('361469fa9f535e599739e04d1d0ceab8')));
    }
    return $git_status_str;
}

function get_git_file_base()
{
    return get_file_base();
}

function find_branch()
{
    $lines = git_exec('status');
    $matches = [];
    if ((array_key_exists(0, $lines)) && (preg_match('#On branch (\w+)$#', $lines[0], $matches) != 0)) {
        return $matches[1];
    }
    return null; // Should not get here
}

function num_unsynched_local_commits()
{
    $result = _git_exec('status');
    $matches = [];
    if (preg_match('# ahead of \'[^\']*\' by (\d+) commit#', $result, $matches) != 0) {
        return intval($matches[1]);
    }
    return '0';
}

function num_unsynched_remote_commits()
{
    git_exec('fetch');

    $result = _git_exec('status');
    $matches = [];
    if (preg_match('# behind \'[^\']*\' by (\d+) commit#', $result, $matches) != 0) {
        return intval($matches[1]);
    }
    return '0';
}

function get_local_changes($include_metadata = false, $include_ignored = false)
{
    $changes = [];

    $lines = git_exec('status --short' . ($include_ignored ? ' --ignored' : ''));

    foreach ($lines as $line) {
        $matches = [];
        if (preg_match('#^\s*([!\?MADRC])+( .* ->)? (.*)$#', $line, $matches) != 0) {
            switch ($matches[1]) {
                case '!':
                    $git_status = GIT_STATUS__IGNORED;
                    break;

                case '?':
                case 'A':
                case 'R':
                case 'C':
                    $git_status = GIT_STATUS__NEW;
                    break;

                case 'M':
                    $git_status = GIT_STATUS__MODIFIED;
                    break;

                case 'D':
                    $git_status = GIT_STATUS__DELETED;
                    break;

                default:
                    continue 2;
            }

            $path = trim($matches[3], '" ');

            $full_path = get_git_file_base() . '/' . $path;
            if ((!file_exists($full_path)) && ($matches[1] != 'D')) {
                continue; // Weird
            }

            if (($include_metadata) && (is_file($full_path))) {
                $file_size = filesize($full_path);
                $mtime = filemtime($full_path);
            } else {
                $file_size = null;
                $mtime = null;
            }

            $changes[$path] = [
                'file_size' => $file_size,
                'mtime' => $mtime,
                'git_status' => $git_status,
            ];
        }
    }

    return $changes;
}

function get_local_diff($path)
{
    return _git_exec('diff ' . cms_escapeshellarg($path));
}

function git_revert($path)
{
    git_exec('checkout -- ' . cms_escapeshellarg($path));
}

function get_remote_changes($include_metadata = false)
{
    if (num_unsynched_remote_commits() == 0) {
        return [];
    }

    $changes = [];

    $branch = find_branch();
    $lines = git_exec('diff HEAD..origin/' . $branch . '  --name-status --no-renames');

    foreach ($lines as $line) {
        $matches = [];
        if (preg_match('#^([AMD])\s+(.*)$#', $line, $matches) != 0) {
            switch ($matches[1]) {
                case 'A':
                    $git_status = GIT_STATUS__NEW;
                    break;

                case 'M':
                    $git_status = GIT_STATUS__MODIFIED;
                    break;

                case 'D':
                    $git_status = GIT_STATUS__DELETED;
                    break;

                default:
                    continue 2;
            }

            $path = trim($matches[2], '" ');

            if ($include_metadata) {
                $file_size = get_remote_file_size($path);
                $mtime = get_remote_mtime($path);
            } else {
                $file_size = null;
                $mtime = null;
            }

            $changes[$path] = [
                'file_size' => $file_size,
                'mtime' => $mtime,
                'git_status' => $git_status,
            ];

            if (count($changes) == 50) {
                break; // For performance
            }
        }
    }

    return $changes;
}

function get_remote_file_size($path)
{
    git_exec('fetch');

    $lines = git_exec('cat-file -s ' . cms_escapeshellarg('origin/' . find_branch() . ':' . $path));
    return array_key_exists(0, $lines) ? intval($lines[0]) : null;
}

function get_remote_mtime($path)
{
    git_exec('fetch');

    $lines = git_exec('log origin/' . find_branch() . ' -1 --format="%aD" -- ' . cms_escapeshellarg($path));
    return array_key_exists(0, $lines) ? strtotime($lines[0]) : null;
}

function get_remote_diff($path)
{
    git_exec('fetch');

    return _git_exec('diff HEAD..origin/' . find_branch() . ' --no-renames ' . cms_escapeshellarg($path));
}

function git_add($path)
{
    return _git_exec('add ' . cms_escapeshellarg($path));
}

function git_commit($paths)
{
    $_paths = '';
    foreach ($paths as $path) {
        $_paths .= ' ' . cms_escapeshellarg($path);
    }
    return _git_exec('commit' . $_paths . ' -m ' . cms_escapeshellarg('Web commit from ' . get_base_url()));
}

function git_push()
{
    return _git_exec('push');
}

function git_pull()
{
    return _git_exec('pull');
}

function get_git_file($path, $revision = 'HEAD')
{
    return _git_exec('show ' . cms_escapeshellarg($revision . ':' . $path));
}

function git_exec($cmd)
{
    return explode("\n", trim(_git_exec($cmd)));
}

function _git_exec($cmd)
{
    static $cache = [];
    if ($cmd == 'fetch') {
        $cache = [];
    }
    if (array_key_exists($cmd, $cache)) {
        return $cache[$cmd];
    }
    chdir(get_git_file_base());

    putenv('GIT_COMMITTER_EMAIL=' . get_option('staff_address'));
    putenv('GIT_AUTHOR_EMAIL=' . get_option('staff_address'));
    putenv('GIT_COMMITTER_NAME=' . get_domain());
    putenv('GIT_AUTHOR_NAME=' . get_domain());
    $_cmd = 'git ' . $cmd . ' 2>&1';

    $cache[$cmd] = shell_exec($_cmd);
    if (!is_string($cache[$cmd])) {
        $cache[$cmd] = '';
    }
    return $cache[$cmd];
}
