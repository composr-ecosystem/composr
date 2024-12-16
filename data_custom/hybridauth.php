<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    hybridauth
 */

$get = $_GET;

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/bootstrap.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require_once($FILE_BASE . '/sources/bootstrap.php');
require_code__bootstrap('global');

if (!addon_installed('hybridauth')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('hybridauth')));
}

header('X-Robots-Tag: noindex');

require_code('hybridauth');
require_lang('hybridauth');

$before_type_strictness = ini_get('ocproducts.type_strictness');
cms_ini_set('ocproducts.type_strictness', '0');
$before_xss_detect = ini_get('ocproducts.xss_detect');
cms_ini_set('ocproducts.xss_detect', '0');

if ((empty($get)) && (empty($_POST))) {
    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('75a3dc9622345563bf4e014b1f717173')));
}

initiate_hybridauth_session_state();

$composr_return_url = get_param_string('composr_return_url', null, INPUT_FILTER_URL_GENERAL);
if ($composr_return_url !== null) {
    if ((get_param_integer('keep_hybridauth_blank_state', 0) == 1) && ($GLOBALS['DEV_MODE'])) {
        @session_destroy();
        session_start();
    }

    // This is the first stage in the flow
    $provider = get_param_string('provider');
    $_SESSION['provider'] = $provider;
    $_SESSION['composr_return_url'] = $composr_return_url;
} else {
    // This is the final stage in the flow
    if ((!isset($_SESSION['provider'])) || (!isset($_SESSION['composr_return_url']))) {
        warn_exit(do_lang_tempcode('HYBRIDAUTH_SESSION_TIMEOUT'));
    }
    $provider = $_SESSION['provider'];
    $composr_return_url = $_SESSION['composr_return_url'];
}

$hybridauth = initiate_hybridauth();

$adapter = $hybridauth->getAdapter($provider);

try {
    $adapter->authenticate($provider); // Will either push the flow off-site, complete the authentication started previously, or just bring the user's authentication credentials out from a previous authentication

    $success = $adapter->isConnected();

    if ($success) {
        $user_profile = $adapter->getUserProfile();

        $member_id = hybridauth_handle_authenticated_account($provider, $user_profile);

        // Set log in
        hybridauth_log_in_authenticated_account($member_id);
    }

    $message = do_lang_tempcode($success ? 'LOGGED_IN_WITH_SUCCESS' : 'LOGGED_IN_WITH_FAILURE', escape_html($provider));
} catch (Hybridauth\Exception\AuthorizationDeniedException $e) {
    $message = do_lang_tempcode('LOGGED_IN_CANCELLED', escape_html($provider));
} catch (Exception $e) {
    warn_exit($e->getMessage());
}

if ($before_type_strictness !== false) {
    cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
}
if ($before_xss_detect !== false) {
    cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
}

$title = get_screen_title('_LOGIN');

if ($composr_return_url !== null) {
    require_code('templates_redirect_screen');
    $tpl = redirect_screen($title, $composr_return_url, $message);
} else {
    $tpl = inform_screen($title, $message);
}

$tpl->evaluate_echo();
