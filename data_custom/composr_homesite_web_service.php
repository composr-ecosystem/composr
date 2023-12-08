<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
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
    exit('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n" . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

require_code('composr_homesite');

header('Content-type: text/plain; charset=' . get_charset());

$call = get_param_string('call');

$parameters = isset($_POST['parameters']) ? $_POST['parameters'] : array();
if (@get_magic_quotes_gpc()) {
    $parameters = array_map('stripslashes', $parameters);
}

$password_given = post_param_string('password', null);
if ($password_given === null) {
    call_user_func_array('server__public__' . $call, $parameters);
} else {
    if (strpos($password_given, ':') !== false) {
        list($username, $password) = array_map('trim', explode(':', $password_given, 2));

        $login_array = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, apply_forum_driver_md5_variant($password, $username), $password);
        $member = $login_array['id'];
        if (($member === null) || (!$GLOBALS['FORUM_DRIVER']->is_super_admin($member))) {
            exit('Access Denied');
        }
    } else {
        require_code('crypt_master');
        if (!check_master_password($password_given)) {
            exit('Access Denied');
        }

        $member = LEAD_DEVELOPER_MEMBER_ID;
    }

    require_code('users_inactive_occasionals');
    create_session($member);

    if (function_exists('server__' . $call)) {
        call_user_func_array('server__' . $call, $parameters);
    } else {
        call_user_func_array('server__public__' . $call, $parameters);
    }
}
