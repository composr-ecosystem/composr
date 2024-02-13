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

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Public non-bundled addons';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => 'e68a3b48474d9c8ad4d7107009fb3e83', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

restrictify();
cms_ini_set('ocproducts.xss_detect', '0');

$_title = get_screen_title('Composr addon building tool', false);
$_title->evaluate_echo();

if (strpos(PHP_OS, 'Darwin') !== false) {
    $command_to_try = 'open';
} elseif (strpos(PHP_OS, 'WIN') !== false) {
    $command_to_try = 'start';
} else {
    $command_to_try = 'nautilus';
}
$command_to_try .= ' ' . get_custom_file_base() . '/exports/addons/';

echo '<p>This has built all the addons into <kbd><a href="#" onclick="fauxmodal_alert(\'&lt;kbd&gt;' . escape_html($command_to_try) . '&lt;/kbd&gt;\',null,\'Command to open folder\',true);"><kbd>exports/addons</kbd></a></kbd>. The <kbd>admin_make_release</kbd> page you probably came from gives details on how to get these live on compo.sr.</p>';

echo '<p>What follows is details if you are republishing addons and want to easily put out "This is updated" messages into the comment topics. It\'s not necessary because just uploading the TARs will make the inbuilt Composr check script see the addon is updated compared to when a user installed it. However, it is nice to keep users updated if you have time.</p>';

echo '<hr />';

require_code('addons2');
require_code('version');
require_code('version2');
require_code('tar');
require_code('addon_publish');

cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

disable_php_memory_limit();

$only = get_param_string('only', null);

$done_addon = false;

$done_message = false;

$addons = find_all_hooks('systems', 'addon_registry');
foreach ($addons as $name => $place) {
    if (($only !== null) && ($only !== $name)) {
        continue;
    }

    if ((get_param_integer('export_bundled_addons', 0) == 0) && ($place == 'sources')) { // We are not normally concerned about these, but maybe it is useful sometimes
        continue;
    }
    if ((get_param_integer('export_addons', 1) == 0) && ($place == 'sources_custom')) {
        continue;
    }

    $addon_info = read_addon_info($name);

    $file = preg_replace('#^[_\.\-]#', 'x', preg_replace('#[^\w\.\-]#', '_', $name)) . '-' . get_version_branch(floatval($addon_info['version'])) . '.tar';
    $full_path = get_custom_file_base() . '/exports/addons/' . $file;

    // Copy through times from previous build IF the files didn't change (as Git munges mtimes)
    if (is_file($full_path)) {
        $tar_file = tar_open($full_path, 'rb');
        $directory = list_to_map('path', tar_get_directory($tar_file));
    } else {
        $tar_file = null;
        $directory = [];

        if (!$done_message) {
            attach_message(comcode_to_tempcode('At least one addon file isn\'t on disk already. If this build is updating addons already released for this major/minor version you should put the addons in [tt]exports/addons[/tt] so that mtimes can be set properly, then refresh.'), 'warn');
            $done_message = true;
        }
    }

    // Build up file list
    $new_addon_files = [];
    $mtimes = [];
    foreach ($addon_info['files'] as $_file) {
        if (substr($_file, -9) != '.editfrom') { // This would have been added back in automatically
            if (isset($directory[$_file])) {
                $file_info = tar_get_file($tar_file, $_file);
                if ($file_info['data'] == cms_file_get_contents_safe(get_file_base() . '/' . $_file, FILE_READ_LOCK)) {
                    $mtimes[$_file] = $directory[$_file]['mtime'];
                }
            }

            $new_addon_files[] = $_file;
        }
    }

    if ($tar_file !== null) {
        tar_close($tar_file);
    }

    $old_time = @filemtime(get_custom_file_base() . '/exports/addons/' . $file);

    // Archive it off to exports/addons
    create_addon(
        $file,
        $new_addon_files,
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
        'exports/addons',
        $mtimes
    );

    clearstatcache();
    $new_time = @filemtime(get_custom_file_base() . '/exports/addons/' . $file);

    if ($old_time !== $new_time) {
        $done_addon = true;

        if ($old_time === false) {
            echo '<p>New addon with description:</p><div class="whitespace-visible">' . escape_html(generate_addon_description($addon_info)) . '</div>';
        } else {
            echo nl2br(escape_html(update_addon_descriptions($file, $name, generate_addon_description($addon_info))));
        }
    }
}
if ($done_addon) {
    echo "<hr /><p>Addons have been exported to <kbd>export/addons/</kbd></p>\n";
}

if (get_param_integer('export_themes', 0) == 1) {
    require_code('themes2');
    require_code('files');
    require_code('files2');
    $themes = find_all_themes();

    $page_files = get_directory_contents(get_custom_file_base(), '', 0, true, true, ['txt']);
    foreach (array_keys($themes) as $theme) {
        if (($only !== null) && ($only !== $theme)) {
            continue;
        }

        if ($theme == 'default' || $theme == 'admin') {
            continue;
        }

        $name = get_theme_option('title', '', $theme);
        $author = get_theme_option('author', 'Core Developer Team', $theme);
        if ($author == 'admin') {
            $author = 'Core Developer Team';
        }
        $organisation = get_theme_option('organisation', 'Composr', $theme);
        $version = get_theme_option('version', '1.0', $theme);
        $copyright_attribution = get_theme_option('copyright_attribution', '', $theme);
        $licence = get_theme_option('licence', '(Unstated)', $theme);
        $description = get_theme_option('description', '', $theme);
        $min_cms_version = get_theme_option('min_cms_version', '', $theme);
        $max_cms_version = get_theme_option('max_cms_version', '', $theme);
        $dependencies = get_theme_option('dependencies', '', $theme);
        $incompatibilities = get_theme_option('incompatibilities', '', $theme);

        $file = 'theme-' . preg_replace('#^[_\.\-]#', 'x', preg_replace('#[^\w\.\-]#', '_', $theme)) . '-' . get_version_branch() . '.tar';

        $files2 = [];
        $theme_files = get_directory_contents(get_custom_file_base() . '/themes/' . $theme, 'themes/' . $theme, IGNORE_EDITFROM_FILES | IGNORE_REVISION_FILES);
        foreach ($theme_files as $file2) {
            $files2[] = $file2;
        }
        foreach ($page_files as $file2) {
            $matches = [];
            $regexp = '#^((\w+)/)?pages/comcode_custom/[^/]*/\_' . preg_quote($theme, '#') . '__(\w+)\.txt$#';
            if ((preg_match($regexp, $file2, $matches) != 0) && ($matches[1] != 'docs')) {
                $files2[] = dirname($file2) . '/' . $matches[2];
            }
        }

        $_GET['keep_theme_test'] = '1';
        $_GET['theme'] = $theme;

        $old_time = @filemtime(get_custom_file_base() . '/exports/addons/' . $file);

        create_addon(
            $file,
            $files2,
            $name,
            $incompatibilities,
            $dependencies,
            $author,
            $organisation,
            $version,
            'Themes',
            $copyright_attribution,
            $licence,
            $description,
            $min_cms_version,
            $max_cms_version,
            'exports/addons'
        );

        clearstatcache();
        $new_time = @filemtime(get_custom_file_base() . '/exports/addons/' . $file);

        if ($old_time !== $new_time) {
            echo nl2br(escape_html(update_addon_descriptions($file, $name, $description)));
        }
    }

    if ($only !== null) {
        echo "<hr /><p>All themes have been exported to <kbd>export/addons/</kbd></p>\n";
    }
}

echo "<hr /><p><strong>Done. To deploy live upload changed files from <kbd>export/addons/</kbd> to <kbd>uploads/downloads/</kbd> on live (and add download entries for any new addons using the descriptions given and other correct details &mdash; or use the <kbd>publish_addons_as_downloads</kbd> page).</strong></p>\n";

function update_addon_descriptions($file, $name, $description)
{
    $_description = addslashes($description);
    return <<<END
    :\$GLOBALS['SITE_DB']->query_update('download_downloads', ['the_description' => '{$_description}'], ['url' => 'uploads/downloads/' . rawurlencode('{$file}']));
END;
}
