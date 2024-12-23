<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    addon_publish
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('addon_publish', $error_msg)) {
    return $error_msg;
}

if (!addon_installed('downloads')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
}
if (!addon_installed('galleries')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('galleries')));
}

require_code('addon_publish');
require_code('addons2');
require_code('version');
require_code('version2');
require_code('downloads2');
require_code('galleries2');
require_code('files');
require_code('tar');

$target_cat = get_param_string('cat', null);
if ($target_cat === null) {
    if ($GLOBALS['DEV_MODE']) {
        $target_cat = 'Version ' . float_to_raw_string(cms_version_number(), 2, true);
    } else {
        warn_exit('Please pass the target category name in the URL (?cat=name).');
    }
}
if (strpos($target_cat, 'Version ') !== 0) {
    warn_exit('The target category should start with \'Version \' followed by the major (.minor) version of the software.');
}

$version_branch = get_param_string('version_branch', null);
if ($version_branch === null) {
    if ($GLOBALS['DEV_MODE']) {
        $version_branch = get_version_branch();
    } else {
        warn_exit('Please pass the branch version in the URL (?version_branch=num.x).');
    }
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Publish addon TARs (exports/addons, or uploads/downloads, in that order of priority) to category <kbd>' . escape_html($target_cat) . '</kbd>, version branch <kbd>' . escape_html($version_branch) . '</kbd>? Note, this particular installation of the software should be fully up-to-date, and all installed non-bundled addons should be up-to-date (e.g. their addon registry hooks) before proceeding. Otherwise, wrong / outdated information may be extracted and used on the downloads.';
    $title = get_screen_title('Publish addons as downloads', false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => '06eba6d4c63652892ec737c96ccaf3fa', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

$title = get_screen_title('Publish addons', false);
$title->evaluate_echo();

define('DOWNLOAD_OWNER', 2); // FUDGE: Hard-coded ID of user that gets ownership of the downloads

$c_main_id = find_addon_category_download_category($target_cat);

// Addons...

if (get_param_integer('import_addons', 1) == 1) {
    set_mass_import_mode(true);

    $addon_count = 0;

    $categories = find_addon_category_list();
    foreach ($categories as $category) {
        $cat_id = find_addon_category_download_category($category, $c_main_id);
        $addon_arr = get_addons_list_under_category($category, $version_branch);
        foreach ($addon_arr as $addon_name) {
            $addon_count++;
            publish_addon($addon_name, $version_branch, $cat_id);
        }
    }

    set_mass_import_mode(false);

    if ($addon_count == 0) {
        echo '<p>No addons to import.</p>';
    } else {
        echo '<p>All addons have been imported as downloads.</p>';
    }
}

// Now themes...

if (get_param_integer('import_themes', 1) == 1) {
    set_mass_import_mode(true);

    $cat_id = find_addon_category_download_category('Themes', $c_main_id);
    $cat_id = find_addon_category_download_category('Professional Themes', $cat_id);

    $dh = opendir(get_custom_file_base() . '/exports/addons');
    $theme_count = 0;
    while (($file = readdir($dh)) !== false) {
        if (preg_match('#^theme-.*\.tar$#', $file) != 0) {
            $theme_count++;

            publish_theme($file, $version_branch, $cat_id);
        }
    }
    closedir($dh);

    set_mass_import_mode(false);

    if ($theme_count == 0) {
        echo '<p>No themes to import.</p>';
    } else {
        echo '<p>All themes have been imported as downloads.</p>';
    }
}

function publish_addon($addon_name, $version_branch, $cat_id)
{
    $file = $addon_name . '-' . $version_branch . '.tar';

    $from = get_custom_file_base() . '/exports/addons/' . $file;
    $to = get_custom_file_base() . '/uploads/downloads/' . $file;

    if ((file_exists($from)) && (file_exists($to))) {
        @unlink($to);
        copy($from, $to);
    } elseif (file_exists($from)) {
        copy($from, $to);
    } elseif (!file_exists($to)) {
        warn_exit('Missing: ' . $from);
    }
    fix_permissions($to);
    sync_file($to);

    $addon_url = 'uploads/downloads/' . urlencode($file);

    $fsize = filesize(get_file_base() . '/' . urldecode($addon_url));

    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'url', ['url' => $addon_url]);
    if ($test === null) {
        if (is_file($to)) {
            $tar = tar_open($to, 'rb');
            $info_file = tar_get_file($tar, 'addon.inf', true);
            $ini_info = cms_parse_ini_file_fast(null, $info_file['data']);
            tar_close($tar);

            $addon_info = read_addon_info($addon_name, false, null, $ini_info);

            $name = titleify($addon_info['name']);
            $author = $addon_info['author'];
            $category = $addon_info['category'];
            $version = $addon_info['version'];

            $description = generate_addon_description($addon_info);

            $download_owner = $GLOBALS['FORUM_DRIVER']->get_member_from_username($author);
            if ($download_owner === null) {
                $download_owner = DOWNLOAD_OWNER;
            }

            $download_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', ['category_id' => $cat_id, $GLOBALS['SITE_DB']->translate_field_ref('name') => $name]);
            if ($download_id === null) {
                $download_id = add_download($cat_id, $name, $addon_url, $description, $author, 'Addon version ' . $version, null, 1, 1, 2, 1, '', $addon_name . '.tar', $fsize, 0, 0, null, null, 0, 0, $download_owner);
            } else {
                edit_download($download_id, $cat_id, $name, $addon_url, $description, $author, 'Addon version ' . $version, null, 1, 1, 1, 2, 1, '', $addon_name . '.tar', $fsize, 0, 0, null, '', '');
            }

            $screenshot_url = 'data_custom/images/addon_screenshots/' . $addon_name . '.png';
            if (file_exists(get_custom_file_base() . '/' . $screenshot_url)) {
                $image_id = $GLOBALS['SITE_DB']->query_select_value_if_there('images', 'id', ['cat' => 'download_' . strval($download_id)]);
                if ($image_id === null) {
                    add_image('', 'download_' . strval($download_id), '', $screenshot_url, 1, 0, 0, 0, '', null, null, null, 0);
                } else {
                    edit_image($image_id, '', 'download_' . strval($download_id), '', $screenshot_url, 1, 0, 0, 0, '', '', '');
                }
            }
        } else {
            attach_message(do_lang_tempcode('MISSING_ADDON', escape_html($addon_name)), 'warn');
        }
    }
}

function publish_theme($file, $version_branch, $cat_id)
{
    $addon_name = basename($file, '.tar');
    $new_file = $addon_name . '-' . $version_branch . '.tar';

    $from = get_custom_file_base() . '/exports/addons/' . $file;
    $to = get_custom_file_base() . '/uploads/downloads/' . $new_file;

    if ((file_exists($from)) && (file_exists($to))) {
        @unlink($to);
        copy($from, $to);
    } elseif (file_exists($from)) {
        copy($from, $to);
    } elseif (!file_exists($to)) {
        warn_exit('Missing: ' . $from);
    }
    fix_permissions($to);
    sync_file($to);

    $addon_url = 'uploads/downloads/' . urlencode($new_file);

    $fsize = filesize(get_file_base() . '/' . urldecode($addon_url));

    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'url', ['url' => $addon_url]);
    if ($test === null) {
        if (is_file($to)) {
            $tar = tar_open($to, 'rb');
            $info_file = tar_get_file($tar, 'addon.inf', true);
            $ini_info = cms_parse_ini_file_fast(null, $info_file['data']);
            tar_close($tar);

            $addon_info = read_addon_info($addon_name, false, null, $ini_info);

            $description = $addon_info['description'];
            $author = $addon_info['author'];
            $version = $addon_info['version'];

            $download_owner = $GLOBALS['FORUM_DRIVER']->get_member_from_username($author);
            if ($download_owner === null) {
                $download_owner = DOWNLOAD_OWNER;
            }
            $download_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', ['category_id' => $cat_id, $GLOBALS['SITE_DB']->translate_field_ref('name') => $addon_name]);
            if ($download_id === null) {
                $download_id = add_download($cat_id, $addon_name, $addon_url, $description, $author, 'Addon version ' . $version, null, 1, 1, 2, 1, '', $new_file, $fsize, 0, 0, null, null, 0, 0, $download_owner);
            } else {
                edit_download($download_id, $cat_id, $addon_name, $addon_url, $description, $author, 'Addon version ' . $version, null, 1, 1, 1, 2, 1, '', $new_file, $fsize, 0, 0, null, '', '');
            }

            $screenshot_url = 'data_custom/images/addon_screenshots/' . urlencode(preg_replace('#^theme-#', 'theme__', preg_replace('#\d+$#', '', basename($file, '.tar'))) . '.png');
            if (file_exists(get_custom_file_base() . '/' . $screenshot_url)) {
                $image_id = $GLOBALS['SITE_DB']->query_select_value_if_there('images', 'id', ['cat' => 'download_' . strval($download_id)]);
                if ($image_id === null) {
                    add_image('', 'download_' . strval($download_id), '', $screenshot_url, 1, 0, 0, 0, '', null, null, null, 0);
                } else {
                    edit_image($image_id, '', 'download_' . strval($download_id), '', $screenshot_url, 1, 0, 0, 0, '', '', '');
                }
            }
        } else {
            attach_message(do_lang_tempcode('MISSING_ADDON', escape_html($file)), 'warn');
        }
    }
}
