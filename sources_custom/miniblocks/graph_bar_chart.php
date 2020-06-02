<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    visualisation
 */

$error_msg = new Tempcode();
if (!addon_installed__messaged('visualisation', $error_msg)) {
    return $error_msg;
}

require_code('graphs');

$width = empty($map['width']) ? null : $map['width'];
$height = empty($map['height']) ? null : $map['height'];

$x_axis_label = @cms_empty_safe($map['x_axis_label']) ? '' : $map['x_axis_label'];
$y_axis_label = @cms_empty_safe($map['y_axis_label']) ? '' : $map['y_axis_label'];

$begin_at_zero = !empty($map['begin_at_zero']);
$show_data_labels = !empty($map['show_data_labels']);

$color_pool = @cms_empty_safe($map['color_pool']) ? [] : explode(',', $map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/bar_chart.csv' : $map['file'];

$datapoints = [];
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
while (($line = $sheet_reader->read_row()) !== false) {
    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    $datapoints[] = [
        'label' => $line[0],
        'value' => $line[1],
        'tooltip' => implode(',', array_slice($line, 2)),
    ];
}
$sheet_reader->close();

$tpl = graph_bar_chart($datapoints, $x_axis_label, $y_axis_label, $begin_at_zero, $show_data_labels, $color_pool, $width, $height);
$tpl->evaluate_echo();
