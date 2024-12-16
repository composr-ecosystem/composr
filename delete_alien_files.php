<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    meta_toolkit
 */

/*EXTRA FUNCTIONS: shell_exec*/

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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/bootstrap.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require_once($FILE_BASE . '/sources/bootstrap.php');
require_code__bootstrap('global');

header('Content-Type: text/plain; charset=' . get_charset());

cms_ini_set('ocproducts.xss_detect', '0');

if (!is_cli()) {
    exit('Must run this script on command line, for security reasons');
}

echo "Suggested commands to run follow...\n";

require_code('files');
require_code('files2');

$files_to_always_keep = [
    'delete_alien_files.php',
    'themes/default/templates_custom/MAIL.tpl',
    'themes/default/text_custom/MAIL.txt',
];

// **********
$addons_definitely_not_wanted = [
    // CUSTOMISE THIS TO REMOVE ADDON FILES
    //  IT ONLY WORKS WHILE HOOK FILES ARE STILL IN PLACE
    //  AFTER WHICH ANY ALIEN FILES WILL FLAG TO DELETE IF THEY ARE NOT POSSIBLE USER CUSTOMISED FILES
];
// ^^^^^^^^^^

$extra_files_to_delete = [];

if (!in_array(git_repos(), ['master', 'main'])) {
    $addons_definitely_not_wanted[] = 'installer';
    $extra_files_to_delete[] = 'install_ok';
    $extra_files_to_delete[] = 'install.php';
    $extra_files_to_delete[] = 'install.sql';
    if (git_repos() != 'cms_homesite_v11') {
        $extra_files_to_delete[] = 'data_custom/images/addon_screenshots';
    }
}

if (git_repos() == 'cms_homesite_v11') {
    // Wanted so that icons, documentation, etc, can still work
    $files_to_always_keep[] = '#^lang/EN/#';
    $files_to_always_keep[] = '#^themes/default/images/#';
    $files_to_always_keep[] = '#^sources_custom/hooks/endpoints/cms_homesite/#';
}

foreach ($extra_files_to_delete as $file) {
    if (file_exists(get_file_base() . '/' . $file)) {
        if (is_dir(get_file_base() . '/' . $file)) {
            echo 'rm -rf ' . cms_escapeshellarg($file) . "\n";
        } else {
            echo 'rm -f ' . cms_escapeshellarg($file) . "\n";
        }
    }
}

global $GFILE_ARRAY;
$GFILE_ARRAY = [];
do_dir();

// Non-installed addons
$hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
$installed_addons = collapse_1d_complexity('addon_name', $GLOBALS['SITE_DB']->query_select('addons', ['addon_name']));
$intersection = array_intersect(array_keys($hooks), $installed_addons);
if (empty($intersection)) {
    echo 'All addons seem still installed. You should uninstall from within Composr.';
} else {
    foreach ($hooks as $hook => $ob) {
        $files = $ob->get_file_list();
        foreach ($files as $path) {
            if (((!in_array($hook, $installed_addons)) || (in_array($hook, $addons_definitely_not_wanted))) && (!force_keep($path, $files_to_always_keep))) {
                if (file_exists(get_file_base() . '/' . $path)) {
                    echo 'rm -f ' . cms_escapeshellarg($path) . "\n";
                }
            }
            unset($GFILE_ARRAY[$path]);
        }
    }
}

// Alien files (non-ignored files not within one of the known addons)
foreach (array_keys($GFILE_ARRAY) as $path) {
    if (!force_keep($path, $files_to_always_keep)) {
        echo 'rm -f ' . cms_escapeshellarg($path) . "\n";
    }
}

// Empty dirs
$directories = get_directory_contents(get_file_base(), '', null, true, false);
$cnt = 0;
foreach ($directories as $directory) {
    $_files = get_directory_contents(get_file_base() . '/' . $directory, $directory, null, false, true);
    $_directories = get_directory_contents(get_file_base() . '/' . $directory, $directory, null, false, false);
    if ((empty($_files)) && (empty($_directories))) {
        echo 'rmdir ' . cms_escapeshellarg($directory) . "\n";
        $cnt++;
    }
}

echo "DONE\n";

if ($cnt > 0) {
    echo "Re-run delete_alien_files.php after running the commands, as the deletions may have led to other empty directories to remove\n";
}

function force_keep($file, $files_to_always_keep)
{
    foreach ($files_to_always_keep as $_file) {
        if (substr($_file, 0, 1) == '#') {
            if (preg_match($_file, $file) != 0) {
                return true;
            }
        } else {
            if ($_file == $file) {
                return true;
            }
        }
    }

    if ((in_array('git_only', $_SERVER['argv'])) && (shell_exec('git ls-files ' . cms_escapeshellarg($file)) === '')) {
        return true;
    }

    return false;
}

function do_dir($dir = '')
{
    global $GFILE_ARRAY;

    $full_dir = get_file_base() . '/' . $dir;
    $dh = opendir($full_dir);
    while (($file = readdir($dh)) !== false) {
        $ignore = IGNORE_CUSTOM_DIRS | IGNORE_UPLOADS | IGNORE_NONBUNDLED | IGNORE_FLOATING | IGNORE_CUSTOM_ZONES | IGNORE_UNSHIPPED_VOLATILE | IGNORE_EDITFROM_FILES | IGNORE_REVISION_FILES;
        if (should_ignore_file($dir . $file, $ignore)) {
            continue;
        }

        $is_dir = is_dir($full_dir . $file);

        if ($is_dir) {
            do_dir($dir . $file . '/');
        } else {
            $GFILE_ARRAY[$dir . $file] = true;
        }
    }
    closedir($dh);
}
