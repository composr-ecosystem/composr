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

cms_ini_set('auto_detect_line_endings', '1'); // TODO: Remove with #3032
$myfile = fopen(get_custom_file_base() . '/' . $file, 'rb');
// TODO: #3032
$data = array();
while (($line = fgetcsv($myfile)) !== false) {
    if (implode('', $line) == '') {
        continue;
    }

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
fclose($myfile);

$tpl = pins_on_map($data, $color_pool, null, $width, $height);
$tpl->evaluate_echo();
