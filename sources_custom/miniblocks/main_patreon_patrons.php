<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
$_patreon_patrons = [];
foreach ($patreon_patrons as $patron) {
    $_patreon_patrons[] = [
        'NAME' => $patron['name'],
        'USERNAME' => $patron['username'],
        'TIER' => $patron['tier'],
    ];
}

$tpl = do_template('BLOCK_MAIN_PATREON_PATRONS', ['_GUID' => '8b7ed8319aa6ec0e6bc0e8b5e1fede4d', 'PATREON_PATRONS' => $_patreon_patrons]);
$tpl->evaluate_echo();
