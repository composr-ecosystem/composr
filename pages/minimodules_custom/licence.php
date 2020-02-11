<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_homesite', $error_msg)) {
    return $error_msg;
}

$text = cms_file_get_contents_safe(get_file_base() . '/text/EN/licence.txt', FILE_READ_LOCK | FILE_READ_BOM);

$text = preg_replace('#(^|\n)(.*)\n=+\r?\n#', '${1}[title]${2}[/title]' . "\n", $text);
$text = preg_replace('#(^|\n)(.*)\n\-+\r?\n#', '${1}[title="2"]${2}[/title]' . "\n", $text);

require_code('comcode');
echo static_evaluate_tempcode(comcode_to_tempcode($text));
