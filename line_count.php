<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    meta_toolkit
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $FILE_BASE = $_SERVER['SCRIPT_FILENAME']; // this is with symlinks-unresolved (__FILE__ has them resolved); we need as we may want to allow zones to be symlinked into the base directory without getting path-resolved
    $FILE_BASE = dirname($FILE_BASE);
    if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
        $RELATIVE_PATH = basename($FILE_BASE);
        $FILE_BASE = dirname($FILE_BASE);
    } else {
        $RELATIVE_PATH = '';
    }
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = true;
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/bootstrap.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require_once $FILE_BASE . '/sources/bootstrap.php';
require_code__bootstrap('global');

require_code('files2');

disable_php_memory_limit();

$mode = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'reasonable';

// Config
$debug_output = true;
switch ($mode) {
    case 'overestimate':
        $include_third_party_etc = true;
        $include_txt = true;
        $include_blank_lines = true;
        $include_duplicate_lines = true;
        break;

    case 'underestimate':
        $include_third_party_etc = false;
        $include_txt = false;
        $include_blank_lines = false;
        $include_duplicate_lines = false;
        break;

    case 'reasonable':
    default:
        $include_third_party_etc = false;
        $include_txt = true;
        $include_blank_lines = true;
        $include_duplicate_lines = true;
        break;
}

$file_extensions = [
    'bat',
    'config',
    'css',
    'htaccess',
    'htm',
    'html',
    'ini',
    'java',
    'js',
    'php',
    'sh',
    'sh',
    'tpl',
    'xml',
];
if ($include_txt) {
    $file_extensions = array_merge($file_extensions, [
        'txt',
    ]);
}

$exceptions_directories = array_merge(list_untouchable_third_party_directories(), [
    'data_custom/sitemaps'
]);
$exceptions_files = array_merge(list_untouchable_third_party_files(), [
    'aps/APP-META.xml',
    'text/unbannable_ips.txt',
]);

$total_lines = 0;

$lines = [];

$found = [];

$files = get_directory_contents(get_file_base(), '', IGNORE_ACCESS_CONTROLLERS | IGNORE_EDITFROM_FILES | IGNORE_REVISION_FILES | IGNORE_REBUILDABLE_OR_TEMP_FILES_FOR_BACKUP | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, $file_extensions);
foreach ($files as $file) {
    if (!$include_third_party_etc) {
        if (preg_match('#^(' . implode('|', $exceptions_directories) . ')/#', $file) != 0) {
            continue;
        }
        if (in_array($file, $exceptions_files)) {
            continue;
        }
    }

    $c = file_get_contents(get_file_base() . '/' . $file);

    if ($include_duplicate_lines) {
        if ($include_blank_lines) {
            $line_count = substr_count($c, "\n");
        } else {
            $_lines = explode("\n", $c);
            $line_count = 0;
            foreach ($_lines as $line) {
                if (trim($line) != '') {
                    $line_count++;
                }
            }
        }

        $total_lines += $line_count;
    } else {
        $line_count = substr_count($c, "\n"); // Just for finding large files, we don't really use this

        $_lines = explode("\n", $c);
        foreach ($_lines as $line) {
            if (($include_blank_lines) || (trim($line) != '')) {
                $lines[$line] = true;
            }
        }
    }

    $ext = get_file_extension($file);

    if (!isset($found[$ext])) {
        $found[$ext] = [];
    }
    $found[$ext][$file] = $line_count;
}

if (!$include_duplicate_lines) {
    $total_lines = count($lines);
}

if ($debug_output) {
    ksort($found);
    foreach ($found as $ext => $files) {
        arsort($files);
        $found[$ext] = array_slice($files, 0, 100);
    }

    echo "Longest files by file type...\n";
    var_dump($found);
}

echo integer_format($total_lines) . "\n";
