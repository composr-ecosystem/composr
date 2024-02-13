<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class permission_modules_test_set extends cms_test_case
{
    public function testPermissionModuleReferences()
    {
        $patterns = [
            "\\\$permissions_module_require = '(\w+)';" => 0,
            "\\\$permissions_module_require_b = '(\w+)';" => 0,
            "has_actual_page_access\([^,()]+(\(\))?, [^,()]+, [^,()]+, \['(\w+)'" => 0,
            "has_category_access\([^,()]+(\(\))?, '(\w+)'" => 0,
            "set_global_category_access\('(\w+)'" => 0,
            "query_delete\('group_category_access', \['module_the_name' => '(\w+)'" => 0,
            "query_delete\('group_privileges', \['module_the_name' => '(\w+)'" => 0,
            "db_string_equal_to\('\w+\.module_the_name', '(\w+)'\)" => 0,
            "_all_members_who_have_enabled_with_category_access\([^,()]+, '(\w+)'" => 0,
            "'permission_module' => '(\w+)'" => 0,
            "'permission_module' => .*\['(\w+)', '\w+'\]" => 0,
            "'permission_module' => .*\['\w+', '(\w+)'\]" => 0,
            "get_category_permissions_for_environment\('(\w+)'" => 0,
            "set_category_permissions_from_environment\('(\w+)'" => 0,
            "check_submit_permission\('\w+', \['(\w+)'" => 0,
            "has_submit_permission\('\w+', [^,()]+(\(\))?, [^,()]+(\(\))?, '\w+', \['(\w+)'" => 0,
            "check_some_edit_permission\('\w+', \['(\w+)'" => 0,
            "check_edit_permission\('\w+', [^,()]+, \['(\w+)'" => 0,
            "has_edit_permission\('\w+', [^,()]+(\(\))?, [^,()]+, '\w+', \['(\w+)'" => 0,
            "check_delete_permission\('\w+', [^,()]+, \['(\w+)'" => 0,
            "has_delete_permission\('\w+', [^,()]+(\(\))?, [^,()]+, '\w+', \['(\w+)'" => 0,
            "has_some_cat_privilege\([^,()]+(\(\))?, '\w+', [^,()]+, '(\w+)'" => 0,
            "has_privilege\([^,()]+(\(\))?, '\w+', [^,()]+, \['(\w+)'" => 0,
            "has_privilege_group\([^,()]+(\(\))?, '\w+', [^,()]+, \['(\w+)'" => 0,
            "load_up_all_module_category_permissions\([^,()]+(\(\))?, '(\w+)'" => 0,
            "get_category_permission_where_clause\('(\w+)'" => 0,
            "check_privilege\('\w+', \['(\w+)'" => 0,
            "set_privilege\([^,()]+, '\w+', [^,()]+, [^,()]+, '(\w+)'" => 0,
        ];

        $results = [];

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            foreach (array_keys($patterns) as $pattern) {
                $matches = [];
                $num_matches = preg_match_all('#' . $pattern . '#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $found = $matches[count($matches) - 1][$i];
                    if (!isset($results[$found])) {
                        $results[$found] = 0;
                    }
                    $results[$found]++;

                    $patterns[$pattern]++;
                }
            }
        }

        ksort($results);

        foreach ($results as $found => $count) {
            $this->assertTrue(in_array($found, [
                'award',
                'banners',
                'calendar',
                'catalogues_catalogue',
                'catalogues_category',
                'chat',
                'downloads',
                'forums',
                'galleries',
                'news',
                'quiz',
                'theme',
                'tickets',
                'wiki_page',
            ]), 'Unknown permissions module: ' . $found . ' found ' . integer_format($count) . 'times');
        }
    }
}
