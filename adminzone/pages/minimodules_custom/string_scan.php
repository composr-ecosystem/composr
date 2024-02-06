<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('meta_toolkit', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Categorise language strings', false);
$title->evaluate_echo();

require_code('string_scan');

$lang = get_param_string('lang', fallback_lang());
if (!does_lang_exist($lang)) {
    $lang = fallback_lang();
}
list($just_lang_strings_admin, $just_lang_strings_non_admin, $lang_strings_shared, $lang_strings_unknown, $all_strings_in_lang, $strings_files) = string_scan($lang);

require_all_lang();

$langs = find_all_langs();
echo '<p>Show completeness for a language:</p><ul>';
foreach (array_keys($langs) as $lang) {
    echo '<li><a href="' . escape_html(get_self_url(true, false, ['lang' => $lang])) . '">' . escape_html($lang) . '</a></li>';
}
echo '</ul>';

echo '<p>These are known admin language strings:</p><ul class="spaced-list">';
foreach ($just_lang_strings_admin as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li><abbr title="' . escape_html(do_lang($str)) . '">' . escape_html($str) . '</abbr> (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are known non-admin language strings:</p><ul class="spaced-list">';
foreach ($just_lang_strings_non_admin as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li><abbr title="' . escape_html(do_lang($str)) . '">' . escape_html($str) . '</abbr> (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are shared language strings:</p><ul class="spaced-list">';
foreach ($lang_strings_shared as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li><abbr title="' . escape_html(do_lang($str)) . '">' . escape_html($str) . '</abbr> (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are strings of unknown status:</p><ul class="spaced-list">';
foreach ($lang_strings_unknown as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li><abbr title="' . escape_html(do_lang($str)) . '">' . escape_html($str) . '</abbr> (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '
    <h2>Summary</h2>
    <dl>
        <dt>Admin:</dt>
        <dd>' . escape_html(integer_format(count($just_lang_strings_admin))) . '</dd>

        <dt>Non-Admin:</dt>
        <dd>' . escape_html(integer_format(count($just_lang_strings_non_admin))) . '</dd>

        <dt>Shared:</dt>
        <dd>' . escape_html(integer_format(count($lang_strings_shared))) . '</dd>

        <dt>Unknown:</dt>
        <dd>' . escape_html(integer_format(count($lang_strings_unknown))) . '</dd>
    </dl>
';
