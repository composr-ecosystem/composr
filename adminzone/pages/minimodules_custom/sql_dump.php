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

/*EXTRA FUNCTIONS: shell_exec*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$intended_db_type = get_param_string('type', get_db_type());

// Where to save dump
$out_filename = 'dump_' . uniqid('', true) . '.sql';
$out_file_path = get_custom_file_base() . '/safe_mode_temp/' . $out_filename;

// Generate dump
$done = false;
if ((php_function_allowed('shell_exec')) && (strpos(get_db_type(), 'mysql') !== false) && (strpos($intended_db_type, 'mysql') !== false)) {
    $cmd = 'mysqldump -h' . get_db_site_host() . ' -u' . get_db_site_user() . ' -p' . get_db_site_password() . ' ' . get_db_site() . ' 2>&1';
    $cmd .= ' > ' . $out_file_path;
    $msg = shell_exec($cmd);
    if (($msg == '') && (filesize($out_file_path) == 0)) {
        $done = true;
    }
}
if (!$done) {
    require_code('database_relations');

    $out_file = fopen($out_file_path, 'wb');
    get_sql_dump($out_file, true, false, null, null, null, $intended_db_type);
    fclose($out_file);
}

// Headers
if (!isset($_GET['testing'])) {
    $filename = 'composr-' . get_site_name() . '.' . date('Y-m-d') . '.sql';
    header('Content-Type: application/octet-stream' . '; authoritative=true;');
    header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');
} else {
    header('Content-type: text/plain; charset=' . get_charset());
}

// Output
cms_ob_end_clean();
readfile($out_file_path);

// Delete
@unlink($out_file_path);

// No screen needed
$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
