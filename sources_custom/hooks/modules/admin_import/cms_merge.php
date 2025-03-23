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
     * NOTE: Actually, we expect that you already migrated all the Mantis data over manually (because we cannot / should not re-map issue IDs, for example).
     * This tool only ensures member IDs are correctly re-mapped.
     *
     * @param  object $db The database connector to import from
     * @param  string $table_prefix The table prefix the target prefix is using
     * @param  PATH $file_base The base directory we are importing from
     */
    public function import_mantis(object $db, string $table_prefix, string $file_base)
    {
        // Table => array of user ID columns to update
        $to_update = [
            'mantis_user_table' => ['id'], // most important

            'mantis_api_token_table' => ['user_id'],
            'mantis_bugnote_table' => ['reporter_id'],
            'mantis_bug_file_table' => ['user_id'],
            'mantis_bug_history_table' => ['user_id'],
            'mantis_bug_monitor_table' => ['user_id'],
            'mantis_bug_revision_table' => ['user_id'],
            'mantis_bug_table' => ['reporter_id', 'handler_id'],
            'mantis_bug_tag_table' => ['user_id'],
            'mantis_category_table' => ['user_id'],
            'mantis_config_table' => ['user_id'],
            'mantis_filters_table' => ['user_id'],
            'mantis_news_table' => ['poster_id'],
            'mantis_project_file_table' => ['user_id'],
            'mantis_project_user_list_table' => ['user_id'],
            'mantis_sponsorship_table' => ['user_id'],
            'mantis_tag_table' => ['user_id'],
            'mantis_tokens_table' => ['owner'],
            'mantis_user_pref_table' => ['user_id'],
            'mantis_user_print_pref_table' => ['user_id'],
            'mantis_user_profile_table' => ['user_id'],
        ];

        $max = 100;
        $start = 0;
        do {
            $rows = $GLOBALS['SITE_DB']->query('SELECT username,id FROM mantis_user_table ORDER BY id', $max, $start);
            if ($rows === null) {
                return;
            }

            foreach ($rows as $row) {
                if (import_check_if_imported('mantis_user', $row['id'])) {
                    continue;
                }

                $new_id = $GLOBALS['SITE_DB']->query_select_value_if_there('f_members', 'id', ['m_username' => $row['username']]);

                // Could not find mapped member; probably deleted so delete / update from Mantis
                if ($new_id === null) {
                    // Delete the member themselves
                    $GLOBALS['SITE_DB']->query_parameterised('DELETE FROM mantis_user_table WHERE id={id}', ['id' => $row['id']], null, 0);
                    $GLOBALS['SITE_DB']->query_parameterised('DELETE FROM mantis_project_user_list_table WHERE user_id={id}', ['id' => $row['id']], null, 0);
                    $GLOBALS['SITE_DB']->query_parameterised('DELETE FROM mantis_user_pref_table WHERE user_id={id}', ['id' => $row['id']], null, 0);
                    $GLOBALS['SITE_DB']->query_parameterised('DELETE FROM mantis_user_print_pref_table WHERE user_id={id}', ['id' => $row['id']], null, 0);
                    $GLOBALS['SITE_DB']->query_parameterised('DELETE FROM mantis_user_profile_table WHERE user_id={id}', ['id' => $row['id']], null, 0);

                    // For everything else, update to guest
                    foreach ($to_update as $table => $columns) {
                        foreach ($columns as $i => $column) {
                            $GLOBALS['SITE_DB']->query_parameterised('UPDATE {table} SET {column}=1 WHERE {column}={old_id}', ['table' => $table, 'column' => $column, 'old_id' => $row['id']], null, 0);
                        }
                    }
                    continue;
                }

                // Update stuff
                foreach ($to_update as $table => $columns) {
                    foreach ($columns as $i => $column) {
                        $GLOBALS['SITE_DB']->query_parameterised('UPDATE {table} SET {column}={new_id} WHERE {column}={old_id}', ['table' => $table, 'column' => $column, 'new_id' => $new_id, 'old_id' => $row['id']], null, 0);
                    }
                }

                import_id_remap_put('mantis_user', strval($row['id']), $new_id);
            }

            $start += $max;
        } while (($rows !== null) && (count($rows) > 0));
    }
}
