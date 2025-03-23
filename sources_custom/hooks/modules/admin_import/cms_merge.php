<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

 /*FORCE_ORIGINAL_LOAD_FIRST*/

class Hx_import_cms_merge extends Hook_import_cms_merge
{
    /**
     * Standard importer hook info function.
     *
     * @return ?array Importer handling details, including lists of all the import types covered (import types are not necessarily the same as actual tables) (null: importer is disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('import')) {
            return null;
        }
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        $info = parent::info();
        if ($info === null) {
            return null;
        }

        if (addon_installed('cms_homesite_tracker')) {
            $info['import'][] = 'mantis';
            $info['dependencies']['mantis'] = ['cns_members'];
        }

        return $info;
    }

    /**
     * Standard import function.
     * WARNING: We do not support merging for the issue tracker (we have to maintain issue numbers)! This will instead replace the issue tracker data on the new site with the old site (and re-map members).
     *
     * @param  object $db The database connector to import from
     * @param  string $table_prefix The table prefix the target prefix is using
     * @param  PATH $file_base The base directory we are importing from
     */
    public function import_mantis(object $db, string $table_prefix, string $file_base)
    {
        // Table to migrate => array of user ID columns that need re-mapped
        $to_migrate = [
            'mantis_user_table' => ['id'], // Must be done first

            'mantis_api_token_table' => ['user_id'],
            'mantis_bugnote_table' => ['reporter_id'],
            'mantis_bugnote_text_table' => [],
            'mantis_bug_file_table' => ['user_id'],
            'mantis_bug_history_table' => ['user_id'],
            'mantis_bug_monitor_table' => ['user_id'],
            'mantis_bug_relationship_table' => [],
            'mantis_bug_revision_table' => ['user_id'],
            'mantis_bug_table' => ['reporter_id', 'handler_id'],
            'mantis_bug_tag_table' => ['user_id'],
            'mantis_bug_text_table' => [],
            'mantis_category_table' => ['user_id'],
            'mantis_config_table' => ['user_id'],
            'mantis_custom_field_project_table' => [],
            'mantis_custom_field_string_table' => [],
            'mantis_custom_field_table' => [],
            'mantis_email_table' => [],
            'mantis_filters_table' => ['user_id'],
            'mantis_news_table' => ['poster_id'],
            'mantis_plugin_table' => [],
            'mantis_project_file_table' => ['user_id'],
            'mantis_project_hierarchy_table' => [],
            'mantis_project_table' => [],
            'mantis_project_user_list_table' => ['user_id'],
            'mantis_project_version_table' => [],
            'mantis_sponsorship_table' => ['user_id'],
            'mantis_tag_table' => ['user_id'],
            'mantis_tokens_table' => ['owner'],
            'mantis_user_pref_table' => ['user_id'],
            'mantis_user_print_pref_table' => ['user_id'],
            'mantis_user_profile_table' => ['user_id'],
        ];

        // These tables cannot accept guest IDs if a member is missing, so skip records whose member does not exist
        $no_guest_tables = [
            'mantis_user_table',
            'mantis_project_user_list_table',
            'mantis_user_pref_table',
            'mantis_user_print_pref_table',
            'mantis_user_profile_table',
            'mantis_bug_monitor_table',
        ];

        foreach ($to_migrate as $table => $columns) {
            // Empty the Mantis table on our end if we have not done so already
            if (!import_check_if_imported('mantis_table', $table)) {
                $GLOBALS['SITE_DB']->query('DELETE FROM ' . $table, null, 0, false, true);
                import_id_remap_put('mantis_table', $table, 0);
            }

            $max = 50;
            $start = 0;
            do {
                $rows = $db->query('SELECT * FROM ' . $table, $max, $start);
                if ($rows === null) {
                    return;
                }

                foreach ($rows as $row) {
                    if (import_check_if_imported('mantis_table__' . $table, md5(serialize($row)))) {
                        continue;
                    }

                    // Add into our site carefully
                    $part_a = [];
                    $part_b = [];
                    $part_c = [];
                    foreach ($row as $key => $value) {
                        // Use our new member ID instead of the old one, or map to guest if we are allowed to and cannot find a member
                        if (in_array($key, $columns)) {
                            $new_id = import_id_remap_get('member', strval($row[$key]), true);
                            if ($new_id !== null) {
                                $value = $new_id;
                            } elseif (in_array($table, $no_guest_tables)) {
                                import_id_remap_put('mantis_table__' . $table, md5(serialize($row)), 0);
                                continue 2; // Do not even insert this record at all
                            } else {
                                $value = 1;
                            }
                        }

                        // Prepare for parameterisation
                        $part_a[] = $key;
                        $part_b[] = '{key_' . strval($key) . '}';
                        $part_c['key_' . strval($key)] = $value;
                    }
                    $GLOBALS['SITE_DB']->query_parameterised('INSERT INTO ' . $table . ' (' . implode(', ', $part_a) . ') VALUES (' . implode(', ', $part_b) . ')', $part_c);

                    import_id_remap_put('mantis_table__' . $table, md5(serialize($row)), 0);
                }

                $start += $max;
            } while (($rows !== null) && (count($rows) > 0));
        }
    }
}
