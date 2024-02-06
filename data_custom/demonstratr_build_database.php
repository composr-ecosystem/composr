<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/*
Run this script after installing a fresh site. Don't run the Setup Wizard or do any config before running it. template.sql will be generated and it should replace the template.sql in Git.
*/

/*EXTRA FUNCTIONS: shell_exec*/

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
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
}

header('X-Robots-Tag: noindex');

if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
    access_denied();
}

// Close site with message
require_code('config2');
set_option('closed', "This is a Composr demo.\n\nLog in using the details you put in when you set up the demo, or if this is the shared demo use the username 'admin' and the password 'demo123'.");
set_option('site_closed', '1');

// Checks
if (!addon_installed('setupwizard')) {
    warn_exit('Setup Wixzard addon must be installed');
}

// Install test content
require_code('setupwizard');
install_test_content();

// Set some options
set_option('url_scheme', 'PG');

// Force option into DB, so Demonstratr can set it with an UPDATE query
get_option('staff_address');

// Save SQL dump...

$filename = 'template.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . escape_header($filename) . '"');

cms_ini_set('ocproducts.xss_detect', '0');

require_code('database_relations');

$out_file_path = cms_tempnam('sql');

get_sql_dump($out_file_path);

// Output
cms_ob_end_clean();
readfile($out_file_path);

// Delete
@unlink($out_file_path);

$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
