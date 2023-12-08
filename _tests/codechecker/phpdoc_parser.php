<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    code_quality
 */

/*
Parse PHPdoc in all scripts under project directory
*/

global $COMPOSR_PATH;

require(dirname(__FILE__) . '/lib.php');

if (isset($_SERVER['argv'])) {
    $extra = array();
    foreach ($_SERVER['argv'] as $index => $argv) {
        $argv = str_replace('\\\\', '\\', $argv);
        $_SERVER['argv'][$index] = $argv;
        $explode = explode('=', $argv, 2);
        if (count($explode) == 2) {
            $extra[$explode[0]] = trim($explode[1], '"');
            unset($_SERVER['argv'][$index]);
        }
    }
    $_SERVER['argv'] = array_merge($_SERVER['argv'], $extra);
    if (array_key_exists('path', $_SERVER['argv'])) {
        $GLOBALS['COMPOSR_PATH'] = $_SERVER['argv']['path'];
    }
}

require_code('php');

$no_custom = true;
if ((isset($_GET['allow_custom'])) && ($_GET['allow_custom'] == '1')) {
    $no_custom = false;
}
$files = do_dir($COMPOSR_PATH, $no_custom, true);
if ($no_custom) {
    $files[] = $COMPOSR_PATH . '/sources_custom/phpstub.php';
}

ini_set('memory_limit', '-1');

$classes = array();
$global = array();
global $TO_USE;
//$files = array($COMPOSR_PATH . '/sources/global2.php'); For debugging
foreach ($files as $filename) {
    if (strpos($filename, 'sabredav/') !== false || strpos($filename, 'Swift/') !== false || strpos($filename, 'tracker/') !== false) { // Lots of complex code we want to ignore, even if doing custom files
        continue;
    }

    $TO_USE = $filename;

    $_filename = ($COMPOSR_PATH == '') ? $filename : substr($filename, strlen($COMPOSR_PATH) + 1);
    if ($_filename == 'sources/minikernel.php') {
        continue;
    }
    //echo 'SIGNATURES-DOING ' . $_filename . cnl();
    $result = get_php_file_api($_filename, false);

    foreach ($result as $i => $r) {
        if ($r['name'] == '__global') {
            if (($_filename != 'sources/global.php') && ($_filename != 'phpstub.php')) {
                foreach (array_keys($r['functions']) as $f) {
                    if ((isset($global[$f])) && (!in_array($f, array('do_lang', 'mixed', 'qualify_url', 'http_download_file', 'get_forum_type', 'cms_srv', 'mailto_obfuscated', 'get_custom_file_base')))) {
                        echo 'DUPLICATE-FUNCTION ' . $f . ' (in ' . $filename . ')' . cnl();
                    }
                }
            }
            $global = array_merge($global, $r['functions']);
        }
    }
    foreach ($result as $in) {
        if ($in['name'] != '__global') {
            $class = $in['name'];
            if (isset($classes[$class])) {
                echo 'DUPLICATE_CLASS' . ' ' . $class . cnl();
            }
            $classes[$class] = $in;
        }
    }
    //echo 'SIGNATURES-DONE ' . $_filename . cnl();
}

$classes['__global'] = array('functions' => $global);
if (file_exists($COMPOSR_PATH . '/data_custom')) {
    $myfile = fopen($COMPOSR_PATH . '/data_custom/functions.bin', 'wb');
} else {
    $myfile = fopen('functions.bin', 'wb');
}
flock($myfile, LOCK_EX);
fwrite($myfile, serialize($classes));
flock($myfile, LOCK_UN);
fclose($myfile);

echo 'DONE Compiled signatures';
