<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    theme_debug
 */

/*
Search for CSS classes used in templates, and check they exist in the default theme CSS files.

This is a general Composr tool for testing out the default theme manually but with some help. theme_debug.php has a theme-specific checker.

TODO: Maybe merge into ":theme_debug"
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('CSS analyser', false);
$title->evaluate_echo();

$used = find_used();
$existing = find_existing();
sort($used);
sort($existing);

echo '<p>The following used CSS classes are not present in CSS...</p>';
echo '<ul>';
foreach (array_diff($used, $existing) as $x) {
    if (strpos($x, 'box___') === false) {
        echo '<li>' . escape_html($x) . '</li>';
    }
}
echo '</ul>';

echo '<p>The following non-used CSS classes are present in CSS (as far as can be told - may well be used by symbolic substitution)...</p>';
echo '<ul>';
foreach (array_diff($existing, $used) as $x) {
    echo '<li>' . escape_html($x) . '</li>';
}
echo '</ul>';

function find_existing()
{
    $out = array();
    $d = opendir(get_file_base() . '/themes/default/css');
    while (($e = readdir($d)) !== false) {
        if (substr($e, -4) == '.css') {
            $contents = file_get_contents(get_file_base() . '/themes/default/css/' . $e);
            $matches = array();
            $found = preg_match_all('#\.([a-z][a-z_\d]*)[ ,:]#', $contents, $matches);
            for ($i = 0; $i < $found; $i++) {
                if ($matches[1][$i] != 'txt') {
                    $out[] = $matches[1][$i];
                }
            }
        }
    }
    closedir($d);
    return array_unique($out);
}

function find_used()
{
    $out = array();
    $d = opendir(get_file_base() . '/themes/default/templates');
    while (($e = readdir($d)) !== false) {
        if (substr($e, -4) == '.tpl') {
            $contents = file_get_contents(get_file_base() . '/themes/default/templates/' . $e);
            $matches = array();
            $found = preg_match_all('#class="([\w ]+)"#', $contents, $matches);
            for ($i = 0; $i < $found; $i++) {
                $out = array_merge($out, explode(' ', $matches[1][$i]));
            }
        }
    }
    closedir($d);
    return array_unique($out);
}
