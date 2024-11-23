<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
$clamp_y_axis = !isset($map['clamp_y_axis']) ? false : intval($map['clamp_y_axis']);
$logarithmic = !isset($map['logarithmic']) ? false : ($map['logarithmic'] == '1');

$wordwrap_tooltip_at = !isset($map['wordwrap_tooltip_at']) ? null : intval($map['wordwrap_tooltip_at']);

$color_pool = @cms_empty_safe($map['color_pool']) ? [] : _parse_color_pool_string($map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/bar_chart.csv' : $map['file'];

$datapoints = [];
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
while (($line = $sheet_reader->read_row()) !== false) {
    if (substr($line[0], 0, 1) == '#') {
        continue; // Comment line
    }

    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('2c574da92f325a0384bb885dfc55e4c1')));
    }

    $tooltip = implode("\n", array_slice($line, 2));
    if ($wordwrap_tooltip_at !== null) {
        $tooltip = wordwrap($tooltip, $wordwrap_tooltip_at);
    }

    $datapoints[] = [
        'label' => $line[0],
        'value' => $line[1],
        'tooltip' => $tooltip,
    ];
}
$sheet_reader->close();

$options = ['begin_at_zero' => $begin_at_zero, 'show_data_labels' => $show_data_labels, 'horizontal' => $horizontal, 'clamp_y_axis' => $clamp_y_axis, 'wordwrap_tooltip_at' => $wordwrap_tooltip_at, 'logarithmic' => $logarithmic];
if (!empty($map['id'])) {
    $options['id'] = $map['id'];
}

$tpl = graph_bar_chart($datapoints, $x_axis_label, $y_axis_label, $options, $color_pool, $width, $height);
$tpl->evaluate_echo();
