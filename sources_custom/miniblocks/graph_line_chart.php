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

require_code('graphs');

$width = empty($map['width']) ? null : $map['width'];
$height = empty($map['height']) ? null : $map['height'];

$x_axis_label = empty($map['x_axis_label']) ? '' : $map['x_axis_label'];
$y_axis_label = empty($map['y_axis_label']) ? '' : $map['y_axis_label'];

$begin_at_zero = empty($map['begin_at_zero']) ? true : ($map['begin_at_zero'] == '1');
$show_data_labels = empty($map['show_data_labels']) ? true : ($map['show_data_labels'] == '1');

$color_pool = empty($map['color_pool']) ? array() : explode(',', $map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/line_chart.csv' : $map['file'];

$datasets = array();
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
$x_labels = $sheet_reader->read_row();
array_shift($x_labels); // Irrelevant corner
while (($line = $sheet_reader->read_row()) !== false) {
    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    $label = array_shift($line);
    $datapoints = array();
    $i = 0;
    foreach ($line as $x) {
        if (is_numeric($x)) {
            $datapoints[$i] = array(
                'value' => $x,
            );
            $i++;
        } elseif ($i > 0) {
            $datapoints[$i - 1] += array(
                'tooltip' => $x,
            );
        }
    }

    $datasets[] = array(
        'label' => $label,
        'datapoints' => $datapoints,
    );
}
$sheet_reader->close();

$tpl = graph_line_chart($datasets, $x_labels, $x_axis_label, $y_axis_label, $begin_at_zero, $show_data_labels, $color_pool, $width, $height);
$tpl->evaluate_echo();
