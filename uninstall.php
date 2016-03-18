<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    uninstaller
 */

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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

appengine_general_guard();

if (uninstall_check_master_password(post_param_string('given_password', null))) {
    $uninstalled = do_template('BASIC_HTML_WRAP', array('_GUID' => '5614c65c4f388fd47aabb24b9624ce65', 'TITLE' => do_lang_tempcode('UNINSTALL'), 'CONTENT' => do_lang_tempcode('UNINSTALLED')));

    $tables = collapse_1d_complexity('m_table', $GLOBALS['SITE_DB']->query_select('db_meta', array('DISTINCT m_table')));
    foreach ($tables as $table) {
        $GLOBALS['SITE_DB']->drop_table_if_exists($table);
    }
    $GLOBALS['SITE_DB']->drop_table_if_exists('db_meta_indices');
    $GLOBALS['SITE_DB']->drop_table_if_exists('db_meta');

    $uninstalled->evaluate_echo();
} else {
    $echo = do_template('BASIC_HTML_WRAP', array('_GUID' => '009e7517e7df76167b4d13ca77308704', 'NOFOLLOW' => true, 'TITLE' => do_lang_tempcode('UNINSTALL'), 'CONTENT' => do_template('UNINSTALL_SCREEN')));
    $echo->evaluate_echo();
}

/**
 * Check the given master password is valid.
 *
 * @param  ?SHORT_TEXT $password_given Given master password (null: none)
 * @return boolean Whether it is valid
 */
function uninstall_check_master_password($password_given)
{
    if (is_null($password_given)) {
        return false;
    }

    global $SITE_INFO;
    if (!array_key_exists('master_password', $SITE_INFO)) {
        exit('No master password defined in _config.php currently so cannot authenticate');
    }
    $actual_password_hashed = $SITE_INFO['master_password'];
    if ((function_exists('password_verify')) && (strpos($actual_password_hashed, '$') !== false)) {
        return password_verify($password_given, $actual_password_hashed);
    }
    $salt = '';
    if ((substr($actual_password_hashed, 0, 1) == '!') && (strlen($actual_password_hashed) == 33)) {
        $actual_password_hashed = substr($actual_password_hashed, 1);
        $salt = 'cms';

        // LEGACY
        if ($actual_password_hashed != md5($password_given . $salt)) {
            $salt = 'ocp';
        }
    }
    return (((strlen($password_given) != 32) && ($actual_password_hashed == $password_given)) || ($actual_password_hashed == md5($password_given . $salt)));
}
