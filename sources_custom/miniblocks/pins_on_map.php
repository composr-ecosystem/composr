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

require_code('maps');

$width = empty($map['width']) ? null : $map['width'];
$height = empty($map['height']) ? null : $map['height'];

$color_pool = @cms_empty_safe($map['color_pool']) ? null : _parse_color_pool_string($map['color_pool']);

$file = empty($map['file']) ? 'uploads/website_specific/graph_test/pins_on_map.csv' : $map['file'];

$data = [];
require_code('files_spreadsheets_read');
$sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/' . $file, null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);
while (($line = $sheet_reader->read_row()) !== false) {
    if (substr($line[0], 0, 1) == '#') {
        continue; // Comment line
    }

    if (count($line) < 2) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('ec9709b15d3c5218a180a0a7dd83a01a')));
    }

    $data[] = [
        'latitude' => $line[0],
        'longitude' => $line[1],
        'intensity' => @cms_empty_safe($line[2]) ? '' : $line[2],
        'label' => empty($line[3]) ? '' : $line[3],
        'description' => implode(',', array_slice($line, 4)),
    ];
}
$sheet_reader->close();

$tpl = pins_on_map($data, $color_pool, null, $width, $height);
$tpl->evaluate_echo();
