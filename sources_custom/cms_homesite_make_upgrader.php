<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 You may not distribute a modified version of this file, unless it is solely as a Composr modification.
 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/* Returns triple: PATH or null if critical error, null or error string if error */
function make_upgrade_get_path($from_version_dotted, $to_version_dotted, $addons_in_upgrader = null)
{
    $err = null;

    require_code('version2');
    require_code('cms_homesite');
    require_code('tar');
    require_code('files');
    require_code('files2');
    require_code('addons');
    require_code('addons2');

    require_lang('addons');

    $from_version_pretty = ($from_version_dotted === null) ? null : get_version_pretty__from_dotted($from_version_dotted);
    $to_version_pretty = get_version_pretty__from_dotted($to_version_dotted);

    if ($from_version_dotted !== null) {
        if (str_replace('.', '', $from_version_dotted) == '') {
            $err = 'Source version not entered correctly.';
            return [null, $err];
        }
    }

    if ($from_version_dotted !== null) {
        if ($from_version_dotted == '..') {
            warn_exit(do_lang_tempcode('NO_PARAMETER_SENT', 'from version'));
        }
    }
    if ($to_version_dotted == '..') {
        warn_exit(do_lang_tempcode('NO_PARAMETER_SENT', 'to version'));
    }

    if ($from_version_dotted !== null) {
        if ($from_version_dotted == $to_version_dotted) {
            if ($addons_in_upgrader === null) {
                $err = 'Put in the version number you are upgrading <strong>from</strong>, not to. Then a specialised upgrade file will be generated for you.';
            } else {
                $err = 'You appear to already be running the latest version.';
            }
            return [null, $err];
        }
    }

    $version_parts_b = explode('.', $to_version_dotted);
    $b = intval($version_parts_b[0]);

    if ($from_version_dotted !== null) {
        $version_parts_a = explode('.', $from_version_dotted);
        $a = intval($version_parts_a[0]);

        if ((get_base_url() == 'https://composr.app') || (get_base_url() == 'https://www.composr.app')) {
            if (($a == 10) && ($b >= 11)) { // TODO: remove when v11 is stable
                attach_message('It is strongly recommended not to upgrade a version 10 site to version 11 until version 11 enters beta status. You will likely break your site! Proceed at your own risk and only if you are just testing and have taken proper backups.', 'warn');
            }

            // LEGACY: Cannot upgrade <11.alpha4 to 11.beta or higher; must first upgrade to 11.alpha4
            if ((strpos($from_version_pretty, '11 alpha') === 0) && ($from_version_pretty != '11 alpha4') && (strpos($to_version_pretty, '11 alpha') === false)) {
                return [null, 'You need to upgrade to 11 alpha4 first before upgrading to a later release. This is because changes made in the upgrader will corrupt your site if you immediately skip 11 alpha4. Please go to <a href="https://composr.app/news/view/releases/composr-11-alpha4.htm?blog=0">this news article</a> (Make a Composr upgrader box) to upgrade to 11 alpha4. After upgrading fully to 11 alpha4, run the upgrader again normally, and you should be able to then upgrade to the latest release.'];
            }
        }
    }

    if (get_base_url() == 'https://compo.sr' || get_base_url() == 'https://www.compo.sr') {
        if ($b > 10) {
            $url = hyperlink('https://composr.app', 'Composr.app', true, true);
            $err = 'Compo.sr does not host version 11+. Instead, please go to ' . $url->evaluate();
            return [null, $err];
        }
    }
    if (get_base_url() == 'https://composr.app' || get_base_url() == 'https://www.composr.app') {
        if ($b == 10) {
            $url = hyperlink('https://compo.sr', 'Compo.sr', true, true);
            $err = 'Composr.app does not host version 10. Instead, please go to ' . $url->evaluate();
            return [null, $err];
        }
    }
    if ($b < 10) {
        $err = 'You cannot upgrade to a version prior to 10 as versions < 10 are no longer supported.';
        return [null, $err];
    }

    $old_limit = cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

    // Find out path/filenames for the upgrade file we're making
    if ($from_version_dotted !== null) {
        $filename = 'upgrade-' . $from_version_dotted . '-' . $to_version_dotted;
    } else {
        $filename = 'omni-upgrade-' . $to_version_dotted;
    }
    if ((get_param_integer('supports_gzip', 0) == 1) && (function_exists('gzopen'))) {
        $filename .= '.cms.gz';
    } elseif ((get_param_integer('supports_zip', 0) == 1) && (class_exists('ZipArchive', false))) {
        $filename .= '.zip';
    } else {
        $filename .= '.cms';
    }
    if ($addons_in_upgrader !== null) {
        $filename = md5(serialize($addons_in_upgrader)) . '-' . $filename;
    }
    $build_path = get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/tars/' . $filename;
    $build_path_tmp = get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/tars/tmp-' . $filename;
    $_wip_path = 'uploads/website_specific/cms_homesite/upgrades/tar_build/' . $filename;
    $wip_path = get_file_base() . '/' . $_wip_path;

    // Find out paths for the directories holding untarred full manual installers
    if ($from_version_dotted !== null) {
        $old_base_path = get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/full/' . $from_version_dotted;
    } else {
        $old_base_path = null;
    }
    $new_base_path = get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/full/' . $to_version_dotted;

    // Find corresponding download rows
    $old_download_row = ($from_version_dotted === null) ? null : find_version_download($from_version_pretty);
    $new_download_row = find_version_download($to_version_pretty);
    if ($new_download_row === null) {
        cms_set_time_limit($old_limit);

        return [null, escape_html('Could not find version ' . $to_version_pretty . ' in the download database')];
    }
    $mtime = $new_download_row['add_date'];
    if ($new_download_row['edit_date'] !== null) {
        $mtime = $new_download_row['edit_date'];
    }
    $mtime_disk = filemtime(get_file_base() . '/' . rawurldecode($new_download_row['url']));
    if ($mtime_disk > $mtime) {
        $mtime = $mtime_disk;
    }

    $force = (get_param_integer('force', 0) == 1) && ($GLOBALS['FORUM_DRIVER']->is_super_admin(get_member()));

    // Exists already
    if (file_exists($build_path)) {
        if ((filemtime($build_path) > $mtime) && (!$force)) {
            cms_set_time_limit($old_limit);

            return [$build_path, $err];
        } else { // Outdated
            unlink($build_path);

            @deldir_contents($new_base_path, false, true);
            if (($old_base_path) !== null) {
                @deldir_contents($old_base_path, false, true);
            }
        }
    }

    // Stop a race-condition
    if (((file_exists($wip_path))) || ($force)) {
        if (!$force) {
            cms_set_time_limit($old_limit);

            return [null, 'An upgrade is currently being generated by another user. Please try again in a minute.'];
        }

        @deldir_contents($wip_path, false, true);
    }
    @mkdir($wip_path, 0777);

    // Unzip old
    if ($old_download_row !== null) {
        @mkdir($old_base_path, 0777);
        if (!url_is_local($old_download_row['url'])) {
            cms_set_time_limit($old_limit);

            return [null, escape_html('Non-local URL found (' . $old_download_row['url'] . '). Unexpected.')];
        }
        recursive_unzip(get_file_base() . '/' . rawurldecode($old_download_row['url']), $old_base_path);
    }

    // Unzip new
    @mkdir($new_base_path, 0777);
    if (!url_is_local($new_download_row['url'])) {
        cms_set_time_limit($old_limit);

        return [null, escape_html('Non-local URL found (' . $new_download_row['url'] . '). Unexpected.')];
    }
    recursive_unzip(get_file_base() . '/' . rawurldecode($new_download_row['url']), $new_base_path);

    // Find out about addon structure
    _find_helper($new_base_path);

    // Work out files for upgrader
    make_upgrader_do_dir($wip_path, $new_base_path, $old_base_path, $addons_in_upgrader);
    if ($addons_in_upgrader !== null) {
        @mkdir($wip_path . '/exports', 0777);
        @mkdir($wip_path . '/exports/addons', 0777);
        @mkdir($wip_path . '/imports', 0777);
        @mkdir($wip_path . '/imports/addons', 0777);

        // Build all addon TARs
        global $CACHE_FROM_ADDONS;
        foreach ($CACHE_FROM_ADDONS as $addon_name => $addon_files) {
            $addon_info = read_addon_info($addon_name, true, null, null, $new_base_path . '/sources/hooks/systems/addon_registry/' . $addon_name . '.php');
            create_addon(
                $addon_name . '.tar',
                $addon_files,
                $addon_info['name'],
                implode(',', $addon_info['incompatibilities']),
                implode(',', $addon_info['dependencies']),
                $addon_info['author'],
                $addon_info['organisation'],
                $addon_info['version'],
                $addon_info['category'],
                implode("\n", $addon_info['copyright_attribution']),
                $addon_info['licence'],
                $addon_info['description'],
                $addon_info['min_cms_version'],
                $addon_info['max_cms_version'],
                $_wip_path . '/exports/addons',
                [],
                $new_base_path
            );

            rename($wip_path . '/exports/addons/' . $addon_name . '.tar', $wip_path . '/imports/addons/' . $addon_name . '.tar');
        }
    }

    // Make actual upgrader
    if ($old_base_path !== null) {
        @copy($old_base_path . '/data/files.bin', $wip_path . '/data/files_previous.bin');
        fix_permissions($wip_path . '/data/files_previous.bin');
    }
    $log_file = cms_fopen_text_write(get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/build.log', true);
    if (substr($filename, -3) == '.zip') {
        require_code('zip');
        $_file_array = get_directory_contents($wip_path, '', null);
        $file_array = [];
        foreach ($_file_array as $file_path_to_add) {
            $file_array[] = [
                'time' => filemtime($wip_path . '/' . $file_path_to_add),
                'full_path' => $wip_path . '/' . $file_path_to_add,
                'name' => $file_path_to_add,
            ];
        }
        create_zip_file($build_path_tmp, $file_array);
    } else {
        $tar_handle = tar_open($build_path_tmp, 'wb');
        tar_add_folder($tar_handle, $log_file, $wip_path, null, '', [], null, false, null);
        tar_close($tar_handle);
    }
    flock($log_file, LOCK_UN);
    fclose($log_file);
    @rename($build_path_tmp, $build_path);
    sync_file($build_path);

    // Clean up
    @deldir_contents($wip_path, false, true);

    cms_set_time_limit($old_limit);

    return [$build_path, $err];
}

function make_upgrader_do_dir($build_path, $new_base_path, $old_base_path, $addons_in_upgrader, $dir = '', $pretend_dir = '')
{
    require_code('files');

    $dh = opendir($new_base_path . '/' . $dir);
    while (($file = readdir($dh)) !== false) {
        $is_dir = is_dir($new_base_path . '/' . $dir . $file);

        if (should_ignore_file($pretend_dir . $file, IGNORE_FLOATING | IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE)) {
            continue;
        }

        if ($is_dir) {
            @mkdir($build_path . '/' . $pretend_dir . $file, 0777);
            make_upgrader_do_dir($build_path, $new_base_path, $old_base_path, $addons_in_upgrader, $dir . $file . '/', $pretend_dir . $file . '/');

            // If it's empty still, delete it
            @rmdir($build_path . '/' . $pretend_dir . $file);
        } else {
            $contents = cms_file_get_contents_safe($new_base_path . '/' . $dir . $file, FILE_READ_UNIXIFIED_TEXT);
            if (($old_base_path === null) || (strpos($dir, '/addon_registry') !== false) || (!file_exists($old_base_path . '/' . $pretend_dir . '/' . $file)) || ($contents != cms_file_get_contents_safe($old_base_path . '/' . $pretend_dir . '/' . $file, FILE_READ_UNIXIFIED_TEXT))) {
                if ($addons_in_upgrader !== null) {
                    $addon_name = find_file_addon($new_base_path, $dir . $file);
                    if ($addon_name === null) {
                        continue;
                    }
                    if ((!isset($addons_in_upgrader[$addon_name])) && (substr($addon_name, 0, 5) != 'core_')) {
                        continue;
                    }
                }

                copy($new_base_path . '/' . $dir . $file, $build_path . '/' . $pretend_dir . $file);
                fix_permissions($build_path . '/' . $pretend_dir . $file);
                touch($build_path . '/' . $pretend_dir . $file, filemtime($new_base_path . '/' . $dir . $file));
            }
        }
    }
    closedir($dh);
}

function find_file_addon($new_base_path, $file)
{
    global $CACHE_FROM_PATHS;
    return isset($CACHE_FROM_PATHS[$file]) ? $CACHE_FROM_PATHS[$file] : null;
}

function find_addon_files($new_base_path, $addon_name)
{
    global $CACHE_FROM_ADDONS;
    return isset($CACHE_FROM_ADDONS[$addon_name]) ? $CACHE_FROM_ADDONS[$addon_name] : [];
}

function _find_helper($new_base_path)
{
    global $CACHE_FROM_PATHS, $CACHE_FROM_ADDONS;
    $CACHE_FROM_PATHS = [];
    $CACHE_FROM_ADDONS = [];

    $path = $new_base_path . '/sources/hooks/systems/addon_registry';
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if (substr($file, -4) == '.php') {
            $hook = basename($file, '.php');

            $_hook_bits = extract_module_functions($path . '/' . $file, ['get_file_list']);
            if ($_hook_bits[0] !== null) {
                $file_list = is_array($_hook_bits[0]) ? call_user_func_array($_hook_bits[0][0], $_hook_bits[0][1]) : cms_eval($_hook_bits[0], $path . '/' . $file);
            } else {
                $file_list = [];
            }

            $CACHE_FROM_ADDONS[$hook] = $file_list;

            foreach ($file_list as $_file) {
                $CACHE_FROM_PATHS[$_file] = $hook;
            }
        }
    }
    closedir($dh);
}
