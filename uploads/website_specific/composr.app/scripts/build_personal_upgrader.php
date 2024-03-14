<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 You may not distribute a modified version of this file, unless it is solely as a Composr modification.
 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = realpath(__FILE__);
$deep = 'uploads/website_specific/composr.app/scripts/';
$FILE_BASE = str_replace($deep, '', $FILE_BASE);
$FILE_BASE = str_replace(str_replace('/', '\\', $deep), '', $FILE_BASE);
if (substr($FILE_BASE, -4) == '.php') {
    $a = strrpos($FILE_BASE, '/');
    $b = strrpos($FILE_BASE, '\\');
    $FILE_BASE = dirname($FILE_BASE);
}
$RELATIVE_PATH = '';
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<html><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('composr_homesite')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
}

if (!addon_installed('downloads')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
}
if (!addon_installed('news')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('news')));
}

require_code('version2');
require_code('composr_homesite');
require_code('uploads/website_specific/composr.app/upgrades/make_upgrader.php');

$to_version_dotted = get_param_string('to', null);
if ($to_version_dotted === null) {
    $to_version_dotted = get_latest_version_dotted();
}

$from_version_dotted = get_param_string('from', null);

// LEGACY
$addon_name_remap = [
    'cedi' => 'wiki',
    'occle' => 'commandr',
    'ocf_avatars' => 'cns_avatars',
    'ocf_cartoon_avatars' => 'cns_cartoon_avatars',
    'ocf_clubs' => 'cns_clubs',
    'ocf_contactmember' => 'cns_contact_member',
    'ocf_cpfs' => 'cns_cpfs',
    'ocf_forum' => 'cns_forum',
    'ocf_member_avatars' => 'cns_member_avatars',
    'ocf_member_photos' => 'cns_member_photos',
    'ocf_member_titles' => 'cns_member_titles',
    'ocf_multi_moderations' => 'cns_multi_moderations',
    'ocf_post_templates' => 'cns_post_templates',
    'ocf_reported_posts' => 'cns_reported_posts',
    'ocf_signatures' => 'cns_signatures',
    'ocf_thematic_avatars' => 'cns_thematic_avatars',
    'ocf_warnings' => 'cns_warnings',
];

$addons = [];
foreach (array_keys($_GET) as $key) {
    if (substr($key, 0, 6) == 'addon_') {
        $addon_name = substr($key, 6);

        if (isset($addon_name_remap[$addon_name])) {
            $addon_name = $addon_name_remap[$addon_name];
        }

        $addons[$addon_name] = true;
    }
}
ksort($addons);

list($tar_path, $err) = make_upgrade_get_path($from_version_dotted, $to_version_dotted, $addons);
if ($tar_path === null) {
    warn_exit(protect_from_escaping($err));
}

// Note by default wget ignores these Content-Disposition filenames. You can set a custom one with '-O', or use '--content-disposition' to make it respect the one here

header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename="' . escape_header(basename($tar_path), true) . '"');

cms_ob_end_clean();
readfile($tar_path);
