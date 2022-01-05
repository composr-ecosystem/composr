<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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

$begin_at_zero = !isset($map['begin_at_zero']) ? true : ($map['begin_at_zero'] == '1');
$show_data_labels = !isset($map['show_data_labels']) ? true : ($map['show_data_labels'] == '1');
$horizontal = !isset($map['horizontal']) ? false : ($map['horizontal'] == '1');
$stacked = !isset($map['stacked']) ? true : ($map['stacked'] == '1');

$color_pool = empty($map['color_pool']) ? [] : explode(',', $map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/stacked_bar_chart.csv' : $map['file'];

require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);

$header = $sheet_reader->read_row();
$num_datasets = count($header) - 1;

$sheet_data = [];
while (($line = $sheet_reader->read_row()) !== false) {
    if (implode('', $line) != '') {
        $sheet_data[] = $line;
    }
}

$labels = [];

$datasets = [];
for ($i = 0; $i < $num_datasets; $i++) {
    $datapoints = [];

    foreach ($sheet_data as $line) {
        $datapoints[$line[0]] = $line[$i + 1];

        if ($i == 0) {
            $labels[] = $line[0];
        }
    }

    arsort($datapoints);

    $datasets[] = [
        'label' => $header[$i + 1],
        'datapoints' => $datapoints,
    ];
}
$sheet_reader->close();

$options = ['begin_at_zero' => $begin_at_zero, 'show_data_labels' => $show_data_labels, 'horizontal' => $horizontal, 'stacked' => $stacked];

$tpl = graph_stacked_bar_chart($datasets, $labels, $x_axis_label, $y_axis_label, $options, $color_pool, $width, $height);
$tpl->evaluate_echo();
