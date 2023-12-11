<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    git_status
 */

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
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
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
    $matches = array();
    if ((array_key_exists(0, $lines)) && (preg_match('#On branch (\w+)$#', $lines[0], $matches) != 0)) {
        return $matches[1];
    }
    return null; // Should not get here
}

function num_unsynched_local_commits()
{
    $lines = git_exec('status');
    $matches = array();
    if (preg_match('# ahead of \'[^\']*\' by (\d+) commit#', implode("\n", $lines), $matches) != 0) {
        return intval($matches[1]);
    }
    return '0';
}

function num_unsynched_remote_commits()
{
    git_exec('fetch');

    $lines = git_exec('status');
    $matches = array();
    if (preg_match('# behind \'[^\']*\' by (\d+) commit#', implode("\n", $lines), $matches) != 0) {
        return intval($matches[1]);
    }
    return '0';
}

function get_local_changes($include_metadata = false, $include_ignored = false)
{
    $changes = array();

    $lines = git_exec('status --short' . ($include_ignored ? ' --ignored' : ''));

    foreach ($lines as $line) {
        $matches = array();
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

            $changes[$path] = array(
                'file_size' => $file_size,
                'mtime' => $mtime,
                'git_status' => $git_status,
            );
        }
    }

    return $changes;
}

function get_local_diff($path)
{
    $lines = git_exec('diff "' . $path . '"');
    return implode("\n", $lines);
}

function git_revert($path)
{
    git_exec('checkout -- "' . $path . '"');
}

function get_remote_changes($include_metadata = false)
{
    if (num_unsynched_remote_commits() == 0) {
        return array();
    }

    $changes = array();

    $branch = find_branch();
    $lines = git_exec('diff HEAD..origin/' . $branch . '  --name-status --no-renames');

    foreach ($lines as $line) {
        $matches = array();
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

            $changes[$path] = array(
                'file_size' => $file_size,
                'mtime' => $mtime,
                'git_status' => $git_status,
            );

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

    $lines = git_exec('cat-file -s "origin/' . find_branch() . ':' . $path . '"');
    return array_key_exists(0, $lines) ? intval($lines[0]) : null;
}

function get_remote_mtime($path)
{
    git_exec('fetch');

    $lines = git_exec('log origin/' . find_branch() . ' -1 --format="%aD" -- ' . '"' . $path . '"');
    return array_key_exists(0, $lines) ? strtotime($lines[0]) : null;
}

function get_remote_diff($path)
{
    git_exec('fetch');

    $lines = git_exec('diff HEAD..origin/' . find_branch() . ' --no-renames "' . $path . '"');
    return implode("\n", $lines);
}

function git_exec($cmd)
{
    static $cache = array();
    if (array_key_exists($cmd, $cache)) {
        return $cache[$cmd];
    }
    chdir(get_git_file_base());
    $lines = shell_exec('git ' . $cmd . ' 2>&1');
    $cache[$cmd] = explode("\n", trim($lines));
    return $cache[$cmd];
}
