<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/*
    NB: Multi line do_template calls may be uglified. You can find those in your IDE using
    do_template[^\n]*_GUID[^\n]*\n\t+'
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Plug in missing GUIDs', false);
$title->evaluate_echo();

global $FOUND_GUID;
$FOUND_GUID = array();
global $GUID_LANDSCAPE;
$GUID_LANDSCAPE = array();
global $FILENAME, $IN;

require_code('files');
require_code('files2');

$limit_file = isset($_GET['file']) ? $_GET['file'] : '';
if ($limit_file == '') {
    $files = get_directory_contents(get_file_base());
} else {
    $files = array($limit_file);
}
foreach ($files as $i => $file) {
    if (preg_match('#^exports/#', $file) != 0) {
        continue;
    }
    if (substr($file, -4) != '.php') {
        continue;
    }
    if (strpos($file, 'plug_guid') !== false) {
        continue;
    }

    $FILENAME = $file;

    echo 'Doing ' . escape_html($file) . '<br />';

    $IN = file_get_contents(get_custom_file_base() . '/' . $file);

    $out = preg_replace_callback("#do_template\('([^']*)', array\(\s*'([^']+)' => ('[^\']+')#", 'callback', $IN); // Existing GUIDs
    if ($IN != $out) {
        echo '<span style="color: orange">Re-saved ' . escape_html($file) . '</span><br />';
        cms_file_put_contents_safe(get_file_base() . '/' . $file, $out, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }

    $out = preg_replace_callback("#do_template\('([^']*)', array\(\s*'([^']+)' => #", 'callback', $IN); // Potentially missing GUIDs
    if ($IN != $out) {
        echo '<span style="color: orange">Re-saved ' . escape_html($file) . '</span><br />';
        cms_file_put_contents_safe(get_file_base() . '/' . $file, $out, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}
echo 'Finished! You will want to check that any changed do_template arrays are not now ugly, because there\'s no autoformatter here.';

if ($limit_file == '') {
    cms_file_put_contents_safe(get_file_base() . '/data/guids.bin', serialize($GUID_LANDSCAPE), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

function callback($match)
{
    //echo $match[0].'<br />';
    //return $match[0];
    global $GUID_LANDSCAPE, $FILENAME, $IN;
    $new_guid = md5(uniqid('', true));
    if (!array_key_exists($match[1], $GUID_LANDSCAPE)) {
        $GUID_LANDSCAPE[$match[1]] = array();
    }
    $line = substr_count(substr($IN, 0, strpos($IN, $match[0])), "\n") + 1;
    if ($match[2] != '_GUID') { // Potentially missing GUIDs
        // Missing GUIDs
        echo 'Insert needed for ' . escape_html($match[1]) . '<br />';
        $GUID_LANDSCAPE[$match[1]][] = array($FILENAME, $line, $new_guid);
        return "do_template('" . $match[1] . "', array('_GUID' => '" . $new_guid . "', '" . $match[2] . '\' => ' . (isset($match[3]) ? $match[3] : '');
    }
    if (isset($match[3])) { // Existing GUIDs
        global $FOUND_GUID;
        $guid_value = str_replace('\'', '', $match[3]);
        if (array_key_exists($guid_value, $FOUND_GUID)) {
            echo 'Repair needed for ' . escape_html($match[1]) . ' (line ' . $line . ', new GUID ' . $new_guid . ')<br />';
            $GUID_LANDSCAPE[$match[1]][] = array($FILENAME, $line, $new_guid);
            return "do_template('" . $match[1] . "', array('_GUID' => '" . $new_guid . "'";
        }
        $FOUND_GUID[$guid_value] = true;
        $GUID_LANDSCAPE[$match[1]][] = array($FILENAME, $line, $guid_value);
    }
    return $match[0];
}
