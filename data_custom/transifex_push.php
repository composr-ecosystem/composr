<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    transifex
 */

// Examples:
// http://localhost/composr/data_custom/transifex_push.php?username=info@ocproducts.com&password=xxx
// http://localhost/composr/data_custom/transifex_push.php?username=info@ocproducts.com&password=xxx&core_only=1&push_cms=0&push_ini=1&limit_substring=global

// Parameters:
//    username                  Transifex username (required, if not saved)
//    password                  Transifex password (required, if not saved)
//    core_only=0|1             Set to 1 to only upload translations for bundled addons (labelled as 'core' within Transifex) (default: 0)
//    push_cms=0|1              Set to 0 to not push CMS file resources (i.e. pages and templates), Leave as 1 to push them (default: 1)
//    push_ini=0|1              Set to 0 to not push .ini resources, Leave as 1 to push them (default: 1)
//    push_translations=0|1     Set to 1 to push translations also (default: 0)
//    limit_substring           Set to a resource file name substring to upload just that resource file(s) (default: blank)

/*
To save the username/password credentials...
:set_value('transifex_username', 'xxx', true);
:set_value('transifex_password', 'xxx', true);
*/

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
if (substr($FILE_BASE, -4) == '.php') {
    $a = strrpos($FILE_BASE, '/');
    if ($a === false) {
        $a = 0;
    }
    $b = strrpos($FILE_BASE, '\\');
    if ($b === false) {
        $b = 0;
    }
    $FILE_BASE = dirname($FILE_BASE);
}
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $a = strrpos($FILE_BASE, '/');
    if ($a === false) {
        $a = 0;
    }
    $b = strrpos($FILE_BASE, '\\');
    if ($b === false) {
        $b = 0;
    }
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/bootstrap.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require_once($FILE_BASE . '/sources/bootstrap.php');
require_code__bootstrap('global');

require_code('transifex');
transifex_push_script();
