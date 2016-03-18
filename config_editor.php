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
@chdir($FILE_BASE);

if (get_magic_quotes_gpc()) {
    foreach ($_POST as $key => $val) {
        if (is_array($val)) {
            $_POST[$key] = array_map('stripslashes', $val);
        } else {
            $_POST[$key] = stripslashes($val);
        }
    }
    foreach ($_GET as $key => $val) {
        $_GET[$key] = stripslashes($val);
    }
}

require_once($FILE_BASE . '/_config.php');
if (isset($GLOBALS['SITE_INFO']['admin_password'])) {
    $GLOBALS['SITE_INFO']['master_password'] = $GLOBALS['SITE_INFO']['admin_password'];
    unset($GLOBALS['SITE_INFO']['admin_password']);
}

if (!is_writable($FILE_BASE . '/_config.php')) {
    ce_do_header();
    echo('<em>_config.php is not writeable, so the config editor cannot edit it. Please either edit the file manually or change it\'s permissions appropriately.</em>');
    ce_do_footer();
    exit();
}

ce_do_header();
if ((array_key_exists('given_password', $_POST))) {
    $given_password = $_POST['given_password'];
    if (co_check_master_password($given_password)) {
        if (count($_POST) == 1) {
            do_access($given_password);
        } else {
            do_set();
        }
    } else {
        ce_do_login();
    }
} else {
    ce_do_login();
}
ce_do_footer();

/**
 * Output the config editors page header.
 */
function ce_do_header()
{
    echo '
<!DOCTYPE html>
<html lang="EN">
<head>
    <title>Composr Installation Options editor</title>
    <link rel="icon" href="http://compo.sr/favicon.ico" type="image/x-icon" />
    <style>
';
    @print(preg_replace('#/\*\s*\*/\s*#', '', str_replace('url(\'\')', 'none', str_replace('url("")', 'none', preg_replace('#\{\$[^\}]*\}#', '', preg_replace('#\{\$\?,\{\$MOBILE\},([^,]+),([^,]+)\}#', '$2', file_get_contents($GLOBALS['FILE_BASE'] . '/themes/default/css/global.css')))))));
    echo '
        .screen_title { text-decoration: underline; display: block; background: url(\'themes/default/images/icons/48x48/menu/_generic_admin/tool.png\') top left no-repeat; min-height: 42px; padding: 10px 0 0 60px; }
        .button_screen { padding: 0.5em 0.3em !important; }
        a[target="_blank"], a[onclick$="window.open"] { padding-right: 0; }
    </style>

    <meta name="robots" content="noindex, nofollow" />
</head>
<body class="website_body" style="margin: 1em"><div class="global_middle">
    <h1 class="screen_title">Composr Installation Options editor</h1>
    <p>This is an editor kept as simple as possible, to allow fixing of configuration problems when Composr is not in a workable state. It is provided in English only, and only modifies the configuration file, not the database.</p>
    <form action="config_editor.php" method="post">
';
}

/**
 * Output the config editors page footer.
 */
function ce_do_footer()
{
    echo '
        </form>
    ';

    global $SITE_INFO;
    if (array_key_exists('base_url', $SITE_INFO)) {
        echo '
            <hr />
            <ul class="actions_list" role="navigation">
                    <li><a href="' . htmlentities($SITE_INFO['base_url']) . '/adminzone/index.php">Go to Admin Zone</a></li>
            </ul>
        ';
    }
    echo '
        </div></body>
    </html>
    ';
}

/**
 * Output a login page.
 */
function ce_do_login()
{
    if (@$_POST['given_password']) {
        echo '<p><strong>Invalid password</strong></p>';
    }

    echo '
        <label for="given_password">Master Password: <input type="password" name="given_password" id="given_password" /></label>

        <p><input class="button_screen menu__site_meta__user_actions__login" type="submit" value="Log in" /></p>
    ';
}

/**
 * Output the editing page.
 *
 * @param  string $given_password The password given to get here (so we don't need to re-enter it each edit).
 */
function do_access($given_password)
{
    $settings = array(
        'admin_username' => 'The username used for the administrator when Composr is installed to not use a forum. On the vast majority of sites this setting does nothing.',
        'master_password' => 'If you wish the password to be changed, enter a new password here. Otherwise leave blank.',

        'base_url' => 'A critical option, that defines the URL of the site (no trailing slash). You can blank this out for auto-detection, but only do this during development -- if you do it live and somehow multiple domains can get to your site, random errors will occur due to caching problems.',
        'domain' => 'The domain that e-mail addresses are registered on. This applies only to the Point Store and may be ignored by most.',
        'default_lang' => 'The default language used on the site (language codename form, of subdirectory under lang/).',
        'block_mod_rewrite' => 'Whether to block the short-URL (mod_rewrite) option. Set this to 1 if you turned on short-URLs and find your site no longer works.',
        'on_msn' => 'Whether this is a site on an Conversr multi-site-network (enable for to trigger URLs to avatars and photos to be absolute).',

        'forum_type' => '<em>Forum:</em> The forum driver to use. Note that it is unwise to change this unless expert, as member-IDs and usergroup-IDs form a binding between portal and forum, and would need remapping. To convert to Conversr, the forum importers can handle all of this automatically.',
        'board_prefix' => '<em>Forum:</em> This is the base-URL for the forums. If it is not correct, various links, such as links to topics, will not function correctly.',

        'db_type' => '<em>Database:</em> The database driver to use (code of PHP file in sources[_custom]/database/). Only MySQL supported officially.',
        'table_prefix' => '<em>Database:</em> The table prefix for Composr\'s database tables.',
        'db_site' => '<em>Database:</em> The database name of the Composr database.',
        'db_site_host' => '<em>Database:</em> The database hosting computer name (usually localhost) for the Composr database.',
        'db_site_user' => '<em>Database:</em> The database username for Composr to connect to the Composr database with.',
        'db_site_password' => '<em>Database:</em> The password for the Composr database username.',
        'cns_table_prefix' => '<em>Database:</em> The table prefix for Conversr, if Conversr is being used.',
        'db_forums' => '<em>Database:</em> The database name for the forum driver to tie in to.',
        'db_forums_host' => '<em>Database:</em> The database hosting computer name (usually localhost) for the forum driver to tie in to.',
        'db_forums_user' => '<em>Database:</em> The database username for the forum driver to connect to the forum database with.',
        'db_forums_password' => '<em>Database:</em> The password for the forum database username.',
        'use_persistent' => '<em>Database:</em> Whether to use persistent database connections (most shared webhosts do not like these to be used).',
        'database_charset' => '<em>Database:</em> The MySQL character set for the connection. Usually you can just leave this blank, but if MySQL\'s character set for your database has been overridden away from the server-default then you will need to set this to be equal to that same character set.',
        'skip_fulltext_sqlserver' => '<em>Database:</em> Set to \'1\' if you are using Microsoft SQL Server and want to disable full-text search support.',

        'user_cookie' => '<em>Cookies:</em> The name of the cookie used to hold usernames/ids for each user. Dependant on the forum system involved, and may use a special serialisation notation involving a colon (there is no special notation for Conversr).',
        'pass_cookie' => '<em>Cookies:</em> The name of the cookie used to hold passwords for each user.',
        'session_cookie' => '<em>Cookies:</em> The name of the cookie used to hold session IDs.',
        'cookie_domain' => '<em>Cookies:</em> The domain name the cookies are tied to. Only URLs with this domain, or a subdomain there-of, may access the cookies. You probably want to leave it blank. Use blank if running Composr off the DNS system (e.g. localhost), or if you want the active-domain to be used (i.e. autodetection). <strong>It\'s best not to change this setting once your community is active, as it can cause logging-out problems.</strong>',
        'cookie_path' => '<em>Cookies:</em> The URL path the cookeis are tied to. Only URLs branching from this may access the cookies. Either set it to the path portion of the base-URL, or a shortened path if cookies need to work with something elsewhere on the domain, or leave blank for auto-detection. <strong>It\'s best not to change this setting once your community is active, as it can cause logging-out problems.</strong>',
        'cookie_days' => '<em>Cookies:</em> The number of days to store login cookies for.',

        'use_persistent_cache' => '<em>Performance:</em> Whether persistent memory caching is to be used (caches data in memory between requests using whatever appropriate PHP extensions are available).',
        'fast_spider_cache' => '<em>Performance:</em> The number of hours that the spider/bot cache lasts for (this sets both HTTP caching, and server retention of cached screens).',
        'any_guest_cached_too' => '<em>Performance:</em> Whether Guests are cached with the spider cache time too.',
        'self_learning_cache' => '<em>Performance:</em> Whether to allow pages to learn what resources they need, for efficient bulk loading of essentials while avoiding loading full resource sets upfront. Stores copies of some resources within the self-learning cache itself. If you disable this you should also disable the Composr "Output streaming" config option.',

        'max_execution_time' => '<em>Performance:</em> The time in seconds to use for PHP\'s maximum execution time option. Composr defaults to 60 and raises it in known situations that require more time.',

        'disable_smart_decaching' => '<em>Tuning/Disk performance:</em> Don\'t check file times to check caches aren\'t stale.',
        'no_disk_sanity_checks' => '<em>Tuning/Disk performance:</em> Assume that there are no missing language directories, or other configured directories; things may crash horribly if they are missing and this is enabled.',
        'hardcode_common_module_zones' => '<em>Tuning/Disk performance:</em> Don\'t search for common modules, assume they are in default positions.',
        'charset' => '<em>Tuning/Disk performance:</em> The character set (if set, it skips an extra disk check inside the language files).',
        'known_suexec' => '<em>Tuning/Disk performance:</em> Whether we know suEXEC is on the server so will skip checking for it (which involves a disk access).',
        'assume_full_mobile_support' => '<em>Tuning/Disk performance:</em> Whether to assume that the current theme fully supports mobile view-mode, on all pages. This skips a disk access.',
        'no_extra_bots' => '<em>Tuning/Disk performance:</em> Whether to only use the hard-coded bot detection list. This saves a disk access.',
        'no_extra_closed_file' => '<em>Tuning/Disk performance:</em> Whether to not recognise a closed.html file. This saves a disk access.',
        'no_extra_logs' => '<em>Tuning/Disk performance:</em> Whether to not populate extra logs even if writable files have been put in place for this. This saves disk accesses to look for these files.',
        'no_extra_mobiles' => '<em>Tuning/Disk performance:</em> Whether to only use the hard-coded mobile-device detection list. This saves a disk access.',
        'no_installer_checks' => '<em>Tuning/Disk performance:</em> Whether to skip complaining if the install.php file has been left around. This is intended only for developers working on development machines.',

        'prefer_direct_code_call' => '<em>Tuning:</em> Assume a good opcode cache is present, so load up full code files via this rather than trying to save RAM by loading up small parts of files on occasion.',

        'backdoor_ip' => '<em>Security:</em> Always allow users accessing from this IP address in, automatically logged in as the oldest admin of the site.',
        'full_ips' => '<em>Security:</em> Whether to match sessions to the full IP addresses. Set this to 1 if you are sure users don\'t jump around IP addresses on the same 255.255.255.0 subnet (e.g. due to proxy server randomisation).',
        /*  Don't want this in here, we want it autodetected unless explicitly overridden
        'dev_mode' => '<em>Development:</em> Whether development mode is enabled (<strong>intended only for core Composr programmers</strong>).',
        */
        'force_no_eval' => '<em>Development:</em> Whether to force extra strictness that is required for Composr to run on non-native PHP environments.',
        'no_keep_params' => '<em>Development:</em> Whether to disable support for \'keep_\' params in Composr. You probably don\'t want to disable them!',
        'safe_mode' => '<em>Development:</em> Whether Composr is to be forced into Safe Mode, meaning no custom files will load and most caching will be disabled.',
        'no_email_output' => '<em>Development:</em> Whether emails should never be sent.',
        'email_to' => '<em>Development:</em> If you have set up a customised critical error screen, and a background e-mailing process, this defines where error e-mails will be sent.',
        'no_ssl' => '<em>Development:</em> Disable SSL (useful for conditionally disabling on development sites without SSL, when running a database with SSL configured)',
        'nodejs_binary_path' => '<em>Development:</em> Provide the path to your installed Node.js binary to use it for compiling .less stylesheets. You will also need to run `npm install less` in your Composr directory to install the NPM module.',

        'failover_mode' => '<em>Failover:</em> The failover mode. Either \'off\' or \'on\' or \'auto_off\' or \'auto_on\'. Usually it will be left to \'off\', meaning there is no active failover mode. The next most common setting will be \'auto_off\', which means the failover_script.php script is allowed to set it to \'auto_on\' if it detects the site is failing (and back to \'auto_off\' again when things are okay again). Setting it to \'on\' is manually declaring the site has failed and you want to keep it in failover mode.',
        'failover_apache_rewritemap_file' => '<em>Failover:</em> Set to \'1\' to maintain an Apache RewriteMap file that maps disk cache files to URLs directly. This is a very advanced option and needs server-level Apache configuration by a programmer. You can also set to \'-\' which is like \'1\' except mobile hits are not differentiated from desktop hits.',
        'failover_cache_miss_message' => '<em>Failover:</em> Error message shown if failover mode misses a cache hit (i.e. cannot display a page from the cache).',
        'failover_check_urls' => '<em>Failover:</em> Relative URL(s) separated by \';\' that failover mode should check when deciding to activate/deactivate.',
        'failover_email_contact' => '<em>Failover:</em> E-mail address separated by \';\' that failover mode notifications are sent to.',
        'failover_loadaverage_threshold' => '<em>Failover:</em> Minimum load average before failover mode activates.',
        'failover_loadtime_threshold' => '<em>Failover:</em> Minimum page load time in seconds before failover mode activates.',
        'failover_message' => '<em>Failover:</em> Message shown at top of the screen when failover mode is activated.',
        'failover_message_place_after' => '<em>Failover:</em> failover_message will be placed after this HTML marker.',
        'failover_message_place_before' => '<em>Failover:</em> failover_message will be placed before this HTML marker. May be specified in addition to failover_message_place_after, so that two messages show.',

        'rate_limiting' => '<em>Rate limiting:</em> Whether to enable rate limiting for IPs. Set to \'1\' to enable it. It is not enabled by default. The data_custom/rate_limiter.php file must exist and be writeable (on a suExec-style server the file will auto-create, otherwise just make it as an empty file). IP addresses passed to PHP must be accurate.',
        'rate_limit_time_window' => '<em>Rate limiting:</em> The number of seconds hits are counted across. Defaults to \'10\'.',
        'rate_limit_hits_per_window' => '<em>Rate limiting:</em> The number of hits per IP going back as far as the time window. Defaults to \'5\'.',

        'gae_application' => '<em>Google App Engine:</em> Application name',
        'gae_bucket_name' => '<em>Google App Engine:</em> Cloud Storage bucket name',
    );

    global $SITE_INFO;

    echo '
        <table class="results_table">
    ';

    // Display UI to set all settings
    foreach ($settings as $key => $notes) {
        $val = array_key_exists($key, $SITE_INFO) ? $SITE_INFO[$key] : '';

        if (($key == 'master_password') || ($key == 'confirm_master_password')) {
            $val = '';
        }

        if (is_array($val)) {
            foreach ($val as $val2) {
                echo '<input type="hidden" name="' . htmlentities($key) . '[]" value="' . htmlentities($val2) . '" />';
            }
            continue;
        }

        $type = 'text';
        if (strpos($key, 'password') !== false) {
            $type = 'password';
        } elseif (strpos($key, 'Whether') !== false) {
            $type = 'checkbox';
            $checked = ($val == 1);
            $val = '1';
        }

        $_key = htmlentities($key);
        $_val = htmlentities($val);

        echo '
            <tr>
                <th style="text-align: right">
                    ' . $_key . '
                </th>
                <td>
                    <input type="' . $type . '" name="' . $_key . '" value="' . $_val . '" ' . (($type == 'checkbox') ? ($checked ? 'checked="checked"' : '') : 'size="20"') . ' />
                </td>
                <td>
                    ' . $notes . '
                </td>
            </tr>
        ';
        if ($key == 'master_password') {
            echo '
                <tr>
                    <th style="text-align: right">
                        &raquo; Confirm password
                    </th>
                    <td>
                        <input type="' . $type . '" name="confirm_master_password" value="' . $_val . '" size="20" />
                    </td>
                    <td>
                    </td>
                </tr>
            ';
        }
    }

    // Any other settings that we don't actually implicitly recognise need to be relayed
    foreach ($SITE_INFO as $key => $val) {
        if (!array_key_exists($key, $settings)) {
            if (is_array($val)) {
                foreach ($val as $val2) {
                    echo '<input type="hidden" name="' . htmlentities($key) . '[]" value="' . htmlentities($val2) . '" />';
                }
            } else {
                echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) . '" />';
            }
        }
    }

    echo '
        </table>

        <p class="proceed_button" style="text-align: center">
            <input class="button_screen buttons__save" type="submit" value="Edit" />
        </p>

        <input type="hidden" name="given_password" value="' . htmlentities($given_password) . '" />
    ';
}

/**
 * Do the editing.
 */
function do_set()
{
    $given_password = $_POST['given_password'];

    $new = array();
    foreach ($_POST as $key => $val) {
        if ($key != 'given_password') {
            if (($key == 'master_password') || ($key == 'confirm_master_password')) {
                if ($val == '') {
                    $val = $given_password;
                }
                if (function_exists('password_hash')) { // PHP5.5+
                    $val = password_hash($val, PASSWORD_BCRYPT, array('cost' => 12, 'salt' => md5('cms')));
                } else {
                    $val = '!' . md5($val . 'cms');
                }
            }
            $new[$key] = $val;
        }
    }
    if ($new['confirm_master_password'] != $new['master_password']) {
        echo '<hr /><p><strong>Your passwords do not match up.</strong></p>';
        return;
    }
    unset($new['confirm_master_password']);

    // Test cookie settings. BASED ON CODE FROM INSTALL.PHP
    $base_url = $new['base_url'];
    $cookie_domain = $new['cookie_domain'];
    $cookie_path = $new['cookie_path'];
    $url_parts = parse_url($base_url);
    if (!array_key_exists('host', $url_parts)) {
        $url_parts['host'] = 'localhost';
    }
    if (!array_key_exists('path', $url_parts)) {
        $url_parts['path'] = '';
    }
    if (substr($url_parts['path'], -1) != '/') {
        $url_parts['path'] .= '/';
    }
    if (substr($cookie_path, -1) == '/') {
        $cookie_path = substr($cookie_path, 0, strlen($cookie_path) - 1);
    }
    if (($cookie_path != '') && (substr($url_parts['path'], 0, strlen($cookie_path) + 1) != $cookie_path . '/')) {
        echo '<hr /><p><strong>The cookie path must either be blank or correspond with some or all of the path in the base URL (which is <kbd>' . htmlentities($url_parts['path']) . '</kbd>).</strong></p>';
        return;
    }
    if ($cookie_domain != '') {
        if (strpos($url_parts['host'], '.') === false) {
            echo '<hr /><p><strong>You are using a non-DNS domain in your base URL, which means you will need to leave your cookie domain blank (otherwise it won\'t work).</strong></p>';
            return;
        }
        if (substr($cookie_domain, 0, 1) != '.') {
            echo '<hr /><p><strong>The cookie domain must either be blank or start with a dot.</strong></p>';
            return;
        } elseif (substr($url_parts['host'], 1 - strlen($cookie_domain)) != substr($cookie_domain, 1)) {
            echo '<hr /><p><strong>The cookie domain must either be blank or correspond to some or all of the domain in the base URL (which is <kbd>' . htmlentities($url_parts['host']) . '</kbd>). It must also start with a dot, so a valid example is <kbd>.' . htmlentities($url_parts['host']) . '</kbd>.</strong></p>';
            return;
        }
    }

    // Delete old cookies, if our settings changed- to stop user getting confused by overrides
    global $SITE_INFO;
    if ((@$new['cookie_domain'] !== @$SITE_INFO['cookie_domain']) || (@$new['cookie_path'] !== @$SITE_INFO['cookie_path'])) {
        $cookie_path = array_key_exists('cookie_path', $SITE_INFO) ? $SITE_INFO['cookie_path'] : '/';
        if ($cookie_path == '') {
            $cookie_path = null;
        }
        $cookie_domain = array_key_exists('cookie_domain', $SITE_INFO) ? $SITE_INFO['cookie_domain'] : null;
        if ($cookie_domain == '') {
            $cookie_domain = null;
        }

        foreach (array_keys($_COOKIE) as $cookie) { // Delete all cookies, to clean up the mess - don't try and be smart, it just creates more confusion that it's worth
            @setcookie($cookie, '', time() - 100000, $cookie_path, $cookie_domain);
        }

        echo '<p><strong>You have changed your cookie settings. Your old login cookies have been deleted, and the software will try and delete all cookie variations from your member\'s computers when they log out. However there is a chance you may need to let some members know that they need to delete their old cookies manually.</strong></p>';
    }

    // _config.php
    global $FILE_BASE;
    $config_file = '_config.php';
    $path = $FILE_BASE . '/exports/file_backups/' . $config_file . '.' . strval(time());
    $copied_ok = @copy($FILE_BASE . '/' . $config_file, $path);
    if ($copied_ok !== false) {
        co_sync_file($path);
    }
    $config_file_handle = fopen($FILE_BASE . '/' . $config_file, 'wt');
    if ($config_file_handle === false) {
        exit();
    }
    @flock($config_file_handle, LOCK_EX);
    ftruncate($config_file_handle, 0);
    fwrite($config_file_handle, "<" . "?php\n");
    foreach ($new as $key => $val) {
        if (is_array($val)) {
            foreach ($val as $val2) {
                $_val = str_replace('\\', '\\\\', $val2);
                if (fwrite($config_file_handle, '$SITE_INFO[\'' . $key . '\'][] = \'' . $_val . "';\n") === false) {
                    echo '<strong>Could not save to file. Out of disk space?<strong>';
                }
            }
        } else {
            $_val = str_replace('\\', '\\\\', $val);
            if (fwrite($config_file_handle, '$SITE_INFO[\'' . $key . '\'] = \'' . $_val . "';\n") === false) {
                echo '<strong>Could not save to file. Out of disk space?<strong>';
            }
        }
    }
    @flock($config_file_handle, LOCK_UN);
    fclose($config_file_handle);
    co_sync_file($config_file);

    echo '<hr /><p>Edited configuration. If you wish to continue editing you must <a href="config_editor.php">login again.</a></p>';
    echo '<hr /><p>The _config.php file was backed up at ' . htmlentities($path) . '</p>';
}

/**
 * Provides a hook for file synchronisation between mirrored servers.
 *
 * @param  PATH $filename File/directory name to sync on (may be full or relative path)
 */
function co_sync_file($filename)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once($FILE_BASE . '/data_custom/sync_script.php');
        if (substr($filename, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $filename = substr($filename, strlen($FILE_BASE));
        }
        if (function_exists('master__sync_file')) {
            master__sync_file($filename);
        }
    }
}

/**
 * Provides a hook for file synchronisation between mirrored servers.
 *
 * @param  PATH $old File/directory name to move from (may be full or relative path)
 * @param  PATH $new File/directory name to move to (may be full or relative path)
 */
function co_sync_file_move($old, $new)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once($FILE_BASE . '/data_custom/sync_script.php');
        if (substr($old, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $old = substr($old, strlen($FILE_BASE));
        }
        if (substr($new, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $new = substr($new, strlen($FILE_BASE));
        }
        if (function_exists('master__sync_file_move')) {
            master__sync_file_move($old, $new);
        }
    }
}

/**
 * Check the given master password is valid.
 *
 * @param  SHORT_TEXT $password_given Given master password
 * @return boolean Whether it is valid
 */
function co_check_master_password($password_given)
{
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
