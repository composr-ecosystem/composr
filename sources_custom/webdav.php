<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    webdav
 */

/*CQC: No check*/

use Sabre\DAV;

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__webdav()
{
    global $COMMANDR_FS_LISTING_CACHE;
    $COMMANDR_FS_LISTING_CACHE = [];

    global $WEBDAV_LOG_FILE;
    $WEBDAV_LOG_FILE = null;
}

/**
 * Main WebDAV entry-point script.
 */
function webdav_script()
{
    if (!addon_installed('webdav')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('webdav')));
    }

    if (!addon_installed('commandr')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('commandr')));
    }

    cms_ini_set('ocproducts.type_strictness', '0');

    header('X-Robots-Tag: noindex');

    require_code('sabredav/vendor/autoload');

    require_code('webdav_commandr_fs');

    // Optional logging (create this file and give write access to it)
    $log_path = get_custom_file_base() . '/data_custom/modules/webdav/tmp/debug.log';
    global $WEBDAV_LOG_FILE;
    if (is_file($log_path)) {
        $WEBDAV_LOG_FILE = fopen($log_path, 'ab');
        $log_message = 'Request... ' . $_SERVER['REQUEST_METHOD'] . ': ' . $_SERVER['REQUEST_URI'];
        //$log_message.="\n".file_get_contents('php://input'); // Only enable when debugging, as breaks PUT requests (see http://stackoverflow.com/questions/3107624/why-can-php-input-be-read-more-than-once-despite-the-documentation-saying-othe)
        webdav_log($log_message);
    }

    // Initialise...

    $root_dir = new webdav_commandr_fs\Directory('');
    $server = new DAV\Server($root_dir);

    $parsed = parse_url(get_base_url());
    if (!isset($parsed['path'])) {
        $parsed['path'] = '';
    }
    if (substr($parsed['path'], -1) != '/') {
        $parsed['path'] .= '/';
    }
    $webdav_root = get_value('webdav_root');
    if ($webdav_root === null) {
        $webdav_root = (preg_match('#^' . preg_quote($parsed['path'], '#') . 'webdav#', $_SERVER['REQUEST_URI']) != 0) ? 'webdav' : '';
    }
    $server->setBaseUri($parsed['path'] . $webdav_root);

    if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) { // If already admin (e.g. backdoor_ip), no need for access check
        $auth_backend = new webdav_commandr_fs\Auth();
        $auth_plugin = new DAV\Auth\Plugin($auth_backend);
        $server->addPlugin($auth_plugin);
    }

    $lock_backend = new DAV\Locks\Backend\File(get_custom_file_base() . '/data_custom/modules/webdav/locks/locks.dat');
    $lock_plugin = new DAV\Locks\Plugin($lock_backend);
    $server->addPlugin($lock_plugin);

    $tffp = new DAV\TemporaryFileFilterPlugin(get_custom_file_base() . '/data_custom/modules/webdav/tmp');
    $server->addPlugin($tffp);

    $plugin = new DAV\Browser\Plugin();
    $server->addPlugin($plugin);

    $server->start();

    // Close off log
    if ($WEBDAV_LOG_FILE !== null) {
        fclose($WEBDAV_LOG_FILE);
        $WEBDAV_LOG_FILE = null;
    }
}

/**
 * Log something to the WebDAV log.
 *
 * @param  string $str String to log
 */
function webdav_log(string $str)
{
    global $WEBDAV_LOG_FILE;
    if ($WEBDAV_LOG_FILE !== null) {
        fwrite($WEBDAV_LOG_FILE, $str . "\n\n");
    }
}
