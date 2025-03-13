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

if (function_exists('php_sapi_name')) {
    if ((php_sapi_name() != 'cli') || (!empty($_SERVER['REMOTE_ADDR']))) {
        exit('This script must be called on the command line.');
    }
} else {
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
require_once $FILE_BASE . '/sources/bootstrap.php';
require_code__bootstrap('global');

global $SITE_INFO;

print('Validating _config.php...' . "\n");

$must_be_set = ['db_site_host', 'db_site_user', 'db_site_password', 'db_site'];
foreach ($must_be_set as $key) {
    if (!isset($SITE_INFO[$key])) {
        exit('_config.php has not been configured. You must install Composr, or grab an install _config.php');
    }
}

print('Loading up database metadata...' . "\n");

require_code('database_relations');
$all_tables = get_table_purpose_flags();
$tables_with_data = [];
$tables_no_data = [];

foreach ($all_tables as $table => $flags) {
    if (table_has_purpose_flag($table, TABLE_PURPOSE__NO_BACKUPS)) {
        $tables_no_data[] = $table;
    } else {
        $tables_with_data[] = $table;
    }
}

print('Preparing db.sql...' . "\n");

$path = $FILE_BASE . '/db.sql';
$file = fopen($path, 'wb');
if ($file === false) {
    exit('Failed to open db.sql for writing.');
}

print('Dumping non-volatile tables (structure and data)...' . "\n");

get_sql_dump($file, true, true, true, [], $tables_with_data);

print('Dumping volatile tables (structure only)...' . "\n");

get_sql_dump($file, true, false, true, [], $tables_no_data);

print('DONE! Your dump is ready in db.sql.');
