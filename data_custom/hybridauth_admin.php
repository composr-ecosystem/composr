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
if (!is_file($FILE_BASE . '/sources/global.php')) {
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
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('hybridauth')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('hybridauth')));
}

header('X-Robots-Tag: noindex');

require_code('hybridauth_admin');
require_lang('hybridauth');

$before_type_strictness = ini_get('ocproducts.type_strictness');
cms_ini_set('ocproducts.type_strictness', '0');
$before_xss_detect = ini_get('ocproducts.xss_detect');
cms_ini_set('ocproducts.xss_detect', '0');

if ((empty($get)) && (empty($_POST))) {
    warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('701b4747b13e551eb913cb8724b4dab6')));
}

$provider = get_param_string('provider', null);
if ($provider !== null) {
    $alternate_config = get_param_string('alternate_config', null);
    list($hybridauth, $admin_storage) = initiate_hybridauth_admin(0, $alternate_config, $provider);

    if (!has_page_access(get_member(), 'admin_oauth', 'adminzone')) {
        access_denied('PAGE_ACCESS');
    }

    if (get_param_integer('hybridauth_blank_state', 0) == 1) {
        $admin_storage->deleteMatch($provider);
    }

    // This is the first stage in the flow
    $admin_storage->set('provider', $provider);
    $admin_storage->set('alternate_config', $alternate_config);
} else {
    require_code('hybridauth_admin_storage');
    $admin_storage = new ComposrHybridauthValuesStorage();
    $alternate_config = $admin_storage->get('alternate_config');
    list($hybridauth, $admin_storage) = initiate_hybridauth_admin(0, $alternate_config);

    // This is the final stage in the flow
    if ($admin_storage->get('provider') === null) {
        warn_exit(do_lang_tempcode('HYBRIDAUTH_SESSION_TIMEOUT'));
    }
    $provider = $admin_storage->get('provider');
}

$adapter = $hybridauth->getAdapter($provider);

try {
    $adapter->authenticate($provider); // Will either push the flow off-site, complete the authentication started previously, or just bring the user's authentication credentials out from a previous authentication

    $success = $adapter->isConnected();

    $message = do_lang_tempcode($success ? 'HYBRIDAUTH_ADMIN_SUCCESS' : 'HYBRIDAUTH_ADMIN_FAILURE', escape_html($provider));

    $admin_storage->delete($provider);

    // Empty block cache
    $GLOBALS['SITE_DB']->query_delete('cache_on');
    $GLOBALS['SITE_DB']->query_delete('cache');
    if (function_exists('persistent_cache_empty')) {
        persistent_cache_empty();
    }
} catch (Hybridauth\Exception\AuthorizationDeniedException $e) {
    $message = do_lang_tempcode('HYBRIDAUTH_ADMIN_CANCELLED', escape_html($provider));
} catch (Exception $e) {
    warn_exit($e->getMessage());
}

cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

$title = get_screen_title('HYBRIDAUTH');
$composr_return_url = build_url(['page' => 'admin_oauth'], get_module_zone('admin_oauth'));

require_code('templates_redirect_screen');
$tpl = redirect_screen($title, $composr_return_url, $message);
$tpl->evaluate_echo();
