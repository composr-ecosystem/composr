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

/**
 * Find how tables might be ignored for backups etc.
 * This is mainly used for building automated tests that make sure things are consistently implemented.
 *
 * @return array List of tables and their status regarding being ignored for backups etc
 */
function get_table_purpose_flags() : array
{
    $ret = non_overridden__get_table_purpose_flags();

    if (!addon_installed('meta_toolkit', false, true, true, true)) {
        return $ret;
    }

    $more = [
        'achievements_earned' => TABLE_PURPOSE__NORMAL,
        'achievements_progress' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
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
        'early_access_codes' => TABLE_PURPOSE__NORMAL,
        'early_access_code_content' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__SUBDATA/*under early_access_codes*/,
        'giftr' => TABLE_PURPOSE__NORMAL,
        'group_points' => TABLE_PURPOSE__NORMAL,
        'karma' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'locations' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__AUTOGEN_STATIC,
        'mail_opt_sync_queue' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'may_feature' => TABLE_PURPOSE__NORMAL,
        'members_diseases' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'members_gifts' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'members_mentors' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under f_members*/,
        'referees_qualified_for' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'referrer_override' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE,
        'telemetry_errors' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__NO_BACKUPS | TABLE_PURPOSE__MISC_NO_MERGE | TABLE_PURPOSE__FLUSHABLE,
        'telemetry_errors_ignore' => TABLE_PURPOSE__NORMAL,
        'telemetry_sites' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'telemetry_stats' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
        'reported_content' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE | TABLE_PURPOSE__SUBDATA/*under <content>*/,
        'sites' => TABLE_PURPOSE__NORMAL | TABLE_PURPOSE__FLUSHABLE,
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

    if (!addon_installed('meta_toolkit', false, true, true, true)) {
        return $ret;
    }

    $more = [
        'achievements_earned' => 'a log of the achievements each member unlocked and when',
        'achievements_progress' => 'a cache of progress for each individual qualification / requirement of an achievement; on its own, you cannot derive contextual meaning due to hash-based matching',
        'activities' => 'a log of activities posted in the activity feed',
        'bank' => 'a ledger of transactions and dividends from bankr',
        'bookable' => 'a database of booking events and their metadata',
        'bookable_blacked' => 'a database of date ranges that cannot be booked',
        'bookable_blacked_for' => 'mappings of bookable_blacked to bookable',
        'bookable_codes' => 'individual codes that can be booked in a bookable',
        'bookable_supplement' => 'additional metadata for bookable_codes',
        'bookable_supplement_for' => 'mappings of bookable_supplement to bookable',
        'booking' => 'a database of bookings made by members',
        'booking_supplement' => 'mappings of booking to bookable_supplement',
        'cached_weather_codes' => 'a cache of weather codes and their description',
        'ecom_classifieds_prices' => 'a list of available classified ads and the associated catalogues that can be purchased',
        'community_billboard' => 'community billboard messages that have been purchased by members',
        'content_read' => 'a log of when members have read certain content',
        'credit_charge_log' => 'a log of support credits charged from members',
        'credit_purchases' => 'a log of support credits purchased by members',
        'device_token_details' => 'the IDs of devices signed up for notifications in the mobile SDK',
        'diseases' => 'a database of disastr diseases',
        'early_access_codes' => 'a database of early access codes, to what they grant access, and their settings',
        'early_access_code_content' => 'a mapping of early access codes to the content they grant access',
        'giftr' => 'a database of virtual gifts that can be purchased',
        'group_points' => 'a list of points that are awarded per group membership',
        'karma' => 'a log of karma that has been applied to members',
        'locations' => 'a database of geographical locations',
        'mail_opt_sync_queue' => 'a log of newsletter opt statuses for members to be synced to SugarCRM',
        'may_feature' => 'URLs that have opted in to be featured on the Composr homesite',
        'members_diseases' => 'a log of diseases members caught',
        'members_gifts' => 'a log of virtual gifts exchanged with members',
        'members_mentors' => 'a log of members and their mentor',
        'referees_qualified_for' => 'qualified referrals',
        'referrer_override' => 'overrides for referrals',
        'reported_content' => 'a log of content that has been reported',
        'sites' => 'a list of Demonstratr sites',
        'sites_advert_pings' => 'a log of advertisement pings from Demonstratr sites',
        'sites_deletion_codes' => 'a list of deletion codes for Demonstratr sites',
        'sites_email' => 'a list of emails between Demonstratr sites',
        'telemetry_errors' => 'A log of error messages sent by installed sites to core developers',
        'telemetry_sites' => 'Sites using the software which have registered with the telemetry service to send data',
        'telemetry_stats' => 'Statistics relayed by installed sites',
        'tutorials_external' => 'a list of external tutorials that have been submitted',
        'tutorials_external_tags' => 'tags for external tutorials',
        'tutorials_internal' => 'a list of tutorials written as an internal Comcode page',
        'workflow_approval_points' => 'records which workflows require which points to approve',
        'workflow_content' => 'records which site resources are in which workflows, along with any notes made during the approval process',
        'workflow_content_status' => 'records the status of each approval point for a piece of content and the member who approved the point (if any)',
        'workflow_permissions' => 'stores which usergroups are allowed to approve which points',
        'workflows' => 'a list of workflows',
        'w_attempts' => 'buildr', // TODO: what is this?
        'w_inventory' => 'a log of what items members own in buildr',
        'w_itemdef' => 'available items in buildr',
        'w_items' => 'the location and price of items in buildr',
        'w_members' => 'a log of members and their location / status in buildr',
        'w_messages' => 'chat messages for buildr',
        'w_portals' => 'available portals in buildr',
        'w_realms' => 'available realms in buildr',
        'w_rooms' => 'constructed rooms in buildr',
        'w_travelhistory' => 'a log of where members moved in buildr',
        'translation_cache' => 'cache of translated content',
        'hybridauth_content_map' => 'mapping of Composr content to a Hybridauth provider',
        'patreon_patrons' => 'a list of Patreon patrons',
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

    if (!addon_installed('meta_toolkit', false, true, true, true)) {
        return $ret;
    }

    $more = [
        'bookable_blacked_for.bookable_id' => 'bookable.id',
        'bookable_blacked_for.blacked_id' => 'bookable_blacked.id',
        'bookable_codes.bookable_id' => 'bookable.id',
        'bookable_supplement_for.supplement_id' => 'bookable_supplement.id',
        'bookable_supplement_for.bookable_id' => 'bookable.id',
        'booking.bookable_id' => 'bookable.id',
        'bookable.calendar_type' => 'calendar_types.id',
        'booking.paid_trans_id' => null, // TODO: not implemented
        'booking_supplement.booking_id' => 'booking.id',
        'booking_supplement.supplement_id' => 'bookable_supplement.id',
        'early_access_code_content.a_access_code' => 'early_access_codes.c_access_code',
        'ecom_classifieds_prices.c_catalogue_name' => 'catalogues.c_name',
        'members_diseases.disease_id' => 'diseases.id',
        'members_gifts.gift_id' => 'giftr.id',
        'reported_content.r_session_id' => 'sessions.the_session',
        'telemetry_stats.s_site' => 'telemetry_sites.id',
        'telemetry_errors.e_site' => 'telemetry_sites.id',
        'tutorials_external_tags.t_id' => 'tutorials_external.id',
        'workflow_approval_points.workflow_id' => 'workflows.id',
        'workflow_permissions.workflow_approval_point_id' => 'workflow_approval_points.id',
        'workflow_content.workflow_id' => 'workflows.id',
        'workflow_content_status.workflow_content_id' => 'workflow_content.id',
        'workflow_content_status.workflow_approval_point_id' => 'workflow_approval_points.id',
        // TODO: Buildr
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
