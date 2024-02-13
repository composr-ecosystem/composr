<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    helper_scripts
 */

/*EXTRA FUNCTIONS: php_sapi_name|set_time_limit*/

@header('Content-Type: text/plain; charset=utf-8');

if (!fixperms_is_cli()) {
    exit('This script must be called on the command line.');
}

$opts = getopt('h', ['help', 'trial', 'verbose', 'full', 'web_username::', 'is_suexec_like', 'has_ftp_loopback_for_write::', 'minimum_level::', 'web-username::', 'is-suexec-like', 'has-ftp-loopback-for-write::', 'minimum-level::']);
foreach ($opts as $opt => $val) { // Tolerance for use of dashes instead of underscores (common mistake)
    if (strpos($opt, '-') !== false) {
        $opts[str_replace('-', '_', $opt)] = $val;
        unset($opts[$opt]);
    }
}

if ((array_key_exists('h', $opts)) || (array_key_exists('help', $opts))) {
    exit('
Usage: php fixperms.php [options]

 where options include:

    --help                                    show this help

    --trial                                   test only, do not change anything

    --verbose                                 show verbose output

    --full                                    set full permissions, which requires bootstrapping Composr
                                              (not guaranteed to work if some basic permissions are missing)

    --web_username=<username|user_id>         On Linux/Mac OS:
                                               specify the username that the website runs under
                                               (if not passed assumes other permissions will be needed)
                                              On Windows:
                                               specify the username that the website runs under
                                               (if not passed assumes IUSR or SYSTEM depending on base directory)

    --has_ftp_loopback_for_write=[true|false] whether irregular file writes like addon management can be
                                              done by PHP via an FTP-loopback

    --minimum_level                           error reporting level, lower means more output
      0 = show full file-by-file breakdown
      1 = handle unimportant excessive permissions
      2 = handle unimportant suggested permissions
      3 = handle unnecessary dangerous permissions (the default)
      4 = handle necessary missing permissions

    --is_suexec_like                          flag to Composr that you are on a suEXEC-like server
                                              (unrelated to permission checks, just helps Composr
                                              create cache files with the right permissions)
');
}

$full = array_key_exists('full', $opts);
$trial = array_key_exists('trial', $opts);
$verbose = array_key_exists('verbose', $opts);
$web_username = ((array_key_exists('web_username', $opts)) && ($opts['web_username'] != '')) ? $opts['web_username'] : null;
$has_ftp_loopback_for_write = array_key_exists('has_ftp_loopback_for_write', $opts) ? ($opts['has_ftp_loopback_for_write'] == 'true') : null;
$minimum_level = ((array_key_exists('minimum_level', $opts)) && ($opts['minimum_level'] != '')) ? intval($opts['minimum_level']) : 3;

if ($full) {
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

    global $FORCE_INVISIBLE_GUEST;
    $FORCE_INVISIBLE_GUEST = false;
    global $EXTERNAL_CALL;
    $EXTERNAL_CALL = true;
    if (!is_file($FILE_BASE . '/sources/global.php')) {
        exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
    }
    require($FILE_BASE . '/sources/global.php');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('ocproducts.xss_detect', '0');
set_time_limit(0);

chdir(__DIR__);

require(__DIR__ . '/sources/file_permissions_check.php');

if ($trial) {
    echo "Running in trial mode...\n\n";

    list(, , $found_any_issue) = scan_permissions(true, false, $web_username, $has_ftp_loopback_for_write, $minimum_level);

    if (!$found_any_issue) {
        echo "No issues found\n";
    }
} else {
    // Git hooks should be writable, and linked in correctly
    if ((file_exists(__DIR__ . '/git-hooks')) && (file_exists(__DIR__ . '/.git'))) {
        echo "0/2 Setting up Git hooks to run correctly\n";

        echo execute_nicely('git config core.hooksPath git-hooks');
        echo execute_nicely('git config core.fileMode false');

        if (strpos(PHP_OS, 'WIN') === false) {
            $ob = new CMSPermissionsScannerLinux();
            $ob->generate_chmod_command('git-hooks/*', 0100, '+');
        }
    }

    // Commonly the uploads directory can be missing in Git repositories backing up live sites (due to size); but we need it
    if ((!file_exists(__DIR__ . '/uploads')) && (file_exists(__DIR__ . '/data'))) {
        mkdir(__DIR__ . '/uploads', 0755);
    }

    // Clear cache first, as we don't chmod cache files in this code
    if (is_file(__DIR__ . '/decache.php')) {
        require(__DIR__ . '/decache.php');
        echo "1/2 Cleared caches\n";
    }

    // Change permissions
    scan_permissions($verbose, true, $web_username, $has_ftp_loopback_for_write, $minimum_level);
    echo "2/2 Fixed permissions of strewn files\n";
}

$_CREATED_FILES = [];

echo "Done\n";

/**
 * Find if running as CLI (i.e. on the command prompt). This implies admin credentials (web users can't initiate a CLI call), and text output.
 *
 * @return boolean Whether running as CLI
 */
function fixperms_is_cli() : bool
{
    return (function_exists('php_sapi_name')) && (php_sapi_name() == 'cli') && (empty($_SERVER['REMOTE_ADDR']));
}
