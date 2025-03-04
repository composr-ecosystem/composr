<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    core
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

require $FILE_BASE . '/_config.php';
if (isset($GLOBALS['SITE_INFO']['admin_password'])) { // LEGACY
    $GLOBALS['SITE_INFO']['master_password'] = $GLOBALS['SITE_INFO']['admin_password'];
    unset($GLOBALS['SITE_INFO']['admin_password']);
}
if (isset($GLOBALS['SITE_INFO']['master_password'])) { // LEGACY
    $GLOBALS['SITE_INFO']['maintenance_password'] = $GLOBALS['SITE_INFO']['master_password'];
    unset($GLOBALS['SITE_INFO']['master_password']);
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
    if (co_check_maintenance_password($given_password)) {
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
 * Output the config editor's page header.
 */
function ce_do_header()
{
    echo '
<!DOCTYPE html>
<html lang="EN">
<head>
    <title>Installation Options editor</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="data/sheet.php?sheet=global" />
    <style>';
    echo '
        .screen-title { text-decoration: underline; display: block; background: url(\'themes/default/images/icons/admin/tool.svg\') top left no-repeat; background-size: 48px 48px; min-height: 42px; padding: 10px 0 0 60px; }
    </style>

    <meta name="robots" content="noindex, nofollow" />';

    global $FILE_BASE;
    $password_check_js = file_get_contents($FILE_BASE . '/themes/default/javascript/password_checks.js');
    $ls_rep = [
        '{!ADMIN_USERS_PASSWORD;^/}' => 'Administration account password',
        '{!MAINTENANCE_PASSWORD;^/}' => 'Maintenance password',
        '{!PASSWORDS_DO_NOT_MATCH;^/}' => 'The given {1} passwords do not match',
        '{!PASSWORDS_DO_NOT_REUSE;^/}' => 'It is important that you do not re-use the database password for the {1} password, as the database password has to be stored as plain-text.',
        '{!PASSWORD_INSECURE;^/}' => 'Are you sure you want such an insecure {1} password? This will leave your installation and web hosting wide open to attack. You should use at least 12 characters and a combination of lower case, upper case, digits, and punctuation symbols.',
        '{!CONFIRM_REALLY;^/}' => 'REALLY?',

        '{PASSWORD_PROMPT;/}' => '',
        '{!installer:CONFIRM_MAINTENANCE_PASSWORD}' => '',
    ];
    $password_check_js = str_replace(array_keys($ls_rep), array_values($ls_rep), $password_check_js);
    @print('<script>' . $password_check_js . '</script>');

    echo '
</head>
<body class="website-body" style="margin: 1em"><div class="global-middle">
    <h1 class="screen-title">Installation Options editor</h1>
    <p>This is an editor accessible to administrators of the website only. It is kept as simple as possible, to allow fixing of configuration problems when the software is not in a workable state. It is provided in English only, and only modifies the configuration file, not the database.</p>
    <p><strong>Caution:</strong> any custom code in the configuration file (such as utilisation of the git_repos function) will be overwritten! You will need to merge the code back into your configuration file from the backup in exports/file_backups.</p>
    <form action="config_editor.php" method="post">
';
}

/**
 * Output the config editor's page footer.
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
            <ul class="actions-list" role="navigation">
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
    if (isset($_POST['given_password'])) {
        echo '<p><strong>Invalid password</strong></p>';
    }
    echo "
        <label for=\"given_password\">Maintenance Password: <input type=\"password\" name=\"given_password\" id=\"given_password\" class=\"form-control\" /></label>
        <p><button class=\"btn btn-primary btn-scr menu--site-meta--user-actions--login\" type=\"submit\">Log in</button></p>
    ";
}

/**
 * Output the editing page.
 *
 * @param  string $given_password The password given to get here (so we don't need to re-enter it each edit)
 */
function do_access(string $given_password)
{
    $settings = [
        'admin_username' => 'The username used for the administrator when the software is installed to not use a forum. On the vast majority of sites this setting does nothing.',
        'maintenance_password' => 'If you wish the maintenance password to be changed, enter a new password here. Otherwise leave blank.',

        'base_url' => 'The URL of your site (no trailing slash). You can blank this out for auto-detection, but only do this during development -- if you do it live and somehow multiple domains can get to your site, random errors will occur due to caching problems.',
        'domain' => 'The domain that e-mail addresses are registered on, and possibly other things. This is only used by some very select parts of the system. It may be different from the domain in the base URL due to not having "www." on for example.',
        'default_lang' => 'The default language used on the site (language codename form, of subdirectory under lang/).',
        'block_url_schemes' => 'Whether to block the URL Scheme (mod_rewrite) option. Set this to 1 if you turned on URL Schemes and find your site no longer works.',
        'on_msn' => 'Whether this is a site on an Conversr multi-site-network (enable to trigger URLs to avatars and photos to be saved into the database as absolute). This option is only needed if you make satellite sites run the forum through a local base URL, as such a configuration does not know the forum base URL.',

        'forum_type' => '<em>Forum:</em> The forum driver to use. Note that it is unwise to change this unless expert, as member-IDs and usergroup-IDs form a binding between portal and forum, and would need remapping. To convert to Conversr, the forum importers can handle all of this automatically.',
        'forum_base_url' => '<em>Forum:</em> This is the base URL for the forums. If it is not correct, various links, such as links to topics, will not function correctly.',

        'db_type' => '<em>Database:</em> The database driver to use (code of PHP file in sources[_custom]/database/). Only MySQL supported officially.',
        'table_prefix' => '<em>Database:</em> The prefix for the software\'s database tables.',
        'db_site' => '<em>Database:</em> The name of the software database.',
        'db_site_host' => '<em>Database:</em> The hostname / IP address (usually localhost) for the software database. You can also include a port name here if you\'re on a non-default port (<kbd>host:port</kbd>), but if doing so you must not use <kbd>localhost</kbd> as the host unless the local socket/pipe connects to the correct MySQL server.',
        'db_site_user' => '<em>Database:</em> The username to connect to the software database with.',
        'db_site_password' => '<em>Database:</em> The password for the software database username.',
        'cns_table_prefix' => '<em>Database:</em> The table prefix for Conversr, if Conversr is being used.',
        'db_forums' => '<em>Database:</em> The database name for the forum driver to tie in to.',
        'db_forums_host' => '<em>Database:</em> The hostname / IP address for the forum driver to tie in to. See <kbd>db_site_host</kbd>.',
        'db_forums_user' => '<em>Database:</em> The username for the forum driver to connect to the forum database with.',
        'db_forums_password' => '<em>Database:</em> The password for the forum database username.',
        'use_persistent_database' => '<em>Database:</em> Whether to use persistent database connections (not recommended unless you have a very high connection overhead and are using a dedicated server, but may be helpful on local development machines).',
        'database_charset' => '<em>Database:</em> The MySQL character set for the connection. Usually you can just leave this blank, but if MySQL\'s character set for your database has been overridden away from the server-default then you will need to set this to be equal to that same character set.',
        'database_collation' => '<em>Database:</em> The MySQL collation for the connection. Usually you can just leave this blank, but if MySQL\'s collation for your database has been overridden away from the server-default then you will need to set this to be equal to that same collation (this can happen when switching between servers, as utf8mb4_unicode_ci, utf8mb4_general_ci, utf8_bin, utf8mb4_unicode_520_ci and utf8mb4_0900_ai_ci [MySQL 8+] are all commonplace).',

        'user_cookie' => '<em>Cookies:</em> The name of the cookie used to hold usernames/ids for each user. Depending on the forum system involved, it may use a special serialisation notation involving a colon (there is no special notation for Conversr). Highly recommended to start it with \'_Secure-\' (the software will handle automatically if your site does not support this).',
        'pass_cookie' => '<em>Cookies:</em> The name of the cookie used to hold passwords for each user. Highly recommended to start it with \'_Secure-\' (the software will handle automatically if your site does not support this).',
        'session_cookie' => '<em>Cookies:</em> The name of the cookie used to hold session IDs. Highly recommended to start it with \'_Host-\' (the software will handle automatically if your site does not support this).',
        'cookie_domain' => '<em>Cookies:</em> The domain name the cookies are tied to. Only URLs with this domain, or a subdomain there-of, may access the cookies. You probably want to leave it blank. Use blank if running the software off the DNS system (e.g. localhost), or if you want the active-domain to be used (i.e. autodetection). <strong>It\'s best not to change this setting once your community is active, as it can cause logging-out problems.</strong>',
        'cookie_path' => '<em>Cookies:</em> The URL path the cookies are tied to. Only URLs branching from this may access the cookies. Either set it to the path portion of the base URL, or a shortened path if cookies need to work with something elsewhere on the domain, or leave blank for auto-detection. <strong>It\'s best not to change this setting once your community is active, as it can cause logging-out problems.</strong>',
        'cookie_days' => '<em>Cookies:</em> The number of days to store login cookies for.',

        'use_persistent_cache' => '<em>Performance:</em> If persistent memory caching is to be used (caches data in memory between requests using whatever appropriate PHP extensions are available). May be set to <kbd>0</kbd> to disable, <kbd>1</kbd> for auto-detection, or the name of a PHP file in <kbd>sources/persistent_caching</kbd> to force a specific method (e.g. <kbd>apc</kbd>).',
        'static_caching_hours' => '<em>Performance:</em> The number of hours that the static cache lasts (this sets both HTTP caching, and server retention of cached screens).',
        'any_guest_cached_too' => '<em>Performance:</em> Whether Guest hits are cached with static caching (by default only spiders/bots get static caching).',
        'static_caching_inclusion_list' => '<em>Performance:</em> A regular expresion determining what URLs are subject to the static cache. Does not need to match full URL unless you code your regexp to anchor itself. If not set all URLs will be cached that don\'t have special GET parameters (non-canonical parameters, or extra parameters to home page).',
        'static_caching_exclusion_list' => '<em>Performance:</em> A regular expresion determining what URLs not subject to the static cache. Does not need to match full URL unless you code your regexp to anchor itself. If not set there will be no exclusion list.',
        'self_learning_cache' => '<em>Performance:</em> Whether to allow pages to learn what resources they need, for efficient bulk loading of essentials while avoiding loading full resource sets upfront. Stores copies of some resources within the self-learning cache itself.',
        'no_nosniff_header' => '<em>Performance:</em> If you can rely on your webserver configuration sending "X-Content-Type-Options: nosniff" enable this to stop the software from duplicating it.',

        'max_execution_time' => '<em>Performance:</em> The time in seconds to use for PHP\'s maximum execution time option. The software defaults to 60 and raises it in known situations that require more time.',

        'disable_smart_decaching' => '<em>Tuning/Disk performance:</em> Don\'t check file times to check caches aren\'t stale. If this is <kbd>1</kbd> then smart decaching is disabled unless you use <kbd>keep_smart_decaching=1</kbd> temporarily in the URL. You can also set it to a format <kbd>3600:/some/file/path</kbd> which will disable it if the given file has not been modified within the given number of seconds; you may point it to an FTP log file for example.',
        'no_disk_sanity_checks' => '<em>Tuning/Disk performance:</em> Whether to assume that there are no missing language directories, or other configured directories; things may crash horribly if they are missing and this is enabled.',
        'hardcode_common_module_zones' => '<em>Tuning/Disk performance:</em> Whether to not search for common modules, assume they are in default positions.',
        'charset' => '<em>Tuning/Disk performance:</em> The character set (if set, it skips an extra disk check inside the language files).',
        'known_suexec' => '<em>Tuning/Disk performance:</em> Whether we know suEXEC is on the server so will skip checking for it (which involves a disk access).',
        'assume_full_mobile_support' => '<em>Tuning/Disk performance:</em> Whether to assume that the current theme fully supports mobile view-mode, on all pages. This skips a disk access.',
        'no_extra_bots' => '<em>Tuning/Disk performance:</em> Whether to only use the hard-coded bot detection list. This saves a disk access.',
        'no_extra_closed_file' => '<em>Tuning/Disk performance:</em> Whether to not recognise a closed.html file. This saves a disk access but could be problematic if you want to shut down a horribly-broken site from public access.',
        'no_extra_logs' => '<em>Tuning/Disk performance:</em> Whether to not populate extra logs even if writable files have been put in place for this. This saves disk accesses to look for these files.',
        'no_extra_mobiles' => '<em>Tuning/Disk performance:</em> Whether to only use the hard-coded mobile-device detection list. This saves a disk access.',
        'no_installer_checks' => '<em>Tuning/Disk performance:</em> Whether to skip complaining if the install.php file has been left around. This is intended only for developers working on development machines.',
        'no_compiled_files' => '<em>Tuning/Disk performance:</em> Whether to disable using the <kbd>_compiled</kbd> directory for code overrides. Disabling will reduce disk access and storage use but will increase memory use, result in weird stack traces sometimes, and disable support for caching custom overrides in PHP at run-time.',

        'prefer_direct_code_call' => '<em>Tuning:</em> Whether to assume a good opcode cache is present, so load up full code files via this rather than trying to save RAM by loading up small parts of files on occasion.',
        'php_path' => '<em>Tuning:</em> The absolute path to the PHP cli binary if the software is not able to auto-detect it (blank: try auto-detecting or use the standard php command).',
        'php_cgi_path' => '<em>Tuning:</em> The absolute path to the PHP cgi interpreter if the software is not able to auto-detect it (blank: try auto-detecting or use the standard php-cgi command).',

        'backdoor_ip' => '<em>Security:</em> Always allow users accessing from this IP address/CIDR/hostname in, automatically logged in as the oldest admin of the site. You can enter comma-separated addresses. Hostname checks only work if <kbd>keep_check_backdoor_ip_dns=1</kbd> is set in the URL, for performance reasons.',
        'trusted_proxies' => '<em>Security:</em> Proxies to trust. For any incoming request by an IP covered in one of the comma-separated IPs (or IP CIDR ranges), "forwarded for" IP headers will be trusted to identify the real IP address. This improves security as the software will be targeting the true IP of visitors rather than the proxy IP, so long as it is a real proxy and not a trick by a hacker trying to masquerade their IP by pretending they\'re just an innocent intermediary node. Defaults to all Cloudflare IP addresses (careful because if you set this, then the software will not use Cloudflare IPs anymore unless you explicitly include them).',
        'full_ip_addresses' => '<em>Security:</em> Whether to match sessions to the full IP addresses instead of the 255.255.255.0 subnet. This increases security but also increases the likelihood members get randomly logged out (e.g. due to proxy server randomisation).',
        'dev_mode' => '<em>Development:</em> Whether development mode is enabled (<strong>intended only for developers who know what they are doing</strong>). This enables special run-time checks and strict PHP type checking. Note this may significantly reduce page load times; make sure your PHP max_execution_time is 30 or higher. Defaults to auto-detect depending on the presence of a git repository.',
        'no_keep_params' => '<em>Development:</em> Whether to disable support for \'keep_\' params. You probably don\'t want to disable them!',
        'safe_mode' => '<em>Development:</em> Whether the software is to be forced into safe mode, meaning no custom files / non-bundled addons will load and most caching will be disabled.',
        'no_email_output' => '<em>Development:</em> Whether emails should never be sent.',
        'redirect_email_output' => '<em>Development:</em> Alternate e-mail address to route all e-mails.',
        'email_to' => '<em>Development:</em> If you have set up a customised critical error screen (via a <kbd>_critical_error.html</kbd> file and empty <kbd>critical_errors</kbd> directory), and a background e-mailing process, this defines where error e-mails will be sent.',
        'keep_fatalistic' => '<em>Development:</em> If you want all terminal errors no matter how small (including user / validation errors) to trigger a stack trace even if keep_fatalistic is not specified in the URL, set this to 1. Or, set to 2 to include additional details (slow and uses a lot of RAM). Generally you will never want to turn this on unless you are performing some external automated testing.',

        'failover_mode' => '<em>Failover:</em> The failover mode. Either <kbd>off</kbd> or <kbd>on</kbd> or <kbd>auto_off</kbd> or <kbd>auto_on</kbd>. Usually it will be left to <kbd>off</kbd>, meaning there is no active failover mode. The next most common setting will be <kbd>auto_off</kbd>, which means the failover_script.php script is allowed to set it to <kbd>auto_on</kbd> if it detects the site is failing (and back to <kbd>auto_off</kbd> again when things are okay again). Setting it to <kbd>on</kbd> is manually declaring the site has failed and you want to keep it in failover mode.',
        'failover_apache_rewritemap_file' => '<em>Failover:</em> Set to <kbd>1</kbd> to maintain an Apache RewriteMap file that maps disk cache files to URLs directly. This is a very advanced option and needs server-level Apache configuration by a programmer. You can also set to <kbd>-</kbd> which is like <kbd>1</kbd> except mobile hits are not differentiated from desktop hits.',
        'failover_cache_miss_message' => '<em>Failover:</em> Error message shown if failover mode misses a cache hit (i.e. cannot display a page from the cache).',
        'failover_check_urls' => '<em>Failover:</em> Relative URL(s) separated by <kbd>;</kbd> that failover mode should check when deciding to activate/deactivate.',
        'failover_email_contact' => '<em>Failover:</em> E-mail address separated by <kbd>;</kbd> that failover mode notifications are sent to.',
        'failover_loadaverage_threshold' => '<em>Failover:</em> Minimum load average before failover mode activates.',
        'failover_loadtime_threshold' => '<em>Failover:</em> Minimum page load time in seconds before failover mode activates.',
        'failover_message' => '<em>Failover:</em> Message shown at top of the screen when failover mode is activated.',
        'failover_message_place_after' => '<em>Failover:</em> failover_message will be placed after this HTML marker.',
        'failover_message_place_before' => '<em>Failover:</em> failover_message will be placed before this HTML marker. May be specified in addition to failover_message_place_after, so that two messages show.',

        'rate_limiting' => '<em>Rate limiting:</em> Whether to enable rate limiting for IPs. The data_custom/rate_limiter.php file must exist and be writeable (on a suExec-style server the file will auto-create, otherwise just make it as an empty file). IP addresses passed to PHP must be accurate (some front-end proxying systems break this).',
        'rate_limit_time_window' => '<em>Rate limiting:</em> The number of seconds hits are counted across. Defaults to <kbd>10</kbd>.',
        'rate_limit_hits_per_window' => '<em>Rate limiting:</em> The number of hits per IP going back as far as the time window. Note that this is any URL hitting the software as a whole, not just pages (i.e. AJAX and banner frames would both count). Defaults to <kbd>5</kbd>.',

        'gae_application' => '<em>Google App Engine:</em> Application name',
        'gae_bucket_name' => '<em>Google App Engine:</em> Cloud Storage bucket name',
    ];

    global $SITE_INFO;

    echo '
        <table class="results-table">
    ';

    // Display UI to set all settings
    foreach ($settings as $key => $notes) {
        $val = array_key_exists($key, $SITE_INFO) ? $SITE_INFO[$key] : '';

        if (($key == 'maintenance_password') || ($key == 'maintenance_password_confirm')) {
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
        } elseif (strpos($notes, 'Whether') !== false) {
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
        if ($key == 'maintenance_password') {
            echo '
                <tr>
                    <th style="text-align: right">
                        &raquo; Confirm password
                    </th>
                    <td>
                        <input type="' . $type . '" name="maintenance_password_confirm" value="' . $_val . '" size="20" />
                    </td>
                    <td>
                    </td>
                </tr>
            ';
        }
    }

    echo '
        </table>
    ';

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
        <p class="proceed-button" style="text-align: center">
            <button class="btn btn-primary btn-scr buttons--save" type="submit" onclick="return checkPasswords(this.form, true);">Save</button>
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

    $new = [];
    foreach ($_POST as $key => $val) {
        // Non-saved fields
        if ($key == 'given_password') {
            continue;
        }
        if ((strpos($key, '_forums') !== false) && (($val == '') || ($val == $_POST[str_replace('_forums', '_site', $key)]))) {
            continue;
        }
        if (($key == 'cns_table_prefix') && (($val == '') || ($val == $_POST['table_prefix']))) {
            continue;
        }

        // If new password is blank use existing one
        if ((($key == 'maintenance_password') || ($key == 'maintenance_password_confirm')) && ($val == '')) {
            $val = $given_password;
        }

        // Save into $new array
        $new[$key] = $val;
    }

    // Check confirm password matches
    if ($new['maintenance_password_confirm'] != $new['maintenance_password']) {
        echo '<hr /><p><strong>Your maintenance passwords do not match up. Please double-check you are putting them in correctly.</strong></p>';
        return;
    }
    unset($new['maintenance_password_confirm']);

    // Encrypt password
    $cost = co_calculate_reasonable_ratchet();
    $new['maintenance_password'] = password_hash($new['maintenance_password'], PASSWORD_BCRYPT, ['cost' => ($cost !== null) ? $cost : 12]);

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
    $backup_path = $FILE_BASE . '/exports/file_backups/' . $config_file . '.' . strval(time()) . '_';
    $backup_path .= substr(md5(random_bytes(13)), 0, 13);
    $copied_ok = @copy($FILE_BASE . '/' . $config_file, $backup_path);
    @chmod($backup_path, 0600);
    if ($copied_ok !== false) {
        co_sync_file($backup_path);
    }
    $out = "<" . "?php\nglobal \$SITE_INFO;";
    $out .= '
if (!function_exists(\'git_repos\')) {
    /**
     * Find the Git branch name. This is useful for making this config file context-adaptive (i.e. dev settings vs production settings).
     *
     * @return ?ID_TEXT Branch name (null: not in Git)
     */
    function git_repos() : ?string
    {
        $path = __DIR__ . \'/.git/HEAD\';
        if (!is_file($path)) {
            return \'\';
            }
        $lines = file($path);
        $parts = explode(\'/\', $lines[0]);
        return trim(end($parts));
    }
}

';
    foreach ($new as $key => $val) {
        if (is_array($val)) {
            foreach ($val as $val2) {
                $_val = addslashes($val2);
                $out .= '$SITE_INFO[\'' . $key . '\'][] = \'' . $_val . "';\n";
            }
        } else {
            $_val = addslashes($val);
            $out .= '$SITE_INFO[\'' . $key . '\'] = \'' . $_val . "';\n";
        }
    }
    $success = file_put_contents($FILE_BASE . '/' . $config_file, $out, LOCK_EX);
    if (!$success) {
        echo '<strong>Could not save to file. Access denied?<strong>';
    }
    co_sync_file($config_file);

    echo '<hr /><p>Edited configuration. If you wish to continue editing you must <a href="config_editor.php">login again.</a></p>';
    echo '<hr /><p>The <kbd>_config.php</kbd> file was backed up at <kbd>' . htmlentities(str_replace('/', DIRECTORY_SEPARATOR, $backup_path)) . '</kbd></p>';
}

/**
 * Provides a hook for file synchronisation between mirrored servers.
 *
 * @param  PATH $filename File/directory name to sync on (may be full or relative path)
 */
function co_sync_file(string $filename)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once $FILE_BASE . '/data_custom/sync_script.php';
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
function co_sync_file_move(string $old, string $new)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once $FILE_BASE . '/data_custom/sync_script.php';
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
 * Check the given maintenance password is valid.
 *
 * @param  SHORT_TEXT $password_given Given maintenance password
 * @return boolean Whether it is valid
 */
function co_check_maintenance_password(string $password_given) : bool
{
    global $FILE_BASE;
    require_once $FILE_BASE . '/sources/crypt_maintenance.php';
    return check_maintenance_password($password_given);
}

/**
 * Calculate a strong ratchet based on the CPU speed for the maintenance password.
 *
 * @return ?integer The suggested ratchet to use (null: password_hash is not supported)
 */
function co_calculate_reasonable_ratchet() : ?int
{
    if (!function_exists('password_hash')) {
        return null;
    }

    // We want the ratchet to be fairly secure as this is a very sensitive password
    $minimum_cost = 10;
    $target_time = 1.0;

    $cost = ($minimum_cost - 1);

    do {
        $cost++;
        if ($cost > 31) { // Costs > 31 are not supported
            break;
        }
        $start = microtime(true);
        password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
        $end = microtime(true);
        $elapsed_time = $end - $start;
    } while ($elapsed_time < $target_time);

    return ($cost - 1); // We don't want to use the cost that exceeded our target time; use the one below it.
}
