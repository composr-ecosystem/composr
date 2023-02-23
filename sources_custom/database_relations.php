<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

/**
 * Find how tables might be ignored for backups etc.
 * This is mainly used for building automated tests that make sure things are consistently implemented.
 *
 * @return array List of tables and their status regarding being ignored for backups etc
 */
function get_table_purpose_flags() : array
{
    $ret = non_overridden__get_table_purpose_flags();

    if (!addon_installed('meta_toolkit')) {
        return $ret;
    }

    $more = [
        'activities' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'bank' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'bookable' => TABLE_PURPOSE__NORMAL,
        'bookable_blacked' => TABLE_PURPOSE__NORMAL,
        'bookable_blacked_for' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under bookable*/,
        'bookable_codes' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under bookable*/,
        'bookable_supplement' => TABLE_PURPOSE__NORMAL,
        'bookable_supplement_for' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under bookable*/,
        'booking' => TABLE_PURPOSE__NORMAL,
        'booking_supplement' => TABLE_PURPOSE__NORMAL,
        'cached_weather_codes' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__NO_BACKUPS | TABLE_PURPOSE__FLUSHABLE,
        'ecom_classifieds_prices' => TABLE_PURPOSE__NORMAL,
        'community_billboard' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'content_read' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'credit_charge_log' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'credit_purchases' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'device_token_details' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'diseases' => TABLE_PURPOSE__NORMAL,
        'giftr' => TABLE_PURPOSE__NORMAL,
        'group_points' => TABLE_PURPOSE__NORMAL,
        'locations' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__AUTOGEN_STATIC,
        'logged' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'mail_opt_sync_queue' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'may_feature' => TABLE_PURPOSE__NORMAL,
        'members_diseases' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'members_gifts' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'members_mentors' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'referees_qualified_for' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'referrer_override' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'reported_content' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under <content>*/,
        'sites' => TABLE_PURPOSE__NORMAL,
        'sites_advert_pings' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'sites_deletion_codes' => TABLE_PURPOSE__NORMAL,
        'sites_email' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under sites*/,
        'tutorials_external' => TABLE_PURPOSE__NORMAL,
        'tutorials_external_tags' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under tutorials_external*/,
        'tutorials_internal' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE | TABLE_PURPOSE__AUTOGEN_STATIC,
        'workflow_approval_points' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under workflows*/,
        'workflow_content' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under <content>*/,
        'workflow_content_status' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under <content>*/,
        'workflow_permissions' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under workflows*/,
        'workflows' => TABLE_PURPOSE__NORMAL,
        'w_attempts' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'w_inventory' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'w_itemdef' => TABLE_PURPOSE__NORMAL,
        'w_items' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'w_members' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'w_messages' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'w_portals' => TABLE_PURPOSE__NORMAL,
        'w_realms' => TABLE_PURPOSE__NORMAL,
        'w_rooms' => TABLE_PURPOSE__NORMAL,
        'w_travelhistory' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'translation_cache' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'hybridauth_content_map' => TABLE_PURPOSE__NORMAL,
        'patreon_patrons' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
    ];
    foreach ($more as $table => $flags) {
        $ret[$table] = $flags | TABLE_PURPOSE__NON_BUNDLED;
    }
    return $ret;
}

/**
 * Get a map of table descriptions.
 *
 * @return array Map of table descriptions
 */
function get_table_descriptions() : array
{
    $ret = non_overridden__get_table_descriptions();

    if (!addon_installed('meta_toolkit')) {
        return $ret;
    }

    $more = [
    ];
    return $ret + $more;
}

/**
 * Get a map of foreign key relations.
 *
 * @return array Map of foreign key relations
 */
function get_relation_map() : array
{
    $ret = non_overridden__get_relation_map();

    if (!addon_installed('meta_toolkit')) {
        return $ret;
    }

    $more = [
    ];
    return $ret + $more;
}

/*
The following code is strictly intended for building up a *FAKE* InnoDB schema for the
database.

It is not intended for real-world backups.
*/

function get_all_innodb_tables()
{
    $_tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['*']);
    $all_tables = [];
    foreach ($_tables as $t) {
        if (!isset($all_tables[$t['m_table']])) {
            $all_tables[$t['m_table']] = [];
        }

        $all_tables[$t['m_table']][$t['m_name']] = $t['m_type'];
    }
    unset($_tables);

    ksort($all_tables);

    $all_tables['anything'] = ['id' => '*ID_TEXT'];

    return $all_tables;
}

function get_innodb_tables_by_addon()
{
    $tables = collapse_1d_complexity('m_table', $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table']));
    $tables = array_fill_keys($tables, '1');

    $hooks = find_all_hooks('systems', 'addon_registry');
    $tables_by = [];
    foreach ($hooks as $hook => $hook_type) {
        if ((strpos($hook_type, '_custom') !== false) && (get_param_integer('include_custom', 0) == 0)) {
            continue;
        }

        require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($hook));
        $object = object_factory('Hook_addon_registry_' . filter_naughty_harsh($hook));
        $files = $object->get_file_list();
        $addon_name = $hook;
        foreach ($files as $path) {
            if ((strpos($path, 'blocks/') !== false) || (strpos($path, 'pages/modules') !== false) || (strpos($path, 'hooks/systems/addon_registry') !== false)) {
                if (!is_file(get_file_base() . '/' . $path)) {
                    continue;
                }

                $file_contents = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);

                $matches = [];
                $num_matches = preg_match_all("#create_table\('([^']+)'#", $file_contents, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $table_name = $matches[1][$i];
                    if (strpos($file_contents, "/*\$GLOBALS['SITE_DB']->create_table('" . $table_name . "'") === false) {
                        if ($table_name == 'group_page_access') {
                            $addon_name = 'core';
                        }
                        if ($table_name == 'group_zone_access') {
                            $addon_name = 'core';
                        }

                        if (!isset($tables_by[$addon_name])) {
                            $tables_by[$addon_name] = [];
                        }
                        $tables_by[$addon_name][] = $table_name;
                        unset($tables[$table_name]);
                    }
                }
            }
        }
    }

    foreach (array_keys($tables) as $table) {
        if (substr($table, 0, 2) == 'f_') {
            $tables_by['core_cns'][] = $table;
        } else {
            $tables_by['core'][] = $table;
        }
    }

    ksort($tables_by);

    return $tables_by;
}

function get_innodb_table_sql($tables, $all_tables)
{
    $out = '';

    $relations = [];
    $relation_map = get_relation_map();

    $db = $GLOBALS['SITE_DB'];
    $table_prefix = $db->get_table_prefix();

    require_code('database_helper');
    require_code('database/mysqli');
    $db_static = object_factory('Database_Static_mysqli', false, [$table_prefix]);

    for ($loop_it = 0; $loop_it < count($tables); $loop_it++) { // Loops over $tables, which is growing as we pull in tables needed due to foreign key references
        $tables_keys = array_keys($tables);
        $tables_values = array_values($tables);

        $table_name = $tables_keys[$loop_it];

        if ($table_name == 'translate') {
            continue; // Only used in multi-lang mode, which is the exception
        }
        if (table_has_purpose_flag($table_name, TABLE_PURPOSE__NON_BUNDLED) && get_param_integer('include_custom', 0) == 0) {
            continue;
        }

        $fields = $tables_values[$loop_it];

        $keys = [];

        if (!is_array($fields)) { // Error
            @print($out);
            @var_dump($fields);
            exit();
        }

        foreach ($fields as $field => $type) {
            if (strpos($type, '*') !== false) {
                $keys[] = $field;
            }
            if (isset($relation_map[$table_name . '.' . $field])) {
                $relations[$table_name . '.' . $field] = $relation_map[$table_name . '.' . $field];
            }
            if (strpos($type, 'MEMBER') !== false) {
                $relations[$table_name . '.' . $field] = 'f_members.id';
            }
            if (strpos($type, 'GROUP') !== false) {
                $relations[$table_name . '.' . $field] = 'f_groups.id';
            }
            /*if (strpos($type, 'TRANS') !== false) {   We don't bother showing this anymore
                $relations[$table_name . '.' . $field] = 'translate.id';
            }*/
            if ((strpos($field, 'author') !== false) && ($type == 'ID_TEXT') && ($table_name != 'authors') && ($field != 'block_author') && ($field != 'module_author')) {
                $relations[$table_name . '.' . $field] = 'authors.author';
            }

            if (isset($relations[$table_name . '.' . $field])) {
                $mapped_table = preg_replace('#\..*$#', '', $relations[$table_name . '.' . $field]);
                if (!isset($tables[$mapped_table])) {
                    $tables[$mapped_table] = $all_tables[$mapped_table];
                }
            }
        }
        $save_bytes = _helper_needs_to_save_bytes($table_name, $fields);
        $queries = $db_static->create_table__sql($table_prefix . $table_name, $fields, $db->connection_write, $table_name, $save_bytes);
        foreach ($queries as $sql) {
            $sql = str_replace('MyISAM', 'InnoDB', $sql);
            $out .= $sql . ";\n";
        }

        $out .= "\n";
    }

    foreach ($relations as $from => $to) {
        $from_table = preg_replace('#\..*$#', '', $from);
        $to_table = preg_replace('#\..*$#', '', $to);
        $from_field = preg_replace('#^.*\.#', '', $from);
        $to_field = preg_replace('#^.*\.#', '', $to);
        $source_id = strval(array_search($from_table, array_keys($tables)));
        $target_id = strval(array_search($to_table, array_keys($tables)));
        $out .= "\nCREATE INDEX `{$from}` ON {$table_prefix}{$from_table}({$from_field});\n";
        $out .= "ALTER TABLE {$table_prefix}{$from_table} ADD FOREIGN KEY `{$from}` ({$from_field}) REFERENCES {$table_prefix}{$to_table} ({$to_field});\n";
    }

    return $out;
}
