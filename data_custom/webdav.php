<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    webdav
 */

/*EXTRA FUNCTIONS: strtolower*/

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

if (basename($_SERVER['REQUEST_URI']) == 'webdav.php') {
    exit('The webdav.php script should not be called directly');
}

// Workaround for Mac OS slow-downs
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'PROPFIND') {
    $filename = basename($_SERVER['REQUEST_URI']);
    $bad_files = [
        '.ds_store',
        '.hidden',
        '.metadata_never_index',
        '.metadata_never_index_unless_rootfs',
        '.spotlight-v100',
        'backups.backupdb',
        'mach_kernel',
        '.trashes',
        '.temporaryitems',

        // Windows too
        'desktop.ini',
        'autorun.inf',
        'srvsvc',
    ];
    if (in_array(strtolower($filename), $bad_files)) {
        return;
    }
}
if ($method == 'GET' || $method == 'PROPFIND') {
    $filename = basename($_SERVER['REQUEST_URI']);
    $bad_files = [
        '.ds_store',
    ];
    if (in_array(strtolower($filename), $bad_files)) {
        return;
    }
    if (substr($filename, 0, 2) == '._') {
        return;
    }
}

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;

$ua = $_SERVER['HTTP_USER_AGENT'];
$ipod = stripos($ua, 'iPod') !== false;
$iphone = stripos($ua, 'iPhone') !== false;
$ipad = stripos($ua, 'iPad') !== false;
$android = stripos($ua, 'Android') !== false;
if ($ipod && !$iphone && !$ipad && !$android) {
    // If it might be a web browser we need to be really careful about CSRF, as cookies persist and we could have drive-by attacks between sites
    global $CSRF_TOKENS;
    $CSRF_TOKENS = true;
}

if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

require_code('webdav');
webdav_script();
