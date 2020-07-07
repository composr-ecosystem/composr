<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    theme_debug
 */

/*
This script will spot when a theme has bits of default theme CSS that is not adjusted for that theme's seed.
It could fix CSS upgraded via diff, or just poor copy-and-pasting of code back from the default theme CSS files.

Take BACKUPs before running this.

TODO: Merge into ":theme_debug" #3361
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('theme_debug', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Theme Wizard theme repair', false);
$title->evaluate_echo();

require_code('themewizard');
require_code('themes2');

$theme = get_param_string('theme', null);
if ($theme === null) {
    echo '<p>You must pick a theme&hellip;</p><ul>';

    require_code('themes2');
    $themes = find_all_themes();
    $cnt = 0;
    foreach (array_keys($themes) as $theme) {
        if ($theme != 'default') {
            echo '<li><a href="' . static_evaluate_tempcode(build_url(['page' => 'fix_partial_themewizard_css', 'theme' => $theme], 'adminzone')) . '">' . escape_html($theme) . '</a></li>';
            $cnt++;
        }
    }
    if ($cnt == 0) {
        echo '<li>(No non-default themes)</li>';
    }

    echo '</ul>';

    return;
}

$seed = find_theme_seed($theme);
$dark = find_theme_dark($theme);

$default_seed = get_param_string('force_default_seed', find_theme_seed('default'));

if ($default_seed == $seed) {
    warn_exit('Theme has same seed as default theme, cannot continue, would be a no-op.');
}

list($canonical_theme_map, $canonical_theme_landscape) = calculate_theme($default_seed, 'default', 'equations', 'colours', false);

list($theme_map, $theme_landscape) = calculate_theme($seed, 'default', 'equations', 'colours', $dark);

// ===
// CSS
// ===

echo '<p>Made changes for:</p><ul>';

$dh = opendir(get_file_base() . '/themes/default/css');
while (($sheet = readdir($dh)) !== false) {
    if (substr($sheet, -4) == '.css') {
        $saveat = get_custom_file_base() . '/themes/' . filter_naughty($theme) . '/css_custom/' . $sheet;

        if (!file_exists($saveat)) {
            copy(get_file_base() . '/themes/default/css/' . $sheet, $saveat);
            fix_permissions($saveat);
            sync_file($saveat);
        }

        $output = cms_file_get_contents_safe($saveat, FILE_READ_LOCK | FILE_READ_BOM);
        $before = $output;

        foreach ($canonical_theme_landscape as $peak) {
            $matches = [];
            $num_matches = preg_match_all('#\#[A-Fa-f0-9]{6}(.*)' . preg_quote($peak[2], '#') . '#', $output, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                if (cms_strtolower_ascii($matches[0][$i]) == cms_strtolower_ascii('#' . $peak[3] . $matches[1][$i] . $peak[2])) { // i.e. unaltered in our theme
                    foreach ($theme_landscape as $new_peak) { // Try and find the new-seeded solution to this particular equation
                        if ($new_peak[2] == $peak[2]) {
                            $output = str_replace([cms_strtoupper_ascii($matches[0][$i]), cms_strtolower_ascii($matches[0][$i])], ['#' . $new_peak[3] . $matches[1][$i] . $new_peak[2], '#' . $new_peak[3] . $matches[1][$i] . $new_peak[2]], $output);
                            break;
                        }
                    }
                }
            }
        }

        if ($output != $before) {
            require_code('files');
            cms_file_put_contents_safe($saveat, $output, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);

            echo '<li>' . escape_html($sheet) . '</li>';
        }
    }
}
closedir($dh);

echo '</ul><p>Finished CSS.</p>';

// =================
// Make theme images
// =================

echo '<p>Making missing theme images</p><ul>';

load_themewizard_params_from_theme('default');

global $THEMEWIZARD_IMAGES, $THEMEWIZARD_IMAGES_NO_WILD, $THEME_IMAGES_CACHE;
if (function_exists('imagecolorallocatealpha')) {
    require_code('themes2');
    require_code('themes3');
    require_code('abstract_file_manager');
    $full_img_set = [];
    foreach ($THEMEWIZARD_IMAGES as $expression) {
        if (substr($expression, -1) == '*') {
            $expression = substr($expression, 0, strlen($expression) - 2); // remove "/*"
            $full_img_set = array_merge($full_img_set, array_keys(get_all_image_codes(get_file_base() . '/' . filter_naughty('default') . '/default/images', $expression)));
            $full_img_set = array_merge($full_img_set, array_keys(get_all_image_codes(get_file_base() . '/themes/' . filter_naughty('default') . '/images/' . fallback_lang(), $expression)));
        } else {
            $full_img_set[] = $expression;
        }
    }

    $temp_all_ids = collapse_2d_complexity('id', 'url', $GLOBALS['SITE_DB']->query_select('theme_images', ['id', 'url'], ['theme' => $theme]));

    foreach ($full_img_set as $image_code) {
        if (!in_array($image_code, $THEMEWIZARD_IMAGES_NO_WILD)) {
            if ((array_key_exists($image_code, $temp_all_ids)) && (strpos($temp_all_ids[$image_code], $theme . '/images_custom/') !== false) && ((!url_is_local($temp_all_ids[$image_code])) || (file_exists(get_custom_file_base() . '/' . $temp_all_ids[$image_code])))) {
                continue;
            }

            $orig_url = find_theme_image($image_code, true, true, 'default', 'EN');
            if ($orig_url == '') {
                continue; // Theme has specified non-existent image as themewizard-compatible
            }

            if (strpos($orig_url, '/' . fallback_lang() . '/') !== false) {
                $composite = 'themes/' . filter_naughty($theme) . '/images/EN/';
            } else {
                $composite = 'themes/' . filter_naughty($theme) . '/images/';
            }
            afm_make_directory($composite, true);
            $saveat = get_custom_file_base() . '/' . $composite . $image_code . '.png';
            $saveat_url = $composite . $image_code . '.png';
            if (!file_exists($saveat)) {
                $image = calculate_theme($seed, 'default', 'equations', $image_code, $dark, $theme_map, $theme_landscape, 'EN');
                if ($image !== null) {
                    $pos = strpos($image_code, '/');
                    if (($pos !== false) || (strpos($orig_url, '/EN/') !== false)) {
                        afm_make_directory($composite . substr($image_code, 0, $pos), true, true);
                    }
                    require_code('images');
                    cms_imagesave($image, $saveat) or intelligent_write_error($saveat);
                    imagedestroy($image);
                    actual_edit_theme_image($image_code, $theme, 'EN', $image_code, $saveat_url, true);

                    echo '<li>' . escape_html($image_code) . '</li>';
                }
            }
        }
    }
}

echo '</ul><p>Finished theme images.</p>';
