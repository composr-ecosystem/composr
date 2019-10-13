<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite')) {
    return do_template('RED_ALERT', array('_GUID' => 'rltg3g7ssx2l3oux03qnqnwhwgj8vrcs', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))));
}

$myfile = fopen(get_file_base() . '/data/maintenance_status.csv', 'rb');
// TODO: #3467
// TODO: #3032 (must default charset to utf-8 if no BOM though)

$header_row = fgetcsv($myfile); // Header row
unset($header_row[0]);

$rows = array();
while (($row = fgetcsv($myfile)) !== false) {
    $codename = $row[0];
    unset($row[0]);
    $rows[$codename] = array('DATA' => array_values($row), 'CODENAME' => $codename);
}
cms_mb_ksort($rows, SORT_NATURAL | SORT_FLAG_CASE);

fclose($myfile);

return do_template('BLOCK_COMPOSR_MAINTENANCE_STATUS', array(
    '_GUID' => '8c7ba3e7a2c667e7eebf36b9fe067868',
    'HEADER_ROW' => array_values($header_row),
    'ROWS' => $rows,
));
