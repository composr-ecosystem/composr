<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('hybridauth')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('hybridauth')));
}

require_code('hybridauth_admin');
require_lang('hybridauth');

$before_type_strictness = ini_get('ocproducts.type_strictness');
cms_ini_set('ocproducts.type_strictness', '0');
$before_xss_detect = ini_get('ocproducts.xss_detect');
cms_ini_set('ocproducts.xss_detect', '0');

if ((empty($get)) && (empty($_POST))) {
    warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
}

list($hybridauth, $admin_storage, $providers) = initiate_hybridauth_admin();

$provider = get_param_string('provider');

if (!isset($providers[$provider])) {
    warn_exit($provider . ' is not configured.');
}

$adapter = $hybridauth->getAdapter($provider);

if (!$adapter->isConnected()) {
    warn_exit($provider . ' is not connected.');
}

if (!$adapter instanceof Hybridauth\Adapter\AtomInterface) {
    warn_exit('Atom interface not implemented by ' . $provider);
}

$max = get_param_integer('max', 30);
$filter = new \Hybridauth\Atom\Filter();
$filter->categoryFilter = get_param_string('categoryFilter', null);
if ($filter->categoryFilter === '') {
    $filter->categoryFilter = null;
}
$filter->enclosureTypeFilter = get_param_integer('enclosureTypeFilter', null);
$filter->includeContributedContent = (get_param_integer('includeContributedContent', 0) == 1);
$filter->includePrivate = (get_param_integer('includePrivate', 0) == 1);
$filter->limit = $max;

$truly_valid = (get_param_integer('truly_valid', 0) == 1);

try {
    $feed = $adapter->buildAtomFeed($filter, $truly_valid);
} catch (Exception $e) {
    warn_exit($e->getMessage());
}

header('Content-Type: text/xml; charset=utf-8');
echo $feed;

cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
