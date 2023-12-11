<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('database_relations');
$table_descriptions = get_table_descriptions();
$relation_map = get_relation_map();

$tables_by = get_innodb_tables_by_addon();

$title = get_screen_title('Table relations by addon', false);
$title->evaluate_echo();

echo '<div style="background: white">';

foreach ($tables_by as $t => $ts) {
    echo '<h2>';
    echo escape_html($t);
    echo '</h2>';
    sort($ts);
    echo '<ul>';
    foreach ($ts as $table) {
        if (table_has_purpose_flag($table, TABLE_PURPOSE__NON_BUNDLED) && get_param_integer('include_custom', 0) == 0) {
            continue;
        }

        echo '<li>' . escape_html($table);
        if (isset($table_descriptions[$table])) {
            echo ' &ndash; <span style="color: green">' . $table_descriptions[$table] . '</span>';
        }
        echo '<ul>';
        $fields = $GLOBALS['SITE_DB']->query_select('db_meta', array('m_name', 'm_type'), array('m_table' => $table));
        foreach ($fields as $field) {
            $type = str_replace('?', '', str_replace('*', '', $field['m_type']));
            $extra = '';
            if (array_key_exists($table . '.' . $field['m_name'], $relation_map)) {
                $relation = $relation_map[$table . '.' . $field['m_name']];
                $extra .= ' ( &rarr; <strong>' . escape_html(is_null($relation) ? '*' : $relation) . '</strong>)';
            }
            if (strpos($field['m_type'], '*') !== false) {
                $extra .= ' (<span style="text-decoration: underline"">Key field</span>)';
            }
            if (strpos($field['m_type'], '?') !== false) {
                $extra .= ' (<em>May be NULL</em>)';
            }
            echo '<li><strong>' . escape_html($type) . '</strong> ' . escape_html($field['m_name']) . $extra . '</li>';
        }
        echo '</ul>';
        echo '<br /></li>';
    }
    echo '</ul>';
}

echo '<div>';
