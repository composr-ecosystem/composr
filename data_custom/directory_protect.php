<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    directory_protect
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $FILE_BASE = $_SERVER['SCRIPT_FILENAME']; // this is with symlinks-unresolved (__FILE__ has them resolved); we need as we may want to allow zones to be symlinked into the base directory without getting path-resolved
    $FILE_BASE = dirname($FILE_BASE);
    if (!is_file($FILE_BASE . '/sources/global.php')) {
        $RELATIVE_PATH = basename($FILE_BASE);
        $FILE_BASE = dirname($FILE_BASE);
    } else {
        $RELATIVE_PATH = '';
    }
}
@chdir($FILE_BASE);

global $NON_PAGE_SCRIPT;
$NON_PAGE_SCRIPT = true;
global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

$file = get_param_string('file', false, true);
$filename = basename($file);

if (strpos($file, '..') !== false) {
    log_hack_attack_and_exit('PATH_HACK');
}
if (substr(strtolower($file), -4) == '.php') {
    log_hack_attack_and_exit('TRY_TO_DOWNLOAD_SCRIPT');
}

$_full = get_custom_file_base() . '/' . $file;
if (!is_file($_full)) {
    warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
}

$size = filesize($_full);

// Check permissions
$search = array('"' . $file . '"', "'" . $file . "'", '"/' . $file . '"', "'/" . $file . "'", '"' . get_custom_base_url() . '/' . $file . '"', "'" . get_custom_base_url() . '/' . $file . "'");
$okay = false;
$zones = find_all_zones();
foreach ($zones as $zone) {
    $lang = get_site_default_lang();
    foreach (array(array('comcode_custom/' . $lang, 'txt'), array('html_custom/' . $lang, 'htm')) as $_scope) {
        list($page_type, $ext) = $_scope;
        $pages = array_keys(find_all_pages($zone, $page_type, $ext));
        foreach ($pages as $page) {
            $page_path = get_custom_file_base() . (($zone == '') ? '' : ('/' . $zone)) . '/pages/' . $page_type . '/' . $page . '.' . $ext;
            $page_contents = file_get_contents($page_path);
            foreach ($search as $s) {
                if (strpos($page_contents, $s) !== false) {
                    if (has_actual_page_access(get_member(), $page, $zone)) {
                        $okay = true;
                        break 4;
                    }
                }
            }
        }
    }
}
if (!$okay) {
    access_denied('PAGE_ACCESS');
}

// Send header
header('Content-Type: application/octet-stream' . '; authoritative=true;');
require_code('mime_types');
header('Content-Type: ' . get_mime_type(get_file_extension($filename), false) . '; authoritative=true;');
header('Content-Disposition: inline; filename="' . escape_header($filename, true) . '"');
header('Accept-Ranges: bytes');

// Caching
header('Pragma: private');
header('Cache-Control: private');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 24 * 365) . ' GMT');
$time = filemtime($_full);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');

if (cms_srv('REQUEST_METHOD') == 'HEAD') {
    return '';
}

// Default to no resume
$from = 0;
$new_length = $size;

safe_ini_set('zlib.output_compression', 'Off');

// They're trying to resume (so update our range)
$httprange = cms_srv('HTTP_RANGE');
if (strlen($httprange) > 0) {
    $_range = explode('=', cms_srv('HTTP_RANGE'));
    if (count($_range) == 2) {
        if (strpos($_range[0], '-') === false) {
            $_range = array_reverse($_range);
        }
        $range = $_range[0];
        if (substr($range, 0, 1) == '-') {
            $range = strval($size - intval(substr($range, 1)) - 1) . $range;
        }
        if (substr($range, -1, 1) == '-') {
            $range .= strval($size - 1);
        }
        $bits = explode('-', $range);
        if (count($bits) == 2) {
            list($from, $to) = array_map('intval', $bits);
            if (($to - $from != 0) || ($from == 0)) { // Workaround to weird behaviour on Chrome
                $new_length = $to - $from + 1;

                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes ' . $range . '/' . strval($size));
            } else {
                $from = 0;
            }
        }
    }
}
header('Content-Length: ' . strval($new_length));
if (php_function_allowed('set_time_limit')) {
    @set_time_limit(0);
}
error_reporting(0);

// Send actual data
$myfile = fopen($_full, 'rb');
fseek($myfile, $from);
if ($size == $new_length) {
    cms_ob_end_clean();
    fpassthru($myfile);
} else {
    $i = 0;
    flush(); // LEGACY Works around weird PHP bug that sends data before headers, on some PHP versions
    while ($i < $new_length) {
        $content = fread($myfile, min($new_length - $i, 1048576));
        echo $content;
        $len = strlen($content);
        if ($len == 0) {
            break;
        }
        $i += $len;
    }
    fclose($myfile);
}
