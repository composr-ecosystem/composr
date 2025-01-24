<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    meta_toolkit
 */

/*EXTRA FUNCTIONS: shell_exec*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('meta_toolkit', $error_msg)) {
    return $error_msg;
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Generate SQL schema';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => '1ee815cdaffe1ffe8a805cb6a8947e90', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

$intended_db_type = get_param_string('type', get_db_type());

// Where to save dump
$out_filename = 'dump_' . uniqid('', true) . '.sql';
$out_file_path = cms_tempnam('sql');

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

    require_code('files');
    $out_file = cms_fopen_text_write($out_file_path);
    get_sql_dump($out_file, true, true, false, [], null, null, $intended_db_type);
    fclose($out_file);
}

// Headers
if (!isset($_GET['testing'])) {
    $filename = 'composr-' . get_site_name() . '.' . date('Y-m-d') . '.sql';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');
} else {
    header('Content-Type: text/plain; charset=' . get_charset());
}

// Output
cms_ob_end_clean();
readfile($out_file_path);

// Delete
@unlink($out_file_path);

// No screen needed
$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
