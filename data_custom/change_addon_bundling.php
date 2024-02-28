<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('addon_publish')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('addon_publish')));
}

if (!addon_installed('downloads')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
}
if (!addon_installed('galleries')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('galleries')));
}

header('X-Robots-Tag: noindex');

$password = post_param_string('password', null, INPUT_FILTER_PASSWORD);
if ($password === null) {
    @exit('<form action="#" method="post"><label>Maintenance password <input type="password" name="password" value="" /></label><button class="btn btn-danger btn-scr" type="submit">' . do_template('ICON', ['_GUID' => '7904bb84e51bd2ae7c3d9bd0e480406d', 'NAME' => 'admin/delete3'])->evaluate() . ' Delete programmed data</button></form>');
}
require_code('crypt_master');
if (!check_maintenance_password($password)) {
    warn_exit('Access denied - you must pass the maintenance password through correctly');
}

header('Content-Type: text/plain; charset=' . get_charset());

$mode = get_param_string('mode'); // bundle | unbundle
$addon_name = get_param_string('addon');

if ($mode == 'unbundle') {
    require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($addon_name));
    $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($addon_name));
    $files = $ob->get_file_list();
    foreach ($files as $path) {
        $new_file = null;
        $matches = [];

        if (preg_match('#^themes/default/images/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/images_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/css/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/css_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/templates/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/templates_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/javascript/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/javascript_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/xml/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/xml_custom/' . $matches[1];
        }
        if (preg_match('#^themes/default/text/(.*)$#', $path, $matches) != 0) {
            $new_file = 'themes/default/text_custom/' . $matches[1];
        }
        if (preg_match('#^sources/(.*)$#', $path, $matches) != 0) {
            $new_file = 'sources_custom/' . $matches[1];
        }
        if (preg_match('#^pages/modules/(.*)$#', $path, $matches) != 0) {
            $new_file = 'pages/modules_custom/' . $matches[1];
        }
        if (preg_match('#^(.*)/pages/modules/(.*)$#', $path, $matches) != 0) {
            $new_file = $matches[1] . '/pages/modules_custom/' . $matches[2];
        }
        if (preg_match('#^lang/(.*)$#', $path, $matches) != 0) {
            $new_file = 'lang_custom/' . $matches[1];
        }

        if ($new_file !== null) {
            //var_dump($new_file);continue;
            if (!file_exists(get_file_base() . '/' . $new_file)) {
                @mkdir(dirname($new_file), 0777, true);
                rename(get_file_base() . '/' . $path, get_file_base() . '/' . $new_file);
                sync_file_move(get_file_base() . '/' . $path, get_file_base() . '/' . $new_file);

                require_code('files');
                $data = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
                $data = str_replace('

NOTE TO PROGRAMMERS:
  Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
  **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****
', '', $data);
                cms_file_put_contents_safe(get_file_base() . '/' . $new_file, $data, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
            }
            // else already moved
        } else {
            //var_dump($path);
        }
    }
}

if ($mode == 'bundle') {
    // Not currently implemented
}

echo 'Done.';
