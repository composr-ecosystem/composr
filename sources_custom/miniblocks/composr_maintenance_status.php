<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite')) {
    return do_template('RED_ALERT', ['_GUID' => 'rltg3g7ssx2l3oux03qnqnwhwgj8vrcs', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
}

$nonbundled_addons = isset($map['include_non_bundled']) ? $map['include_non_bundled'] : 'exclude';

require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv', null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);

$header_row = $sheet_reader->read_row(); // Header row
unset($header_row[0]);

$rows = [];
while (($row = $sheet_reader->read_row()) !== false) {
    $codename = $row[0];
    unset($row[0]);
    $data = array_values($row);

    if (($nonbundled_addons != 'include') && (cms_strtolower_ascii($data[4]) == 'yes')) {
        continue;
    }

    $rows[$codename] = ['DATA' => $data, 'CODENAME' => $codename];
}

$sheet_reader->close();

cms_mb_ksort($rows, SORT_NATURAL | SORT_FLAG_CASE);

return do_template('BLOCK_COMPOSR_MAINTENANCE_STATUS', [
    '_GUID' => '8c7ba3e7a2c667e7eebf36b9fe067868',
    'HEADER_ROW' => array_values($header_row),
    'ROWS' => $rows,
]);
