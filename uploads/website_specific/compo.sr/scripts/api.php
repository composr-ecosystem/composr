<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 You may not distribute a modified version of this file, unless it is solely as a Composr modification.
 See docs/LICENSE.md for full licensing information.

*/

/*EXTRA FUNCTIONS: get_problem_match_script*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = realpath(__FILE__);
$deep = 'uploads/website_specific/compo.sr/scripts/';
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
$FORCE_INVISIBLE_GUEST = true;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<html><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
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

require_code('composr_homesite');

switch (get_param_string('type')) {
    case 'is_release_discontinued':
        $version = floatval(get_param_string('version'));
        $result = is_release_discontinued(float_to_raw_string($version, 1, true));
        break;

    default:
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
}

echo json_encode($result);
