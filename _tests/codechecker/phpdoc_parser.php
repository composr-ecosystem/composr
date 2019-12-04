<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*
Parse PHPdoc in all scripts under project directory
*/

require(__DIR__ . '/lib.php');

// Handle options
$available_options = [
    'base_path' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
];
if (empty($_GET)) { // CLI
    $longopts = [];
    foreach ($available_options as $key => $settings) {
        $longopts[] = $key . ($settings['takes_value'] ? '::' : '');
    }
    $optind = 1;
    $options = getopt('', $longopts, $optind);
} else {
    $options = $_GET;
}
if (array_key_exists('base_path', $options)) {
    $COMPOSR_PATH = $options['base_path'];
} else {
    $COMPOSR_PATH = '.';
}

require_code('php');

$enable_custom = false;
if ((isset($_GET['allow_custom'])) && ($_GET['allow_custom'] == '1')) {
    $enable_custom = true;
}
$files = do_dir($COMPOSR_PATH, '', $enable_custom, true);
if (!$enable_custom) {
    $files[] = 'sources_custom/phpstub.php';
}

ini_set('memory_limit', '-1');

$classes = [];
$global = [];
global $TO_USE;
//$files = ['sources/global2.php']; For debugging
foreach ($files as $filename) {
    if (strpos($filename, 'sabredav/') !== false || strpos($filename, 'Swift/') !== false || strpos($filename, 'tracker/') !== false) { // Lots of complex code we want to ignore, even if doing custom files
        continue;
    }

    $TO_USE = $COMPOSR_PATH . '/' . $filename;

    if ($filename == 'sources/minikernel.php') {
        continue;
    }
    //echo 'SIGNATURES-DOING ' . $_filename . cnl();
    $result = get_php_file_api($filename, false, true);

    foreach ($result as $i => $r) {
        if ($r['name'] == '__global') {
            if (($filename != 'sources/global.php') && ($filename != 'phpstub.php')) {
                foreach (array_keys($r['functions']) as $f) {
                    if ((isset($global[$f])) && (!in_array($f, ['do_lang', 'mixed', 'qualify_url', 'http_get_contents', 'get_forum_type', 'mailto_obfuscated', 'get_custom_file_base']))) {
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

$classes['__global'] = ['functions' => $global, 'name' => '__global', 'inherits_from' => [], 'type' => null];

// Save file
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
