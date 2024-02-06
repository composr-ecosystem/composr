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

/**
 * Callback for saving a page-link into static output.
 *
 * @param  array $node The Sitemap node
 */
function _page_link_to_static(array $node)
{
    $page_link = $node['page_link'];
    if ($page_link === null) {
        return;
    }

    $add_date = $node['extra_meta']['add_date'];
    $edit_date = $node['extra_meta']['edit_date'];

    if (strpos($page_link, ':static_export') === false) {
        global $STATIC_EXPORT_TAR, $STATIC_EXPORT_WARNINGS;

        $only_page_links = get_param_string('only_page_links', null, INPUT_FILTER_GET_COMPLEX);
        if ($only_page_links !== null) {
            if (!in_array($page_link, explode(',', $only_page_links))) {
                return;
            }
        }

        $date = time();
        if (get_param_integer('dir', 0) == 0) { // For dir export we actually use times to represent overall change times, to benefit upload
            if (get_param_integer('guess_dates', 0) == 1) { // Bad idea actually, aggregate data can easily make the dates wrong
                if ($add_date !== null) {
                    $date = $add_date;
                }
                if ($edit_date !== null) {
                    $date = $edit_date;
                }
            }
        }

        $langs = find_all_langs();
        foreach (array_keys($langs) as $lang) {
            if (($lang != fallback_lang()) && (count(get_directory_contents(get_custom_file_base() . '/lang_custom/' . $lang, '', null, false, true)) < 5)) {
                continue; // Probably this is just the utf8 addon
            }

            $url_test = page_link_to_url($page_link, true);
            if (strpos($url_test, '?') !== false) {
                continue;
            }

            $extended_page_link = $page_link;
            if ($page_link == '') {
                $extended_page_link = ':' . get_zone_default_page('');
            }
            if (count($langs) != 1) {
                $extended_page_link .= ':keep_lang=' . $lang . ':max=10000';
            }
            $url = page_link_to_url($extended_page_link, true);

            $target_path = urldecode(preg_replace('#\?.*$#', '', preg_replace('#^' . preg_quote(get_base_url(), '#') . '/#', '', $url)));

            $save_target_path = (count($langs) == 1) ? $target_path : ($lang . '/' . $target_path);

            $directory = $STATIC_EXPORT_TAR['directory'];
            foreach ($directory as $entry) { // Make sure it does not exist, due to some kind of dynamic parameter being folded out, or complexities within sitemap callback API
                if ($entry['path'] == $save_target_path) {
                    continue 2;
                }
            }

            $session_cookie_id = get_session_cookie();
            $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'trigger_error' => false]);
            if ($data === null) {
                continue;
            }

            if (get_param_integer('save__deps_files', 1) == 1) {
                $data = preg_replace_callback('#"([^"]*/(attachment|dload)\.php\?id=(\d+)[^"]*)"#', '_static_export_scriptrep_callback', $data);
                $data = preg_replace_callback('#"([^"]*/(catalogue_file)\.php\?file=([^"&]+)&amp;original_filename=([^"&]+)[^"]*)"#', '_static_export_scriptrep_callback', $data);
            }

            // Redirect forms to mailer
            if (strpos($data, '<form') !== false) {
                $STATIC_EXPORT_WARNINGS[] = 'Form(s) on ' . $page_link . ' redirected to mailer.php if POST, else left alone if GET. Check this is correct! If it is a sort form you might want to remove that from the templates and re-export.';

                $new_form_action = escape_html(get_base_url() . ((count($langs) > 1) ? ('/' . $lang) : '') . '/mailer.php');
                $data = preg_replace('#(\s)action="[^"]*"([^>]*\smethod="post")#', '${1}action="' . $new_form_action . '"${2}', $data);
                $data = preg_replace('#(\smethod="post"[^>]*)(\s)action="[^"]*"#', '${1}action="' . $new_form_action . '"${2}', $data);

                // Set a JS session cookie for a very basic anti-spam system
                $data = str_replace('</head>', '<script>document.cookie="js_on=1";</script></head>', $data);
            }

            // Change absolute paths to relative ones
            $path_bits = explode('/', $target_path);
            array_pop($path_bits);
            $relative_root = '';
            foreach ($path_bits as $path_bit) {
                $relative_root .= '../';
            }
            $data = static_remove_dynamic_references($data, $relative_root);

            // Potential warnings
            if (strpos($data, '/ajax') !== false) {
                $STATIC_EXPORT_WARNINGS[] = 'AJAX being included on ' . $page_link . ', likely it doesn\'t work!';
            }

            tar_add_file($STATIC_EXPORT_TAR, $save_target_path, $data, 0644, $date, false);
        }
    }
}

/**
 * Cleanup some HTML for static use.
 *
 * @param  string $data The dirty HTML
 * @param  string $relative_root Root to replace base URL with
 * @return string Cleaned up HTML
 */
function static_remove_dynamic_references(string $data, string $relative_root = '') : string
{
    $data = str_replace(get_base_url() . '/', $relative_root, $data);
    $data = preg_replace('#<base\s[^>]*href="[^"]*"[^>]*>#', '', $data);
    $data = preg_replace('#<link rel="baseurl" href="[^"]*" />#', '', $data);

    // Remove any references to other Composr PHP scripts (e.g. RSS script) or callbacks with dynamic parameters
    $data = preg_replace('#<meta\s[^>]*content="[^"]*\.php[^"]*"[^>]*>\s*#', '', $data);
    $data = preg_replace('#<link\s[^>]*href="[^"]*\.php[^"]*"[^>]*>\s*#', '', $data);
    $data = preg_replace('#<li><a href="[^"]*keep_mobile=1">.*</a></li>#U', '', $data);
    $data = preg_replace('#<noscript><a href="[^"]*keep_has_js=0">.*</a></noscript>#U', '', $data);
    $data = preg_replace('#<li><a href="[^"]*login[^"]*">.*</a></li>#U', '', $data);
    $data = preg_replace('#\?redirect=[^&"]*&amp;#', '?', $data);
    $data = preg_replace('#\?redirect=[^&"]*#', '', $data);
    $data = preg_replace('#&amp;redirect=[^&"]*#', '', $data);
    $data = preg_replace('#<link rel="canonical" href="[^"]*" />#', '', $data);
    $data = str_replace('&amp;max=10000', '', $data);
    $data = str_replace('max=10000&amp;', '', $data);

    return $data;
}

/**
 * Callback for replacing dynamic script links with static ones.
 *
 * @param  array $matches The matches
 * @return string Replaced string
 */
function _static_export_scriptrep_callback(array $matches) : string
{
    global $STATIC_EXPORT_TAR;

    $new_url = $matches[1];

    switch ($matches[2]) {
        case 'attachment':
            $id = intval($matches[3]);

            $thumb = (strpos($matches[1], 'thumb=1') !== false);

            $field = 'a_url';
            if ($thumb) {
                $field = 'a_thumb_url';
            }
            $attachment = $GLOBALS['SITE_DB']->query_select('attachments', ['id', $field, 'a_original_filename'], ['id' => $id], '', 1);

            if (url_is_local($attachment[0][$field])) {
                $prefix = ($thumb ? 'thumbnail__' : '') . $attachment[0]['id'] . '__';

                $attachment[0]['a_original_filename'] = _static_export_make_ascii($attachment[0]['a_original_filename']);

                $new_url = get_base_url() . '/media/' . $prefix . $attachment[0]['a_original_filename'];

                if (get_param_integer('save__deps_files', 1) == 1) {
                    $full_path = get_custom_file_base() . '/' . urldecode($attachment[0][$field]);
                    tar_add_file($STATIC_EXPORT_TAR, 'media/' . $prefix . $attachment[0]['a_original_filename'], $full_path, 0644, filemtime($full_path), true, true);
                }
            } else {
                $new_url = $attachment[0][$field];
            }

            break;

        case 'catalogue_file':
            $file = urldecode($matches[3]);
            $original_filename = _static_export_make_ascii(urldecode($matches[4]));

            $new_url = get_base_url() . '/catalogue_files/' . md5($file) . '__' . $original_filename;

            if (get_param_integer('save__deps_files', 1) == 1) {
                $full_path = get_custom_file_base() . '/uploads/catalogues/' . $file;
                tar_add_file($STATIC_EXPORT_TAR, 'catalogue_files/' . md5($file) . '__' . $original_filename, $full_path, 0644, filemtime($full_path), true, true);
            }

            break;

        case 'dload':
            $id = intval($matches[3]);

            $download = $GLOBALS['SITE_DB']->query_select('download_downloads', ['id', 'url', 'original_filename'], ['id' => $id], '', 1);

            if (!array_key_exists(0, $download)) {
                return $matches[0];
            }

            if (url_is_local($download[0]['url'])) {
                $download[0]['original_filename'] = _static_export_make_ascii($download[0]['original_filename']);

                $new_url = get_base_url() . '/files/' . $download[0]['id'] . '__' . $download[0]['original_filename'];

                if (get_param_integer('save__deps_files', 1) == 1) {
                    $full_path = get_custom_file_base() . '/' . urldecode($download[0]['url']);
                    tar_add_file($STATIC_EXPORT_TAR, 'files/' . strval($download[0]['id']) . '__' . $download[0]['original_filename'], $full_path, 0644, filemtime($full_path), true, true);
                }
            } else {
                $new_url = $download[0]['url'];
            }

            break;
    }
    return '"' . $new_url . '"';
}

/**
 * Make a filename ASCII, for archive/cross-platform portability.
 *
 * @param  string $filename The filename
 * @return string The ASCII version
 */
function _static_export_make_ascii(string $filename) : string
{
    for ($i = 0; $i < strlen($filename); $i++) {
        if ((ord($filename[$i]) < 32) || (ord($filename[$i]) > 127)) {
            $filename[$i] = '_';
        }
    }

    return $filename;
}
