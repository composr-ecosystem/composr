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

/*EXTRA FUNCTIONS: php_sapi_name*/

error_reporting(E_ALL);
ini_set('display_errors', '1');

@header('Content-Type: text/plain; charset=utf-8');

if (!decache_is_cli()) {
    exit('This script must be called on the command line.');
}
chdir(__DIR__);

$to_delete = [
    'themes/*/templates_cached/*' => ['tcp', 'js', 'css', 'gz', 'br'],
    'caches/http' => ['bin'],
    'caches/persistent' => ['bin'],
    'caches/self_learning' => ['gcd'],
    'caches/lang' => ['lcd'],
    'caches/lang/*' => ['lcd'],
    'caches/static' => ['htm', 'br', 'gz', 'xml'],
];

if (is_dir(__DIR__ . '/sites')) {
    $to_delete_sites = [];
    foreach ($to_delete as $directory_glob => $exts) {
        $to_delete_sites['sites/' . $directory_glob] = $exts;
    }
    $to_delete = array_merge($to_delete, $to_delete_sites);
}

foreach ($to_delete as $directory_glob => $exts) {
    $directories = glob(__DIR__ . '/' . $directory_glob, GLOB_NOSORT);
    foreach ($directories as $directory) {
        $dh = @opendir($directory);
        if ($dh !== false) {
            while (($f = readdir($dh)) !== false) {
                foreach ($exts as $ext) {
                    if (substr($f, -strlen($ext) - 1) == '.' . $ext) {
                        $path = $directory . '/' . $f;
                        unlink($path);
                        echo 'Deleted ' . $path . "\n";
                    }
                }
            }
            closedir($dh);
        }
    }
}

if (is_file(__DIR__ . '/data_custom/failover_rewritemap.txt')) {
    file_put_contents(__DIR__ . '/data_custom/failover_rewritemap.txt', '');
    file_put_contents(__DIR__ . '/data_custom/failover_rewritemap__mobile.txt', '');
}

if (is_file(__DIR__ . '/_config.php')) {
    $append = "\n\nif (!defined('DO_PLANNED_DECACHE')) define('DO_PLANNED_DECACHE', true);";
    file_put_contents(__DIR__ . '/_config.php', $append, FILE_APPEND | LOCK_EX);
}

// Useful script, outside of web dir, for doing custom decaching
if (@is_file(dirname(__DIR__) . '/decache.php')) {
    require(dirname(__DIR__) . '/decache.php');
}

if (strpos($_SERVER['argv'][0], 'decache.php') !== false) {
    echo "Done\n";
}

/**
 * Find if running as CLI (i.e. on the command prompt). This implies admin credentials (web users can't initiate a CLI call), and text output.
 *
 * @return boolean Whether running as CLI
 */
function decache_is_cli() : bool
{
    return (php_sapi_name() == 'cli') && (empty($_SERVER['REMOTE_ADDR']));
}
