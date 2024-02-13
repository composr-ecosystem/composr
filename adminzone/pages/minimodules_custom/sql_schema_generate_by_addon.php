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

/*
Used to generate a database schema in the form of SQL code that can be imported into MySQL Workbench

First run this, then run SQLEditor on the files created in uploads/website_specific.
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('meta_toolkit', $error_msg)) {
    return $error_msg;
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Generate database schema, by addon';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => '20abeb186755f6e74d755586dcea3c56', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

cms_ini_set('ocproducts.xss_detect', '0');

require_code('database_relations');

$all_tables_detailed = get_all_innodb_tables();

$tables_by_addon = get_innodb_tables_by_addon();

$file_array = [];

foreach ($tables_by_addon as $addon_name => $tables_in_addon) {
    $tables_in_addon_detailed = [];
    foreach ($tables_in_addon as $table_in_addon) {
        if (array_key_exists($table_in_addon, $all_tables_detailed)) {
            $tables_in_addon_detailed[$table_in_addon] = $all_tables_detailed[$table_in_addon];
        }
        // else not installed
    }

    $data = get_innodb_table_sql($tables_in_addon_detailed, $all_tables_detailed);

    $file_array[] = [
        'time' => time(),
        'data' => $data,
        'name' => 'composr_erd__' . $addon_name . '.sql',
    ];
}

$filename = 'erd_sql__by_addon.zip';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');

cms_ob_end_clean();
cms_ini_set('ocproducts.xss_detect', '0');

require_code('zip');
create_zip_file('php://output', $file_array);

$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
