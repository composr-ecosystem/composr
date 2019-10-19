<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sortable_tables
 */

$error_msg = new Tempcode();
if (!addon_installed__messaged('sortable_tables', $error_msg)) {
    return $error_msg;
}

require_code('maps');

$width = empty($map['width']) ? null : $map['width'];
$height = empty($map['height']) ? null : $map['height'];

$color_pool = empty($map['color_pool']) ? null : explode(',', $map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/pins_on_map.csv' : $map['file'];

$data = array();
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
while (($line = $sheet_reader->read_row()) !== false) {
    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    $data[] = array(
        'latitude' => $line[0],
        'longitude' => $line[1],
        'intensity' => empty($line[2]) ? '' : $line[2],
        'label' => empty($line[3]) ? '' : $line[3],
        'description' => implode(',', array_slice($line, 4)),
    );
}
$sheet_reader->close();

$tpl = pins_on_map($data, $color_pool, null, $width, $height);
$tpl->evaluate_echo();
