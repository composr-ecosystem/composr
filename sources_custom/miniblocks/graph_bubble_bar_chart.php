<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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

$x_axis_label = empty($map['x_axis_label']) ? '' : $map['x_axis_label'];
$y_axis_label = empty($map['y_axis_label']) ? '' : $map['y_axis_label'];
$z_axis_label = empty($map['z_axis_label']) ? '' : $map['z_axis_label'];
$title = empty($map['title']) ? '' : $map['title'];

$show_data_labels = !isset($map['show_data_labels']) ? true : ($map['show_data_labels'] == '1');

$wordwrap_tooltip_at = !isset($map['wordwrap_tooltip_at']) ? null : intval($map['wordwrap_tooltip_at']);

$color_pool = @cms_empty_safe($map['color_pool']) ? [] : _parse_color_pool_string($map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/bubble_bar_chart.csv' : $map['file'];

require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);

$header = $sheet_reader->read_row();

$sheet_data = [];
while (($line = $sheet_reader->read_row()) !== false) {
    if (implode('', $line) != '') {
        $sheet_data[] = $line;
    }
    if (substr($line[0], 0, 1) == '#') {
        continue; // Comment line
    }
}

$sheet_reader->close();

$datasets = [];
foreach ($sheet_data as $line) {
    $num_datasets = count($line);

    if (empty($header[$num_datasets - 1])) {
        $tooltip = $line[$num_datasets - 1];
        if ($wordwrap_tooltip_at !== null) {
            $tooltip = wordwrap($tooltip, $wordwrap_tooltip_at);
        }

        $num_datasets--;
    } else {
        $tooltip = '';
    }

    $datapoints = [];
    for ($i = 0; $i < $num_datasets - 1; $i++) {
        $datapoints[$header[$i + 1]] = isset($line[$i + 1]) ? $line[$i + 1] : '';
    }

    $datasets[] = [
        'label' => $line[0],
        'tooltip' => $tooltip,
        'datapoints' => $datapoints,
    ];
}

$options = ['show_data_labels' => $show_data_labels, 'wordwrap_tooltip_at' => $wordwrap_tooltip_at];
if (!empty($map['id'])) {
    $options['id'] = $map['id'];
}

$tpl = graph_bubble_bar_chart($datasets, $x_axis_label, $y_axis_label, $z_axis_label, $title, $options, $color_pool, $width, $height);
$tpl->evaluate_echo();
