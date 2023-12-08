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

/*
Used to generate a database schema in the form of SQL code that can be imported into MySQL Workbench

First run this, then run SQLEditor on the files created in uploads/website_specific.
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('database_relations');

$all_tables_detailed = get_all_innodb_tables();

$tables_by_addon = get_innodb_tables_by_addon();

$file_array = array();

foreach ($tables_by_addon as $addon => $tables_in_addon) {
    $tables_in_addon_detailed = array();
    foreach ($tables_in_addon as $table_in_addon) {
        if (array_key_exists($table_in_addon, $all_tables_detailed)) {
            $tables_in_addon_detailed[$table_in_addon] = $all_tables_detailed[$table_in_addon];
        } // else not installed
    }

    $data = get_innodb_table_sql($tables_in_addon_detailed, $all_tables_detailed);

    $file_array[] = array(
        'time' => time(),
        'data' => $data,
        'name' => 'composr_erd__' . $addon . '.sql',
    );
}

$filename = 'erd_sql__by_addon.zip';
header('Content-Type: application/octet-stream' . '; authoritative=true;');
header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');

require_code('zip');
create_zip_file($file_array, true);

$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();
