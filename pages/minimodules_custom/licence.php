<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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

$licence = cms_file_get_contents_safe(get_file_base() . '/docs/LICENSE.md', FILE_READ_LOCK | FILE_READ_BOM);
$licence = preg_replace('#\((\w+\.md)\)#', '(https://gitlab.com/composr-foundation/composr/-/blob/' . STABLE_BRANCH_NAME . '/docs/$1)', $licence);

require_code('comcode');
echo static_evaluate_tempcode(comcode_to_tempcode($licence));
