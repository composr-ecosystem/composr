<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    static_export
 */

/*
There are hidden parameters you can use...
 - dir (directly extract to exports/static rather than downloading to a TAR file, default=0)
 - only_page_links (comma-separated inclusion list of page-links to export, default is to export all)
 - save__deps_files (set to 1 to export attached files, default=1)
 - guess_dates (set to 1 to use last page add/edit time as file date for that page, default=0)
 - save__pages (set to 1 to save pages, default=1)
 - save__misc_uploads (set to 1 to save uploads not covered by the deps above, default=1)
 - save__theme_files (set to 1 to save CSS/JavaScript/theme-images, default=1)
 - save__redirects (set to 1 to create stub pages that redirect to the browse screen type when not given in the URL, default=1)
 - save__htaccess (set to 1 to to create a simple .htaccess rule to cover some basic redirections among other things, default=1)
 - save__robots_txt (set to 1 to create a simple robots.txt to cover some basic things, default=1)
 - save__mailer (set to 1 to create a PHP mailer script, default=1)
 - save__warnings (set to 1 to create a _warnings.txt file with export warnings, default=1)
 - save__sitemaps (set to 1 to export the XML sitemap, default=1)
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('static_export', $error_msg)) {
    return $error_msg;
}

$extract = (get_param_integer('dir', 0) == 1);

$title = get_screen_title('Export static site', false);

if (post_param_integer('confirm', 0) == 0) {
    $preview = protect_from_escaping($extract ? 'Export to <kbd>exports/static</kbd>.' : 'Export to TAR file.');

    $url = get_self_url(false, false);

    $hidden = new Tempcode();
    $hidden->attach(form_input_hidden('confirm', '1'));
    $hidden->attach(form_input_hidden('csrf_token_preserve', '1'));

    return do_template('CONFIRM_SCREEN', ['_GUID' => '517159d4bde58ca67392922f3a53c0d7', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => $hidden, 'URL' => $url]);
}

disable_php_memory_limit();
cms_disable_time_limit();
push_db_scope_check(false);

$GLOBALS['STATIC_TEMPLATE_TEST_MODE'] = true;

require_code('tar');
require_code('files');
require_code('files2');
require_code('config2');

if (get_option('url_scheme') != 'HTM') {
    set_option('url_scheme', 'HTM');
    warn_exit(protect_from_escaping('A URL Scheme of "Use .htm to identify CMS pages" must be enabled before the export can happen, as this is the tidy scheme we export to. It is now enabled - click the back icon, refresh, and click proceed again.'));
}

if (get_option('show_inline_stats') == '1') {
    set_option('show_inline_stats', '0');
    warn_exit('Inline stats must be disabled. It is now disabled - click the back icon, refresh, and click proceed again.');
}

if (get_option('site_closed') == '1') {
    set_option('site_closed', '0');
    warn_exit('Site must not be closed. It is now open - click the back icon, refresh, and click proceed again.');
}

if ((get_option('is_on_comments') == '1') || (get_option('is_on_trackbacks') == '1') || (get_option('is_on_rating') == '1')) {
    set_option('is_on_comments', '0');
    set_option('is_on_trackbacks', '0');
    set_option('is_on_rating', '0');
    warn_exit('Comments/trackbacks/rating must not be enabled. It is now all disabled - click the back icon, refresh, and click proceed again.');
}

if (get_option('enable_previews') == '1') {
    set_option('enable_previews', '0');
    warn_exit('Previews must be disabled. It is now disabled - click the back icon, refresh, and click proceed again.');
}

$filename = 'static-' . get_site_name() . '.' . date('Y-m-d') . '.tar';

if (!$extract) {
    header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');
}

global $STATIC_EXPORT_TAR, $STATIC_EXPORT_WARNINGS;
$STATIC_EXPORT_WARNINGS = [];
if (get_forum_type() != 'none') {
    $STATIC_EXPORT_WARNINGS[] = 'Not on \'none\' forum driver, you may possibly still have some bundled login links etc to remove';
}
$tar_path = null;
if (!$extract) {
    $tar_path = 'php://output';
} else {
    $tar_path = cms_tempnam();
}
$STATIC_EXPORT_TAR = tar_open($tar_path, 'wb');

push_query_limiting(false);

// The content in the sitemap
require_code('sitemap');
require_code('static_export');
if (get_param_integer('save__pages', 1) == 1) {
    $callback = '_page_link_to_static';
    $meta_gather = SITEMAP_GATHER_TIMES;
    retrieve_sitemap_node(
        '',
        $callback,
        /*$valid_node_types=*/null,
        /*$child_cutoff=*/null,
        /*$max_recurse_depth=*/null,
        /*$options=*/SITEMAP_GEN_CHECK_PERMS | SITEMAP_GEN_AS_GUEST,
        /*$zone=*/'_SEARCH',
        $meta_gather
    );
}

$subpaths = [
    'data/images',
    'data/polyfills',
];

// Misc uploads
if (get_param_integer('save__misc_uploads', 1) == 1) {
    foreach (get_directory_contents(get_custom_file_base() . '/uploads', '', IGNORE_ACCESS_CONTROLLERS, false, false) as $subpath) {
        if (!in_array($subpath, ['downloads', 'attachments', 'attachments_thumbs', 'incoming', 'captcha', 'website_specific'])) {
            $subpaths = array_merge($subpaths, ['uploads/' . $subpath]);
        }
    }
}

// Theme files
if (get_param_integer('save__theme_files', 1) == 1) {
    $themes = [];
    $themes[] = 'default';
    $themes[] = $GLOBALS['FORUM_DRIVER']->get_theme('');
    $themes = array_unique($themes);

    $css_files = [];
    $js_files = [];
    foreach ($themes as $theme) {
        foreach (['css', 'css_custom'] as $_dir) {
            $dir = get_file_base() . '/themes/' . $theme . '/' . $_dir;
            $dh = opendir($dir);
            while (($f = readdir($dh)) !== false) {
                if (substr($f, -4) == '.css') {
                    $css_files[substr($f, 0, strlen($f) - 4)] = true;
                }
            }
            closedir($dh);
        }

        foreach (['javascript', 'javascript_custom'] as $_dir) {
            $dir = get_file_base() . '/themes/' . $theme . '/' . $_dir;
            $dh = opendir($dir);
            while (($f = readdir($dh)) !== false) {
                if (substr($f, -3) == '.js') {
                    $js_files[substr($f, 0, strlen($f) - 3)] = true;
                }
            }
            closedir($dh);
        }
    }

    foreach ($themes as $theme) {
        foreach (array_keys($css_files) as $c) {
            css_enforce($c, $theme);
        }
        foreach (array_keys($js_files) as $j) {
            javascript_enforce($j, $theme);
        }

        $subpaths = array_merge($subpaths, [
            'themes/' . $theme . '/templates_cached',
            'themes/' . $theme . '/images',
            'themes/' . $theme . '/images_custom',
        ]);
    }
}

foreach ($subpaths as $subpath) {
    if (substr($subpath, -strlen('/templates_cached')) == '/templates_cached') {
        // We just want .css and .js files from templates_cached/*
        $web_resources = get_directory_contents(get_custom_file_base() . '/' . $subpath, '', 0, true, true, ['css', 'js']);
        foreach ($web_resources as $file) {
            tar_add_file($STATIC_EXPORT_TAR, $subpath . '/' . $file, get_custom_file_base() . '/' . $subpath . '/' . $file, 0644, time(), true);
        }
    } else {
        tar_add_folder($STATIC_EXPORT_TAR, null, get_file_base(), null, $subpath, [], null, false, IGNORE_ACCESS_CONTROLLERS);
    }
}

// .htaccess
$data = '';
$data .= "<FilesMatch \"(?<!\.jpg|\.jpeg|\.gif|\.png|\.ico|\.cur|\.svg)$\">\nErrorDocument 404 /sitemap.htm\n</FilesMatch>\n\n";
$data .= 'RewriteEngine on' . "\n";
$data .= "\n";
$data .= "\n";
$data .= 'RewriteRule ^/?$ home.htm [R,L]' . "\n";
$data .= "\n";
$data .= "\n";
$directory = $STATIC_EXPORT_TAR['directory'];
$langs = find_all_langs();
$done_non_spec = [];
foreach ($directory as $entry) {
    $dir_name = preg_replace('#^[A-Z][A-Z]/#', '', dirname($entry['path']));
    if ($dir_name == '.') {
        $dir_name = '';
    }
    if (isset($done_non_spec[$dir_name])) {
        continue;
    }
    $done_non_spec[$dir_name] = 1;
    if (($dir_name != '') && (basename($entry['path']) == 'browse.htm')) {
        $data .= 'RewriteRule ^/?' . $dir_name . '\.htm(.*) ' . $dir_name . '/browse.htm$1 [R,L]' . "\n";

        // If .htaccess not supported let it redirect via simple stub file instead
        if (get_param_integer('save__redirects', 1) == 1) {
            $datax = '<meta http-equiv="refresh" content="0;' . escape_html(basename($dir_name)) . '/browse.htm" />';
            foreach (array_keys($langs) as $lang) {
                tar_add_file($STATIC_EXPORT_TAR, ((count($langs) != 1) ? ($lang . '/') : '') . $dir_name . '.htm', $datax, 0644, time(), false);
            }
        }
    }
}
$data .= "\n";
$data .= "\n";
if (count($langs) != 1) {
    // Recognise when language explicitly called
    foreach (array_keys($langs) as $lang) {
        $data .= 'RewriteRule ' . $lang . ' - [L]' . "\n"; // This stops it looping; [L] ends an iteration for a directory level but doesn't stop inter-level recursions
        $data .= 'RewriteCond %{QUERY_STRING} keep_lang=' . $lang . "\n";
        $data .= 'RewriteRule ^/?(.*\.htm.*$) ' . $lang . '/$1 [L]' . "\n";

        $data .= "\n";
    }

    // Recognise when language supported by browser
    if (get_option('detect_lang_browser') == '1') {
        foreach (array_keys($langs) as $lang) {
            $data .= 'RewriteCond %{HTTP:Accept-Language} (^' . cms_strtolower_ascii($lang) . ') [NC]' . "\n";
            $data .= 'RewriteRule ^/?(.*\.htm.*$) ' . $lang . '/$1 [L]' . "\n";

            $data .= "\n";
        }
    }

    // And default to English
    $data .= "\n";
    $data .= 'RewriteRule ^/?(.*\.htm.*$) EN/$1 [L]' . "\n";

    $data .= "\n";
}
if (get_param_integer('save__htaccess', 1) == 1) {
    tar_add_file($STATIC_EXPORT_TAR, '.htaccess', $data, 0644, time(), false);
}

require_code('mail');

// Mailer
$robots_data = '';
foreach (array_keys($langs) as $lang) {
    $mailer_script = '
<' . '?php

function is_control_field($field_name, $include_email_metafields = false, $include_login_fields = false, $extra_boring_fields = [])
{
    // NB: Keep this function synced with the copy of it in static_export.php

    $boring_fields = [
        // Passed through metadata
        "id",

        // Passed through data
        "stub",

        // Passed through context
        "from_url",
        "http_referer",
        "redirect",
        "redirect_passon",

        // Image buttons send these
        "x",
        "y",

        // Password fields
        "password",
        "confirm_password",
        "edit_password",

        // Relating to uploads/attachments
        "MAX_FILE_SIZE",

        // Relating to preview
        "perform_webstandards_check",

        // Relating to posting form/WYSIWYG
        "posting_ref_id",
        "f_face",
        "f_colour",
        "f_size",

        // Relating to security
        "session_id",
        "csrf_token",
        "js_token",
        "y" . md5(get_site_name() . ": antispam"),

        // Data relaying for Suhosin workaround
        "post_data",
    ];
    if ($include_email_metafields) {
        $boring_fields = array_merge($boring_fields, [
            "subject",
            "title",
            "name",
            "email",
            "to_members_email",
            "to_written_name",
        ]);
    }
    if ($include_login_fields) {
        $boring_fields = array_merge($boring_fields, [
            "username",
            "password",
            "remember_me",
            "login_invisible",
        ]);
    }
    if (in_array($field_name, $boring_fields)) {
        return true;
    }

    if (in_array($field_name, $extra_boring_fields)) {
        return true;
    }

    $prefixes = [
        // Standard hidden-fields convention
        "_",

        // Form metadata
        "label_for__",
        "description_for__",
        "tick_on_form__",
        "require__",

        // Relating to uploads/attachments
        "hid_file_id_",
        "hidFileName_",

        // Relating to preview
        "pre_f_",
        "tempcodecss__",
        "comcode__",

        // Relating to permissions setting
        "access_",
    ];
    if ($include_email_metafields) {
        $prefixes = array_merge($prefixes, [
            "field_tagged__",
        ]);
    }
    foreach ($prefixes as $prefix) {
        if (substr($field_name, 0, strlen($prefix)) == $prefix) {
            return true;
        }
    }

    $suffixes = [
        // Relating to posting form/WYSIWYG
        "_parsed",
        "__is_wysiwyg",
    ];
    foreach ($suffixes as $suffix) {
        if (substr($field_name, -strlen($suffix)) == $suffix) {
            return true;
        }
    }

    $substrings = [
        // Passed through metadata
        "confirm",
    ];
    foreach ($substrings as $substring) {
        if (strpos($field_name, $substring) !== false) {
            return true;
        }
    }

    if (($field_name[0] == "x") && (strlen($field_name) == 33)) {
        return true;
    }

    return false;
}

function post_param_string($key, $default)
{
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function cms_ucwords_ascii($str)
{
    $starting_word = false;
    global $ascii_lcase_map;
    $ascii_lcase_map = ["a" => "A", "b" => "B", "c" => "C", "d" => "D", "e" => "E", "f" => "F", "g" => "G", "h" => "H", "i" => "I", "j" => "J", "k" => "K", "l" => "L", "m" => "M", "n" => "N", "o" => "O", "p" => "P", "q" => "Q", "r" => "R", "s" => "S", "t" => "T", "u" => "U", "v" => "V", "w" => "W", "x" => "X", "y" => "Y", "z" => "Z"];
    $ret = "";
    foreach (str_split($str) as $c) {
        $is_whitespace = (trim($c) == "");
        if (($starting_word) && (!$is_whitespace)) {
            $ret .= isset($ascii_lcase_map[$c]) ? $ascii_lcase_map[$c] : $c;
            $starting_word = false;
        } else {
            if ($is_whitespace) {
                $starting_word = true;
            }
            $ret .= $c;
        }
    }
    return $ret;
}

function titleify($boring)
{
    return cms_ucwords_ascii(str_replace("_", " ", $boring));
}

if (!isset($_COOKIE["js_on"])) {
    exit("Error: cookies must be enabled, for anti-spam reasons.");
}

$title = post_param_string("title", "' . do_lang('UNKNOWN') . '");

$to = "' . get_option('staff_address') . '";

$email = post_param_string("email", $to);
$name = post_param_string("name", "' . do_lang('UNKNOWN') . '");

$post = post_param_string("post", "");

$fields = [];
foreach (array_keys($_POST) as $key) {
    if (!is_control_field($key)) {
        $fields[$key] = post_param_string("label_for__" . $key, titleify($key));
    }
}
foreach ($fields as $field => $field_title) {
    $field_val = post_param_string($field, "");
    if ($field_val != "") {
        $post .= "\n\n" . $field_title . ": " . $field_val;
    }
}

$subject = $title;
$message = $post;
$headers = "";
$website_email = "' . addslashes(get_option('website_email')) . '";
if ($website_email == "") {
    $website_email = $email;
}
$headers .= "From: \"" . str_replace("\"", "", $name) . "\" <{$website_email}>\n";
$headers .= "Reply-To: \"" . str_replace("\"", "", $name) . "\" <{$email}>\n";
$random_hash = md5(date("r", time()));
$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-{$random_hash}\"";
$mime_message = "";
$mime_message .= "--PHP-mixed-{$random_hash}\n";
$mime_message .= "Content-Type: text/plain; charset=\"' . get_charset() . '\"\n\n";
$mime_message .= $message . "\n\n";
foreach ($_FILES as $f) {
    $mime_message .= "--PHP-mixed-{$random_hash}\n";
    $mime_message .= "Content-Type: application/octet-stream; name=\"" . addslashes($f["name"]) . "\"\n";
    $mime_message .= "Content-Transfer-Encoding: base64\n";
    $mime_message .= "Content-Disposition: attachment\n\n";
    $mime_message .= base64_encode(cms_file_get_contents_safe($f["tmp_name"], FILE_READ_LOCK));
}
$mime_message .= "--PHP-mixed-{$random_hash}--\n\n";
if (trim($post) != "") {
    mail($to, $subject, $mime_message, $headers);
}

?' . '>

<p>' . do_lang('MESSAGE_SENT', null, null, null, $lang) . '</p>
';
    if (get_param_integer('save__mailer', 1) == 1) {
        require_code('crypt');
        $mailer_path = get_custom_file_base() . '/pages/html_custom/' . $lang . '/_mailer_temp.htm';
        cms_file_put_contents_safe($mailer_path, $mailer_script, FILE_WRITE_FIX_PERMISSIONS);
        $session_cookie_id = get_session_cookie();
        $data = http_get_contents(static_evaluate_tempcode(build_url(['page' => '_mailer_temp', 'keep_lang' => (count($langs) != 1) ? $lang : null], '', [], false, false, true)), ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'cookies' => [$session_cookie_id => get_secure_random_string()]]);
        unlink($mailer_path);
        $data = preg_replace('#<title>.*</title>#', '<title>' . escape_html(get_site_name()) . '</title>', $data);
        $relative_root = (count($langs) != 1) ? '../' : '';
        tar_add_file($STATIC_EXPORT_TAR, ((count($langs) != 1) ? ($lang . '/') : '') . 'mailer.php', static_remove_dynamic_references($data, $relative_root), 0644, time(), false);

        $robots_data .= 'Deny /' . ((count($langs) != 1) ? ($lang . '/') : '') . 'mailer.php' . "\n";
    }
}
if (get_param_integer('save__robots_txt', 1) == 1) {
    $robots_txt = 'User-agent: *' . "\n" . $robots_data;
    $robots_txt .= "\n\nSitemap: /data_custom/sitemaps/index.xml\n";
    tar_add_file($STATIC_EXPORT_TAR, 'robots.txt', $robots_txt, 0644, time(), false);
}

// Add warnings file
if (get_param_integer('save__warnings', 1) == 1) {
    if (!empty($STATIC_EXPORT_WARNINGS)) {
        tar_add_file($STATIC_EXPORT_TAR, '_warnings.txt', implode("\n", $STATIC_EXPORT_WARNINGS), 0644, time(), false);
    }
}

// Sitemap, if it has been built
if (get_param_integer('save__sitemaps', 1) == 1) {
    if (file_exists(get_custom_file_base() . '/data_custom/sitemaps/index.xml')) {
        tar_add_folder($STATIC_EXPORT_TAR, null, get_custom_file_base(), null, 'data_custom/sitemaps');
    }
}

tar_close($STATIC_EXPORT_TAR);

if (!$extract) {
    $GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
    exit();
}

// Extract
$myfile = tar_open($tar_path, 'rb');
if (!file_exists(get_custom_file_base() . '/exports/static')) {
    mkdir(get_custom_file_base() . '/exports/static', 0777);
    fix_permissions(get_custom_file_base() . '/exports/static');
    sync_file(get_custom_file_base() . '/exports/static');
}
tar_extract_to_folder($myfile, '/exports/static');
tar_close($myfile);
unlink($tar_path);

$title->evaluate_echo();

echo 'Exported to <kbd>exports/static</kbd>.';
