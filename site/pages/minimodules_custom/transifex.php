<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    transifex
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Composr translations, via Transifex', false);

require_code('transifex');
require_code('lang2');

$project_slug = 'composr-cms-' . str_replace('.', '-', strval(cms_version()));

$test = cache_and_carry('_transifex', array('/project/' . $project_slug . '/languages/', 'GET', null, false), 10);
if (is_string($test)) {
    $test = unserialize($test);
}
if ($test[1] == '200') {
    $languages = list_to_map('language_code', json_decode($test[0], true));
} else {
    $languages = array();
}

$_languages = array();

foreach ($languages as $language_code => $language_details_basic) {
    if ($language_details_basic['reviewers'] == array() && $language_details_basic['translators'] == array()) {
        continue; // Not started yet
    }

    $language_name = lookup_language_full_name(strtoupper($language_code));

    $test = cache_and_carry('_transifex', array('/project/' . $project_slug . '/language/' . $language_code . '/?details', 'GET', null, false), 10);
    if (is_string($test)) {
        $test = unserialize($test);
    }
    if ($test[1] == '200') {
        $language_details = json_decode($test[0], true);

        $percentage = intval(round(100.0 * $language_details['translated_segments'] / $language_details['total_segments'])); // calculate %age

        $download_url = find_script('transifex_pull');
        $download_url .= '?lang=' . urlencode(strtoupper($language_code));
        $download_url .= '&output=1';

        $download_core_url = find_script('transifex_pull');
        $download_core_url .= '?lang=' . urlencode(strtoupper($language_code));
        $download_core_url .= '&core_only=1';
        $download_core_url .= '&output=1';

        $_languages[str_pad(strval($percentage), 3, '0', STR_PAD_LEFT) . '__' . $language_code] = array(
            'LANGUAGE_CODE' => strtoupper($language_code),
            'LANGUAGE_NAME' => $language_name,
            'TRANSLATORS' => implode(', ', array_merge($language_details_basic['reviewers'], $language_details_basic['translators'])),
            'PERCENTAGE' => integer_format($percentage) . '%',
            'DOWNLOAD_URL' => $download_url,
            'DOWNLOAD_CORE_URL' => $download_core_url,
        );
    }
}

ksort($_languages);
$_languages = array_reverse($_languages);

return do_template('TRANSIFEX_SCREEN', array(
    '_GUID' => '56c6b6d32f1794be3114a1b95f0a7ec5',
    'TITLE' => $title,
    'LANGUAGES' => $_languages,
));
