<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/*
> If you are seeing this message in your web browser then you need to install PHP on your web server.<!--
*/

/*EXTRA FUNCTIONS: ftp_.*|fileowner*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    installer
 */

// Requirements check
if (version_compare(PHP_VERSION, '7.2', '<')) {
    exit('PHP version 7.2 or newer is required');
}

ignore_user_abort(false);

$functions = ['fopen'];
foreach ($functions as $function) {
    if (preg_match('#[^,\s]' . $function . '[$,\s]#', ini_get('disable_functions')) != 0) {
        header('Content-Type: text/plain; charset=utf-8');
        exit('The ' . $function . ' function appears to have been manually disabled in your PHP installation. This is a basic and necessary function, required for Composr.');
    }
}

if ((array_key_exists('type', $_GET)) && ($_GET['type'] != 'finish') && (file_exists('install_locked'))) {
    header('Content-Type: text/plain; charset=utf-8');
    exit('Installer is locked for security reasons (delete the \'install_locked\' file to return to the installer)');
}

global $IN_MINIKERNEL_VERSION;
$IN_MINIKERNEL_VERSION = true;

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
$RELATIVE_PATH = '';
@chdir($FILE_BASE);

error_reporting(E_ALL);

cms_ini_set('display_errors', '1');
cms_ini_set('assert.active', '0');
cms_ini_set('opcache.revalidate_freq', '1'); // Bitnami WAMP puts it to 60 by default, breaking reading of _config.php

global $DEFAULT_FORUM;
$DEFAULT_FORUM = 'cns';

global $REQUIRED_BEFORE;
$REQUIRED_BEFORE = [];

global $SITE_INFO;
$SITE_INFO = [];

global $CURRENT_SHARE_USER;
$CURRENT_SHARE_USER = null;

$GLOBALS['DEV_MODE'] = false;
$GLOBALS['SEMI_DEV_MODE'] = true;

@ob_end_clean(); // Reset to have no output buffering by default (we'll use it internally, taking complete control)

// Are we in a special version of PHP?
define('GOOGLE_APPENGINE', isset($_SERVER['APPLICATION_ID']));

define('URL_CONTENT_REGEXP', '\w\-\x80-\xFF'); // PHP is done using ASCII (don't use the 'u' modifier). Note this doesn't include dots, this is intentional as they can cause problems in filenames
define('URL_CONTENT_REGEXP_JS', '\w\-\u0080-\uFFFF'); // JavaScript is done using Unicode

$shl = @ini_get('suhosin.memory_limit');
if (($shl === false) || ($shl == '') || ($shl == '0')) {
    cms_ini_set('memory_limit', '-1');
} else {
    if (is_numeric($shl)) {
        $shl .= 'M'; // Units are in MB for this, while PHP's memory limit setting has it in bytes
    }
    cms_ini_set('memory_limit', $shl);
}

// Tunnel into some Composr code we can use
require_code('critical_errors');
require_code('permissions');
require_code('minikernel');
require_code('file_permissions_check');
require_code('forum_stub');
require_code('global3');
require_code('zones');
require_code('temporal');
$GLOBALS['PERSISTENT_CACHE'] = null;
require_code('files');
require_code('lang');
require_code('tempcode');
require_code('templates');
require_code('version');
require_code('urls');
require_code('zones');
require_code('comcode');
require_code('themes');

if (!array_key_exists('type', $_GET)) {
    if (empty($_GET)) {
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<' . '!' . 'DOCTYPE html' . '>' . "\n";
    if (empty($_GET)) { // Special code to skip checks if need-be. The XHTML here is invalid but unfortunately it does need to be.
        echo '<' . 'script' . '>
            window.setTimeout(function () { if (!document.getElementsByTagName("div")[0]) window.location+="?skip_slow_checks=1"; }, 30000);
            window.setInterval(function () { if ((!document.getElementsByTagName("div")[0]) && (document.body) && (document.body.innerHTML) && (document.body.innerHTML.indexOf("Maximum execution time")!=-1)) window.location+="?skip_slow_checks=1"; }, 500);
        </' . 'script' . '>';
    }
}

if (!array_key_exists('type', $_GET)) {
    send_http_output_ping();
}

global $CACHE_TEMPLATES;
if (is_writable(get_file_base() . '/themes/default/templates_cached/' . user_lang())) {
    $CACHE_TEMPLATES = true;
}

// Set up some globals
global $INSTALL_LANG, $VERSION_BEING_INSTALLED, $USER_LANG_CACHED;
$INSTALL_LANG = fallback_lang();
if (array_key_exists('default_lang', $_GET)) {
    $INSTALL_LANG = $_GET['default_lang'];
}
if (array_key_exists('default_lang', $_POST)) {
    $INSTALL_LANG = $_POST['default_lang'];
}
$USER_LANG_CACHED = $INSTALL_LANG;

// Language files we can use
require_lang('global');
require_lang('critical_error');
require_lang('installer');
require_lang('version');

// If we are referencing this file in order to extract dependant url's from a pack
handle_self_referencing_embedment();

// Set up some globals
$minor = cms_version_minor();
$VERSION_BEING_INSTALLED = strval(cms_version());
if ($minor != '') {
    $VERSION_BEING_INSTALLED .= (is_numeric($minor[0]) ? '.' : '-') . $minor;
}

global $PASSWORD_PROMPT;
$PASSWORD_PROMPT = new Tempcode();

if (!array_key_exists('step', $_GET)) {
    $_GET['step'] = '1';
}

if (intval($_GET['step']) == 1) { // Language
    $content = step_1();
}

if (intval($_GET['step']) == 2) { // Licence
    $content = step_2();
}

if (intval($_GET['step']) == 3) { // Welcome
    $content = step_3();
}

if (intval($_GET['step']) == 4) { // Define settings
    $content = step_4();
    $forum_type = get_param_string('forum_type', '');
    if ($forum_type == 'none') {
        $username = 'admin';
    }
    $PASSWORD_PROMPT = do_lang_tempcode('CONFIRM_MASTER_PASSWORD');
}

if (intval($_GET['step']) == 5) {
    $content = step_5();
}

if (intval($_GET['step']) == 6) {
    $content = step_6();
}

if (intval($_GET['step']) == 7) {
    $content = step_7();
}

if (intval($_GET['step']) == 8) {
    $content = step_8();
}

if (intval($_GET['step']) == 9) {
    $content = step_9();
}

if (intval($_GET['step']) == 10) {
    $content = step_10();
}

if (intval($_GET['step']) == 11) {
    $content = step_11();
}

if ($DEFAULT_FORUM === null) {
    $DEFAULT_FORUM = 'cns'; // Shouldn't happen, but who knows
}
require_code('tempcode_compiler');
$css_nocache = _do_template('default', '/css/', 'no_cache', 'no_cache', 'EN', '.css');

$installer_js = new Tempcode();
$installer_js->attach(do_template('global', [], null, false, null, '.js', 'javascript'));
$installer_js->attach(do_template('installer', [], null, false, null, '.js', 'javascript'));

$out_final = do_template('INSTALLER_HTML_WRAP', [
    '_GUID' => '29aa056c05fa360b72dbb01c46608c4b',
    'CSS_NOCACHE' => $css_nocache,
    'DEFAULT_FORUM' => $DEFAULT_FORUM,
    'PASSWORD_PROMPT' => $PASSWORD_PROMPT,
    'RESOURCE_BASE_URL' => 'install.php?type=',
    'STEP' => integer_format(intval($_GET['step'])),
    'CONTENT' => $content,
    'VERSION' => $VERSION_BEING_INSTALLED,
    'INSTALLER_JS' => $installer_js
]);
unset($css_nocache);
unset($content);
$out_final->evaluate_echo();

global $DATADOTCMS_FILE;
if (@is_resource($DATADOTCMS_FILE)) {
    if ((intval($_GET['step']) == 10) && (!is_suexec_like())) {
        $conn = false;
        $domain = post_param_string('ftp_domain', false, INPUT_FILTER_POST_IDENTIFIER);
        $port = 21;
        if (strpos($domain, ':') !== false) {
            list($domain, $_port) = explode(':', $domain, 2);
            $port = intval($_port);
        }
        if (function_exists('ftp_ssl_connect')) {
            $conn = @ftp_ssl_connect($domain, $port);
        }
        $ssl = ($conn !== false);
        $username = post_param_string('ftp_username', false, INPUT_FILTER_POST_IDENTIFIER);
        $password = post_param_string('ftp_password', false, INPUT_FILTER_PASSWORD);
        if (($ssl) && (!@ftp_login($conn, $username, $password))) {
            $conn = false;
            $ssl = false;
        }
        if ($conn === false) {
            $conn = ftp_connect($domain, $port);
        }
        if (!$ssl) {
            ftp_login($conn, $username, $password);
        }
        $ftp_folder = post_param_string('ftp_folder', false, INPUT_FILTER_POST_IDENTIFIER);
        if (substr($ftp_folder, -1) != '/') {
            $ftp_folder .= '/';
        }
        ftp_chdir($conn, $ftp_folder);
        if (file_exists('cms_inst_tmp')) {
            file_put_contents(get_file_base() . '/cms_inst_tmp/tmp', '');
            ftp_put($conn, 'install_locked', get_file_base() . '/cms_inst_tmp/tmp', FTP_BINARY);
            ftp_put($conn, 'install_ok', get_file_base() . '/cms_inst_tmp/tmp', FTP_BINARY);
            @unlink(get_file_base() . '/cms_inst_tmp/tmp'); // Might not be able to unlink on a Windows server, if has permission to create but not delete
            @unlink(get_file_base() . '/cms_inst_tmp');
            @ftp_rmdir($conn, 'cms_inst_tmp');
            if (function_exists('ftp_close')) {
                ftp_close($conn);
            }
        }
    }
}

/**
 * Propagate certain keep_ parameters to a URL.
 *
 * @param  URLPATH $url The URL
 * @return URLPATH Corrected URL
 */
function prepare_installer_url(string $url) : string
{
    if (in_safe_mode()) {
        $url .= '&keep_safe_mode=1';
    }
    if (get_param_integer('keep_quick_hybrid', 0) == 1) {
        $url .= '&keep_quick_hybrid=1';
    }
    $kdfs = get_param_integer('keep_debug_fs', 0);
    if ($kdfs != 0) {
        $url .= '&keep_debug_fs=' . strval($kdfs);
    }
    $kst = get_param_integer('keep_show_timings', 0);
    if ($kst != 0) {
        $url .= '&keep_show_timings=' . strval($kst);
    }
    return $url;
}

// ========================================
// Installation steps
// ========================================

/**
 * First installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_1() : object
{
    // To stop previous installs interfering
    require_code('caches3');
    erase_cached_templates();
    erase_cached_language();

    // Integrity check
    require_code('files');
    $warnings = new Tempcode();

    // Software version
    $version_status = cms_version_branch_status();
    if ($version_status == VERSION_ALPHA) {
        $warnings->attach(do_template('INSTALLER_WARNING', ['MESSAGE' => do_lang_tempcode('INSTALLING_ALPHA_VERSION')]));
    }
    if ($version_status == VERSION_BETA) {
        $warnings->attach(do_template('INSTALLER_NOTICE', ['MESSAGE' => do_lang_tempcode('INSTALLING_BETA_VERSION')]));
    }
    if ($version_status == VERSION_EOL) {
        $warnings->attach(do_template('INSTALLER_WARNING', ['MESSAGE' => do_lang_tempcode('INSTALLING_EOL_VERSION')]));
    }

    global $DATADOTCMS_FILE;
    if (!@is_resource($DATADOTCMS_FILE)) { // Do an integrity check - missing corrupt files
        $sdc = get_param_integer('skip_disk_checks', null);
        if (($sdc === 1) || (($sdc !== 0) && (file_exists(get_file_base() . '/.git')))) {
            if (!file_exists(get_file_base() . '/.git')) {
                $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => 'c7896b2684fba62f3390624586bbbc3a', 'MESSAGE' => do_lang_tempcode('INSTALL_SLOW_SERVER')]));
            }
        } else {
            $files = @unserialize(cms_file_get_contents_safe(get_file_base() . '/data/files.bin', FILE_READ_LOCK));
            if ($files !== false) {
                $missing = [];
                $corrupt = [];

                // Volatile files (see also list in make_release.php)
                $skipped_files_may_be_changed_or_missing = array_flip([
                    'data/files_previous.bin',
                ]);
                $skipped_files_may_be_changed = array_flip([
                    'sources/version.php',
                    'data/files.bin',

                    // Large file size, skip for performance
                    'data/modules/admin_stats/IP_Country.txt',
                ]);

                foreach ($files as $file => $file_info) {
                    if (isset($skipped_files_may_be_changed_or_missing[$file])) {
                        continue;
                    }

                    if (should_ignore_file($file, IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE)) {
                        continue;
                    }

                    if (!file_exists(get_file_base() . '/' . $file)) {
                        $missing[] = $file;
                    } else {
                        if (substr($file, -4) == '.ttf') {
                            continue;
                        }
                        if (substr($file, -11) == '/index.html') { // These are always empty, no need to check
                            continue;
                        }
                        if (isset($skipped_files_may_be_changed[$file])) {
                            continue;
                        }
                        if (substr($file, -4) == '.php') { // There are so many files, we can't check all - and .php files will give an error when called if corrupt
                            continue;
                        }

                        $contents = @strval(cms_file_get_contents_safe(get_file_base() . '/' . $file));
                        if (sprintf('%u', crc32(preg_replace('#[\r\n\t ]#', '', $contents))) != $file_info[0]) {
                            $corrupt[] = $file;
                        }
                    }
                }

                if (count($missing) > 4) {
                    $warnings->attach(do_template('INSTALLER_WARNING_LONG', [
                        '_GUID' => '515c2f26a5415224f3c09b2429a78a5f',
                        'FILES' => $missing,
                        'MESSAGE' => do_lang_tempcode('_MISSING_INSTALLATION_FILE', escape_html(integer_format(count($missing))))
                    ]));
                } else {
                    foreach ($missing as $file) {
                        $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => 'e7240bff0162e4d43f7d4798212bbe5e', 'MESSAGE' => do_lang_tempcode('MISSING_INSTALLATION_FILE', escape_html($file))]));
                    }
                }
                if (count($corrupt) > 4) {
                    $warnings->attach(do_template('INSTALLER_WARNING_LONG', [
                        '_GUID' => 'f8958458d76bd4f6d146d3fe59132a02',
                        'FILES' => $corrupt,
                        'MESSAGE' => do_lang_tempcode('_CORRUPT_INSTALLATION_FILE', escape_html(integer_format(count($corrupt)))),
                    ]));
                } else {
                    foreach ($corrupt as $file) {
                        $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => '7e8438874f7fd5dc79a77e73358ab4fc', 'MESSAGE' => do_lang_tempcode('CORRUPT_INSTALLATION_FILE', escape_html($file))]));
                    }
                }
            }
        }
    }

    // Health Checks
    $_warnings = installer_health_checks();
    foreach ($_warnings as $_warning) {
        $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => '6f6390d4410fae3b2a3761eb65b99db4', 'MESSAGE' => $_warning]));
    }

    // Some checks relating to installation permissions
    global $FILE_ARRAY;
    if (!@is_array($FILE_ARRAY)) { // Talk about manual permission setting a bit
        if ((is_suexec_like()) && (strpos(PHP_OS, 'WIN') === false)) { // NB: Could also be that files are owned by 'apache'/'nobody'. In these cases the users have consciously done something special and know what they're doing (they have open_basedir at least hopefully!) so we'll still consider this 'suexec'. It's too much an obscure situation.
            $warnings->attach(do_template('INSTALLER_NOTICE', ['_GUID' => 'ed0c38f4e68b0cd456ec7029d061a070', 'MESSAGE' => do_lang_tempcode('SUEXEC_SERVER')]));
        } elseif (cms_is_writable(get_file_base() . '/install.php')) {
            $warnings->attach(do_template('INSTALLER_NOTICE', ['_GUID' => '2f22076de944028f70f40388c43fb96e', 'MESSAGE' => do_lang_tempcode('RECURSIVE_SERVER')]));
        }
    }
    if ((file_exists(get_file_base() . '/_config.php')) && (!cms_is_writable(get_file_base() . '/_config.php')) && (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN')) {
        $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => '368fe19bba96ef38027e7822e1a0a432', 'MESSAGE' => do_lang_tempcode('TROUBLESOME_WINDOWS_SERVER', escape_html(get_tutorial_url('tut_install_permissions')))]));
    }

    // Some sanity checks
    if (!@is_array($FILE_ARRAY)) { // Secondary to the file-by-file check. Aims to give more specific information
        if ((file_exists(get_file_base() . '/themes/default/templates/ANCHOR.tpl')) && (!file_exists(get_file_base() . '/themes/default/templates/COMCODE_TABULAR_INLINE_BLOCKS.tpl'))) {
            warn_exit(do_lang_tempcode('CORRUPT_FILES_CROP'));
        }
        if ((!file_exists(get_file_base() . '/themes/default/templates/ANCHOR.tpl')) && (file_exists(get_file_base() . '/themes/default/templates/anchor.tpl'))) {
            warn_exit(do_lang_tempcode('CORRUPT_FILES_LOWERCASE'));
        }
    }

    // GitLab downloads should not be used directly
    if (file_exists(get_file_base() . '/_tests')) {
        $warnings->attach(do_template('INSTALLER_WARNING', ['_GUID' => 'ce4972df590de8415b508ea368a708e1', 'MESSAGE' => 'You appear to be installing via the official GitLab repository as you have the test suite files present. This is not intended for end-users and will lead to a bloated insecure site. You should use an official package from the Composr download page.']));
    }

    // Language selection...

    if (file_exists('lang_custom/langs.ini')) {
        $lookup = cms_parse_ini_file_fast(get_custom_file_base() . '/lang_custom/langs.ini');
    } else {
        $lookup = cms_parse_ini_file_fast(get_file_base() . '/lang/langs.ini');
    }

    $lang_count = [];
    $langs1 = get_dir_contents('lang');
    foreach (array_keys($langs1) as $lang) {
        if (array_key_exists($lang, $lookup)) {
            if (!array_key_exists($lang, $lang_count)) {
                $lang_count[$lang] = 0;
            }

            $files = get_dir_contents('lang/' . $lang);
            foreach (array_keys($files) as $file) {
                if ((substr($file, -4) == '.ini') && (($lang == fallback_lang()) || (is_file(get_file_base() . '/lang/' . fallback_lang() . '/' . $file)))) {
                    $lang_count[$lang] += count(cms_parse_ini_file_fast(get_file_base() . '/lang/' . $lang . '/' . $file));
                }
            }
        }
    }
    $langs2 = get_dir_contents('lang_custom');
    foreach (array_keys($langs2) as $lang) {
        if (array_key_exists($lang, $lookup)) {
            if (!array_key_exists($lang, $lang_count)) {
                $lang_count[$lang] = 0;
            }

            $files = get_dir_contents('lang_custom/' . $lang);
            foreach (array_keys($files) as $file) {
                if ((substr($file, -4) == '.ini') && (is_file(get_file_base() . '/lang/' . fallback_lang() . '/' . $file))) {
                    $lang_count[$lang] += count(cms_parse_ini_file_fast(get_custom_file_base() . '/lang_custom/' . $lang . '/' . $file));
                }
            }
        }
    }
    $langs = array_merge($langs1, $langs2);
    ksort($langs);
    unset($langs['EN']);
    $langs = array_merge(['EN' => 'lang'], $langs);
    $tlanguages = new Tempcode();
    $tcount = 0;
    foreach (array_keys($langs) as $lang) {
        if (array_key_exists($lang, $lookup)) {
            $stub = ($lang == 'EN') ? '' : (' (unofficial, ' . strval(intval(round(100.0 * $lang_count[$lang] / $lang_count['EN']))) . '% changed)');
            $entry = do_template('FORM_SCREEN_INPUT_LIST_ENTRY', ['_GUID' => 'fa5dac0d0c00c7cd95ba3223bc9a8776', 'SELECTED' => $lang == user_lang(), 'DISABLED' => false, 'NAME' => $lang, 'CLASS' => '', 'TEXT' => $lookup[$lang] . $stub]);
            $tlanguages->attach($entry);
            $tcount++;
        }
    }
    if ($tcount == 1) {
        $tlanguages = new Tempcode(); // No selection
    }

    // UI...

    $url = prepare_installer_url('install.php?step=2');

    $hidden = build_keep_post_fields();
    $max = strval(get_param_integer('max', 1000));
    $hidden->attach(form_input_hidden('max', $max));

    return do_template('INSTALLER_STEP_1', [
        '_GUID' => '83f0ca881b9f63ab9378264c6ff507a3',
        'URL' => $url,
        'WARNINGS' => $warnings,
        'HIDDEN' => $hidden,
        'LANGUAGES' => $tlanguages
    ]);
}

/**
 * Second installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_2() : object
{
    if (!array_key_exists('default_lang', $_POST)) {
        $_POST['default_lang'] = 'EN';
    }
    global $FILE_ARRAY;
    if (@is_array($FILE_ARRAY)) {
        $licence = file_array_get('docs/' . filter_naughty($_POST['default_lang']) . '/LICENSE.md');
        if ($licence === null) {
            $licence = file_array_get('docs/LICENSE.md');
        }
        $licence = unixify_line_format(handle_string_bom($licence));
    } else {
        $licence = @cms_file_get_contents_safe(get_file_base() . '/docs/' . filter_naughty($_POST['default_lang']) . '/LICENSE.md', FILE_READ_LOCK | FILE_READ_BOM);
        if ($licence === false) {
            $licence = cms_file_get_contents_safe(get_file_base() . '/docs/LICENSE.md', FILE_READ_LOCK | FILE_READ_BOM);
        }
    }
    $licence = preg_replace('#\((\w+\.md)\)#', '(https://gitlab.com/composr-foundation/composr/-/blob/' . STABLE_BRANCH_NAME . '/docs/$1)', $licence);

    $url = prepare_installer_url('install.php?step=3');

    $hidden = build_keep_post_fields();
    return do_template('INSTALLER_STEP_2', [
        '_GUID' => 'b08b0268784c9a0f44863ae3aece6789',
        'URL' => $url,
        'HIDDEN' => $hidden,
        'LICENCE' => $licence,
    ]);
}

/**
 * Third installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_3() : object
{
    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    global $INSTALL_LANG;

    // Call home, if they asked to
    $advertise_on = post_param_integer('advertise_on', 0);
    $email = $_POST['email'];
    if ($email == do_lang('EMAIL_ADDRESS')) {
        $email = ''; // In case was left as the label
    }
    if (($email != '') || ($advertise_on == 1)) {
        require_code('files');
        http_get_contents('https://compo.sr/uploads/website_specific/compo.sr/scripts/newsletter_join.php?url=' . urlencode(static_evaluate_tempcode(protect_url_parameter(get_base_url()))) . '&email=' . urlencode($email) . '&advertise_on=' . strval($advertise_on) . '&lang=' . $INSTALL_LANG, ['trigger_error' => false]);
    }

    // Forum chooser
    $forums = get_dir_contents('sources/forum', true);
    unset($forums['none']);
    ksort($forums);
    $forums = array_merge(['none' => 'sources/forum'], $forums);
    $forum_info = cms_parse_ini_file_fast(get_file_base() . '/sources/forum/forums.ini');
    $tforums = new Tempcode();
    $classes = [];
    foreach (array_keys($forums) as $forum) {
        $class = array_key_exists($forum . '_class', $forum_info) ? $forum_info[$forum . '_class'] : 'general';
        $classes[$class][] = $forum;
    }
    global $DEFAULT_FORUM;
    if ((file_exists(get_file_base() . '/_config.php')) && (filesize(get_file_base() . '/_config.php') != 0)) {
        require_once(get_file_base() . '/_config.php');
        global $SITE_INFO;
        if (array_key_exists('forum_type', $SITE_INFO)) {
            $DEFAULT_FORUM = $SITE_INFO['forum_type'];
        }
    }
    $default_version = new Tempcode();
    $simple_forums = new Tempcode(); // For is JS is off, this is a simple flat list of all versions (rather than a two level list - with first level being $tforums and the second level being filtered using CSS 'display' from $versions)

    foreach ($classes as $class => $forums) {
        if (trim($class) == '') {
            continue;
        }

        $mapped_name = do_lang('FORUM_CLASS_' . $class, null, null, null, null, false);
        if ($mapped_name === null) {
            $mapped_name = titleify($class);
        }
        $_mapped_name = is_maintained_description('forum_' . $class, $mapped_name);
        $versions = new Tempcode();
        $first = true;
        $forums = array_reverse($forums);
        $rec = in_array($DEFAULT_FORUM, $forums);
        foreach ($forums as $forum) {
            if (GOOGLE_APPENGINE) {
                if ($forum != 'cns') {
                    continue;
                }
            }

            if ($class == 'general') {
                $version = $forum;
            } else {
                $version = array_key_exists($forum . '_version', $forum_info) ? do_lang('VERSION_NUM', $forum_info[$forum . '_version']) : do_lang('VERSION_NUM', do_lang('NA'));
            }
            $versions->attach(do_template('INSTALLER_FORUM_CHOICE_VERSION', ['_GUID' => '159a5a7cd1397620ef34e98c3b06cd7f', 'IS_DEFAULT' => ($DEFAULT_FORUM == $forum) || ($first && !$rec), 'CLASS' => $class, 'NAME' => $forum, 'VERSION' => $version]));
            $first = false;

            $simple_forums->attach(do_template('INSTALLER_FORUM_CHOICE_VERSION', ['_GUID' => 'c4c0e7accab56ae45e8e1a4ff777c42b', 'IS_DEFAULT' => ($DEFAULT_FORUM == $forum) || ($first && !$rec), 'CLASS' => $class, 'NAME' => $forum, 'VERSION' => $mapped_name . ' ' . $version]));
        }
        if ($rec) {
            $default_version = $versions;
        }
        $extra = ($rec) ? 'checked="checked"' : '';
        $tforums->attach(do_template('INSTALLER_FORUM_CHOICE', ['_GUID' => 'a5460829e86c9da3637f8e566cfca63c', 'CLASS' => $class, 'REC' => $rec, 'TEXT' => $_mapped_name, 'VERSIONS' => $versions, 'EXTRA' => $extra]));
    }

    // Database chooser
    $databases = array_merge(get_dir_contents('sources/database', true), get_dir_contents('sources_custom/database', true));
    ksort($databases);
    $database_names = cms_parse_ini_file_fast(get_file_base() . '/sources/database/database.ini');
    $tdatabase = new Tempcode();
    $dbs_found = 0;
    foreach (array_keys($databases) as $database) {
        if (GOOGLE_APPENGINE) {
            if (strpos($database, 'mysql') !== false) {
                continue;
            }
        }

        if (($database == 'xml') && (!file_exists(get_file_base() . '/.git'))) { // XML should only be an available option if running from a git repository
            continue;
        }

        $selected = false;

        if (($database == 'mysql') && (!function_exists('mysql_connect'))) {
            continue;
        }
        if (($database == 'mysqli') && (!function_exists('mysqli_connect'))) {
            continue;
        }
        if (($database == 'mysql_pdo') && ((!class_exists('PDO')) || (!defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')))) {
            continue;
        }
        if ($database == 'mysqli') {
            $selected = true;
        }
        if (($database == 'mysql') && (!function_exists('mysqli_connect'))) {
            $selected = true;
        }
        if (($database == 'mysql_pdo') && (!function_exists('mysql_connect')) && (!function_exists('mysqli_connect'))) {
            $selected = true;
        }
        if (($database == 'ibm') && (!function_exists('odbc_connect'))) {
            continue;
        }
        if (($database == 'oracle') && (!function_exists('ocilogon'))) {
            continue;
        }
        if (($database == 'postgresql') && (!function_exists('pg_connect'))) {
            continue;
        }
        if (($database == 'sqlserver') && (!function_exists('sqlsrv_connect'))) {
            continue;
        }
        if (($database == 'sqlserver_odbc') && (!function_exists('odbc_connect'))) {
            continue;
        }

        if (isset($SITE_INFO['db_type'])) {
            $selected = ($database == $SITE_INFO['db_type']);
        }

        if (array_key_exists($database, $database_names)) {
            $mapped_name = $database_names[$database];
        } else {
            $mapped_name = $database;
        }
        $mapped_name = is_maintained_description('database_' . $database, $mapped_name);
        $tdatabase->attach(do_template('FORM_SCREEN_INPUT_LIST_ENTRY', ['_GUID' => '2dada7c84d14b2ec44fcfa27d9d41513', 'SELECTED' => $selected, 'DISABLED' => false, 'NAME' => $database, 'CLASS' => '', 'TEXT' => $mapped_name]));

        if ($database != 'xml') {
            $dbs_found++;
        }
    }
    if ($tdatabase->is_empty() || $dbs_found == 0/* Better to provide no option, than just the XML option - confuses users*/) {
        warn_exit(do_lang_tempcode('NO_PHP_DB'));
    }

    $url = prepare_installer_url('install.php?step=4');

    $forum_path_default = get_file_base() . DIRECTORY_SEPARATOR . 'forums';

    $hidden = build_keep_post_fields();
    return do_template('INSTALLER_STEP_3', [
        '_GUID' => 'af52ecea73e9a8e2a92c12adbabbf4ab',
        'URL' => $url,
        'HIDDEN' => $hidden,
        'SIMPLE_FORUMS' => $simple_forums,
        'FORUM_PATH_DEFAULT' => $forum_path_default,
        'FORUMS' => $tforums,
        'DATABASES' => $tdatabase,
        'VERSION' => $default_version,
        'IS_QUICK' => @is_array($GLOBALS['FILE_ARRAY']),
    ]);
}

/**
 * Fourth installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_4() : object
{
    global $INSTALL_LANG;

    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    $table_prefix = get_default_table_prefix();

    $db_type = post_param_string('db_type');

    require_code('database');
    require_code('database/' . $db_type);
    $GLOBALS['DB_DRIVER'] = object_factory('Database_Static_' . $db_type, false, [$table_prefix]);

    // Probing

    $base_url = post_param_string('base_url', get_base_url(), INPUT_FILTER_URL_GENERAL);

    // Our forum is
    $forum_type = post_param_string('forum_type');
    require_code('forum/' . $forum_type);
    $GLOBALS['FORUM_DRIVER'] = object_factory('Forum_driver_' . filter_naughty_harsh($forum_type));
    $GLOBALS['FORUM_DRIVER']->MEMBER_ROWS_CACHED = [];

    // Try and grab ourselves forum details
    global $PROBED_FORUM_CONFIG;
    /** When forum drivers are asked to probe configuration from a potentially existing forum, it gets stored in here.
     *
     * @global array $PROBED_FORUM_CONFIG
     */
    $PROBED_FORUM_CONFIG = [];
    $PROBED_FORUM_CONFIG['sql_host'] = '';
    $PROBED_FORUM_CONFIG['sql_database'] = '';
    $PROBED_FORUM_CONFIG['sql_user'] = '';
    $PROBED_FORUM_CONFIG['sql_pass_exists'] = false; // We never auto-fill the password for security reasons; this gets set to true when not blank in the probe
    $board_path = post_param_string('board_path', '', INPUT_FILTER_POST_IDENTIFIER);
    find_forum_path($board_path); // Probe the configuration

    if ((!array_key_exists('forum_base_url', $PROBED_FORUM_CONFIG)) || (!(strlen($PROBED_FORUM_CONFIG['forum_base_url']) > 0))) {
        $file_base = get_file_base();
        for ($i = 0; $i < strlen($board_path); $i++) {
            if ($i >= strlen($file_base)) {
                break;
            }
            if ($board_path[$i] != $file_base[$i]) {
                break;
            }
        }

        $append = str_replace('\\', '/', substr($board_path, $i));
        $PROBED_FORUM_CONFIG['forum_base_url'] = (strlen($append) < 15) ? (substr($base_url, 0, strlen($base_url) - ($i - strlen($board_path))) . ((((strlen($append) > 0) && ($append[0] == '/'))) ? '' : '/') . $append) : ($base_url . '/forums');
    }

    if (($forum_type != 'cns') && ($forum_type != 'none')) {
        // Play safe with our defaults via prefixing; user is unlikely to get cookie domain/path settings right, and integration code could become broken if cookie-encoding scheme changes in 3rd party software
        if (array_key_exists('cookie_member_id', $PROBED_FORUM_CONFIG)) {
            $PROBED_FORUM_CONFIG['cookie_member_id'] = 'cms__' . $PROBED_FORUM_CONFIG['cookie_member_id'];
        } else {
            $PROBED_FORUM_CONFIG['cookie_member_id'] = 'cms_member_id';
        }
        if (array_key_exists('cookie_member_hash', $PROBED_FORUM_CONFIG)) {
            $PROBED_FORUM_CONFIG['cookie_member_hash'] = 'cms__' . $PROBED_FORUM_CONFIG['cookie_member_hash'];
        } else {
            $PROBED_FORUM_CONFIG['cookie_member_hash'] = 'cms_member_hash';
        }
    }

    $cookie_domain = '';//(($domain == 'localhost') || (strpos($domain, '.') === false)) ? '' : ('.' . $domain);
    $cookie_path = '/';
    $cookie_days = '1825';
    $use_persistent = false;
    require_code('version');
    $db_site_host = $PROBED_FORUM_CONFIG['sql_host'];
    if ($db_site_host == '') {
        if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
            $db_site_host = '127.0.0.1';
        } else {
            $db_site_host = 'localhost';
        }
    }
    $db_site_user = $PROBED_FORUM_CONFIG['sql_user'];
    $db_password_exists = $PROBED_FORUM_CONFIG['sql_pass_exists'];
    $db_site = $PROBED_FORUM_CONFIG['sql_database'];
    $forum_base_url = $PROBED_FORUM_CONFIG['forum_base_url'];
    $user_cookie = $PROBED_FORUM_CONFIG['cookie_member_id'];
    $pass_cookie = $PROBED_FORUM_CONFIG['cookie_member_hash'];
    $multi_lang_content = ((file_exists(get_file_base() . '/.git')/*randomise in dev mode*/) && ($db_type != 'xml')) ? mt_rand(0, 1) : 0;
    $domain = preg_replace('#:.*#', '', get_request_hostname());

    $forum_driver_specifics = $GLOBALS['FORUM_DRIVER']->install_specifics();

    $use_msn = post_param_integer('use_msn', 0);
    if ($use_msn == 0) {
        $use_msn = post_param_integer('use_multi_db', 0);
    }

    // Now we've gone through all the work of detecting it, lets grab from _config.php to see what we had last time we installed
    global $SITE_INFO;
    if ((file_exists(get_file_base() . '/_config.php')) && (filesize(get_file_base() . '/_config.php') != 0)) {
        // De-set what we know we can't re-use because we have better info now
        require_once(get_file_base() . '/_config.php');
        if (($PROBED_FORUM_CONFIG['sql_database'] != '') && ($forum_type != 'cns') && ($forum_type != 'none')) {
            if ((!array_key_exists('forum_type', $SITE_INFO)) || ($SITE_INFO['forum_type'] != $forum_type)) { // Don't want to throw detected versions of these away
                unset($SITE_INFO['user_cookie']);
                unset($SITE_INFO['pass_cookie']);
            }
            foreach ($forum_driver_specifics as $specific) {
                if (array_key_exists($specific['name'], $SITE_INFO)) {
                    unset($SITE_INFO[$specific['name']]);
                }
            }
            unset($SITE_INFO['db_forums_host']);
            unset($SITE_INFO['db_forums_user']);
            unset($SITE_INFO['db_forums_password']);
            unset($SITE_INFO['db_forums']);
            unset($SITE_INFO['db_site_host']);
            unset($SITE_INFO['db_site_user']);
            unset($SITE_INFO['db_site_password']);
            unset($SITE_INFO['db_site']);
        }
        unset($SITE_INFO['base_url']);

        // Copy from last time
        if (isset($SITE_INFO['cookie_domain'])) {
            $cookie_domain = $SITE_INFO['cookie_domain'];
        }
        if (isset($SITE_INFO['cookie_path'])) {
            $cookie_path = $SITE_INFO['cookie_path'];
        }
        if (!empty($SITE_INFO['cookie_days'])) {
            $cookie_days = $SITE_INFO['cookie_days'];
        }
        if (isset($SITE_INFO['table_prefix'])) {
            $table_prefix = $SITE_INFO['table_prefix'];
        }
        if (!empty($SITE_INFO['db_site_host'])) {
            $db_site_host = $SITE_INFO['db_site_host'];
        }
        if (!empty($SITE_INFO['db_site_user'])) {
            $db_site_user = $SITE_INFO['db_site_user'];
        }
        if (!empty($SITE_INFO['db_site_password'])) {
            $db_password_exists = ($SITE_INFO['db_site_password'] != '');
        }
        if (!empty($SITE_INFO['db_site'])) {
            $db_site = $SITE_INFO['db_site'];
        }
        if (!empty($SITE_INFO['user_cookie'])) {
            $user_cookie = $SITE_INFO['user_cookie'];
        }
        if (!empty($SITE_INFO['pass_cookie'])) {
            $pass_cookie = $SITE_INFO['pass_cookie'];
        }
        if (isset($SITE_INFO['domain'])) {
            $domain = $SITE_INFO['domain'];
        }
        if ((!file_exists(get_file_base() . '/.git')) || ($use_msn == 1)) {
            if (isset($SITE_INFO['multi_lang_content'])) {
                $multi_lang_content = intval($SITE_INFO['multi_lang_content']);
            }
        }
    }

    $db_forums_host = $db_site_host;
    $db_forums_user = $db_site_user;
    $db_forums_password_exists = $db_password_exists;
    $db_forums = $db_site;

    $sections = new Tempcode();

    // Detect FTP settings...

    if (php_function_allowed('posix_getpwuid')) {
        $u_info = posix_getpwuid(fileowner(get_file_base() . '/install.php'));
        if ($u_info !== false) {
            $ftp_username = $u_info['name'];
        } else {
            $ftp_username = '';
        }
    } else {
        $ftp_username = '';
    }
    if ($ftp_username === null) {
        $ftp_username = '';
    }
    $dr = array_key_exists('DOCUMENT_ROOT', $_SERVER) ? $_SERVER['DOCUMENT_ROOT'] : '';
    if (strpos($dr, '/') !== false) {
        $dr_parts = explode('/', $dr);
    } else {
        $dr_parts = explode('\\', $dr);
    }
    $webdir_stub = $dr_parts[count($dr_parts) - 1];

    // If we have a host where the FTP is two+ levels down (often when we have one FTP covering multiple virtual hosts), then this "last component" rule would be insufficient; do a search through for critical strings to try and make a better guess
    $special_root_dirs = ['public_html', 'www', 'webroot', 'httpdocs', 'httpsdocs', 'wwwroot', 'Documents'];
    $webdir_stub = $dr_parts[count($dr_parts) - 1];
    foreach ($dr_parts as $i => $part) {
        if (in_array($part, $special_root_dirs)) {
            $webdir_stub = implode('/', array_slice($dr_parts, $i));
        }
    }

    $ftp_folder = '/' . $webdir_stub . basename($_SERVER['SCRIPT_NAME']);
    $ftp_domain = $domain;

    // Is this autoinstaller? FTP settings...

    global $FILE_ARRAY;
    if ((@is_array($FILE_ARRAY)) && (!is_suexec_like())) {
        $title = protect_from_escaping(escape_html('FTP'));
        $text = do_lang_tempcode('AUTO_INSTALL');
        $hidden = new Tempcode();
        $options = new Tempcode();
        $options->attach(make_option(do_lang_tempcode('FTP_DOMAIN'), new Tempcode(), 'ftp_domain', post_param_string('ftp_domain', $ftp_domain, INPUT_FILTER_POST_IDENTIFIER), false, true));
        $options->attach(make_option(do_lang_tempcode('FTP_USERNAME'), new Tempcode(), 'ftp_username', post_param_string('ftp_username', $ftp_username, INPUT_FILTER_POST_IDENTIFIER), false, true));
        $options->attach(make_option(do_lang_tempcode('FTP_PASSWORD'), new Tempcode(), 'ftp_password', post_param_string('ftp_password', '', INPUT_FILTER_PASSWORD), true));
        $options->attach(make_option(do_lang_tempcode('FTP_DIRECTORY'), do_lang_tempcode('FTP_FOLDER'), 'ftp_folder', post_param_string('ftp_folder', $ftp_folder, INPUT_FILTER_POST_IDENTIFIER)));
        $options->attach(make_option(do_lang_tempcode('FTP_FILES_PER_GO'), do_lang_tempcode('DESCRIPTION_FTP_FILES_PER_GO'), 'max', post_param_string('max', '1000')));
        $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => '50fcb00f4d1da1813e94d86529ea0862', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]));
    }

    // General settings...

    $title = do_lang_tempcode('GENERAL_SETTINGS');
    $text = new Tempcode();
    $options = new Tempcode();
    $hidden = new Tempcode();
    if (!GOOGLE_APPENGINE) {
        $options->attach(make_option(do_lang_tempcode('BASE_URL'), example('', 'BASE_URL_TEXT'), 'base_url', $base_url, false, true));
    } else {
        $options->attach(make_option(do_lang_tempcode('GAE_APPLICATION'), do_lang_tempcode('DESCRIPTION_GAE_APPLICATION'), 'gae_application', preg_replace('#^.*~#', '', $_SERVER['APPLICATION_ID']), false, true));
        $hidden->attach(form_input_hidden('base_url', $base_url));
        $options->attach(make_option(do_lang_tempcode('GAE_BUCKET_NAME'), do_lang_tempcode('DESCRIPTION_GAE_BUCKET_NAME'), 'gae_bucket_name', '<application>', false, true));
    }
    $options->attach(make_option(do_lang_tempcode('EMAIL_ADDRESS'), example('', 'INSTALLER_EMAIL_ADDRESS'), 'email', post_param_string('email', '', INPUT_FILTER_POST_IDENTIFIER), false, false));
    $master_password = '';
    $options->attach(make_option(do_lang_tempcode('MASTER_PASSWORD'), ($forum_type == 'none') ? example('', 'CHOOSE_MASTER_PASSWORD_ADMIN') : example('', 'CHOOSE_MASTER_PASSWORD_NO_ADMIN'), 'master_password', $master_password, true));
    require_lang('config');
    require_lang('privacy');
    $options->attach(make_tick(do_lang_tempcode('SEND_ERROR_EMAILS_OCPRODUCTS'), example('', 'CONFIG_OPTION_send_error_emails_ocproducts'), 'send_error_emails_ocproducts', 1));
    $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => 'f051465e86a7a53ec078e0d9de773993', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]));
    $hidden->attach(form_input_hidden('self_learning_cache', '1'));

    // Database settings for forum (if applicable)...

    $forum_text = new Tempcode();
    if (($forum_type == 'cns') || ($forum_type == 'none')) {
        $forum_title = do_lang_tempcode('MEMBER_SETTINGS');
    } else {
        $_forum_type = do_lang('FORUM_CLASS_' . preg_replace('#\d+$#', '', $forum_type), null, null, null, null, false);
        if ($_forum_type === null) {
            $_forum_type = cms_ucwords_ascii($forum_type);
        }
        $forum_title = do_lang_tempcode('_FORUM_SETTINGS', escape_html($_forum_type));
    }
    $forum_options = new Tempcode();
    $forum_type = post_param_string('forum_type');
    if ($forum_type != 'none') {
        if ($use_msn == 1) {
            if ($forum_type != 'cns') {
                $forum_text = do_lang_tempcode('AUTODETECT');
            }
            $forum_options->attach(make_option(do_lang_tempcode('DATABASE_NAME'), new Tempcode(), 'db_forums', $db_forums, false, true));
            if (!$GLOBALS['DB_DRIVER']->is_flat_file_simple()) {
                $forum_options->attach(make_option(do_lang_tempcode('DATABASE_HOST'), example('', 'DATABASE_HOST_TEXT'), 'db_forums_host', $db_forums_host, false, true));
                $forum_options->attach(make_option(do_lang_tempcode('DATABASE_USERNAME'), new Tempcode(), 'db_forums_user', $db_forums_user, false, true));
                $forum_options->attach(make_option(do_lang_tempcode('DATABASE_PASSWORD'), new Tempcode(), 'db_forums_password', '', true, $db_forums_password_exists));
            } else {
                $hidden->attach(form_input_hidden('db_forums_host', 'localhost'));
                $hidden->attach(form_input_hidden('db_forums_user', ''));
                $hidden->attach(form_input_hidden('db_forums_password', ''));
            }
            $hidden->attach(form_input_hidden('use_msn', strval($use_msn)));
        }
        if (($forum_type != 'cns') || ($use_msn == 1)) {
            $forum_options->attach(make_option(do_lang_tempcode('BASE_URL'), example('FORUM_BASE_URL_EXAMPLE', 'BASE_URL_TEXT_FORUM'), 'forum_base_url', $forum_base_url, false, true));
        }
    }
    foreach ($forum_driver_specifics as $specific) {
        if ($specific['name'] == 'clear_existing_forums_on_install') {
            $hidden->attach(form_input_hidden('clear_existing_forums_on_install', 'yes'));
        } elseif (($specific['name'] != 'cns_table_prefix') || ($use_msn == 1)) {
            $forum_options->attach(make_option(
                is_object($specific['title']) ? $specific['title'] : make_string_tempcode($specific['title']),
                is_object($specific['description']) ? $specific['description'] : make_string_tempcode($specific['description']),
                $specific['name'],
                @cms_empty_safe($SITE_INFO[$specific['name']]) ? $specific['default'] : $SITE_INFO[$specific['name']],
                strpos($specific['name'], 'password') !== false,
                array_key_exists('required', $specific) ? $specific['required'] : false
            ));
        }
    }

    // Database settings for site...

    $text = ($use_msn == 1) ? do_lang_tempcode(($forum_type == 'cns') ? 'DUPLICATE_CNS' : 'DUPLICATE') : new Tempcode();
    $options = make_option(do_lang_tempcode('DATABASE_NAME'), new Tempcode(), 'db_site', $db_site, false, true);
    if (!$GLOBALS['DB_DRIVER']->is_flat_file_simple()) {
        $options->attach(make_option(do_lang_tempcode('DATABASE_HOST'), example('', 'DATABASE_HOST_TEXT'), 'db_site_host', $db_site_host, false, true));
        $options->attach(make_option(do_lang_tempcode('DATABASE_USERNAME'), new Tempcode(), 'db_site_user', $db_site_user, false, true));
        $options->attach(make_option(do_lang_tempcode('DATABASE_PASSWORD'), new Tempcode(), 'db_site_password', '', true, $db_password_exists));
    } else {
        $hidden->attach(form_input_hidden('db_site_host', 'localhost'));
        $hidden->attach(form_input_hidden('db_site_user', ''));
        $hidden->attach(form_input_hidden('db_site_password', ''));
    }
    if ($db_type != 'xml') {
        $options->attach(make_option(do_lang_tempcode('TABLE_PREFIX'), example('', 'TABLE_PREFIX_TEXT'), 'table_prefix', $table_prefix));
    } else {
        $hidden->attach(form_input_hidden('table_prefix', $table_prefix));
    }
    /*if (!GOOGLE_APPENGINE) {   Excessive, let user tune later
        $options->attach(make_tick(do_lang_tempcode('USE_PERSISTENT'), example('', 'USE_PERSISTENT_TEXT'), 'use_persistent', $use_persistent ? 1 : 0));
    }*/

    $title = do_lang_tempcode((($forum_type == 'cns' || $forum_type == 'none') && $use_msn == 0) ? 'DATABASE_SETTINGS' : 'COMPOSR_SETTINGS');
    if (GOOGLE_APPENGINE) {
        $title = do_lang_tempcode('DEV_DATABASE_SETTINGS');
        $text = do_lang_tempcode('DEV_DATABASE_SETTINGS_HELP');
        $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => 'b83e25e6f09776a1b69c4e329bbd267d', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]));

        $title = do_lang_tempcode('LIVE_DATABASE_SETTINGS');
        $text = do_lang_tempcode('LIVE_DATABASE_SETTINGS_HELP');
        $options = new Tempcode();
        $options->attach(make_option(do_lang_tempcode('DATABASE_HOST'), new Tempcode(), 'gae_live_db_site_host', ':/cloudsql/<application>:<application>', false, true));
        $options->attach(make_option(do_lang_tempcode('DATABASE_NAME'), new Tempcode(), 'gae_live_db_site', '<application>', false, true));
        $options->attach(make_option(do_lang_tempcode('DATABASE_USERNAME'), new Tempcode(), 'gae_live_db_site_user', 'root', false, true));
        $options->attach(make_option(do_lang_tempcode('DATABASE_PASSWORD'), new Tempcode(), 'gae_live_db_site_password', '', true));
        $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => '4d6ef04f3ab75634cad2a82a53d55955', 'HIDDEN' => '', 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]));
    } else {
        if (!$forum_options->is_empty()) {
            $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => '232b69a995f384275c1cd9269a42c3b8', 'HIDDEN' => '', 'TITLE' => $forum_title, 'TEXT' => $forum_text, 'OPTIONS' => $forum_options]));
        }
        $sections->attach(do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => '15e0f275f78414b6c4fe7775a1cacb23', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]));
    }

    // Cookie settings...

    if (!GOOGLE_APPENGINE) {
        $visible_cookie_options = ($forum_type != 'cns' && $forum_type != 'none');
        $title = do_lang_tempcode('COOKIE_SETTINGS');
        $text = new Tempcode();
        $options = new Tempcode();
        $hidden = new Tempcode();
        $cookie_text = do_lang_tempcode('COOKIE_TEXT');
        if (substr($user_cookie, 0, 5) != 'cms__') {
            $cookie_text->attach('<br />');
            $cookie_text->attach(do_lang_tempcode('COOKIE_TEXT__CMS_PREFIX'));
        }
        $options->attach(make_option(do_lang_tempcode('COOKIE'), example('COOKIE_EXAMPLE', $cookie_text), 'user_cookie', $user_cookie, false, true));
        $cookie_password_text = do_lang_tempcode('COOKIE_PASSWORD_TEXT');
        if (substr($pass_cookie, 0, 5) != 'cms__') {
            $cookie_password_text->attach('<br />');
            $cookie_password_text->attach(do_lang_tempcode('COOKIE_TEXT__CMS_PREFIX'));
        }
        $options->attach(make_option(do_lang_tempcode('COOKIE_PASSWORD'), example('COOKIE_PASSWORD_EXAMPLE', $cookie_password_text), 'pass_cookie', $pass_cookie, false, true));
        $options->attach(make_option(do_lang_tempcode('COOKIE_DOMAIN'), example('COOKIE_DOMAIN_EXAMPLE', 'COOKIE_DOMAIN_TEXT'), 'cookie_domain', $cookie_domain));
        $options->attach(make_option(do_lang_tempcode('COOKIE_PATH'), example('COOKIE_PATH_EXAMPLE', 'COOKIE_PATH_TEXT'), 'cookie_path', $cookie_path));
        $options->attach(make_option(do_lang_tempcode('COOKIE_DAYS'), example('COOKIE_DAYS_EXAMPLE', 'COOKIE_DAYS_TEXT'), 'cookie_days', $cookie_days, false, true));
        $cookie_options = do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => '3b9ea022164801f4b60780a4a966006f', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]);
        if ($visible_cookie_options) {
            $sections->attach($cookie_options);
        }
    }

    // General advanced settings...

    $title = do_lang_tempcode('GENERAL');
    $text = new Tempcode();
    $options = new Tempcode();
    $hidden = new Tempcode();

    // Attach values to set
    if (isset($PROBED_FORUM_CONFIG['set_values'])) {
        foreach ($PROBED_FORUM_CONFIG['set_values'] as $value_pair) {
            $hidden->attach(form_input_hidden('value__' . strval($value_pair[0]), strval($value_pair[1])));
        }
    }

    $options->attach(make_tick(do_lang_tempcode('MULTI_LANG_CONTENT'), is_maintained_description('multi_lang_content', example('', 'MULTI_LANG_CONTENT_TEXT')), 'multi_lang_content', $multi_lang_content));
    $general_advanced_options = do_template('INSTALLER_STEP_4_SECTION', ['_GUID' => 'g051465e86a7a53ec078e0d9de773993', 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => $text, 'OPTIONS' => $options]);

    $temp = new Tempcode();
    if (!GOOGLE_APPENGINE) {
        if (!$visible_cookie_options) {
            $temp->attach($cookie_options);
        }
    }
    $temp->attach($general_advanced_options);
    $sections->attach(do_template('INSTALLER_STEP_4_SECTION_HIDE', ['_GUID' => '42eb3d44bcf8ef99987b6daa9e6530aa', 'TITLE' => do_lang('ADVANCED_BELOW'), 'CONTENT' => $temp]));

    // ----

    $message = paragraph(do_lang_tempcode('BASIC_CONFIG'));
    if (($forum_type != 'none') && ($forum_type != 'cns')) {
        $message->attach(paragraph(do_lang_tempcode('FORUM_DRIVER_NATIVE_LOGIN')));
    }

    $url = prepare_installer_url('install.php?step=5');

    $hidden = build_keep_post_fields();

    return do_template('INSTALLER_STEP_4', [
        '_GUID' => '73c3ac0a7108709b74b2e89cae30be12',
        'URL' => $url,
        'HIDDEN' => $hidden,
        'MESSAGE' => $message,
        'LANG' => $INSTALL_LANG,
        'DB_TYPE' => $db_type,
        'FORUM_TYPE' => $forum_type,
        'BOARD_PATH' => $board_path,
        'SECTIONS' => $sections,
        'MAX' => strval(post_param_integer('max', 1000)),
    ]);
}

/**
 * Fifth installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_5() : object
{
    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    if (isset($_POST['table_prefix'])) {
        $_POST['table_prefix'] = preg_replace('#[^\w]#', '', $_POST['table_prefix']);
    }
    if (isset($_POST['cns_table_prefix'])) {
        $_POST['cns_table_prefix'] = preg_replace('#[^\w]#', '', $_POST['cns_table_prefix']);
    }

    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    $url = prepare_installer_url('install.php?step=6');

    $use_msn = post_param_integer('use_msn', 0);
    if ($use_msn == 0) {
        $use_msn = post_param_integer('use_multi_db', 0);
    }
    if ($use_msn == 0) { // If not on a multi-site-network, forum access is the same as site access.
        $_POST['db_forums'] = $_POST['db_site'];
        $_POST['db_forums_host'] = $_POST['db_site_host'];
        $_POST['db_forums_user'] = $_POST['db_site_user'];
        $_POST['db_forums_password'] = $_POST['db_site_password'];
        $_POST['cns_table_prefix'] = array_key_exists('table_prefix', $_POST) ? $_POST['table_prefix'] : get_default_table_prefix();
    }

    // Checkbox fields that need to be explicitly saved, as the default is not 0
    $multi_lang_content = post_param_integer('multi_lang_content', 0);
    if ($multi_lang_content == 0) {
        $_POST['multi_lang_content'] = '0';
    }

    // Cleanup base URL
    $_POST['base_url'] = normalise_idn_url($_POST['base_url']);

    // Test URL
    $parsed = @parse_url(post_param_string('base_url'));
    if (($parsed === false) || (!array_key_exists('scheme', $parsed)) || (!array_key_exists('host', $parsed))) {
        warn_exit(do_lang_tempcode('INVALID_BASE_URL', escape_html(post_param_string('base_url'))));
    }

    // Check cookie settings. IF THIS CODE IS CHANGED ALSO CHANGE COPY&PASTED CODE IN CONFIG_EDITOR.PHP
    $cookie_path = post_param_string('cookie_path', false, INPUT_FILTER_POST_IDENTIFIER);
    $cookie_domain = post_param_string('cookie_domain', false, INPUT_FILTER_POST_IDENTIFIER);
    $base_url = post_param_string('base_url', false, INPUT_FILTER_URL_GENERAL);
    if (strpos($base_url, '://') === false) {
        $base_url = 'http://' . $base_url;
    }
    if (substr($base_url, -1) == '/') {
        $base_url = substr($base_url, 0, strlen($base_url) - 1);
    }
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
        warn_exit(do_lang_tempcode('COOKIE_PATH_MUST_MATCH', escape_html($url_parts['path'])));
    }
    if ($cookie_domain != '') {
        if (strpos($url_parts['host'], '.') === false) {
            warn_exit(do_lang_tempcode('COOKIE_DOMAIN_CANT_USE'));
        }
        if (substr($cookie_domain, 0, 1) != '.') {
            warn_exit(do_lang_tempcode('COOKIE_DOMAIN_MUST_START_DOT'));
        } elseif (substr($url_parts['host'], 1 - strlen($cookie_domain)) != substr($cookie_domain, 1)) {
            warn_exit(do_lang_tempcode('COOKIE_DOMAIN_MUST_MATCH', escape_html($url_parts['host'])));
        }
    }

    // Test base URL isn't subject to redirects
    if (preg_replace('#:\d+$#', '', $_SERVER['HTTP_HOST']) != $url_parts['host']) {
        $test_url = $base_url . '/installer_is_testing_base_urls.php';
        require_code('files');
        $http_result = cms_http_request($test_url, ['trigger_error' => false]);
        if ($http_result->download_url != $test_url) {
            if (preg_replace('#www\.#', '', $http_result->download_url) == $test_url) {
                warn_exit(do_lang_tempcode('BASE_URL_REDIRECTS_WITH_WWW'));
            } elseif ($http_result->download_url == preg_replace('#www\.#', '', $test_url)) {
                warn_exit(do_lang_tempcode('BASE_URL_REDIRECTS_WITHOUT_WWW'));
            }
        }
    }

    $forum_type = post_param_string('forum_type');

    // If this exists, we may as well try and read it - may have some special flags in here during installation that we want to propagate
    global $SITE_INFO;
    @include(get_file_base() . '/_config.php');
    foreach ($SITE_INFO as $key => $val) {
        if (isset($_POST[$key])) {
            continue;
        }

        // Ignore reading in forum_base_url
        if (($forum_type == 'cns') && ($use_msn == 0) && ($key == 'forum_base_url')) {
            continue;
        }

        $_POST[$key] = $val;
    }

    // Read in a temporary SITE_INFO, but only so this step has something to run with (the _config.php write doesn't use this data)
    foreach ($_POST as $key => $val) {
        if (in_array($key, [
            'ftp_password',
            'ftp_password_confirm',
            'master_password_confirm',
            'cns_admin_password',
            'cns_admin_password_confirm',
            'post_data',
        ])) {
            continue;
        }

        if ($key == 'master_password') {
            $val = password_hash($val, PASSWORD_BCRYPT, ['cost' => 12]);
        }
        $SITE_INFO[$key] = trim($val);
    }

    // Check database credentials / load DB connection
    $tmp = confirm_db_credentials(true);

    // Give warning if database contains data
    if (post_param_integer('confirm', 0) == 0) {
        $test = $tmp->query_select_value_if_there('db_meta', 'COUNT(*)', [], '', true);
        if ((($test !== null) && ($test > 0)) || file_exists(get_file_base() . '/_config.php')) {
            global $INSTALL_LANG;
            $sections = new Tempcode();

            $url = prepare_installer_url('install.php?step=5');

            $hidden = build_keep_post_fields();
            $hidden->attach(form_input_hidden('confirm', '1'));

            return do_template('INSTALLER_STEP_4', [
                '_GUID' => 'aaf0386966dd4b75c8027a6b1f7454c6',
                'URL' => $url,
                'HIDDEN' => $hidden,
                'MESSAGE' => do_lang_tempcode('WARNING_OVERWRITE', escape_html(get_tutorial_url('tut_upgrade'))),
                'LANG' => $INSTALL_LANG,
                'DB_TYPE' => post_param_string('db_type'),
                'FORUM_TYPE' => $forum_type,
                'BOARD_PATH' => post_param_string('board_path', false, INPUT_FILTER_POST_IDENTIFIER),
                'SECTIONS' => $sections,
                'MAX' => null,
            ]);
        }
    }

    // Give warning if setting up a multi-site-network to a bad database
    if (($_POST['db_forums'] != $_POST['db_site']) && (get_forum_type() == 'cns')) {
        $tmp2 = new DatabaseConnector(post_param_string('db_forums', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_host', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_user', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_password', false, INPUT_FILTER_PASSWORD), post_param_string('cns_table_prefix', false, INPUT_FILTER_POST_IDENTIFIER));
        if ($tmp2->query_select_value('db_meta', 'COUNT(*)', [], '', true) === null) {
            warn_exit(do_lang_tempcode('MSN_FORUM_DB_NOT_CNS_ALREADY'));
        }
    }

    // FTP uploads if we're in the quick installer
    global $FILE_ARRAY;
    $still_ftp = false;
    $log = new Tempcode();
    if (@is_array($FILE_ARRAY)) {
        $ftp_status = step_5_ftp();
        $log->attach($ftp_status[0]);
        if ($ftp_status[1] != -1) {
            $url = prepare_installer_url('install.php?step=5&start_from=' . strval($ftp_status[1]));
            $still_ftp = true;
        }
    }

    // If done with FTP, do the main stuff for this step
    if (!$still_ftp) {
        require_code('zones');
        require_code('comcode');
        require_code('themes');

        $log->attach(step_5_checks_a());
        $log->attach(step_5_write_config());
        $log->attach(step_5_checks_b());
        $log->attach(step_5_uninstall());
        $log->attach(step_5_core());
        include_cns();
        $log->attach(step_5_core_2());
    }

    return do_template('INSTALLER_STEP_LOG', [
        '_GUID' => '83ed0405bc32fdf2cc499662bfa51bc9',
        'PREVIOUS_STEP' => '4',
        'CURRENT_STEP' => '5',
        'URL' => $url,
        'LOG' => $log,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Jerry-rig a site tied Conversr, for basic installation, and prepping incase we switch to it.
 */
function include_cns()
{
    require_code('forum/cns');
    global $SITE_INFO; // We will be installing Conversr to our CMS DB, regardless. It's pretty complex - at install time, we install locally - at run time, it could be over an MSN to another install
    $SITE_INFO['db_forums'] = $SITE_INFO['db_site'];
    $SITE_INFO['db_forums_host'] = $SITE_INFO['db_site_host'];
    $SITE_INFO['db_forums_user'] = $SITE_INFO['db_site_user'];
    $SITE_INFO['db_forums_password'] = $SITE_INFO['db_site_password'];
    $SITE_INFO['cns_table_prefix'] = array_key_exists('table_prefix', $SITE_INFO) ? $SITE_INFO['table_prefix'] : get_default_table_prefix();
    $GLOBALS['FORUM_DRIVER'] = object_factory('Forum_driver_cns');
    $GLOBALS['FORUM_DB'] = $GLOBALS['SITE_DB'];
    $GLOBALS['FORUM_DRIVER']->db = $GLOBALS['SITE_DB'];
    $GLOBALS['FORUM_DRIVER']->MEMBER_ROWS_CACHED = [];
    $GLOBALS['CNS_DRIVER'] = $GLOBALS['FORUM_DRIVER'];
}

/**
 * Fifth installation step: FTP upload (not used for manual installer).
 *
 * @return array A pair: progress report/ui, and number of files uploaded so far (or -1 meaning all uploaded)
 */
function step_5_ftp() : array
{
    global $FILE_ARRAY, $DIR_ARRAY;

    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    if (!is_suexec_like()) {
        if (!function_exists('ftp_connect')) {
            warn_exit(do_lang_tempcode('NO_PHP_FTP'));
        }

        $ftp_domain = post_param_string('ftp_domain', false, INPUT_FILTER_POST_IDENTIFIER);
        if (strpos($ftp_domain, 'ftp://') !== false) {
            warn_exit(do_lang_tempcode('FTP_DOMAIN_NOT_LIKE_THIS'));
        }
        $port = 21;
        if (strpos($ftp_domain, ':') !== false) {
            list($ftp_domain, $_port) = explode(':', $ftp_domain, 2);
            $port = intval($_port);
        }

        $conn = false;
        if (function_exists('ftp_ssl_connect')) {
            $conn = @ftp_ssl_connect($ftp_domain, $port);
        }
        $ssl = ($conn !== false);

        $username = post_param_string('ftp_username', false, INPUT_FILTER_POST_IDENTIFIER);
        $password = post_param_string('ftp_password', false, INPUT_FILTER_PASSWORD);

        if (($ssl) && (@ftp_login($conn, $username, $password) === false)) {
            $conn = false;
            $ssl = false;
        }
        if ($conn === false) {
            $conn = ftp_connect($ftp_domain, $port);
        }
        if ($conn === false) {
            warn_exit(do_lang_tempcode('NO_FTP_CONNECT'));
        }

        if ((!$ssl) && (!@ftp_login($conn, $username, $password))) {
            warn_exit(do_lang_tempcode('NO_FTP_LOGIN', cms_error_get_last()));
        }

        $ftp_folder = post_param_string('ftp_folder', false, INPUT_FILTER_POST_IDENTIFIER);
        if (substr($ftp_folder, -1) != '/') {
            $ftp_folder .= '/';
        }
        if (!@ftp_chdir($conn, $ftp_folder)) {
            warn_exit(do_lang_tempcode('NO_FTP_DIR', cms_error_get_last(), '1'));
        }
        $files = @ftp_nlist($conn, '.');
        if ($files === false) { // :(. Weird bug on some systems
            $files = [];
            if (@ftp_rename($conn, 'install.php', 'install.php')) {
                $files = ['install.php', 'data.cms'];
            }
        }
        if (!in_array('install.php', $files)) {
            warn_exit(do_lang_tempcode('NO_FTP_DIR', cms_error_get_last(), '2'));
        }

        $overwrite_ok = !file_exists(get_file_base() . '/cms_inst_tmp/tmp'); // Because if the file doesn't exist, the step completed in full - we DON'T want to overwrite if it didn't, because the step probably timed out and by refreshing we complete the step in pieces

        if (!file_exists('cms_inst_tmp')) { // If it hasn't been here before
            // Make temporary directory
            if ((!in_array('cms_inst_tmp', $files)) && (!is_string(@ftp_mkdir($conn, 'cms_inst_tmp')))) {
                warn_exit(do_lang_tempcode('NO_FTP_ACCESS'));
            }
            @ftp_site($conn, 'CHMOD 0777 cms_inst_tmp');
        }
        if (!cms_is_writable('cms_inst_tmp')) {
            warn_exit(do_lang_tempcode('MANUAL_CHMOD_TMP_FILE'));
        }

        // Test tmp file isn't currently being used by another iteration of process (race issue, causing horrible corruption)
        $lock_myfile = fopen(get_file_base() . '/cms_inst_tmp/tmp', 'cb');
        if (!flock($lock_myfile, LOCK_EX)) {
            warn_exit(do_lang_tempcode('DATA_FILE_CONFLICT'));
        }
        $file_size_before = @filesize(get_file_base() . '/cms_inst_tmp/tmp');
        if (php_function_allowed('usleep')) {
            usleep(1000000);
        }
        $file_size_after = @filesize(get_file_base() . '/cms_inst_tmp/tmp');
        if ($file_size_before !== $file_size_after) {
            warn_exit(do_lang_tempcode('DATA_FILE_CONFLICT'));
        }
        flock($lock_myfile, LOCK_UN);
        fclose($lock_myfile);
    } else {
        $overwrite_ok = true;
        $files = [];
        if (file_exists(get_file_base() . '/_config.php')) {
            $files[] = '_config.php';
        }
    }

    // Make folders
    $langs1 = get_dir_contents('lang');
    $langs2 = get_dir_contents('lang_custom');
    $langs = array_merge($langs1, $langs2);
    foreach ($DIR_ARRAY as $dir) {
        if (strpos($dir, '/' . fallback_lang()) !== false) {
            foreach (array_keys($langs) as $lang) {
                if (($lang == fallback_lang()) || (strpos($lang, '.') !== false)) {
                    continue;
                }

                if (is_suexec_like()) {
                    @mkdir(get_file_base() . '/' . str_replace('/' . fallback_lang(), '/' . $lang, $dir), 0777);
                    fix_permissions(get_file_base() . '/' . str_replace('/' . fallback_lang(), '/' . $lang, $dir));
                } else {
                    @ftp_mkdir($conn, str_replace('/' . fallback_lang(), '/' . $lang, $dir));
                    @ftp_site($conn, 'CHMOD 755 ' . str_replace('/' . fallback_lang(), '/' . $lang, $dir));
                }
            }
        }
        if (is_suexec_like()) {
            @mkdir(get_file_base() . '/' . $dir, 0777);
            fix_permissions(get_file_base() . '/' . $dir);
        } else {
            @ftp_mkdir($conn, $dir);
            if (($dir == 'exports/addons') && (!is_suexec_like())) {
                @ftp_site($conn, 'CHMOD 777 ' . $dir);
            } else {
                @ftp_site($conn, 'CHMOD 755 ' . $dir);
            }
        }
    }

    // Upload files
    $count = file_array_count();
    $php_perms = fileperms(get_file_base() . '/install.php');
    $start_pos = get_param_integer('start_from', 0);
    $done_all = false;
    $time_start = time();
    $max_time = min(20, intval(round(floatval(ini_get('max_execution_time')) / 1.5)));
    $max = post_param_integer('max', is_suexec_like() ? 5000 : 1000);
    for ($i = $start_pos; $i < $start_pos + $max; $i++) {
        list($filename, $contents) = file_array_get_at($i);
        if (is_string($contents)) {
            $file_size = strlen($contents);
        } else {
            list($file_size, $dump_myfile, $dump_offset) = $contents;
        }

        if (($filename != '_config.php') || (!in_array('_config.php', $files))) {
            if (
                (($overwrite_ok) || (!file_exists(get_file_base() . '/' . $filename)) || (/*@ for possible race condition reported in #53910*/@filemtime(get_file_base() . '/' . $filename) < filemtime(get_file_base() . '/install.php')) || (filesize(get_file_base() . '/' . $filename) != $file_size))
                &&
                (($filename != 'forum/index.php') || (!file_exists(get_file_base() . '/' . $filename)))
            ) {
                if ((strpos($filename, '/' . fallback_lang() . '/') !== false) && (is_string($contents))) {
                    foreach (array_keys($langs) as $lang) { // Write out all the files under language directories (as we only packed them into our installer under EN) {
                        if (($lang == fallback_lang()) || (strpos($lang, '.') !== false)) {
                            continue;
                        }

                        if (is_suexec_like()) {
                            file_put_contents(get_file_base() . '/' . str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $filename), $contents);
                            fix_permissions(get_file_base() . '/' . str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $filename));
                        } else {
                            @ftp_delete($conn, str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $filename));
                            file_put_contents(get_file_base() . '/cms_inst_tmp/tmp', $contents);
                            ftp_put($conn, str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $filename), get_file_base() . '/cms_inst_tmp/tmp', FTP_BINARY);
                            $mask = 0;
                            if (get_file_extension($filename) == 'php') {
                                if (($php_perms & 0100) == 0100) { // If PHP files need to be marked user executable
                                    $mask = $mask | 0100;
                                }
                                if (($php_perms & 0010) == 0010) { // If PHP files need to be marked group executable
                                    $mask = $mask | 0010;
                                }
                                if (($php_perms & 0001) == 0001) { // If PHP files need to be marked other executable
                                    $mask = $mask | 0001;
                                }
                            }
                            @ftp_site($conn, 'CHMOD 0' . decoct(0644 | $mask) . ' ' . str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $filename));
                        }
                    }
                }
            }
            if (is_suexec_like()) {
                $myfile = fopen(get_file_base() . '/' . $filename, 'wb');
                if (is_string($contents)) {
                    fwrite($myfile, $contents);
                } else {
                    fseek($dump_myfile, $dump_offset, SEEK_SET);
                    $amount_read = 0;
                    while ($amount_read < $file_size) {
                        $read_amount = min(4096, $file_size - $amount_read);
                        $shuttle_contents = fread($dump_myfile, $read_amount);
                        fwrite($myfile, $shuttle_contents);
                        $amount_read += strlen($shuttle_contents);
                    }
                }
                fclose($myfile);
                fix_permissions(get_file_base() . '/' . $filename);
            } else {
                @ftp_delete($conn, $filename);
                $tmp = fopen(get_file_base() . '/cms_inst_tmp/tmp', 'wb');
                if (is_string($contents)) {
                    fwrite($tmp, $contents);
                } else {
                    fseek($dump_myfile, $dump_offset, SEEK_SET);
                    $amount_read = 0;
                    while ($amount_read < $file_size) {
                        $read_amount = min(4096, $file_size - $amount_read);
                        $shuttle_contents = fread($dump_myfile, $read_amount);
                        fwrite($tmp, $shuttle_contents);
                        $amount_read += strlen($shuttle_contents);
                    }
                }
                fclose($tmp);
                if (!@ftp_put($conn, $filename, get_file_base() . '/cms_inst_tmp/tmp', FTP_BINARY)) {
                    if (strpos(cms_error_get_last(), 'bind() failed') !== false) {
                        warn_exit(do_lang_tempcode('FTP_FIREWALL_ERROR'));
                    } else {
                        warn_exit(cms_error_get_last());
                    }
                }
                $mask = 0;
                if (get_file_extension($filename) == 'php') {
                    if (($php_perms & 0100) == 0100) { // If PHP files need to be marked user executable
                        $mask = $mask | 0100;
                    }
                    if (($php_perms & 0010) == 0010) { // If PHP files need to be marked group executable
                        $mask = $mask | 0010;
                    }
                    if (($php_perms & 0001) == 0001) { // If PHP files need to be marked other executable
                        $mask = $mask | 0001;
                    }
                }
                @ftp_site($conn, 'CHMOD ' . decoct(0644 | $mask) . ' ' . $filename);
            }
        }

        if (($max_time > 0) && ((time() - $time_start) >= $max_time)) {
            break;
        }

        if ($i + 1 == $count) {
            $done_all = true;
            $i++;
            break; // That's them all
        }
    }

    if (!is_suexec_like()) {
        if (!file_exists(get_file_base() . '/cms_inst_tmp/tmp')) {
            warn_exit(do_lang_tempcode('DOUBLE_INSTALL_DO'));
        }
        @unlink(get_file_base() . '/cms_inst_tmp/tmp');
    }

    test_htaccess(is_suexec_like() ? null : $conn);

    $log = new Tempcode();
    if ($done_all) {
        // If the file user is different to the FTP user, we need to make it world writeable
        if (!is_suexec_like()) {
            // Chmod
            $chmodding_errors = false;
            $chmod_array = get_chmod_array();
            foreach ($chmod_array as $chmod) {
                if ((file_exists($chmod)) && (!@ftp_site($conn, 'CHMOD 0777 ' . $chmod))) {
                    $chmodding_errors = true;
                }
            }
            $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '2e4ccdd5a0b034125ee62403d5a48319', 'SOMETHING' => do_lang_tempcode($chmodding_errors ? 'CHMOD_FAIL' : 'CHMOD_PASS')]));
        }
    }

    if (!is_suexec_like()) {
        if (function_exists('ftp_close')) {
            ftp_close($conn);
        }
    }
    $log->attach(do_template('INSTALLER_DONE_SOMETHING', [
        '_GUID' => '1b447cee9e9aa3ad8e24530d4dceb03f',
        'SOMETHING' => do_lang_tempcode('FILES_TRANSFERRED', strval($i + 1 - 1/*+1 is due to counting from zero and -1 is because a for variable ends 1 more than it executed for*/), strval($count)),
    ]));
    return [$log, $done_all ? -1 : $i];
}

/**
 * Fifth installation step: sanity checks.
 *
 * @return Tempcode Progress report / UI
 */
function step_5_checks_a() : object
{
    $log = new Tempcode();

    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    // Check path
    if (!file_exists(get_file_base() . '/sources/global.php')) {
        warn_exit(do_lang_tempcode('BAD_PATH'));
    }

    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '48b15e3e8486e5654563a7c3b5e6af58', 'SOMETHING' => do_lang_tempcode('GOOD_PATH')]));

    // Check permissions (after extraction known to have happened)
    list(, , $paths) = scan_permissions(false, false, null, null, CMSPermissionsScanner::RESULT_TYPE_ERROR_MISSING);
    foreach ($paths as $path) {
        intelligent_write_error($path);
    }

    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'e2daeaa9060623786decb008289068da', 'SOMETHING' => do_lang_tempcode('FILE_PERM_GOOD')]));

    return $log;
}

/**
 * Fifth installation step: sanity checks (after config written).
 *
 * @return Tempcode Progress report / UI
 */
function step_5_checks_b() : object
{
    $log = new Tempcode();

    // MySQL check (could not be checked earlier due to lack of active connection)
    $_warnings = installer_health_checks(['Installation environment \\ MySQL version']);
    foreach ($_warnings as $_warning) {
        $warning = do_template('INSTALLER_WARNING', ['_GUID' => 'fb748246869dd9ebffa4d2967a2931cd', 'MESSAGE' => $_warning]);
        $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '214790e075dc09f8dff8e69789311984', 'SOMETHING' => $warning]));
    }

    return $log;
}

/**
 * Run installer Health Checks.
 *
 * @param  ?array $sections_to_run Which check sections to run (null: all)
 * @return array Warnings
 */
function installer_health_checks(?array $sections_to_run = null) : array
{
    $_warnings = [];
    if (addon_installed('health_check')) {
        require_code('health_check');
        health_check_log_start();
        $hook_obs = find_all_hook_obs('systems', 'health_checks', 'Hook_health_check_');
        foreach ($hook_obs as $ob) {
            list(, $sections) = $ob->run($sections_to_run, CHECK_CONTEXT__INSTALL);
            foreach ($sections as $results) {
                foreach ($results as $_result) {
                    if ($_result[0] == HEALTH_CHECK__FAIL) {
                        if ((substr(trim($_result[1]), -1) != '.') && (substr(trim($_result[1]), -8) != '.[/html]')) {
                            $_result[1] .= '.';
                        }
                        $_warning = comcode_to_tempcode($_result[1]);
                        $_warnings[] = $_warning;
                    }
                }
            }
        }
        health_check_log_stop();
    }
    return $_warnings;
}

/**
 * Fifth installation step: writing of configuration.
 *
 * @return Tempcode Progress report / UI
 */
function step_5_write_config() : object
{
    $log = new Tempcode();

    $base_url = post_param_string('base_url', false, INPUT_FILTER_URL_GENERAL);
    if (substr($base_url, -1) == '/') {
        $base_url = substr($base_url, 0, strlen($base_url) - 1);
    }

    // Open up _config.php
    $config_file = '_config.php';
    $config_path = get_file_base() . '/' . $config_file;

    $config_contents = "<" . "?php\nglobal \$SITE_INFO;";

    $config_contents .= '
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

    // Write in inputted settings
    foreach ($_POST as $key => $val) {
        if (in_array($key, [
            'ftp_password',
            'ftp_password_confirm',
            'master_password_confirm',
            'cns_admin_password',
            'cns_admin_password_confirm',

            'clear_existing_forums_on_install',
            'send_error_emails_ocproducts',
            'board_path',
            'confirm',
            'email',
            'advertise_on',
            'forum',
            'max',
            'use_msn',
            'use_multi_db',

            'gae_live_db_site',
            'gae_live_db_site_host',
            'gae_live_db_site_user',
            'gae_live_db_site_password',

            'post_data',
        ])) {
            continue;
        }

        if (substr($key, 0, 7) == 'value__') {
            continue;
        }

        if (($key == 'admin_username') && (post_param_string('forum_type') != 'none')) {
            continue;
        }

        if ((GOOGLE_APPENGINE) && (($key == 'base_url') || (substr($key, 0, 9) == 'db_forums'))) {
            continue;
        }

        if ((strpos($key, '_forums') !== false) && ($val == $_POST[str_replace('_forums', '_site', $key)])) {
            continue;
        }

        if (($key == 'cns_table_prefix') && ($val == $_POST['table_prefix'])) {
            continue;
        }

        if ($key == 'master_password') {
            $val = password_hash($val, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if ($key == 'base_url') {
            $val = $base_url;
        }

        $_val = addslashes(trim($val));
        $config_contents .= '$SITE_INFO[\'' . $key . '\'] = \'' . $_val . "';\n";
    }

    // Derive a session cookie name based on the base_url, to stop conflicts between sites
    if (!isset($_POST['session_cookie'])) {
        $config_contents .= '$SITE_INFO[\'session_cookie\'] = \'cms_session__' . md5($base_url) . "';\n";
    }

    // Copy over current safe mode setting (if testing installer in safe mode)
    if (in_safe_mode()) {
        $config_contents .= '$SITE_INFO[\'safe_mode\'] = \'1\';' . "\n";
    }

    // On the live GAE, we need to switch in different settings to the local dev server
    if (GOOGLE_APPENGINE) {
        $gae_live_code = "
if (appengine_is_live()) {
    \$SITE_INFO['db_site'] = '" . addslashes(post_param_string('gae_live_db_site', false, INPUT_FILTER_POST_IDENTIFIER)) . "';
    \$SITE_INFO['db_site_host'] = '" . addslashes(post_param_string('gae_live_db_site_host', false, INPUT_FILTER_POST_IDENTIFIER)) . "';
    \$SITE_INFO['db_site_user'] = '" . addslashes(post_param_string('gae_live_db_site_user', false, INPUT_FILTER_POST_IDENTIFIER)) . "';
    \$SITE_INFO['db_site_password'] = '" . addslashes(post_param_string('gae_live_db_site_password', false, INPUT_FILTER_PASSWORD)) . "';
    \$SITE_INFO['custom_file_base'] = '" . addslashes('gs://' . post_param_string('gae_bucket_name', false, INPUT_FILTER_POST_IDENTIFIER)) . "';
    if ((strpos(\$_SERVER['HTTP_HOST'],'.appspot.com') !== false) || (!tacit_https())) {
        \$SITE_INFO['custom_base_url'] = '" . addslashes((tacit_https() ? 'https://' : 'http://') . post_param_string('gae_bucket_name', false, INPUT_FILTER_POST_IDENTIFIER) . '.storage.googleapis.com') . "';
    } else { // Assumes a storage.<domain> CNAME has been created
        \$SITE_INFO['custom_base_url'] = '" . addslashes((tacit_https() ? 'https://' : 'http://') . 'storage.') . "'.\$_SERVER['HTTP_HOST'];
    }
    \$SITE_INFO['no_extra_logs'] = '1';
    \$SITE_INFO['no_disk_sanity_checks'] = '1';
    \$SITE_INFO['no_installer_checks'] = '1';
    \$SITE_INFO['disable_smart_decaching'] = '1';
} else {
    \$SITE_INFO['custom_file_base'] = '" . addslashes(get_file_base() . '/data_custom/modules/google_appengine') . "';
    \$SITE_INFO['custom_base_url'] = '" . addslashes(get_base_url() . '/data_custom/modules/google_appengine') . "';

    // Or this for more accurate (but slower) testing (assumes app name matches bucket name)...
    //\$SITE_INFO['custom_file_base'] = 'gs://" . addslashes(post_param_string('gae_application')) . "';
    //\$SITE_INFO['custom_base_url'] = 'http://localhost:8080/data/modules/google_appengine/cloud_storage_proxy.php?';
}
\$SITE_INFO['use_persistent_cache'] = '1';
\$SITE_INFO['self_learning_cache'] = '1';
\$SITE_INFO['charset'] = 'utf-8';
";
        $config_contents .= $gae_live_code;
    }

    // ---

    // If a _config.php file already exists, copy it to _config.php.bak.timestamp
    $current_config = cms_file_get_contents_safe($config_path);
    if ($current_config) {
        $backup_config_file = $config_path . '.bak.' . time();
        $success_status = cms_file_put_contents_safe($backup_config_file, $current_config, FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS);
        if (!$success_status) {
            warn_exit(do_lang_tempcode('INSTALL_WRITE_ERROR', escape_html($backup_config_file)));
        }
    }

    $success_status = cms_file_put_contents_safe($config_path, $config_contents, FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS);
    if (!$success_status) {
        warn_exit(do_lang_tempcode('INSTALL_WRITE_ERROR', escape_html($config_file)));
    }

    require_once(get_file_base() . '/' . $config_file);

    global $FILE_ARRAY, $DIR_ARRAY;
    if ((@is_array($FILE_ARRAY)) && (!is_suexec_like())) {
        $conn = false;
        $domain = post_param_string('ftp_domain', false, INPUT_FILTER_POST_IDENTIFIER);
        $port = 21;
        if (strpos($domain, ':') !== false) {
            list($domain, $_port) = explode(':', $domain, 2);
            $port = intval($_port);
        }
        if (function_exists('ftp_ssl_connect')) {
            $conn = @ftp_ssl_connect($domain, $port);
        }
        $ssl = ($conn !== false);

        $username = post_param_string('ftp_username', false, INPUT_FILTER_POST_IDENTIFIER);
        $password = post_param_string('ftp_password', false, INPUT_FILTER_PASSWORD);

        if (($ssl) && (!@ftp_login($conn, $username, $password))) {
            $conn = false;
            $ssl = false;
        }
        if ($conn === false) {
            $conn = ftp_connect($domain, $port);
        }
        if (!$ssl) {
            ftp_login($conn, $username, $password);
        }
        $ftp_folder = post_param_string('ftp_folder', false, INPUT_FILTER_POST_IDENTIFIER);
        if (substr($ftp_folder, -1) != '/') {
            $ftp_folder .= '/';
        }
        ftp_chdir($conn, $ftp_folder);
        if (!is_suexec_like()) {
            @ftp_site($conn, 'CHMOD 666 _config.php'); // Can't be 644, because it might have been uploaded (thus not nobodies)
        }
        if (function_exists('ftp_close')) {
            ftp_close($conn);
        }
    }

    if (GOOGLE_APPENGINE) {
        require_code('files');

        // Customise .user.ini file
        $php_ini = cms_file_get_contents_safe(get_file_base() . '/.user.ini', FILE_READ_LOCK);
        $php_ini = str_replace('<application>', post_param_string('gae_application'), $php_ini);
        cms_file_put_contents_safe(get_file_base() . '/.user.ini', $php_ini, FILE_WRITE_FIX_PERMISSIONS);

        // Copy in default YAML files
        $dh = opendir(get_file_base() . '/data/modules/google_appengine');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -5) == '.yaml') {
                @unlink(get_file_base() . '/' . $f);
                copy(get_file_base() . '/data/modules/google_appengine/' . $f, get_file_base() . '/' . $f);
            }
        }

        // Customise app.yaml file
        $app_yaml = cms_file_get_contents_safe(get_file_base() . '/app.yaml', FILE_READ_LOCK);
        $app_yaml = preg_replace('#^application: .*$#m', 'application: ' . post_param_string('gae_application'), $app_yaml);
        cms_file_put_contents_safe(get_file_base() . '/app.yaml', $app_yaml | FILE_WRITE_FIX_PERMISSIONS);
    }

    if (is_suexec_like()) {
        fix_permissions(get_custom_file_base() . '/_config.php', 0600); // Don't allow other accounts to read
    }

    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '261a1eb80baed15cbbce1a684d4a354d', 'SOMETHING' => do_lang_tempcode('WROTE_CONFIGURATION')]));
    return $log;
}

/**
 * Fifth installation step: uninstallation of old install.
 *
 * @return Tempcode Progress report / UI
 */
function step_5_uninstall() : object
{
    $log = new Tempcode();

    require_code('config');
    require_code('database');
    require_all_core_cms_code();

    // Verify database
    $sitedb = new DatabaseConnector(trim(post_param_string('db_site')), trim(post_param_string('db_site_host')), trim(post_param_string('db_site_user')), trim(post_param_string('db_site_password')), trim(post_param_string('table_prefix')));
    if (post_param_string('forum_type') != 'none') {
        $forumdb = new DatabaseConnector(get_db_forums(), get_db_forums_host(), get_db_forums_user(), get_db_forums_password(), trim(array_key_exists('table_prefix', $_POST) ? $_POST['table_prefix'] : get_default_table_prefix()));
    }
    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'dae0677246aa2f1394b90c3739490ff7', 'SOMETHING' => do_lang_tempcode('DATABASE_VALID', 'Composr')]));

    // UNINSTALL STUFF

    // Delete directories
    require_code('files');
    deldir_contents('uploads/attachments', true);
    deldir_contents('uploads/attachments_thumbs', true);
    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'fcb01d21fc6041add6ba6a4a0bc79fb6', 'SOMETHING' => do_lang_tempcode('DELETED_ATTACHMENTS')]));

    // Delete database tables
    $tables = $sitedb->query_select('db_meta', ['DISTINCT m_table'], [], '', null, 0, true);
    if ($tables !== null) {
        foreach ($tables as $i => $table) {
            // These tables must be dropped last
            if (($table['m_table'] == 'db_meta') || ($table['m_table'] == 'db_meta_indices')) {
                continue;
            }

            if (strpos($table['m_table'], 'f_') === 0) {
                $forumdb->drop_table_if_exists($table['m_table']);
            } else {
                $sitedb->drop_table_if_exists($table['m_table']);
            }
        }
        $sitedb->drop_table_if_exists('db_meta');
        $sitedb->drop_table_if_exists('db_meta_indices');
        $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'ci3u3uocbociu3g98fcucg3ovc', 'SOMETHING' => do_lang_tempcode('DROPPED_TABLES')]));
    }

    unset($sitedb);
    unset($forumdb);

    return $log;
}

/**
 * Fifth installation step: core tables.
 *
 * @return Tempcode Progress report / UI
 */
function step_5_core() : object
{
    $tables = [
        'db_meta',
        'db_meta_indices',
    ];
    $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    $GLOBALS['SITE_DB']->create_table('db_meta', [
        'm_table' => '*ID_TEXT',
        'm_name' => '*ID_TEXT',
        'm_type' => 'ID_TEXT',
    ]);
    $GLOBALS['SITE_DB']->create_table('db_meta_indices', [
        'i_table' => '*ID_TEXT',
        'i_name' => '*ID_TEXT',
        'i_fields' => 'LONG_TEXT',
    ]);

    $tables = [
        'translate',
        'values',
        'config',
        'group_privileges',
        'privilege_list',
        'attachments',
        'attachment_refs',
    ];
    $GLOBALS['SITE_DB']->drop_table_if_exists($tables);

    $GLOBALS['SITE_DB']->create_index('db_meta', 'findtransfields', ['m_type']);

    $fields = [
        'id' => '*AUTO',
        'language' => '*LANGUAGE_NAME',
        'importance_level' => 'SHORT_INTEGER',
        'text_original' => 'LONG_TEXT',
        'text_parsed' => 'LONG_TEXT',
        'broken' => 'BINARY',
        'source_user' => 'MEMBER',
    ];
    if ($GLOBALS['SITE_DB']->driver->full_text_needs_single_key()) { // FUDGE: Full-text search requires a single key
        $fields['_id'] = '*AUTO';
        $fields['id'] = 'AUTO_LINK';
        $fields['language'] = 'LANGUAGE_NAME';
    }
    $GLOBALS['SITE_DB']->create_table('translate', $fields);
    $GLOBALS['SITE_DB']->create_index('translate', '#tsearch', ['text_original'], null, true);
    $GLOBALS['SITE_DB']->create_index('translate', 'importance_level', ['importance_level']);
    if (strpos(get_db_type(), 'mysql') !== false) {
        // Only MySQL has these prefix indexes https://compo.sr/tracker/view.php?id=4909
        $GLOBALS['SITE_DB']->create_index('translate', 'equiv_lang', ['text_original(4)']); // Make finding particular strings easier, but this is not a common operation
        $GLOBALS['SITE_DB']->create_index('translate', 'decache', ['text_parsed(2)']); // Makes Comcode cache emptying a little faster, but this is not a common operation
    }

    $GLOBALS['SITE_DB']->create_table('values', [
        'the_name' => '*ID_TEXT',
        'the_value' => 'SHORT_TEXT',
        'date_and_time' => 'TIME',
    ]);
    $GLOBALS['SITE_DB']->create_index('values', 'date_and_time', ['date_and_time']);

    $GLOBALS['SITE_DB']->create_table('config', [
        'c_name' => '*ID_TEXT',
        'c_set' => 'BINARY',
        'c_value' => 'LONG_TEXT',
        'c_value_trans' => '?LONG_TRANS', // If it's a translatable/Comcode one, we store the language ID in here (or just a string if we don't have multi-lang-content enabled)
        'c_needs_dereference' => 'BINARY',
    ]);
    $email = post_param_string('email', '', INPUT_FILTER_POST_IDENTIFIER | INPUT_FILTER_EMAIL_ADDRESS);
    if ($email != '') {
        $GLOBALS['SITE_DB']->query_insert('config', [
            'c_name' => 'staff_address',
            'c_set' => 1,
            'c_value' => $email,
            'c_value_trans' => multi_lang_content() ? null : '',
            'c_needs_dereference' => 0,
        ]);
        $GLOBALS['SITE_DB']->query_insert('config', [
            'c_name' => 'website_email',
            'c_set' => 1,
            'c_value' => $email,
            'c_value_trans' => multi_lang_content() ? null : '',
            'c_needs_dereference' => 0,
        ]);
    }

    // Privileges
    $GLOBALS['SITE_DB']->create_table('group_privileges', [
        'group_id' => '*INTEGER',
        'privilege' => '*ID_TEXT',
        'the_page' => '*ID_TEXT',
        'module_the_name' => '*ID_TEXT', // The permission module (NOT the page module which is the_page)
        'category_name' => '*ID_TEXT',
        'the_value' => 'BINARY',
    ], false, false, true);
    $GLOBALS['SITE_DB']->create_index('group_privileges', 'group_id', ['group_id']);

    $GLOBALS['SITE_DB']->create_table('privilege_list', [ // Why does this table exist? It could be done cleanly in hooks (which are easier to version) like config is, but when we add a privilege we do need to carefully define who gets it (as an immediate-op with potential complex code) -- it is cleaner to just handle definition in same place as that code).
        'p_section' => 'ID_TEXT',
        'the_name' => '*ID_TEXT',
        'the_default' => '*BINARY',
    ]);

    $GLOBALS['SITE_DB']->create_table('attachments', [
        'id' => '*AUTO',
        'a_member_id' => 'MEMBER',
        'a_file_size' => '?INTEGER', // null means non-local. Doesn't count to quota
        'a_url' => 'SHORT_TEXT',
        'a_description' => 'SHORT_TEXT',
        'a_thumb_url' => 'SHORT_TEXT',
        'a_original_filename' => 'SHORT_TEXT',
        'a_num_downloads' => 'INTEGER',
        'a_last_downloaded_time' => '?INTEGER',
        'a_add_time' => 'INTEGER',
    ]);
    $GLOBALS['SITE_DB']->create_index('attachments', 'ownedattachments', ['a_member_id']);
    $GLOBALS['SITE_DB']->create_index('attachments', 'attachmentlimitcheck', ['a_add_time']);

    $GLOBALS['SITE_DB']->create_table('attachment_refs', [
        'id' => '*AUTO',
        'r_referer_type' => 'ID_TEXT',
        'r_referer_id' => 'ID_TEXT',
        'a_id' => 'AUTO_LINK',
    ]);

    return do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'c6b6d92c670b7f1b223798ace54102f9', 'SOMETHING' => do_lang_tempcode('PRIMARY_CORE_INSTALLED')]);
}

/**
 * Fifth installation step: more core tables.
 *
 * @return Tempcode Progress report / UI
 */
function step_5_core_2() : object
{
    global $INSTALL_LANG;

    $GLOBALS['SITE_DB']->drop_table_if_exists('zones');
    $GLOBALS['SITE_DB']->create_table('zones', [
        'zone_name' => '*ID_TEXT',
        'zone_title' => 'SHORT_TRANS',
        'zone_default_page' => 'ID_TEXT',
        'zone_header_text' => 'SHORT_TRANS',
        'zone_theme' => 'ID_TEXT',
        'zone_require_session' => 'BINARY',
    ]);

    // Create default zones
    require_lang('zones');
    $trans1 = insert_lang('zone_header_text', '', 1, null, false, null, $INSTALL_LANG);
    $h1 = insert_lang('zone_title', do_lang('_WELCOME'), 1, null, false, null, $INSTALL_LANG);
    $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => '', 'zone_default_page' => DEFAULT_ZONE_PAGE_NAME, 'zone_theme' => '-1', 'zone_require_session' => 0] + $trans1 + $h1);
    $trans2 = insert_lang('zone_header_text', do_lang('HEADER_TEXT_ADMINZONE'), 1, null, false, null, $INSTALL_LANG);
    $h2 = insert_lang('zone_title', do_lang('ADMIN_ZONE'), 1, null, false, null, $INSTALL_LANG);
    $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'adminzone', 'zone_default_page' => DEFAULT_ZONE_PAGE_NAME, 'zone_theme' => 'admin', 'zone_require_session' => 1] + $trans2 + $h2);
    $trans4 = insert_lang('zone_header_text', '', 1, null, false, null, $INSTALL_LANG);
    $h4 = insert_lang('zone_title', do_lang('SITE'), 1, null, false, null, $INSTALL_LANG);
    $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'site', 'zone_default_page' => DEFAULT_ZONE_PAGE_NAME, 'zone_theme' => '-1', 'zone_require_session' => 0] + $trans4 + $h4);
    $trans5 = insert_lang('zone_header_text', do_lang('CMS'), 1, null, false, null, $INSTALL_LANG);
    $h5 = insert_lang('zone_title', do_lang('CMS'), 1, null, false, null, $INSTALL_LANG);
    $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'cms', 'zone_default_page' => 'cms', 'zone_theme' => 'admin', 'zone_require_session' => 1] + $trans5 + $h5);
    // FUDGE: Installing from Git...
    if (file_exists(get_file_base() . '/docs')) {
        $trans6 = insert_lang('zone_header_text', '', 1, null, false, null, $INSTALL_LANG);
        $h6 = insert_lang('zone_title', do_lang('TUTORIALS'), 1, null, false, null, $INSTALL_LANG);
        $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'docs', 'zone_default_page' => 'tutorials', 'zone_theme' => '-1', 'zone_require_session' => 0] + $trans6 + $h6);
    }
    if (file_exists(get_file_base() . '/buildr')) {
        $trans6 = insert_lang('zone_header_text', '', 1, null, false, null, $INSTALL_LANG);
        $h6 = insert_lang('zone_title', 'buildr', 1, null, false, null, $INSTALL_LANG);
        $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'buildr', 'zone_default_page' => 'home', 'zone_theme' => '-1', 'zone_require_session' => 0] + $trans6 + $h6);
    }

    // Forums
    $forum_type = post_param_string('forum_type');
    if ($forum_type == 'cns') {
        $trans6 = insert_lang('zone_header_text', do_lang('FORUM'), 1, null, false, null, $INSTALL_LANG);
        $h6 = insert_lang('zone_title', do_lang('SECTION_FORUMS'), 1, null, false, null, $INSTALL_LANG);
        $GLOBALS['SITE_DB']->query_insert('zones', ['zone_name' => 'forum', 'zone_default_page' => 'forumview', 'zone_theme' => '-1', 'zone_require_session' => 0] + $trans6 + $h6);
    }

    $GLOBALS['SITE_DB']->drop_table_if_exists('modules');
    $GLOBALS['SITE_DB']->create_table('modules', [
        'module_the_name' => '*ID_TEXT',
        'module_author' => 'ID_TEXT',
        'module_organisation' => 'ID_TEXT',
        'module_hacked_by' => 'ID_TEXT',
        'module_hack_version' => '?INTEGER',
        'module_version' => 'INTEGER',
    ]);

    $GLOBALS['SITE_DB']->drop_table_if_exists('blocks');
    $GLOBALS['SITE_DB']->create_table('blocks', [
        'block_name' => '*ID_TEXT',
        'block_author' => 'ID_TEXT',
        'block_organisation' => 'ID_TEXT',
        'block_hacked_by' => 'ID_TEXT',
        'block_hack_version' => '?INTEGER',
        'block_version' => 'INTEGER',
    ]);

    $GLOBALS['SITE_DB']->drop_table_if_exists('sessions');
    $GLOBALS['SITE_DB']->create_table('sessions', [
        'the_session' => '*ID_TEXT',
        'last_activity' => 'TIME',
        'member_id' => 'MEMBER',
        'ip' => 'IP',
        'session_confirmed' => 'BINARY',
        'session_invisible' => 'BINARY',
        'cache_username' => 'ID_TEXT',
        'the_zone' => 'ID_TEXT',
        'the_page' => 'ID_TEXT',
        'the_type' => 'ID_TEXT',
        'the_id' => 'ID_TEXT',
        'the_title' => 'SHORT_TEXT',
    ]);
    $GLOBALS['SITE_DB']->create_index('sessions', 'delete_old', ['last_activity']);
    $GLOBALS['SITE_DB']->create_index('sessions', 'member_id', ['member_id']);
    $GLOBALS['SITE_DB']->create_index('sessions', 'userat', ['the_zone', 'the_page', 'the_id']);

    // What usergroups may view this category
    $GLOBALS['SITE_DB']->drop_table_if_exists('group_category_access');
    $GLOBALS['SITE_DB']->create_table('group_category_access', [
        'module_the_name' => '*ID_TEXT', // The permission module (NOT the page module)
        'category_name' => '*ID_TEXT',
        'group_id' => '*GROUP',
    ]);

    $GLOBALS['SITE_DB']->drop_table_if_exists('seo_meta');
    $GLOBALS['SITE_DB']->create_table('seo_meta', [
        'id' => '*AUTO',
        'meta_for_type' => 'ID_TEXT',
        'meta_for_id' => 'ID_TEXT',
        'meta_description' => 'LONG_TRANS',
    ]);
    $GLOBALS['SITE_DB']->create_index('seo_meta', 'alt_key', ['meta_for_type', 'meta_for_id']);
    $GLOBALS['SITE_DB']->create_index('seo_meta', 'ftjoin_dmeta_description', ['meta_description']);

    $GLOBALS['SITE_DB']->drop_table_if_exists('seo_meta_keywords');
    $GLOBALS['SITE_DB']->create_table('seo_meta_keywords', [
        'id' => '*AUTO',
        'sort_order' => 'INTEGER',
        'meta_for_type' => 'ID_TEXT',
        'meta_for_id' => 'ID_TEXT',
        'meta_keyword' => 'SHORT_TRANS',
    ]);
    $GLOBALS['SITE_DB']->create_index('seo_meta_keywords', 'keywords_alt_key', ['meta_for_type', 'meta_for_id']);
    $GLOBALS['SITE_DB']->create_index('seo_meta_keywords', 'ftjoin_dmeta_keywords', ['meta_keyword']);

    // Set POSTed values where necessary
    foreach ($_POST as $_key => $value) {
        if (substr($_key, 0, 7) == 'value__') {
            $key = substr($_key, 7);
            set_value($key, $value);
            unset($_POST[$_key]);
        }
    }

    return do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '685ebf53cf9fc3f728168fed2f01a5a1', 'SOMETHING' => do_lang_tempcode('SECONDARY_CORE_INSTALLED')]);
}

/**
 * Sixth installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_6() : object
{
    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    $url = prepare_installer_url('install.php?step=7');

    $log = new Tempcode();

    $config_file = '_config.php';
    require_once(get_file_base() . '/' . $config_file);
    require_code('database');
    require_code('config');
    require_all_core_cms_code();
    include_cns();

    // Check database credentials / load DB connection
    confirm_db_credentials();

    require_code('cns_install');
    install_cns();
    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'f268a7e03ca5b06ed9f62b29b1357d25', 'SOMETHING' => do_lang_tempcode('INSTALLED_CNS')]));

    return do_template('INSTALLER_STEP_LOG', [
        '_GUID' => '450f62a4664c67b6780228781218a8f2',
        'PREVIOUS_STEP' => '5',
        'CURRENT_STEP' => '6',
        'URL' => $url,
        'LOG' => $log,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Parts common to any modular installation step.
 */
function big_installation_common()
{
    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    if (empty($_POST)) {
        exit(do_lang('INST_POST_ERROR'));
    }

    $config_file = '_config.php';
    require_once(get_file_base() . '/' . $config_file);

    require_code('database');

    if (!isset($GLOBALS['SITE_DB'])) {
        fatal_exit('Could not initialise database connection');
    }

    // Check database credentials / load DB connection
    confirm_db_credentials();

    $forum_type = get_forum_type();
    require_code('forum/' . $forum_type);
    $GLOBALS['FORUM_DRIVER'] = object_factory('Forum_driver_' . filter_naughty_harsh($forum_type));
    if ($forum_type != 'none') {
        $GLOBALS['FORUM_DRIVER']->db = new DatabaseConnector(get_db_forums(), get_db_forums_host(), get_db_forums_user(), get_db_forums_password(), $GLOBALS['FORUM_DRIVER']->get_drivered_table_prefix());
    }
    $GLOBALS['FORUM_DRIVER']->MEMBER_ROWS_CACHED = [];
    $GLOBALS['FORUM_DB'] = &$GLOBALS['FORUM_DRIVER']->db;

    if (method_exists($GLOBALS['FORUM_DRIVER'], 'check_db')) {
        if (!$GLOBALS['FORUM_DRIVER']->check_db()) {
            warn_exit(do_lang_tempcode('INVALID_FORUM_DATABASE'));
        }
    }

    require_code('config');
    require_code('zones2');
    require_all_core_cms_code();
}

/**
 * Seventh installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_7() : object
{
    big_installation_common();

    $log = [];

    if (method_exists($GLOBALS['FORUM_DRIVER'], 'forum_install_as_needed')) {
        $GLOBALS['FORUM_DRIVER']->forum_install_as_needed();
    }

    $time_start = microtime(true);

    // We must install these modules first
    foreach (['admin_version', 'admin_permissions'] as $module) {
        $time_before = microtime(true);
        if (reinstall_module('adminzone', $module)) {
            $time_after = microtime(true);
            $time = $time_after - $time_before;
            if (get_param_integer('keep_show_timings', 0) == 1) {
                $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '1aafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Module installation of ' . escape_html($module) . ' took ' . float_format($time) . ' seconds')])];
            } else {
                $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'da46e6eb9069c8f700636ab61f76f895', 'SOMETHING' => do_lang_tempcode('INSTALLED_MODULE', escape_html($module))])];
            }
        }
    }

    $modules = find_all_modules('adminzone');
    foreach ($modules as $module => $type) {
        if (($module != 'admin_version') && ($module != 'admin_permissions')) {
            send_http_output_ping();

            $time_before = microtime(true);
            if (reinstall_module('adminzone', $module)) {
                $time_after = microtime(true);
                $time = $time_after - $time_before;
                if (get_param_integer('keep_show_timings', 0) == 1) {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '1fafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Module installation of ' . escape_html($module) . ' took ' . float_format($time) . ' seconds')])];
                } else {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '9fafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => do_lang_tempcode('INSTALLED_MODULE', escape_html($module))])];
                }
            }
        }
    }

    set_option('send_error_emails_ocproducts', strval(post_param_integer('send_error_emails_ocproducts', 0)));

    require_code('addons2');
    $addons = find_all_hooks('systems', 'addon_registry');
    foreach ($addons as $addon_name => $place) {
        send_http_output_ping();

        //if ($place == 'sources_custom') continue;  Now we are actually installing custom addons too

        $time_before = microtime(true);
        reinstall_addon_soft($addon_name);
        $time_after = microtime(true);
        $time = $time_after - $time_before;
        if (get_param_integer('keep_show_timings', 0) == 1) {
            $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '2fafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Addon installation of ' . escape_html($addon_name) . ' took ' . float_format($time) . ' seconds')])];
        } else {
            $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '9fafb3dd014d589fcc057bba54fc4ag3', 'SOMETHING' => do_lang_tempcode('INSTALLED_ADDON', escape_html($addon_name))])];
        }
    }

    $time_end = microtime(true);
    if (get_param_integer('keep_show_timings', 0) == 1) {
        $time = $time_end - $time_start;
        $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '2fafb3dd024d589fcc057bba54fc4ab4', 'SOMETHING' => protect_from_escaping('TOTAL TIME: ' . float_format($time) . ' seconds')])];
    }

    sort_maps_by($log, '!time');
    $_log = new Tempcode();
    foreach ($log as $l) {
        $_log->attach($l['out']);
    }

    $url = prepare_installer_url('install.php?step=8');

    return do_template('INSTALLER_STEP_LOG', [
        '_GUID' => 'c016b2a364d20cf711af7e14c60a7921',
        'PREVIOUS_STEP' => '6',
        'CURRENT_STEP' => '7',
        'URL' => $url,
        'LOG' => $_log,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Eighth installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_8() : object
{
    big_installation_common();

    $log = [];

    $time_start = microtime(true);

    foreach ((get_option('single_public_zone') == '1') ? [''] : ['', 'site'] as $zone) {
        $modules = find_all_modules($zone);
        foreach ($modules as $module => $type) {
            send_http_output_ping();

            $time_before = microtime(true);

            if (reinstall_module($zone, $module)) {
                $time_after = microtime(true);
                $time = $time_after - $time_before;
                if (get_param_integer('keep_show_timings', 0) == 1) {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '3fafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Module installation of ' . escape_html($module) . ' took ' . float_format($time) . ' seconds')])];
                } else {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '9b3c23369e8ca719256ae44b3d42fd4c', 'SOMETHING' => do_lang_tempcode('INSTALLED_MODULE', escape_html($module))])];
                }
            }
        }
    }

    $time_end = microtime(true);
    if (get_param_integer('keep_show_timings', 0) == 1) {
        $time = $time_end - $time_start;
        $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '2fafb3dd024d589fcc057bba54fc4ab5', 'SOMETHING' => protect_from_escaping('TOTAL TIME: ' . float_format($time) . ' seconds')])];
    }

    sort_maps_by($log, '!time');
    $_log = new Tempcode();
    foreach ($log as $l) {
        $_log->attach($l['out']);
    }

    $url = prepare_installer_url('install.php?step=9');

    return do_template('INSTALLER_STEP_LOG', [
        '_GUID' => '27fad5aa7f96d26a51e6afb6b7e5c7b1',
        'PREVIOUS_STEP' => '7',
        'CURRENT_STEP' => '8',
        'URL' => $url,
        'LOG' => $_log,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Ninth installation step.
 *
 * @return Tempcode Progress report / UI
 */
function step_9() : object
{
    big_installation_common();

    $log = [];

    $time_start = microtime(true);

    foreach (['forum', 'cms'] as $zone) {
        if (!is_file(get_file_base() . '/' . $zone . '/index.php')) {
            continue;
        }

        $modules = find_all_modules($zone);
        foreach ($modules as $module => $type) {
            send_http_output_ping();

            $time_before = microtime(true);

            if (reinstall_module($zone, $module)) {
                $time_after = microtime(true);
                $time = $time_after - $time_before;
                if (get_param_integer('keep_show_timings', 0) == 1) {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '4fafb3dd014d589fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Module installation of ' . escape_html($module) . ' took ' . float_format($time) . ' seconds')])];
                } else {
                    $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'c1d95b9713006acb491b44ff6c79099c', 'SOMETHING' => do_lang_tempcode('INSTALLED_MODULE', escape_html($module))])];
                }
            }
        }
    }

    $blocks = find_all_blocks();
    foreach ($blocks as $block => $type) {
        send_http_output_ping();

        $time_before = microtime(true);

        if (reinstall_block($block)) {
            $time_after = microtime(true);
            $time = $time_after - $time_before;
            if (get_param_integer('keep_show_timings', 0) == 1) {
                $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'da7e6b0948b6aa5702123808a4ca894f', 'SOMETHING' => protect_from_escaping('Block installation of ' . escape_html($block) . ' took ' . float_format($time) . ' seconds')])];
            } else {
                $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'dc9f833239d501f77729778b5c6681b6', 'SOMETHING' => do_lang_tempcode('INSTALLED_BLOCK', escape_html($block))])];
            }
        }
    }

    $time_end = microtime(true);
    if (get_param_integer('keep_show_timings', 0) == 1) {
        $time = $time_end - $time_start;
        $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '2fafb3dd024d589fcc057bba54fc4ab6', 'SOMETHING' => protect_from_escaping('TOTAL TIME: ' . float_format($time) . ' seconds')])];
    }

    sort_maps_by($log, '!time');
    $_log = new Tempcode();
    foreach ($log as $l) {
        $_log->attach($l['out']);
    }

    $url = prepare_installer_url('install.php?step=10');

    return do_template('INSTALLER_STEP_LOG', [
        '_GUID' => 'b20121b8f4f84dd8e625e3b821c753b3',
        'PREVIOUS_STEP' => '8',
        'CURRENT_STEP' => '9',
        'URL' => $url,
        'LOG' => $_log,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Tenth installation step: wrapper and special interface.
 *
 * @return Tempcode Progress report / UI
 */
function step_10() : object
{
    big_installation_common();

    $log = new Tempcode();
    $log->attach(step_10_populate_database());
    $log->attach(step_10_forum_stuff());

    if (addon_installed('robots_txt')) {
        require_code('robots_txt');
        $robots_txt_msg = '';
        create_robots_txt(null, $robots_txt_msg);
        if ($robots_txt_msg !== null) {
            $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'eddbb0cbc46520fe767e6292465751a1', 'SOMETHING' => protect_from_escaping($robots_txt_msg)]));
        }
    }

    $final = do_lang_tempcode('FINAL_INSTRUCTIONS_A');
    global $FILE_ARRAY;
    if ((!@is_array($FILE_ARRAY)) && (!is_suexec_like())) {
        $final->attach(' ');
        $final->attach(do_lang_tempcode('FINAL_INSTRUCTIONS_A_SUP'));
    }

    // Empty persistent cache
    $path = get_custom_file_base() . '/caches/persistent/';
    $_dir = @opendir($path);
    if ($_dir !== false) {
        while (false !== ($file = readdir($_dir))) {
            if (substr($file, -4) == '.gcd') {
                @unlink($path . $file);
            }
        }
        closedir($_dir);
    }

    require_code('caches3');
    erase_cached_templates();

    $url = prepare_installer_url('install.php?step=11&type=finish');

    return do_template('INSTALLER_STEP_10', [
        '_GUID' => '0e50bc1b9934c32fb62fb865a3971a9b',
        'PREVIOUS_STEP' => '9',
        'CURRENT_STEP' => '10',
        'FINAL' => $final,
        'LOG' => $log,
        'URL' => $url,
        'HIDDEN' => build_keep_post_fields(),
    ]);
}

/**
 * Tenth installation step: main.
 *
 * @return Tempcode Progress report / UI
 */
function step_10_populate_database() : object
{
    $log = [];

    // Make sure that any menu items here come after what we have already
    global $ADD_MENU_COUNTER;
    $ADD_MENU_COUNTER = 100;

    $zones = find_all_zones();
    // FUDGE: Installing from Git
    if (file_exists(get_file_base() . '/docs')) {
        $zones[] = 'docs';
    }
    if (file_exists(get_file_base() . '/buildr')) {
        $zones[] = 'buildr';
    }
    foreach (array_unique($zones)/*in case find_all_zones did find docs*/ as $zone) {
        if (($zone != 'site') && ($zone != 'adminzone') && ($zone != 'forum') && ($zone != 'cms') && (($zone != '') || (get_option('single_public_zone') == '0'))) {
            $modules = find_all_modules($zone);
            foreach (array_keys($modules) as $module) {
                send_http_output_ping();

                $time_before = microtime(true);
                if (reinstall_module($zone, $module)) {
                    $time_after = microtime(true);
                    $time = $time_after - $time_before;
                    if (get_param_integer('keep_show_timings', 0) == 1) {
                        $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '4fafb3dd0146689fcc057bba54fc4ab3', 'SOMETHING' => protect_from_escaping('Module installation of ' . escape_html($module) . ' took ' . float_format($time) . ' seconds')])];
                    } else {
                        $log[] = ['time' => $time, 'out' => do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '25eb1c88fe122ec5a817f334d5f6bc5e', 'SOMETHING' => do_lang_tempcode('INSTALLED_MODULE', escape_html($module))])];
                    }
                }
            }
        }
    }
    //$log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '6a160da6fd9031e90b37a40aea149137', 'SOMETHING' => do_lang('TABLES_CREATED', 'Composr'])));

    sort_maps_by($log, '!time');
    $_log = new Tempcode();
    foreach ($log as $l) {
        $_log->attach($l['out']);
    }

    return $_log;
}

/**
 * Tenth installation step: forum part.
 *
 * @return Tempcode Progress report / UI
 */
function step_10_forum_stuff() : object
{
    $log = new Tempcode();

    $forum_type = post_param_string('forum_type');

    if ($forum_type != 'none') {
        require_code('cpf_install');
        install_address_fields();
        install_name_fields();
        install_mobile_phone_field();

        $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => 'efdbb0cbc46520fe767c6292465751a1', 'SOMETHING' => do_lang_tempcode('CREATED_CUSTOM_PROFILE_FIELDS')]));
    }

    $log->attach(do_template('INSTALLER_DONE_SOMETHING', ['_GUID' => '53facf1a7e666433d663fee2974cd02b', 'SOMETHING' => do_lang_tempcode('INSTALL_COMPLETE')]));

    return $log;
}

/**
 * Eleventh installation step: directs to proper place inside Composr, with a pre-set admin login.
 *
 * @return Tempcode Redirect screen
 */
function step_11() : object
{
    big_installation_common();
    reload_lang_fields();

    // Create admin login
    require_code('users_active_actions');
    require_code('users_inactive_occasionals');
    create_session(get_first_admin_user(), 1);

    $next = post_param_string('next');
    switch ($next) {
        case 'setupwizard':
            $redirect_url = get_base_url() . '/adminzone/index.php?page=admin_setupwizard&type=browse&came_from_installer=1';
            if (get_param_integer('keep_safe_mode', 0) == 1) {
                $redirect_url .= '&keep_safe_mode=1';
            }
            break;

        case 'testcontent':
            $redirect_url = get_base_url() . '/adminzone/index.php?page=admin_setupwizard&type=install_test_content&came_from_installer=1';
            if (get_param_integer('keep_safe_mode', 0) == 1) {
                $redirect_url .= '&keep_safe_mode=1';
            }
            break;

        case 'blank':
        default:
            $redirect_url = get_base_url() . '/index.php?came_from_installer=1';
            if (get_param_integer('keep_safe_mode', 0) == 1) {
                $redirect_url .= '&keep_safe_mode=1';
            }
            break;
    }

    // Redirect
    require_code('templates_redirect_screen');
    return redirect_screen(get_screen_title('Composr', false), $redirect_url);
}

/**
 * This function is a very important one when coding. It allows you to include a source code file (from root/sources/ or root/sources_custom/) through the proper channels.
 * You should remember this function, and not substitute anything else for it, as that will likely make your code unstable.
 * It is key to source code modularity in Composr.
 *
 * @param  string $codename The codename for the source module to load
 */
function require_code(string $codename)
{
    if ($codename == 'mail' || $codename == 'failure') {
        return;
    }

    global $FILE_ARRAY, $REQUIRED_BEFORE;
    if (array_key_exists($codename, $REQUIRED_BEFORE)) {
        return;
    }

    if (!array_key_exists('type', $_GET)) {
        $prior = memory_get_usage();
        //echo '<' . '!-- Memory: ' . number_format($prior) . ' --' . '>' . "\n"; Can break JS validity if we inject this
        //echo '<' . '!-- Loading code file: ' . $codename . ' --' . '>' . "\n";
        //cms_flush_safe();
    }

    global $FILE_BASE;

    $path = $FILE_BASE . ((strpos($codename, '.php') === false) ? ('/sources/' . $codename . '.php') : ('/' . preg_replace('#(sources|modules|minimodules)_custom#', '${1}', $codename)));
    if (!file_exists($path)) {
        $path = $FILE_BASE . ((strpos($codename, '.php') === false) ? ('/sources_custom/' . $codename . '.php') : ('/' . $codename));
    }

    $REQUIRED_BEFORE[$codename] = true;
    if ((@is_array($FILE_ARRAY)) && ((!isset($_GET['keep_quick_hybrid'])) || (!file_exists($path)))) {
        $file = file_array_get('sources/' . $codename . '.php');
        $file = str_replace('<' . '?php', '', $file);
        $file = str_replace('?' . '>', '', $file);
        if (function_exists('cms_eval')) {
            cms_eval($file, $path);
        } else {
            eval($file);
        }

        if (function_exists('init__' . str_replace('/', '__', $codename))) {
            call_user_func('init__' . str_replace('/', '__', $codename));
        }
    } else {
        if (!file_exists($path)) {
            exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr installer startup error</h1><p>A required installation file, sources/' . $codename . '.php, could not be located. This is almost always due to an incomplete upload of the Composr manual installation package, so please check all files are uploaded correctly.</p><p>Only once all Composr files are in place can the installer can function. Please note that we have a quick installer package which requires uploading only two files, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
        }

        require_once($path);
        if (function_exists('init__' . str_replace('/', '__', $codename))) {
            call_user_func('init__' . str_replace('/', '__', $codename));
        }
    }
    /*if (!array_key_exists('type', $_GET))   Memory usage debugging. Not safe, as can mess up Tempcode generation (mixed echos) {
        echo '<' . '!-- Memory diff for ' . $codename . ' was: ' . @number_format(memory_get_usage() - $prior) . ' --' . '>' . "\n";
    }*/
}

/**
 * Make an object of the given class.
 *
 * @param  string $class The class name
 * @param  boolean $failure_ok Whether to return null if there is no such class
 * @param  array $parameters Array of parameters
 * @return ?object The object (null: could not create)
 */
function object_factory(string $class, bool $failure_ok = false, array $parameters = []) : ?object
{
    return new $class(...$parameters);
}

/**
 * Sets the value of a configuration option, if the PHP environment allows it.
 *
 * @param  string $var Config option
 * @param  string $value New value of option
 * @return ~string Old value of option (false: error)
 */
function cms_ini_set(string $var, string $value)
{
    if ($var == 'memory_limit' && PHP_DEBUG == 1) {
        $value = '-1';
    }

    $to_block = ['disable_functions', 'suhosin.executor.func.blacklist', 'suhosin.executor.include.blacklist', 'suhosin.executor.eval.blacklist'];
    $_blocked = [];
    foreach ($to_block as $func) {
        $ini_val = ini_get($func);
        if ($ini_val !== false) {
            $_blocked[] = $ini_val;
        }
    }
    $blocked = implode(',', $_blocked);
    if (@preg_match('#(\s|,|^)ini_set(\s|$|,)#i', $blocked) != 0) {
        return false;
    }

    return @ini_set($var, $value);
}

/**
 * Flush but don't break Brotli compression.
 */
function cms_flush_safe()
{
    if ((ini_get('output_handler') == '') && (ini_get('brotli.output_compression') !== 'On')) {
        flush();
    }
}

/**
 * Handle GET URLs requesting embedded media files.
 */
function handle_self_referencing_embedment()
{
    // If this is self-referring to CSS or logo
    if (array_key_exists('type', $_GET)) {
        $type = $_GET['type'];

        switch ($type) {
            case 'finish':
                return;

            case 'test_blank_result':
                exit();
                break;

            case 'ajax_ftp_details':
                header('Content-Type: text/plain; charset=' . get_charset());

                if (!function_exists('ftp_connect')) {
                    echo do_lang('NO_PHP_FTP');
                    exit();
                }
                $conn = false;
                $domain = post_param_string('ftp_domain', false, INPUT_FILTER_POST_IDENTIFIER);
                $port = 21;
                if (strpos($domain, ':') !== false) {
                    list($domain, $_port) = explode(':', $domain, 2);
                    $port = intval($_port);
                }
                if (function_exists('ftp_ssl_connect')) {
                    $conn = @ftp_ssl_connect($domain, $port);
                }
                $ssl = ($conn !== false);
                $username = post_param_string('ftp_username', false, INPUT_FILTER_POST_IDENTIFIER);
                $password = post_param_string('ftp_password', false, INPUT_FILTER_PASSWORD);
                $ssl = ($conn !== false);
                if (($ssl) && (!@ftp_login($conn, $username, $password))) {
                    $conn = false;
                    $ssl = false;
                }
                if ($conn === false) {
                    $conn = ftp_connect($domain, $port);
                }
                if ($conn === false) {
                    echo do_lang('NO_FTP_CONNECT');
                    exit();
                }
                if ((!$ssl) && (!@ftp_login($conn, $username, $password))) {
                    echo do_lang('NO_FTP_LOGIN', cms_error_get_last());
                    ftp_close($conn);
                    exit();
                }
                $ftp_folder = post_param_string('ftp_folder', false, INPUT_FILTER_POST_IDENTIFIER);
                if (substr($ftp_folder, -1) != '/') {
                    $ftp_folder .= '/';
                }
                if (!@ftp_chdir($conn, $ftp_folder)) {
                    echo do_lang('NO_FTP_DIR', cms_error_get_last(), '1');
                    ftp_close($conn);
                    exit();
                }
                $files = @ftp_nlist($conn, '.');
                if ($files === false) { // :(. Weird bug on some systems
                    $files = [];
                    if (@ftp_rename($conn, 'install.php', 'install.php')) {
                        $files = ['install.php', 'data.cms'];
                    }
                }
                if (!in_array('install.php', $files)) {
                    echo do_lang('NO_FTP_DIR', cms_error_get_last(), '2');
                }
                ftp_close($conn);
                exit();
                break;

            case 'ajax_db_details':
                header('Content-Type: text/plain; charset=' . get_charset());
                global $SITE_INFO;
                if (!isset($SITE_INFO)) {
                    $SITE_INFO = [];
                }
                $SITE_INFO['db_type'] = post_param_string('db_type', false, INPUT_FILTER_POST_IDENTIFIER);
                require_code('database');
                if (post_param_string('db_site', '', INPUT_FILTER_POST_IDENTIFIER) == '') {
                    $db = new DatabaseConnector(post_param_string('db_forums', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_host', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_user', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_forums_password', false, INPUT_FILTER_PASSWORD), '', true);
                } else {
                    $db = new DatabaseConnector(post_param_string('db_site', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_site_host', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_site_user', false, INPUT_FILTER_POST_IDENTIFIER), post_param_string('db_site_password', false, INPUT_FILTER_PASSWORD), '', true);
                }
                $db->ensure_connected();
                exit();
                break;
        }

        $output = '';
        if (preg_match('#^themes/default/css/#', $type) != 0) {
            header('Content-Type: text/css; charset=' . get_charset());
            foreach (['themes/default/css/_base.css', 'themes/default/css/_colours.css', $type] as $_type) {
                if (!file_exists(get_file_base() . '/' . $_type)) {
                    if (function_exists('file_array_get')) {
                        $file = unixify_line_format(handle_string_bom(file_array_get($_type)));
                    } else {
                        exit();
                    }
                } else {
                    $file = cms_file_get_contents_safe(get_file_base() . '/' . $_type, FILE_READ_LOCK | FILE_READ_BOM);
                }
                $file = preg_replace('#\{\$IMG;?,([^,\}\']+)\}#', 'install.php?type=themes/default/images/${1}.svg', $file);

                require_code('tempcode_compiler');
                $css = template_to_tempcode($file, 0, false, '');
                $output .= $css->evaluate();
            }

            print($output);
            exit();
        }

        if (preg_match('#^data/polyfills/#', $type) != 0) {
            header('Content-Type: text/javascript; charset=' . get_charset());
            if (!file_exists(get_file_base() . '/' . $type)) {
                if (function_exists('file_array_get')) {
                    $output = unixify_line_format(handle_string_bom(file_array_get($type)));
                }
            } else {
                $output = cms_file_get_contents_safe(get_file_base() . '/' . $type, FILE_READ_LOCK);
            }
            print($output);
            exit();
        }

        if (preg_match('#^themes/default/images/.*\.png$#', $type) != 0) {
            header('Content-Type: image/png');
            if (!file_exists(get_file_base() . '/' . $type)) {
                if (function_exists('file_array_get')) {
                    $output = handle_string_bom(file_array_get($type));
                }
            } else {
                $output = cms_file_get_contents_safe(get_file_base() . '/' . $type, FILE_READ_LOCK);
            }
            print($output);
            exit();
        }

        if (preg_match('#^themes/default/images/.*\.svg$#', $type) != 0) {
            header('Content-Type: image/svg+xml; charset=' . get_charset());
            if (!file_exists(get_file_base() . '/' . $type)) {
                if (function_exists('file_array_get')) {
                    $output = unixify_line_format(handle_string_bom(file_array_get($type)));
                }
            } else {
                $output = cms_file_get_contents_safe(get_file_base() . '/' . $type, FILE_READ_LOCK);
            }
            print($output);
            exit();
        }

        if (preg_match('#^themes/default/images/.*\.gif$#', $type) != 0) {
            header('Content-Type: image/gif; charset=' . get_charset());
            if (!file_exists(get_file_base() . '/' . $type)) {
                if (function_exists('file_array_get')) {
                    $output = unixify_line_format(handle_string_bom(file_array_get($type)));
                }
            } else {
                $output = cms_file_get_contents_safe(get_file_base() . '/' . $type, FILE_READ_LOCK);
            }
            print($output);
            exit();
        }

        exit();
    }
}

/**
 * Make the UI for an installer textual option.
 *
 * @param  Tempcode $nice_name The human readable name for the option
 * @param  Tempcode $description A description of the option
 * @param  ID_TEXT $name The name of the option
 * @param  string $value The default/current value of the option
 * @param  boolean $hidden Whether the options value should be kept star'red out (e.g. it is a password)
 * @param  boolean $required Whether the option is required
 * @return Tempcode The option
 */
function make_option(object $nice_name, object $description, string $name, string $value, bool $hidden = false, bool $required = false) : object
{
    if ($value === null) {
        $value = '';
    }

    $_required = ($required ? '-required' : '');

    if ($hidden) {
        $input1 = do_template('INSTALLER_INPUT_PASSWORD', ['_GUID' => '373b85cea71837a30d146df387dc2a42', 'REQUIRED' => $_required, 'NAME' => $name, 'VALUE' => $value]);
        $a = do_template('INSTALLER_STEP_4_SECTION_OPTION', ['_GUID' => '455b0f61e6ce2eaf2acce2844fdd5e7a', 'REQUIRED' => $required, 'NAME' => $name, 'INPUT' => $input1, 'NICE_NAME' => $nice_name, 'DESCRIPTION' => $description]);
        if ((substr($name, 0, 3) != 'db_') && (substr($name, 0, 12) != 'gae_live_db_') && ($name != 'ftp_password')) {
            $input2 = do_template('INSTALLER_INPUT_PASSWORD', ['_GUID' => '0f15bfe5b58f3ca7830a48791f1a6a6d', 'REQUIRED' => $_required, 'NAME' => $name . '_confirm', 'VALUE' => $value]);
            $b = do_template('INSTALLER_STEP_4_SECTION_OPTION', [
                '_GUID' => 'bcbaa5585abb15ef6fbdc5a279c62fae',
                'REQUIRED' => $required,
                'NAME' => $name,
                'INPUT' => $input2,
                'NICE_NAME' => $nice_name,
                'DESCRIPTION' => do_lang_tempcode('CONFIRM_PASSWORD'),
            ]);
            $a->attach($b);
        }
        return $a;
    }

    $input = do_template('INSTALLER_INPUT_LINE', ['_GUID' => '31cdfb760d7c61de65656c5256bf2e88', 'REQUIRED' => $_required, 'NAME' => $name, 'VALUE' => $value]);
    return do_template('INSTALLER_STEP_4_SECTION_OPTION', ['_GUID' => 'a13131994a22b6f646e517c54a7c41d5', 'REQUIRED' => $required, 'NAME' => $name, 'INPUT' => $input, 'NICE_NAME' => $nice_name, 'DESCRIPTION' => $description]);
}

/**
 * Make the UI for an installer tick (check) option.
 *
 * @param  Tempcode $nice_name The human readable name for the option
 * @param  Tempcode $description A description of the option
 * @param  ID_TEXT $name The name of the option
 * @param  BINARY $value The default/current value of the option
 * @return Tempcode The list of usergroups
 */
function make_tick(object $nice_name, object $description, string $name, int $value) : object
{
    $input = do_template('INSTALLER_INPUT_TICK', ['_GUID' => 'a65cb6c868372c6237029113992d87fa', 'CHECKED' => $value == 1, 'NAME' => $name]);
    return do_template('INSTALLER_STEP_4_SECTION_OPTION', ['_GUID' => '0723f86908f66da7f67ebc4cd07bff2e', 'REQUIRED' => false, 'NAME' => $name, 'INPUT' => $input, 'NICE_NAME' => $nice_name, 'DESCRIPTION' => $description]);
}

/**
 * Get an example string for the installer UI (abstraction).
 *
 * @param  string $example The codename of the example text language string (blank: none)
 * @param  mixed $description The codename of the example description language string or Tempcode for a description (blank: none)
 * @return Tempcode The text
 */
function example(string $example, $description) : object
{
    $out = new Tempcode();
    if ($description !== '') {
        if (is_object($description)) {
            $out->attach($description);
        } else {
            $out->attach(do_lang_tempcode($description));
        }
    }
    if ($example != '') {
        $out->attach('<br />');
        $out->attach(do_lang_tempcode('FOR_EXAMPLE', do_lang_tempcode($example)));
    }
    return $out;
}

/**
 * Using the current forum driver, find the forum path.
 * Also probe the configuration.
 *
 * @param  string $given What the user manually gave as the forum path (may be blank)
 * @return ?URLPATH The answer (null: could not find the forum)
 */
function find_forum_path(string $given) : ?string
{
    $filebase = getcwd();
    $paths = $GLOBALS['FORUM_DRIVER']->install_get_path_search_list();
    if (!$GLOBALS['FORUM_DRIVER']->install_test_load_from($given)) {
        foreach ($paths as $path) {
            $result = $GLOBALS['FORUM_DRIVER']->install_test_load_from($filebase . '/' . $path);
            if ($result) {
                return $filebase . '/' . $path;
            }
        }
    }
    return null;
}

/**
 * Get the contents of a directory, with support for searching the installation archive.
 *
 * @param  PATH $dir The directory to get the contents of
 * @param  boolean $php Whether just to get .php files
 * @return array A map of the contents (file=>dir)
 */
function get_dir_contents(string $dir, bool $php = false) : array
{
    $out = [];

    global $DIR_ARRAY, $FILE_ARRAY;
    if (@is_array($DIR_ARRAY)) {
        if (!$php) {
            foreach ($DIR_ARRAY as $dir2) {
                if (strlen($dir) >= strlen($dir2)) {
                    continue;
                }

                $stub = substr($dir2, 0, strlen($dir) + 1);
                if ($dir . '/' == $stub) {
                    $extra = substr($dir2, strlen($dir) + 1);

                    $a = strpos($dir2, '/', strlen($dir) + 1);
                    if ($a === false) {
                        $out[$extra] = $dir;
                    }
                }
            }
            foreach ($FILE_ARRAY as $dir2) {
                if (strlen($dir) >= strlen($dir2)) {
                    continue;
                }

                $stub = substr($dir2, 0, strlen($dir) + 1);
                if ($dir . '/' == $stub) {
                    $extra = substr($dir2, strlen($dir) + 1);

                    $a = strpos($dir2, '/', strlen($dir) + 1);
                    if ($a === false) {
                        $out[$extra] = $dir;
                    }
                }
            }
        } else {
            $count = file_array_count();
            for ($i = 0; $i < $count; $i++) {
                $file = $FILE_ARRAY[$i];

                if (strlen($dir) >= strlen($file)) {
                    continue;
                }

                $stub = substr($file, 0, strlen($dir) + 1);
                $extra = substr($file, strlen($dir) + 1);
                if (($dir . '/' == $stub) && (substr($extra, -4) == '.php')) {
                    $a = strpos($file, '/', strlen($dir) + 1);
                    if ($a === false) {
                        $out[substr($extra, 0, strlen($extra) - 4)] = $dir;
                    }
                }
            }
        }
    }

    $_dir = @opendir($dir);
    if ($_dir !== false) {
        while (false !== ($file = readdir($_dir))) {
            if (($file != 'index.php') && ($file != '.htaccess') && ($file[0] != '.')) {
                if ($php) {
                    if (cms_strtolower_ascii(substr($file, -4, 4)) == '.php') {
                        $file2 = substr($file, 0, strlen($file) - 4);
                        $out[$file2] = $dir;
                    }
                } else {
                    $out[$file] = $dir;
                }
            }
        }
        closedir($_dir);
    }

    return $out;
}

/**
 * Get default table prefix.
 *
 * @return string Default Composr table prefix
 */
function get_default_table_prefix() : string
{
    // If we're running out of the main Git repository (approximation: test framework exists), then sandbox with a version-specific table prefix
    return file_exists(get_file_base() . '/_tests') ? ('cms' . strval(intval(cms_version_number())) . '_') : 'cms_';
}

/**
 * Return decompressed version of the input (at time of writing, no compression being used for quick installer archiving).
 *
 * @param  string $input The file in raw compressed form
 * @return string The decompressed file
 */
function compress_filter(string $input) : string
{
    //return bzdecompress($input);
    return $input;
}

/**
 * Try and get a good .htaccess file built.
 *
 * @param  resource $conn FTP connection to server
 */
function test_htaccess($conn)
{
    $clauses = [];

    $clauses[] = <<<END
# Stop any potential content-type sniffing vulnerabilities
<IfModule mod_headers.c>
Header set X-Content-Type-Options "nosniff"
</IfModule>
END;

    $clauses[] = <<<END
# Disable inaccurate security scanning (Composr has its own). This disabling only works with modsecurity1 unfortunately.
<IfModule mod_security.c>
SecFilterEngine Off
SecFilterScanPOST Off
SecRuleRemoveById 300018 340147 340014 950119 950120 973331
</IfModule>
END;

    $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $clauses[] = <<<END

LimitRequestBody 524288000

# Set Composr to handle 404 errors. Assume Composr is in the root
<FilesMatch "(?<!\.jpg|\.jpeg|\.jpe|\.bmp|\.gif|\.png|\.ico|\.cur|\.svg|\.css|\.js|\.html)$">
ErrorDocument 404 {$base}/index.php?page=404
</FilesMatch>
END;

    $clauses[] = <<<END

# Compress some static resources
<IfModule mod_deflate.c>
<IfModule mod_filter.c>
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript
</IfModule>
</IfModule>

# We do not want for TAR files, due to IE bug http://blogs.msdn.com/b/wndp/archive/2006/08/21/content-encoding-not-equal-content-type.aspx (IE won't decompress again as it thinks it's a mistake) LEGACY
<IfModule mod_setenvif.c>
SetEnvIfNoCase Request_URI \.tar$ no-gzip dont-vary
</IfModule>
END;

    $clauses[] = <<<END

# Exotic file types we may want
AddType text/vtt .vtt

<IfModule mod_alias.c>
# Security - block access to .git
RedirectMatch 404 /\.git
</IfModule>
END;

    /*REWRITE RULES START*/
    $clauses[] = <<<END

# Needed for mod_rewrite. Disable this line if your server does not have AllowOverride permission (can be one cause of Internal Server Errors)
Options +SymLinksIfOwnerMatch -MultiViews

RewriteEngine on

# Needed to pass HTTP-auth header on PHP CGI (it's not automatic, unlike PHP Module)
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

# Ad-hoc redirects go here. If this file is writeable, Composr will write in configured redirects below)
# RewriteRule somerule sometarget (leave this comment here!)

# If rewrites are directing to bogus URLs, try adding a "RewriteBase /" line, or a "RewriteBase /subdir" line if you're in a subdirectory. Requirements vary from server to server.

# Rewrite .gif animations for browsers that support APNG (LEGACY; when possible we want to get rid of the gifs, but IE11 does not yet support)
RewriteCond %{HTTP_USER_AGENT} (Chrom|Firefox|Safari)
RewriteCond %{REQUEST_FILENAME}.png -f
RewriteRule ^(themes/default/images/cns_emoticons/.*\.gif)$ $1.png [L,QSA]
RewriteCond %{HTTP_USER_AGENT} (Chrom|Firefox|Safari)
RewriteCond %{REQUEST_FILENAME}.png -f
RewriteRule ^(themes/default/images/cns_emoticons/.*\.gif)$ $1.png [L,QSA]
RewriteCond %{HTTP_USER_AGENT} (Chrom|Firefox|Safari)
RewriteCond %{REQUEST_FILENAME}.png -f
RewriteRule ^(themes/default/images/loading\.gif)$ themes/default/images/loading.gif.png [L,QSA]

# Anything that would point to a real file should actually be allowed to do so. If you have a "RewriteBase /subdir" command, you may need to change to "%{DOCUMENT_ROOT}/subdir/$1".
RewriteCond $1 ^\d+.shtml [OR]
RewriteCond $1 \.(1st|3g2|3gp|3gp2|3gpp|3p|7z|aac|ai|aif|aifc|aiff|asf|atom|avi|bmp|br|bz2|css|csv|cur|dat|diff|dmg|doc|docx|dot|dotx|eml|exe|f4v|gif|gz|html|ico|ics|ini|iso|jpe|jpeg|jpg|js|json|keynote|log|m2v|m4v|mdb|mid|mov|mp2|mp3|mp4|mpa|mpe|mpeg|mpg|mpv2|numbers|odb|odc|odg|odi|odp|ods|odt|ogg|ogv|otf|pages|patch|pdf|php|png|ppt|pptx|ps|psd|pub|qt|ra|ram|rar|rm|rss|rtf|sql|svg|swf|tar|tga|tgz|tif|tiff|torrent|tpl|ttf|txt|vsd|vtt|wav|weba|webm|webp|wma|wmv|woff|xls|xlsx|xml|xsd|xsl|zip) [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -l [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -d [OR]
RewriteCond $1 -f [OR]
RewriteCond $1 -l [OR]
RewriteCond $1 -d
RewriteRule ^/?(.*) - [L]

# crossdomain.xml is actually Composr-driven
RewriteRule ^/?crossdomain\.xml data/crossdomain.php

# WebDAV implementation (requires the non-bundled WebDAV addon)
RewriteRule ^/?webdav(/.*|$) data_custom/webdav.php
RewriteCond %{HTTP_HOST} ^webdav\..*
RewriteRule ^/?(.*)$ data_custom/webdav.php

#FAILOVER STARTS
### LEAVE THIS ALONE, AUTOMATICALLY MAINTAINED ###
#FAILOVER ENDS

# Redirect away from modules called directly by URL. Helpful as it allows you to "run" a module file in a debugger and still see it running.
RewriteRule ^/?([^=]*)pages/(modules|modules_custom)/([^/]*)\.php$ $1index.php\?page=$3 [L,QSA,R]

# PG STYLE: These have a specially reduced form (no need to make it too explicit that these are Wiki+). We shouldn't shorten them too much, or the actual zone or base URL might conflict
RewriteRule ^/?([^=]*)pg/s/([^\&\?]*)/index\.php$ $1index.php\?page=wiki&id=$2 [L,QSA]

# PG STYLE: These are standard patterns
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/([^/\&\?]*)/([^\&\?]*)/index\.php(.*)$ $1index.php\?page=$2&type=$3&id=$4$5 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/([^/\&\?]*)/index\.php(.*)$ $1index.php\?page=$2&type=$3$4 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/index\.php(.*)$ $1index.php\?page=$2$3 [L,QSA]
RewriteRule ^/?([^=]*)pg/index\.php(.*)$ $1index.php\?page=$2 [L,QSA]

# PG STYLE: Now the same as the above sets, but without any additional parameters (and thus no index.php)
RewriteRule ^/?([^=]*)pg/s/([^\&\?]*)$ $1index.php\?page=wiki&id=$2 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/([^/\&\?]*)/([^\&\?]*)/$ $1index.php\?page=$2&type=$3&id=$4 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/([^/\&\?]*)/([^\&\?]*)$ $1index.php\?page=$2&type=$3&id=$4 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)/([^/\&\?]*)$ $1index.php\?page=$2&type=$3 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?]*)$ $1index.php\?page=$2 [L,QSA]

# PG STYLE: And these for those nasty situations where index.php was missing and we couldn't do anything about it (usually due to keep_session creeping into a semi-cached URL)
RewriteRule ^/?([^=]*)pg/s/([^\&\?\.]*)&(.*)$ $1index.php\?$3&page=wiki&id=$2 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?\.]*)/([^/\&\?\.]*)/([^/\&\?\.]*)&(.*)$ $1index.php\?$5&page=$2&type=$3&id=$4 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?\.]*)/([^/\&\?\.]*)&(.*)$ $1index.php\?$4&page=$2&type=$3 [L,QSA]
RewriteRule ^/?([^=]*)pg/([^/\&\?\.]*)&(.*)$ $1index.php\?$3&page=$2 [L,QSA]

# HTM STYLE: These have a specially reduced form (no need to make it too explicit that these are Wiki+). We shouldn't shorten them too much, or the actual zone or base URL might conflict
RewriteRule ^/?(site|forum|adminzone|cms|docs)/s/([^\&\?]*)\.htm$ $1/index.php\?page=wiki&id=$2 [L,QSA]
RewriteRule ^/?s/([^\&\?]*)\.htm$ index\.php\?page=wiki&id=$1 [L,QSA]

# HTM STYLE: These are standard patterns
RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)/([^/\&\?]*)/([^\&\?]*)\.htm$ $1/index.php\?page=$2&type=$3&id=$4 [L,QSA]
RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)/([^/\&\?]*)\.htm$ $1/index.php\?page=$2&type=$3 [L,QSA]
RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)\.htm$ $1/index.php\?page=$2 [L,QSA]
RewriteRule ^/?([^/\&\?]+)/([^/\&\?]*)/([^\&\?]*)\.htm$ index.php\?page=$1&type=$2&id=$3 [L,QSA]
RewriteRule ^/?([^/\&\?]+)/([^/\&\?]*)\.htm$ index.php\?page=$1&type=$2 [L,QSA]
RewriteRule ^/?([^/\&\?]+)\.htm$ index.php\?page=$1 [L,QSA]

# SIMPLE STYLE: These have a specially reduced form (no need to make it too explicit that these are Wiki+). We shouldn't shorten them too much, or the actual zone or base URL might conflict
#RewriteRule ^/?(site|forum|adminzone|cms|docs)/s/([^\&\?]*)$ $1/index.php\?page=wiki&id=$2 [L,QSA]
#RewriteRule ^/?s/([^\&\?]*)$ index\.php\?page=wiki&id=$1 [L,QSA]

# SIMPLE STYLE: These are standard patterns
#RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)/([^/\&\?]*)/([^\&\?]*)$ $1/index.php\?page=$2&type=$3&id=$4 [L,QSA]
#RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)/([^/\&\?]*)$ $1/index.php\?page=$2&type=$3 [L,QSA]
#RewriteRule ^/?(site|forum|adminzone|cms|docs)/([^/\&\?]+)$ $1/index.php\?page=$2 [L,QSA]
#RewriteRule ^/?([^/\&\?]+)/([^/\&\?]*)/([^\&\?]*)$ index.php\?page=$1&type=$2&id=$3 [L,QSA]
#RewriteRule ^/?([^/\&\?]+)/([^/\&\?]*)$ index.php\?page=$1&type=$2 [L,QSA]
#RewriteRule ^/?([^/\&\?]+)$ index.php\?page=$1 [L,QSA]
END;
    /*REWRITE RULES END*/

    $clauses[] = <<<END
<RequireAll>
require all granted
# IP bans go here (leave this comment here! If this file is writeable, Composr will write in IP bans below, in sync with its own DB-based banning - this makes DOS/hack-attack prevention stronger)
# Require not ip xxx.xx.x.x (leave this comment here!)
</RequireAll>
END;

    if ((cms_is_writable(get_file_base() . '/exports/addons')) && ((!file_exists(get_file_base() . '/.htaccess')) || (trim(cms_file_get_contents_safe(get_file_base() . '/.htaccess', FILE_READ_LOCK)) == ''))) {
        $base_url = post_param_string('base_url', get_base_url(), INPUT_FILTER_URL_GENERAL);

        foreach ($clauses as $i => $clause) {
            cms_file_put_contents_safe(get_file_base() . '/exports/addons/index.php', "<" . "?php
            header('Cache-Control: no-store');
            ");

            cms_file_put_contents_safe(get_file_base() . '/exports/addons/.htaccess', $clause);
            if (php_function_allowed('usleep')) {
                usleep(1000000); // 100ms, some servers are slow to update
            }
            $http_result = cms_http_request($base_url . '/exports/addons/index.php', ['trigger_error' => false]);

            if ($http_result->message != '200') {
                $clauses[$i] = null;
            }

            unlink(get_file_base() . '/exports/addons/.htaccess');
            unlink(get_file_base() . '/exports/addons/index.php');
        }

        $out = '';
        foreach ($clauses as $i => $clause) {
            if ($clause !== null) {
                $out .= $clause . "\n\n";
            }
        }
        if (is_suexec_like()) {
            cms_file_put_contents_safe(get_file_base() . '/.htaccess', $out);
        } else {
            @ftp_delete($conn, '.htaccess');
            file_put_contents(get_file_base() . '/cms_inst_tmp/tmp', $out);
            @ftp_put($conn, '.htaccess', get_file_base() . '/cms_inst_tmp/tmp', FTP_TEXT);
            @ftp_site($conn, 'CHMOD 644 .htaccess');
        }
    }

    $user_ini = ini_get('user_ini.filename');
    if ((!empty($user_ini)) && ($user_ini != '.user.ini')) {
        if (is_suexec_like()) {
            @copy(get_file_base() . '/.user.ini', get_file_base() . '/' . $user_ini);
            fix_permissions(get_file_base() . '/' . $user_ini);
        } else {
            file_put_contents(get_file_base() . '/cms_inst_tmp/tmp', cms_file_get_contents_safe(get_file_base() . '/.user.ini', FILE_READ_LOCK));
            @ftp_put($conn, $user_ini, get_file_base() . '/cms_inst_tmp/tmp', FTP_TEXT);
            @ftp_site($conn, 'CHMOD 644 ' . $user_ini);
        }
    }
}

/**
 * Check for POSTed database settings and ensure they are valid. Also checks for max_allowed_packet.
 *
 * @param  boolean $return_connection Whether to return the database connection (false: close the connection after verifying it and return null)
 * @return mixed Either null of $return_connection is false, or the database connection
 */
function confirm_db_credentials(bool $return_connection = false)
{
    require_code('database');
    $post_db_type = post_param_string('db_type', false, INPUT_FILTER_POST_IDENTIFIER);
    $table_prefix = post_param_string('table_prefix', false, INPUT_FILTER_POST_IDENTIFIER);
    if (($table_prefix !== false) && (strlen($table_prefix) > 18)) {
        warn_exit(do_lang_tempcode('LONG_TABLE_PREFIX', '18', $table_prefix));
    }
    $post_db_site = post_param_string('db_site', false, INPUT_FILTER_POST_IDENTIFIER);
    $post_db_host = post_param_string('db_site_host', false, INPUT_FILTER_POST_IDENTIFIER);
    $post_db_user = post_param_string('db_site_user', false, INPUT_FILTER_POST_IDENTIFIER);
    $post_db_password = post_param_string('db_site_password', false, INPUT_FILTER_PASSWORD);
    if (($post_db_type === false) || ($table_prefix === false) || ($post_db_site === false) || ($post_db_host === false) || ($post_db_user === false) || ($post_db_password === false)) {
        warn_exit(do_lang_tempcode('MISSING_DB_PARAMETERS'));
    }
    if (($post_db_user == 'root') && (!file_exists(get_file_base() . '/.git'))) {
        warn_exit(do_lang_tempcode('NO_ROOT_DB_WITHOUT_GIT'));
    }
    $tmp = new DatabaseConnector($post_db_site, $post_db_host, $post_db_user, $post_db_password, $table_prefix);

    // Check max allowed packet if mySQLi
    if (strpos($post_db_type, 'mysql') !== false) {
        $min = 1024 * 1024 * 4; // 4MB; if you change this value, also change it in the health_check install_env hook.
        $vars = $tmp->query('SHOW VARIABLES LIKE \'max_allowed_packet\'');
        foreach ($vars as $var) {
            $current = intval($var['Value']);
            if ($current < $min) {
                warn_exit(do_lang_tempcode('MAX_ALLOWED_PACKET_TOO_LOW', integer_format($min), integer_format($current)));
            }
        }
    }

    if (!$return_connection) {
        unset($tmp);
        return null;
    }

    return $tmp;
}
