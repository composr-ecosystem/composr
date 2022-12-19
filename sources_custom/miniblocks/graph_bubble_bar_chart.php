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

$x_axis_label = empty($map['x_axis_label']) ? '' : $map['x_axis_label'];
$y_axis_label = empty($map['y_axis_label']) ? '' : $map['y_axis_label'];
$z_axis_label = empty($map['z_axis_label']) ? '' : $map['z_axis_label'];
$title = empty($map['title']) ? '' : $map['title'];

$show_data_labels = !isset($map['show_data_labels']) ? true : ($map['show_data_labels'] == '1');

$color_pool = empty($map['color_pool']) ? [] : explode(',', $map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/bubble_bar_chart.csv' : $map['file'];

$myfile = fopen(get_custom_file_base() . '/' . $file, 'rb');

$header = fgetcsv($myfile);

$sheet_data = [];
while (($line = fgetcsv($myfile)) !== false) {
    if (implode('', $line) != '') {
        $sheet_data[] = $line;
    }
}

$datasets = [];
foreach ($sheet_data as $line) {
    $num_datasets = count($line);

    $datapoints = [];
    for ($i = 0; $i < $num_datasets - 1; $i++) {
        $datapoints[$header[$i + 1]] = isset($line[$i + 1]) ? $line[$i + 1] : '';
    }

    $datasets[] = [
        'label' => $line[0],
        'datapoints' => $datapoints,
    ];
}
fclose($myfile);

$options = ['show_data_labels' => $show_data_labels];
if (!empty($map['id'])) {
    $options['id'] = $map['id'];
}

$tpl = graph_bubble_bar_chart($datasets, $x_axis_label, $y_axis_label, $z_axis_label, $title, $options, $color_pool, $width, $height);
$tpl->evaluate_echo();
