<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

/*
This is a special script for upgrading Demonstratr with minimal effort.

INSTRUCTIONS...

You must have already run an untar:
 - Generate as normal, on the news release for the version you're upgrading
 - Run something like:
  - cd /home/cms/public_html/servers/composr.info
  - wget -O upgrade.cms https://composr.app/upgrades/7.1.2-9%20beta3.cms
  - tar xvf upgrade.cms
  - rm upgrade.cms
Then run this script, http://shareddemo.composr.info/data_custom/demonstratr_upgrade.php
You may need to call it multiple times, with ?from=<number>, if it is timing out
After running the main upgrade this script will tell you files to delete.

NOTES...

Plenty of room for improvement into the future here, e.g. we could move upgraded users over to a separate Demonstratr server as we upgrade them, then erase the old one later.
*/

/*EXTRA FUNCTIONS: shell_exec*/

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
$FORCE_INVISIBLE_GUEST = true;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (array_key_exists('cns', $_GET)) {
    $_GET['use_cns'] = '1';
}

global $SITE_INFO;
$SITE_INFO['no_extra_closed_file'] = '1';

if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('composr_homesite')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
}

header('X-Robots-Tag: noindex');

if (get_base_url() != 'http://shareddemo.composr.info') {
    warn_exit('Must be called for shared demo');
}

require_code('upgrade_integrity_scan');
require_code('upgrade_shared_installs');
require_code('shared_installs');
require_all_core_cms_code();

// Integrity check only?
if (get_param_integer('integrity', 0) == 1) {
    $integrity_check_output = cms_strip_tags(run_integrity_check(), '<input>', false);
    inform_exit(protect_from_escaping($integrity_check_output));
}

// Close site
set_option('closed', do_lang('UPGRADER_CLOSED_FOR_UPGRADES', get_site_name()));
set_option('site_closed', '2');
@rename(get_file_base() . '/closed.html.old', get_file_base() . '/closed.html');

// Clear full cache
clear_caches_1();

// Reset demo
http_get_contents(get_brand_base_url() . '/data_custom/composr_homesite_web_service.php?call=demo_reset');

// Run upgrade
global $SITE_INFO;
$u = current_share_user();
if ($u === null) {
    warn_exit('Eh, this does not seem to be running on a shared site?');
}
upgrade_sharedinstall_sites(get_param_integer('from', 1) - 1);

// Save new SQL dump
$out_path = dirname(get_file_base(), 2) . '/uploads/website_specific/composr.app/demonstratr/template.sql';
if (!file_exists($out_path . '.tmp')) {
    $cmd = '/usr/local/bin/mysqldump';
    if (!is_file($cmd)) {
        $cmd = '/usr/bin/mysqldump';
    }
    $cmd .= ' -u' . cms_escapeshellarg(substr(md5($SITE_INFO['db_site_user'] . '_shareddemo'), 0, 16)) . ' -p' . cms_escapeshellarg($SITE_INFO['db_site_password']) . ' ' . cms_escapeshellarg($SITE_INFO['db_site']) . '_shareddemo';
    $cmd_secret = 'mysqldump -uxxx_shareddemo -pxxx xxx_shareddemo';
    $sql_dump_output = '';
    $sql_dump_output .= '<kbd>' . escape_html($cmd_secret) . ' > ' . $out_path . '.tmp</kbd>:<br />';
    $result = shell_exec($cmd . ' > ' . $out_path . '.tmp');
    $sql_dump_output .= escape_html($result);
    if ((!is_file($out_path . '.tmp')) || (filesize($out_path . '.tmp') == 0)) {
        //echo $cmd . ' > ' . $out_path . '.tmp'; // Temporarily reenable ONLY when debugging, for security reasons
        warn_exit(protect_from_escaping('Failed to create SQL dump (maybe try on command line)...<br /><br />' . $sql_dump_output));
    }
}
@unlink($out_path);
rename($out_path . '.tmp', $out_path);

// Clear rest of cache
clear_caches_2();

// Integrity check
$integrity_check_output = preg_replace('#<input[^<>]*>#', '', run_integrity_check());

// Done
set_option('site_closed', '2');
@rename(get_file_base() . '/closed.html', get_file_base() . '/closed.html.old');
inform_exit(protect_from_escaping('Done! Now, on to the integrity check (action whatever you need to do manually)...<br /><br />' . $integrity_check_output));
