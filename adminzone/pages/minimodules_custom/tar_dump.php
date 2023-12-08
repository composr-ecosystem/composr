<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

disable_php_memory_limit();
if (php_function_allowed('set_time_limit')) {
    @set_time_limit(0);
}
$GLOBALS['NO_DB_SCOPE_CHECK'] = true;

require_code('tar');

$filename = 'composr-' . get_site_name() . '.' . date('Y-m-d') . '.tar';

header('Content-Type: application/octet-stream' . '; authoritative=true;');
header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');

$tar = tar_open(null, 'wb');

$max_size = get_param_integer('max_size', null);
$subpath = get_param_string('path', '');
tar_add_folder($tar, null, get_file_base() . (($subpath == '') ? '' : '/') . $subpath, $max_size, $subpath, null, null, false, true);

tar_close($tar);

$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
