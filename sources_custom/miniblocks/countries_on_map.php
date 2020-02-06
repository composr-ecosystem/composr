<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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

$intensity_label = @cms_empty_safe($map['intensity_label']) ? 'Intensity' : $map['intensity_label'];

$color_pool = empty($map['color_pool']) ? null : explode(',', $map['color_pool']);

$show_labels = !empty($map['show_labels']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/countries_on_map.csv' : $map['file'];

$data = [];
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
while (($line = $sheet_reader->read_row()) !== false) {
    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    $data[] = [
        'region' => $line[0],
        'intensity' => @cms_empty_safe($line[1]) ? '' : $line[1],
        'description' => implode(',', array_slice($line, 2)),
    ];
}
$sheet_reader->close();

$tpl = countries_on_map($data, $intensity_label, $color_pool, $show_labels, null, $width, $height);
$tpl->evaluate_echo();
