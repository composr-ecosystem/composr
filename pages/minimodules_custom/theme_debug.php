<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    theme_debug
 */

// IDEA: Find if conflicting theme images, e.g. foo.png and foo.jpg in the same directory. We have a unit test for this right now, so share the code.

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Theme repair tools', false);
$title->evaluate_echo();

echo '<p>Pick a theme&hellip;</p><ul class="spaced_list">';

$GLOBALS['NO_QUERY_LIMIT'] = true;

require_code('images');

// Find default images
$default_images = array();
foreach (array(get_custom_file_base() . '/themes/default/images', get_custom_file_base() . '/themes/default/images_custom', get_custom_file_base() . '/themes/default/images/EN') as $dir) {
    $dh = opendir($dir);
    while (($f = readdir($dh)) !== false) {
        if (is_image($f) || substr($f, -4) == '.svg') {
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
        echo '<a href="' . static_evaluate_tempcode(build_url(array('page' => 'start', 'keep_theme_test' => '1', 'keep_theme' => $theme), '')) . '">' . escape_html($theme) . '</a><br />
					<span class="associated_details">(Other useful views:
						<a href="' . static_evaluate_tempcode(build_url(array('page' => 'start', 'keep_theme_test' => '1', 'keep_theme' => $theme, 'keep_theme_seed' => '664422', 'keep_theme_algorithm' => 'hsv', 'keep_theme_source' => $theme), '')) . '">with brown seed</a> /
						<a href="' . static_evaluate_tempcode(build_url(array('page' => 'start', 'keep_theme_test' => '1', 'keep_theme' => $theme, 'keep_su' => 'Guest'), '')) . '">as Guest</a> /
						<a href="' . static_evaluate_tempcode(build_url(array('page' => 'cms_news', 'type' => 'ad', 'keep_theme_test' => '1', 'keep_theme' => $theme), 'cms')) . '">Add News Form</a> /
						<a href="' . static_evaluate_tempcode(build_url(array('page' => 'news', 'keep_theme_test' => '1', 'type' => 'view', 'id' => 1, 'keep_theme' => $theme), 'site')) . '">News screen</a> /
						<a href="' . static_evaluate_tempcode(build_url(array('page' => '', 'keep_theme_test' => '1', 'keep_theme' => $theme), 'forum')) . '">forumview</a>)
					</span>';

        // Find unreferenced images
        $images = array();
        $theme_images = get_directory_contents(get_custom_file_base() . '/themes/' . $theme . '/images_custom');
        foreach ($theme_images as $f) {
            if (is_image($f) || substr($f, -4) == '.svg') {
                $img_code = substr($f, 0, strlen($f) - 1 - strlen(get_file_extension($f)));
                if (isset($images[$img_code])) {
                    echo '<br />Duplicated theme image: ' . escape_html($img_code);
                }
                $images[$img_code] = true;
            }
        }
        $images_copy = $images + $default_images;
        $missing_images = array();
        $selectors = array();
        $non_css_contents = '';
        foreach (array(
                     get_custom_file_base() . '/themes/default/css_custom' => false,
                     get_custom_file_base() . '/themes/default/templates_custom' => false,
                     get_custom_file_base() . '/themes/default/css' => false,
                     get_custom_file_base() . '/themes/default/templates' => false,
                     get_custom_file_base() . '/themes/' . $theme . '/css_custom' => true,
                     get_custom_file_base() . '/themes/' . $theme . '/templates_custom' => true,
                     get_custom_file_base() . '/site/pages/comcode_custom/EN' => true,
                     get_custom_file_base() . '/pages/comcode_custom/EN' => true,
                 ) as $dir => $do_checks) {
            $dh = opendir($dir);
            while (($f = readdir($dh)) !== false) {
                if (
                    (substr($f, -4) == '.css') ||
                    (substr($f, -4) == '.tpl') ||
                    ((substr($f, -4) == '.txt') && ((count($themes) < 5) || (substr($f, 0, strlen($theme . '__')) == $theme . '__')))
                ) {
                    $contents = file_get_contents($dir . '/' . $f);

                    if (substr($f, -4) != '.css' || !$do_checks) {
                        $non_css_contents .= $contents;
                    }

                    if (!$do_checks) {
                        continue;
                    }

                    // Let's do a few simple CSS checks, less than a proper validator would do
                    if (substr($f, -4) == '.css' && strpos($contents, '{$,Parser hint: external}') === false) {
                        // Test comment/brace balancing
                        if (substr_count($contents, '{') != substr_count($contents, '}')) {
                            echo '<br />Mismatched braces in ' . escape_html($f);
                        }
                        if (substr_count($contents, '/*') != substr_count($contents, '*/')) {
                            echo '<br />Mismatched comments in ' . escape_html($f);
                        }

                        // Test selectors
                        $matches = array();
                        $num_matches = preg_match_all('#^\s*[^@\s].*[^%\s]\s*\{$#m', $contents, $matches); // @ is media rules, % is keyframe rules. Neither wanted.
                        for ($i = 0; $i < $num_matches; $i++) {
                            $matches2 = array();
                            $num_matches2 = preg_match_all('#[\w\-]+#', preg_replace('#"[^"]*"#', '', preg_replace('#[:@][\w\-]+#', '', $matches[0][$i])), $matches2);
                            for ($j = 0; $j < $num_matches2; $j++) {
                                $selectors[$matches2[0][$j]] = true;
                            }
                        }
                    }

                    // Find missing images
                    $matches = array();
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

        foreach (array_keys($images) as $image) {
            echo '<br />Possibly unused theme image: ' . escape_html($image);
        }

        foreach (array_keys($missing_images) as $image) {
            echo '<br />Missing theme image: ' . escape_html($image);
        }

        ksort($selectors);
        foreach (array_keys($selectors) as $selector) {
            if (strpos($non_css_contents, $selector) === false && preg_match('#^(page|zone)_running_#', $selector) == 0) {
                echo '<br />Possibly unused CSS selector: ' . escape_html($selector);
            }
        }

        echo '</li>';
    }
}

echo '</ul>';
