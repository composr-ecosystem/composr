<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    user_mappr
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

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('user_mappr')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('user_mappr')));
}

set_coordinates();

function set_coordinates()
{
    prepare_backend_response('text/plain');

    if (get_forum_type() != 'cns') {
        warn_exit(do_lang_tempcode('NO_CNS'));
    }

    $_coords = get_param_string('coord', '');
    $member_id = get_param_integer('mid', get_member());

    // Prevent hack attempts
    if ($member_id != get_member()) {
        return;
    }

    $coords = explode('_', $_coords);

    $latitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_latitude']);

    $longitude_cpf_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => 'cms_longitude']);

    // Are there latitude and longtitude Custom Profile Fields ?
    if (($longitude_cpf_id === null) || ($latitude_cpf_id === null)) {
        return;
    }

    // Check for inputed coordinates
    if (!is_array($coords) || !isset($coords[0]) || !isset($coords[1])) {
        return;
    }

    $GLOBALS['FORUM_DB']->query_update('f_member_custom_fields', [
        'field_' . strval($latitude_cpf_id) => $coords[0],
        'field_' . strval($longitude_cpf_id) => $coords[1],
    ], ['mf_member_id' => $member_id]);
}
