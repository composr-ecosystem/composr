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

/**
 * Find if running as CLI (i.e. on the command prompt). This implies admin credentials (web users can't initiate a CLI call), and text output.
 *
 * @return boolean Whether running as CLI
 */
function db_export_is_cli() : bool
{
    return (function_exists('php_sapi_name')) && (php_sapi_name() == 'cli') && (empty($_SERVER['REMOTE_ADDR']));
}

if (!db_export_is_cli()) {
    exit('This script must be called on the command line.');
}

print('Loading up core Composr system...' . "\n");

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
    exit('Cannot initialise the core software; sources/bootstrap.php not found.');
}
if (!is_file($FILE_BASE . '/db.sql')) {
    exit('db.sql not found in the root software directory; did you upload your dump?');
}

require_once $FILE_BASE . '/sources/bootstrap.php';
require_code__bootstrap('global');
require_code('global3');

global $SITE_INFO;

print('Validating _config.php...' . "\n");

$must_be_set = ['db_site_host', 'db_site_user', 'db_site_password', 'db_site'];
foreach ($must_be_set as $key) {
    if (!isset($SITE_INFO[$key])) {
        exit('_config.php has not been configured. You must install Composr, or grab an install _config.php');
    }
}

disable_php_memory_limit();
$old_limit = cms_disable_time_limit();
push_db_scope_check(false);
push_query_limiting(false);

print('Opening db.sql...' . "\n");

$file = fopen($FILE_BASE . '/db.sql', 'rb');
if ($file === false) {
    exit('Unable to open db.sql for reading');
}

print('Importing dump...' . "\n");

$query = '';
$executed = 0;
while (($line = fgets($file)) !== false) {
    $sql_line = trim($line);

    // Empty, divider, or comment lines should be skipped
    if(($sql_line == '') || (strpos($sql_line, '--') === 0) || (strpos($sql_line, '#') === 0) || (strpos($sql_line, '/*') === 0)) {
        continue;
    }

    $query .= $sql_line;

    // Checking whether the line is a valid statement
    if (preg_match('/(.*);$/', $sql_line)) {
        $query = trim($query);
        $query = substr($query, 0, strlen($query) - 1);

        // Execute the query
        $GLOBALS['SITE_DB']->ensure_connected();
        $connection = &$GLOBALS['SITE_DB']->connection_write;
        $GLOBALS['SITE_DB']->driver->query($query, $connection);

        // Count number of queries done
        $executed++;
        if (($executed % 100) == 0) {
            print('Processed ' . strval($executed) . ' queries so far...' . "\n");
        }

        // Reset the variable
        $query = '';
    }
}

fclose($file);

pop_db_scope_check();
pop_query_limiting();

cms_set_time_limit($old_limit);

print('DONE! Total queries: ' . strval($executed));
