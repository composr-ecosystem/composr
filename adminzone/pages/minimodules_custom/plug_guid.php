<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

// This inserts GUIDs throughout, and records them all to guids.dat (which the template editor uses).

// This is useful when wanting to generate quick GUIDs by hand: https://www.browserling.com/tools/random-string

/*
    NB: Multi line do_template calls may be uglified. You can find those in your IDE using
    do_template[^\n]*_GUID[^\n]*\n\t+'
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_release_build', $error_msg)) {
    return $error_msg;
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Plug in missing GUIDs';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', array('TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url));
}

$title = get_screen_title('Plug in missing GUIDs', false);
$title->evaluate_echo();

global $FOUND_GUID;
$FOUND_GUID = array();
global $GUID_LANDSCAPE;
$GUID_LANDSCAPE = array();
global $FILENAME, $IN;

require_code('files');
require_code('files2');

$limit_file = get_param_string('file', '');
if ($limit_file == '') {
    $files = get_directory_contents(get_file_base(), '', 0, true, true, array('php'));
} else {
    $files = array($limit_file);
}
foreach ($files as $i => $file) {
    if (preg_match('#^exports/#', $file) != 0) {
        continue;
    }
    if (strpos($file, 'plug_guid') !== false) {
        continue;
    }

    $FILENAME = $file;

    echo 'Doing ' . escape_html($file) . '<br />';

    $IN = cms_file_get_contents_safe(get_custom_file_base() . '/' . $file, FILE_READ_LOCK);

    $out = preg_replace_callback("#do_template\('([^']*)', array\((\s*)'([^']+)' => ('[^']+')#", 'callback', $IN);
    $out = preg_replace_callback("#do_template\('([^']*)', array\((\s*)'([^']+)' => #", 'callback', $IN);

    if ($IN != $out) {
        if (get_param_integer('debug', 0) == 1) {
            echo '<pre>';
            echo(escape_html($out));
            echo '</pre>';
        } else {
            echo '<span style="color: orange">Re-saved ' . escape_html($file) . '</span><br />';

            cms_file_put_contents_safe(get_file_base() . '/' . $file, $out, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
        }
    }
}
echo 'Finished!';

if ($limit_file == '') {
    cms_file_put_contents_safe(get_file_base() . '/data/guids.bin', serialize($GUID_LANDSCAPE), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

function callback($match)
{
    $full_match_line = $match[0];
    $template_name = $match[1];
    $whitespace = $match[2];
    $first_param_name = $match[3];
    $first_param_value = isset($match[4]) ? $match[4] : null;

    /*
    For debugging:
    echo $full_match_line . '<br />';
    return $full_match_line;
    */

    global $GUID_LANDSCAPE, $FILENAME, $IN;
    $new_guid = md5(uniqid('', true));
    if (!array_key_exists($template_name, $GUID_LANDSCAPE)) {
        $GUID_LANDSCAPE[$template_name] = array();
    }

    $line = substr_count(substr($IN, 0, strpos($IN, $full_match_line)), "\n") + 1;

    // Handle missing GUIDs
    if ($first_param_name != '_GUID') {
        echo 'Insert needed for ' . escape_html($template_name) . '<br />';
        $GUID_LANDSCAPE[$template_name][] = array($FILENAME, $line, $new_guid);
        return "do_template('" . $template_name . "', array(" . $whitespace . "'_GUID' => '" . $new_guid . "'," . $whitespace . "'" . $first_param_name . "' => " . (($first_param_value === null) ? $first_param_value : ' ');
    }

    // Handle existing GUIDs
    if ($first_param_value !== null) {
        global $FOUND_GUID;
        $guid_value = str_replace("'", '', $first_param_value);

        // Handle duplicated GUIDs
        if (array_key_exists($guid_value, $FOUND_GUID)) {
            echo 'Repair needed for ' . escape_html($template_name) . '<br />';
            $GUID_LANDSCAPE[$template_name][] = array($FILENAME, $line, $new_guid);
            return "do_template('" . $template_name . "', array(" . $whitespace . "'_GUID' => '" . $new_guid . "'";
        }

        $FOUND_GUID[$guid_value] = true;
        $GUID_LANDSCAPE[$template_name][] = array($FILENAME, $line, $guid_value);
    }

    return $full_match_line;
}
