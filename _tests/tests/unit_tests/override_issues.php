<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class override_issues_test_set extends cms_test_case
{
    public function testOverrideIssues()
    {
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, array('php'));
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            if ($path == '_tests/tests/unit_tests/override_issues.php') {
                continue;
            }
            if (preg_match('#^tracker/#', $path) != 0) {
                continue;
            }

            $_c = file_get_contents(get_file_base() . '/' . $path);

            $this->assertTrue((strpos($_c, 'function  ') === false) && (strpos($_c, "function\t") === false), 'Problematic function definition will cause Composr override system issues: ' . $path);

            if ((strpos($path, '_custom') === false) && (!in_array($path, array('sources/global.php', 'sources/global2.php')))) {
                if (strpos($_c, 'function init__') !== false) {
                    $this->assertTrue((strpos($_c, "\n    define(") === false), '\'define\' commands need a defined guard, so whole code file can be overridden naively, where init function will run twice: ' . $path);
                }
            }
        }
    }
}
