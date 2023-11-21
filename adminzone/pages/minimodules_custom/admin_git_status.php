<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    git_status
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('git_status');
require_code('files');

require_css('git_status');

require_javascript('git_status');

$git_live_branch = get_option('git_live_branch');
$current_branch = find_branch();
if (($git_live_branch != '') && ($current_branch != $git_live_branch)) {
    warn_exit('This site is currently running out of the ' . $current_branch . ' Git branch. The live branch is ' . $git_live_branch . ', and for this reason content-management commits to Git are disabled.');
}

$type = get_param_string('type', 'browse');
if ($type == 'browse') {
    git_status__browse();
} elseif ($type == 'git_action') {
    $action = post_param_string('git_action');
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

        case 'push':
            git_status__push();
            break;

        case 'pull':
            git_status__pull();
            break;
    }
} elseif ($type == 'local_diff') {
    git_status__local_diff();
} elseif ($type == 'remote_diff') {
    git_status__remote_diff();
} elseif ($type == 'local_view') {
    git_status__local_view();
} elseif ($type == 'remote_view') {
    git_status__remote_view();
}

function git_status__browse($include_ignored = null)
{
    $title = get_screen_title('Git status', false);

    if ($include_ignored === null) {
        $include_ignored = (get_param_integer('include_ignored', 0) == 1);
    }

    $sort = get_param_string('sort', 'path ASC', INPUT_FILTER_GET_COMPLEX);

    $branch = find_branch();
    if ($branch === null) {
        warn_exit('Could not find Git branch, Git may not be operating correctly (at least via the web server)');
    }

    $num_unsynched_local_commits = num_unsynched_local_commits();
    $num_unsynched_remote_commits = num_unsynched_remote_commits();

    $local_changes = get_local_changes(true, $include_ignored);
    $remote_changes = get_remote_changes(true);

    switch ($sort) {
        case 'path ASC':
        case 'path DESC':
            cms_mb_ksort($local_changes, SORT_NATURAL | SORT_FLAG_CASE);
            cms_mb_ksort($remote_changes, SORT_NATURAL | SORT_FLAG_CASE);
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

    $local_files = [];
    foreach ($local_changes as $path => $details) {
        $filename = basename($path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = '';
        }

        $local_files[] = [
            'PATH_HASH' => md5($path),
            'PATH' => $path,
            'PATH_ENCODED' => base64_encode($path),
            'FILENAME' => $filename,
            'DIRECTORY' => $directory,
            'FILE_SIZE' => ($details['file_size'] === null) ? null : clean_file_size($details['file_size']),
            '_FILE_SIZE' => strval($details['file_size']),
            'MTIME' => ($details['mtime'] === null) ? null : get_timezoned_date($details['mtime']),
            '_MTIME' => strval($details['mtime']),
            'GIT_STATUS' => git_status_to_str($details['git_status']),
            '_GIT_STATUS' => strval($details['git_status']),
        ];
    }

    $remote_files = [];
    foreach ($remote_changes as $path => $details) {
        $filename = basename($path);
        $directory = dirname($path);
        if ($directory == '.') {
            $directory = '';
        }

        $remote_files[] = [
            'PATH_HASH' => md5($path),
            'PATH' => $path,
            'PATH_ENCODED' => base64_encode($path),
            'FILENAME' => $filename,
            'DIRECTORY' => $directory,
            'FILE_SIZE' => ($details['file_size'] === null) ? null : clean_file_size($details['file_size']),
            '_FILE_SIZE' => strval($details['file_size']),
            'MTIME' => ($details['mtime'] === null) ? null : get_timezoned_date($details['mtime']),
            '_MTIME' => strval($details['mtime']),
            'GIT_STATUS' => git_status_to_str($details['git_status']),
            '_GIT_STATUS' => strval($details['git_status']),
            'EXISTS_LOCALLY' => is_file(get_git_file_base() . '/' . $path),
        ];
    }

    $tpl = do_template('GIT_STATUS_SCREEN', [
        '_GUID' => 'e9908ded88ba2fc0c75945449fc49b99',
        'TITLE' => $title,
        'BRANCH' => $branch,
        '_NUM_UNSYNCHED_LOCAL_COMMITS' => strval($num_unsynched_local_commits),
        'NUM_UNSYNCHED_LOCAL_COMMITS' => integer_format($num_unsynched_local_commits),
        '_NUM_UNSYNCHED_REMOTE_COMMITS' => strval($num_unsynched_remote_commits),
        'NUM_UNSYNCHED_REMOTE_COMMITS' => integer_format($num_unsynched_remote_commits),
        'LOCAL_FILES' => $local_files,
        'HAS_LOCAL_FILES' => (!empty($local_files)),
        'REMOTE_FILES' => $remote_files,
        'HAS_REMOTE_FILES' => (!empty($remote_files)),
        'HAS_MAX_REMOTE_FILES' => (count($remote_files) == 50),
        'INCLUDE_IGNORED' => $include_ignored,
        'SORT' => $sort,
    ]);
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

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    require_code('tar');
    $myfile = tar_open('php://output', 'wb');
    foreach ($paths as $path) {
        $_path = get_git_file_base() . '/' . $path;
        if (is_file($_path)) {
            tar_add_file($myfile, $path, $_path, 0644, filemtime($_path), true, false, true);
        }
    }
    tar_close($myfile);

    $GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
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

function git_status__local_view()
{
    $title = get_screen_title('Preview local file', false);

    $path = base64_decode(get_param_string('id', false, INPUT_FILTER_NONE));

    $media_current = git_render_preview_from_path($path);

    $tpl = do_template('GIT_STATUS_FILE_SCREEN', [
        '_GUID' => '094403756020dac5e0da574c9cf954a8',
        'TITLE' => $title,
        'MEDIA_CURRENT' => $media_current,
    ]);
    $tpl->evaluate_echo();
}

function git_status__remote_view()
{
    $title = get_screen_title('Preview remote file', false);

    $path = base64_decode(get_param_string('id', false, INPUT_FILTER_NONE));

    $media_current = git_render_preview_from_raw_data($path, get_git_file($path));

    $tpl = do_template('GIT_STATUS_FILE_SCREEN', [
        '_GUID' => 'cce147b8587078b0a245f9225cf567d7',
        'TITLE' => $title,
        'MEDIA_CURRENT' => $media_current,
    ]);
    $tpl->evaluate_echo();
}

function git_status__local_diff()
{
    $path = base64_decode(get_param_string('id', false, INPUT_FILTER_NONE));

    $diff = get_local_diff($path);

    _git_status__diff($diff, $path, false);
}

function git_status__remote_diff()
{
    $path = base64_decode(get_param_string('id', false, INPUT_FILTER_NONE));

    $diff = get_remote_diff($path);

    _git_status__diff($diff, $path, true);
}

function _git_status__diff($diff, $path, $is_remote)
{
    $title = get_screen_title('Git diff', false);

    if (preg_match("#^diff --git a/.* b/.*\nindex \w+\.\.\w+ \d+\nBinary files a/.* and .* differ$#", $diff) != 0) {
        if ($is_remote) {
            $media_before = git_render_preview_from_raw_data($path, get_git_file($path));
            $media_current = git_render_preview_from_raw_data($path, get_git_file($path, 'origin/' . find_branch()));
        } else {
            $media_before = git_render_preview_from_raw_data($path, get_git_file($path));
            $media_current = git_render_preview_from_path($path);
        }

        $tpl = do_template('GIT_STATUS_FILE_SCREEN', [
            '_GUID' => 'e32ef1292392f8285cf4c1aca299d51e',
            'TITLE' => $title,
            'MEDIA_BEFORE' => $media_before,
            'MEDIA_CURRENT' => $media_current,
        ]);
        $tpl->evaluate_echo();
    }

    if (addon_installed('geshi')) {
        require_code('geshi');
        require_code('developer_tools');
        destrictify(false);
        $geshi = new GeSHi($diff, 'diff');
        $geshi->set_header_type(GESHI_HEADER_DIV);
        require_code('xhtml');
        $diff_nice = make_string_tempcode(xhtmlise_html($geshi->parse_code()));
        restrictify();
    } else {
        $diff_nice = with_whitespace(escape_html($diff));
    }

    $tpl = do_template('GIT_STATUS_FILE_SCREEN', [
        '_GUID' => '1ed30738a878cd637c6273c6bd817948',
        'TITLE' => $title,
        'DIFF' => $diff_nice,
    ]);
    $tpl->evaluate_echo();
}

function git_render_preview_from_raw_data($path, $raw_data)
{
    require_code('mime_types');
    $url = 'data:' . get_mime_type(get_file_extension($path), true) . ';base64,' . base64_encode($raw_data);

    require_code('media_renderer');
    $renderers = find_media_renderers($url, [], true);
    $_renderers = array_diff($renderers, ['hyperlink', 'code']);
    if (($renderers === null) || (empty($_renderers))) {
        return with_whitespace(escape_html($raw_data));
    }

    return _git_render_media_preview($path, $url);
}

function git_render_preview_from_path($path)
{
    if (!is_file(get_custom_file_base() . '/' . $path)) {
        return paragraph(do_lang_tempcode('MISSING_RESOURCE'), '', 'red_alert');
    }

    $url = get_custom_base_url() . '/' . str_replace('%2F', '/', rawurlencode($path));

    require_code('media_renderer');
    $renderers = find_media_renderers($url, [], true);
    $_renderers = array_diff($renderers, ['hyperlink', 'code']);
    if (($renderers === null) || (empty($_renderers))) {
        return with_whitespace(escape_html(cms_file_get_contents_safe(get_custom_file_base() . '/' . $path)));
    }

    return _git_render_media_preview($path, $url);
}

function _git_render_media_preview($path, $url)
{
    return render_media_url($url, $url, [], true);
}

function git_status__paths($stub)
{
    $paths = [];
    foreach (array_keys($_POST) as $key) {
        if (substr($key, 0, strlen($stub)) == $stub) {
            $paths[] = post_param_string($key);
        }
    }
    return $paths;
}

function git_status__push()
{
    $title = get_screen_title('Commit & Push', false);
    $title->evaluate_echo();

    if (num_unsynched_remote_commits() > 0) {
        return paragraph('There are remote changes to pull first.', '', 'red_alert');
    }

    $output = [];

    $local_changes = get_local_changes(true, true);

    $paths = git_status__paths('local_select_');
    foreach ($paths as $path) {
        switch ($local_changes[$path]['git_status']) {
            case GIT_STATUS__NEW:
            case GIT_STATUS__IGNORED:
                $output[] = git_add($path);
                break;
        }
    }

    $output[] = git_commit($paths);

    $output[] = git_push();

    foreach ($output as $result) {
        echo static_evaluate_tempcode(with_whitespace(escape_html($result) . "\n"));
    }
}

function git_status__pull()
{
    $title = get_screen_title('Pull', false);
    $title->evaluate_echo();

    echo static_evaluate_tempcode(with_whitespace(escape_html(git_pull())));

    require_code('caches3');
    erase_cached_templates();
    erase_cached_language();
    erase_comcode_cache();
    erase_block_cache(true);
    erase_comcode_page_cache();
    erase_persistent_cache();
}
