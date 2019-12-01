<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    theme_debug
 */

// IDEA: #3360 #2971 Find if conflicting theme images, e.g. foo.png and foo.jpg in the same directory. We have an automated test for this right now, so share the code.

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('theme_debug', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Theme repair tools', false);
$title->evaluate_echo();

echo '<p>Pick a theme&hellip;</p><ul class="spaced-list">';

push_query_limiting(false);

require_code('images');

// Find default images
$default_images = [];
foreach ([get_file_base() . '/themes/default/images', get_file_base() . '/themes/default/images_custom', get_file_base() . '/themes/default/images/EN'] as $dir) {
    $dh = opendir($dir);
    while (($f = readdir($dh)) !== false) {
        if (is_image($f, IMAGE_CRITERIA_NONE)) {
            $img_code = basename($f, '.' . get_file_extension($f));
            $default_images[$img_code] = true;
        }
    }
    closedir($dh);
}

// Go through each theme
require_code('themes2');
require_code('files2');
$themes = find_all_themes();
if (count($themes) == 1) {
    warn_exit(do_lang_tempcode('NO_ENTRIES'));
}
foreach (array_keys($themes) as $theme) {
    if ($theme != 'default') {
        require_code('permissions2');
        set_global_category_access('theme', $theme);

        echo '<li>';
        echo '<a href="' . static_evaluate_tempcode(build_url(['page' => '', 'keep_theme_test' => '1', 'keep_theme' => $theme], '')) . '">' . escape_html($theme) . '</a><br />
            <span class="associated-details">(Other useful views:
                <a href="' . static_evaluate_tempcode(build_url(['page' => '', 'keep_theme_test' => '1', 'keep_theme' => $theme, 'keep_theme_seed' => '664422', 'keep_theme_algorithm' => 'hsv', 'keep_theme_source' => $theme], '')) . '">with brown seed</a> /
                <a href="' . static_evaluate_tempcode(build_url(['page' => '', 'keep_theme_test' => '1', 'keep_theme' => $theme, 'keep_su' => 'Guest'], '')) . '">as Guest</a> /
                <a href="' . static_evaluate_tempcode(build_url(['page' => 'cms_news', 'type' => 'add', 'keep_theme_test' => '1', 'keep_theme' => $theme], 'cms')) . '">Add News Form</a> /
                <a href="' . static_evaluate_tempcode(build_url(['page' => 'news', 'keep_theme_test' => '1', 'type' => 'view', 'id' => 1, 'keep_theme' => $theme], 'site')) . '">News screen</a> /
                <a href="' . static_evaluate_tempcode(build_url(['page' => '', 'keep_theme_test' => '1', 'keep_theme' => $theme], 'forum')) . '">forumview</a>)
            </span>';

        // Find unreferenced images
        $images = [];
        $theme_images = get_directory_contents(get_custom_file_base() . '/themes/' . $theme . '/images_custom');
        foreach ($theme_images as $f) {
            if (is_image($f, IMAGE_CRITERIA_NONE)) {
                $img_code = substr($f, 0, strlen($f) - 1 - strlen(get_file_extension($f)));
                if (isset($images[$img_code])) {
                    echo '<br />Duplicated theme image: ' . escape_html($img_code);
                }
                $images[$img_code] = true;
            }
        }
        $images_copy = $images + $default_images;
        $missing_images = [];
        $selectors = [];
        $non_css_contents = '';
        foreach ([
            get_file_base() . '/themes/default/css_custom' => false,
            get_file_base() . '/themes/default/templates_custom' => false,
            get_file_base() . '/themes/default/css' => false,
            get_file_base() . '/themes/default/templates' => false,
            get_file_base() . '/themes/' . $theme . '/css_custom' => true,
            get_file_base() . '/themes/' . $theme . '/templates_custom' => true,
            get_file_base() . '/site/pages/comcode_custom/EN' => true,
            get_file_base() . '/pages/comcode_custom/EN' => true,
        ] as $dir => $do_checks) {
            $dh = @opendir($dir);
            if ($dh !== false) {
                while (($f = readdir($dh)) !== false) {
                    if (
                        (substr($f, -4) == '.css') ||
                        (substr($f, -4) == '.tpl') ||
                        ((substr($f, -4) == '.txt') && ((count($themes) < 5) || (substr($f, 0, strlen($theme . '__')) == $theme . '__')))
                    ) {
                        $contents = cms_file_get_contents_safe($dir . '/' . $f, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                        if (substr($f, -4) != '.css' || !$do_checks) {
                            $non_css_contents .= $contents;
                        }

                        if (!$do_checks) {
                            continue;
                        }

                        // Let's do a few simple CSS checks, less than a proper validator would do
                        if ((substr($f, -4) == '.css') && (strpos($contents, '{$,parser hint: external}') === false)) {
                            // Test comment/brace balancing
                            if (substr_count($contents, '{') != substr_count($contents, '}')) {
                                echo '<br />Mismatched braces in ' . escape_html($f);
                            }
                            if (substr_count($contents, '/*') != substr_count($contents, '*/')) {
                                echo '<br />Mismatched comments in ' . escape_html($f);
                            }

                            // Test selectors
                            $matches = [];
                            $num_matches = preg_match_all('#^\s*[^@\s].*[^%\s]\s*\{$#m', $contents, $matches); // @ is media rules, % is keyframe rules. Neither wanted.
                            for ($i = 0; $i < $num_matches; $i++) {
                                $matches2 = [];
                                $num_matches2 = preg_match_all('#[\w\-]+#', preg_replace('#"[^"]*"#', '', preg_replace('#[:@][\w\-]+#', '', $matches[0][$i])), $matches2);
                                for ($j = 0; $j < $num_matches2; $j++) {
                                    $selectors[$matches2[0][$j]] = true;
                                }
                            }
                        }

                        // Find missing images
                        $matches = [];
                        $num_matches = preg_match_all('#\{\$IMG.?,([^{}]+)\}#', $contents, $matches);
                        for ($i = 0; $i < $num_matches; $i++) {
                            if ((!isset($images_copy[$matches[1][$i]])) && (strpos($matches[1][$i], '/') === false/*we assume subdir images are fine, as non-default ones won't usually be in subdirs and we have not written recursive search code*/)) {
                                $missing_images[$matches[1][$i]] = true;
                            }
                        }

                        // See if any of the theme images were used
                        foreach (array_keys($images) as $image) {
                            if (strpos($contents, ',' . $image . '}') !== false) {
                                unset($images[$image]);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }

        foreach (array_keys($images) as $image) {
            echo '<br />Possibly unused theme image: ' . escape_html($image);
        }

        foreach (array_keys($missing_images) as $image) {
            echo '<br />Missing theme image: ' . escape_html($image);
        }

        ksort($selectors);
        foreach (array_keys($selectors) as $selector) {
            if ((strpos($non_css_contents, $selector) === false) && (preg_match('#^(page|zone)_running_#', $selector) == 0)) {
                echo '<br />Possibly unused CSS selector: ' . escape_html($selector);
            }
        }

        echo '</li>';
    }
}

echo '</ul>';
