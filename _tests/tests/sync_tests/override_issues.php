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
class override_issues_test_set extends cms_test_case
{
    public function testOverrideIssues()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                '_tests/tests/sync_tests/override_issues.php',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $_c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $this->assertTrue((strpos($_c, 'function  ') === false) && (strpos($_c, "function\t") === false), 'Problematic function definition will cause Composr override system issues: ' . $path);

            if ((strpos($path, '_custom') === false) && (!in_array($path, ['sources/bootstrap.php', 'sources/global.php', 'sources/global2.php']))) {
                if (strpos($_c, 'function init__') !== false) {
                    $this->assertTrue((strpos($_c, "\n    define(") === false), '\'define\' commands need a defined guard, so whole code file can be overridden naively, where init function will run twice: ' . $path);
                }
            }
        }
    }
}
