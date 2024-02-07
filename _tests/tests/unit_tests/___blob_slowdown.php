<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// Test finds possible slow-downs due to bulk querying potentially large blobby fields out of the database

// Also can incidentally find references to tables that don't exist anymore.

// This test is not expected to pass! It's a guide for manual code review.

/**
 * Composr test case class (unit testing).
 */
class ___blob_slowdown_test_set extends cms_test_case
{
    public function testBlobSlowdown()
    {
        // Config
        $return_known_unsafe = true;
        $return_abstract_table = true;

        $tables_status = ['db_meta' => true, 'db_meta_indices' => true];
        $tables_safe = [];
        $tables_unsafe = [];
        $fields = $GLOBALS['SITE_DB']->query_select('db_meta', ['m_table', 'm_type']);
        foreach ($fields as $field) {
            $table = $field['m_table'];
            $type = $field['m_type'];

            $this_unsafe = (substr($type, 0, 5) == 'LONG_');

            if ($this_unsafe) {
                $tables_status[$table] = false;
            } elseif (!array_key_exists($table, $tables_status)) {
                $tables_status[$table] = true;
            }
        }
        foreach ($tables_status as $table => $status) {
            // Actually we'll ignore the above calculations, because I know better! (there are lots of long fields not likely to contain very long data)
            if (in_array($table, [
                'f_topics',
                'translate',
                'f_posts',
                'autosave',
                'cached_comcode_pages',
                'catalogue_efv_long',
                'catalogue_efv_long_trans',
                'logged_mail_messages',
                'cache',
                'digestives_tin',
                'f_welcome_emails',
                'calendar_events',
                'catalogue_categories',
                'download_categories',
                'download_downloads',
                'galleries',
                'images',
                'videos',
                'news',
                'newsletter_archive',
                'quizzes',
                'f_usergroup_sub_mails',
                'wiki_pages',
                'wiki_posts',
                'bookable',
            ])) {
                $status = false;
            } else {
                $status = true;
            }

            if ($status) {
                $tables_safe[] = $table;
            } else {
                $tables_unsafe[] = $table;
            }
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

        require_code('third_party_code');
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php']);
        foreach ($files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                // For forum drivers we don't know safeness so would be too many false-positives
                'sources/forum',
                'sources_custom/forum',
                '_tests',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            if (strpos($path, 'import') !== false) {
                continue;
            }

            if (strpos($path, 'export') !== false) {
                continue;
            }

            $c = file_get_contents(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#query(_select|_value)?.*\*.*#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $line_orig = $matches[0][$i];
                $line = trim($line_orig);

                $line = str_replace('translate_field_ref', '', $line); // Causes false-positives

                if (stripos($line, 'select') === false) {
                    continue; // Not a select query
                }

                if (strpos($line, 'query_select_value') !== false) {
                    continue; // Implicitly single row
                }

                if (strpos($line, 'COUNT(*)') !== false) {
                    continue; // COUNTing is fine
                }

                if ((strpos($line, 'DISTINCT') !== false) || (strpos($line, 'SUM(') !== false) || (strpos($line, 'AVG(') !== false) || (strpos($line, 'MIN(') !== false) || (strpos($line, 'MAX(') !== false)) {
                    continue; // Must be false-positive
                }

                if (preg_match('#, 1[^\d]#', $line) != 0) {
                    continue; // If limited to querying one record then no real issue
                }

                $tables_safe_ref = [];
                $tables_unsafe_ref = [];
                foreach ($tables_safe as $table) {
                    if (preg_match('#[^a-z_]' . $table . '[^a-z_]#', $line) != 0) {
                        $tables_safe_ref[] = $table;
                    }
                }
                foreach ($tables_unsafe as $table) {
                    if (preg_match('#[^a-z_]' . $table . '[^a-z_]#', $line) != 0) {
                        $tables_unsafe_ref[] = $table;
                    }
                }
                $num_tables_safe_ref = count($tables_safe_ref);
                $num_tables_unsafe_ref = count($tables_unsafe_ref);

                if (($num_tables_safe_ref > 0) && ($num_tables_unsafe_ref == 0)) {
                    continue; // Table contains no blobs
                }

                if (!$return_known_unsafe) {
                    if ($num_tables_unsafe_ref > 0) {
                        continue; // Known unsafe but we're configured to not be interested
                    }
                }

                if (!$return_abstract_table) {
                    if (($num_tables_safe_ref == 0) && ($num_tables_unsafe_ref == 0)) {
                        continue; // Cannot be confirmed safe but we're configured to not be interested
                    }
                }

                $line_num = substr_count($c, "\n", 0, strpos($c, $line_orig)) + 1;

                if (get_param_integer('geany_syntax', 0) == 1) {
                    $message = 'geany ' . escapeshellarg($path) . ':' . strval($line_num);
                } else {
                    $message = '"' . $path . '" ' . strval($line_num) . ' 1 ' . $line . ' [tables_safe_ref=' . implode(',', $tables_safe_ref) . ', tables_unsafe_ref=' . implode(',', $tables_unsafe_ref) . ']';
                }
                $this->assertTrue(false, $message);
            }
        }
    }
}
