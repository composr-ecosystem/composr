<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('patreon')) {
    return do_template('RED_ALERT', ['_GUID' => 'h0u9px8wh68nroz053xjgygly0aalnzi', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
}

require_code('patreon');
$level = isset($map['level']) ? intval($map['level']) : 30;
$patreon_patrons = get_patreon_patrons_on_minimum_level($level);

if (empty($patreon_patrons)) {
    // Auto-import if nothing yet
    patreon_sync();
    $patreon_patrons = get_patreon_patrons_on_minimum_level($level);

    // Ok, test data
    if ((empty($patreon_patrons)) && ($GLOBALS['DEV_MODE'])) {
        require_code('lorem');

        $map = [
            'p_member_id' => db_get_first_id() + 1,
            'p_tier' => lorem_word(),
            'p_id' => placeholder_number(),
            'p_monthly' => placeholder_number(),
            'p_name' => lorem_phrase(),
        ];
        $GLOBALS['SITE_DB']->query_insert('patreon_patrons', $map);

        $patreon_patrons = get_patreon_patrons_on_minimum_level($level);
    }
}

$_patreon_patrons = [];
foreach ($patreon_patrons as $patron) {
    $_patreon_patrons[] = [
        'NAME' => $patron['name'],
        'USERNAME' => $patron['username'],
        'MEMBER_ID' => strval($patron['p_member_id']),
        'TIER' => $patron['tier'],
    ];
}

$tpl = do_template('BLOCK_MAIN_PATREON_PATRONS', ['_GUID' => '8b7ed8319aa6ec0e6bc0e8b5e1fede4d', 'PATREON_PATRONS' => $_patreon_patrons]);
$tpl->evaluate_echo();
