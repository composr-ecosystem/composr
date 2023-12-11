<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

// This is the standard zone bootstrap file.

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
$RELATIVE_PATH = '';
if (getcwd() != $FILE_BASE) {
    @chdir($FILE_BASE);
}

$profile = false;//array_key_exists('tick_profile', $_GET);
if ($profile) {
    global $FUNC_WATCH, $MICROTIME;

    $MICROTIME = microtime(true);

    /**
     * Profile tick function.
     */
    function tick_func()
    {
        global $FUNC_WATCH, $MICROTIME;
        $LAST_MICROTIME = $MICROTIME;
        $MICROTIME = microtime(true);

        $trace = debug_backtrace();
        $func = $trace[1]['function'];
        if (isset($trace[1]['class'])) {
            $func = $trace[1]['class'] . $trace[1]['type'] . $func;
        }
        if (!isset($FUNC_WATCH[$func])) {
            $FUNC_WATCH[$func] = 0;
        }
        $FUNC_WATCH[$func] += $LAST_MICROTIME - $MICROTIME;
    }
    /* register_tick_function('tick_func');
        declare(ticks=10);*/
}

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
global $CSRF_TOKENS;
$CSRF_TOKENS = true;
global $STATIC_CACHE_ENABLED;
$STATIC_CACHE_ENABLED = true;
global $IN_SELF_ROUTING_SCRIPT;
$IN_SELF_ROUTING_SCRIPT = true;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}

require($FILE_BASE . '/sources/global.php');

// If we're still here, we're ok to go
do_site();

if ($profile) {
    asort($FUNC_WATCH);
    print_r($FUNC_WATCH);
}
