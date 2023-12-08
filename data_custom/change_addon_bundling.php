<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    addon_publish
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

global $NON_PAGE_SCRIPT;
$NON_PAGE_SCRIPT = true;
global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

$password = post_param_string('password', null);
if (is_null($password)) {
    @exit('<form action="#" method="post"><label>Master password <input type="password" name="password" value="" /></label><input class="menu___generic_admin__delete button_screen" type="submit" value="Delete programmed data" /></form>');
}
require_code('crypt_master');
if (!check_master_password($password)) {
    warn_exit('Access denied - you must pass the master password through correctly');
}

header('Content-type: text/plain; charset=' . get_charset());

$mode = get_param_string('mode'); // bundle | unbundle
$addon = get_param_string('addon');

if ($mode == 'unbundle') {
    require_code('hooks/systems/addon_registry/' . $addon);
    $ob = object_factory('Hook_addon_registry_' . $addon);
    $files = $ob->get_file_list();
    foreach ($files as $file) {
        $new_file = null;
        $matches = array();

        if (preg_match('#^themes/default/images/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/images_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/css/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/css_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/templates/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/templates_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/javascript/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/javascript_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/xml/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/xml_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/text/(.*)$#', $file, $matches) != 0) {
            $new_file = 'themes/default/text_custom/' . $matches[1];
        }
        if (preg_match('#^sources/(.*)$#', $file, $matches) != 0) {
            $new_file = 'sources_custom/' . $matches[1];
        }
        if (preg_match('#^pages/modules/(.*)$#', $file, $matches) != 0) {
            $new_file = 'pages/modules_custom/' . $matches[1];
        }
        if (preg_match('#^(.*)/pages/modules/(.*)$#', $file, $matches) != 0) {
            $new_file = $matches[1] . '/pages/modules_custom/' . $matches[2];
        }
        if (preg_match('#^lang/(.*)$#', $file, $matches) != 0) {
            $new_file = 'lang_custom/' . $matches[1];
        }

        if (!is_null($new_file)) {
            //var_dump($new_file);continue;
            if (!file_exists(get_file_base() . '/' . $new_file)) {
                @mkdir(dirname($new_file), 0777, true);
                rename(get_file_base() . '/' . $file, get_file_base() . '/' . $new_file);
                sync_file_move(get_file_base() . '/' . $file, get_file_base() . '/' . $new_file);

                require_code('files');
                $data = file_get_contents(get_file_base() . '/' . $file);
                $data = str_replace('

NOTE TO PROGRAMMERS:
  Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
  **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****
', '', $data);
                cms_file_put_contents_safe(get_file_base() . '/' . $new_file, $data, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
            } // else already moved
        } else {
            //var_dump($file);
        }
    }
}

if ($mode == 'bundle') {
    // Not currently implemented
}

echo 'Done.';
