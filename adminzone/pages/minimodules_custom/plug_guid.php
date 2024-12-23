<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_release_build
 */

// This inserts GUIDs throughout, and records them all to guids.bin (which the template editor uses).

// This is useful when wanting to generate quick GUIDs by hand: https://www.browserling.com/tools/random-string

/*
    NB: Multi line do_template calls may be uglified. You can find those in your IDE using
    do_template[^\n]*_GUID[^\n]*\n\t+'
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('cms_release_build', $error_msg)) {
    return $error_msg;
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'By proceeding, this tool will scan the code base and automatically: add missing GUIDs, replace duplicated GUIDs, and fix invalid GUIDs. This occurs on do_template calls and INTERNAL_ERROR lang string uses.';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

$title = get_screen_title('Plug in missing GUIDs', false);
$title->evaluate_echo();

require_code('make_release');

// URL parameters
$limit_file = get_param_string('file', '');
$debug = (get_param_integer('debug', 0) == 1); // Show changes, do not save anything

guid_scan_init();

if ($limit_file == '') {
    require_code('files2');
    $files = get_directory_contents(get_file_base(), '', 0, true, true, ['php']);
    $files[] = 'install.php';
} else {
    $files = [$limit_file];
}
foreach ($files as $i => $path) {
    $scan = guid_scan($path);
    if ($scan === null) {
        continue; // Was skipped
    }

    foreach ($scan['errors_missing'] as $error) {
        echo escape_html($error) . '<br />';
    }
    foreach ($scan['errors_duplicate'] as $error) {
        echo escape_html($error) . '<br />';
    }
    foreach ($scan['errors_invalid'] as $error) {
        echo escape_html($error) . '<br />';
    }

    if ($scan['changes']) {
        if ($debug) {
            echo '<pre>';
            echo(escape_html($scan['new_contents']));
            echo '</pre>';
        } else {
            echo '<span style="color: orange">Re-saved ' . escape_html($path) . '</span><br />';

            cms_file_put_contents_safe(get_file_base() . '/' . $path, $scan['new_contents'], FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
        }
    }
}
echo 'Finished!';

// Re-save if we were not limiting scanning to a particular file
if ($limit_file == '') {
    global $GUID_LANDSCAPE;
    cms_file_put_contents_safe(get_file_base() . '/data/guids.bin', serialize($GUID_LANDSCAPE), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}
