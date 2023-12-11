<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        require_code('files');
        require_code('files2');
        $contents = get_directory_contents(get_file_base());
        foreach ($contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_NONBUNDLED_VERY_SCATTERED | IGNORE_BUNDLED_VOLATILE)) && ($c != '_tests/tests/unit_tests/override_issues.php')) {
                $_c = file_get_contents(get_file_base() . '/' . $c);

                $this->assertTrue((strpos($_c, 'function  ') === false) && (strpos($_c, "function\t") === false), 'Problematic function definition will cause Composr override system issues: ' . $c);

                if ((strpos($c, '_custom') === false) && (!in_array($c, array('sources/global.php', 'sources/global2.php')))) {
                    if (strpos($_c, 'function init__') !== false) {
                        $this->assertTrue((strpos($_c, "\n    define(") === false), 'define commands need a defined guard, so whole code file can be overridden naively, where init function will run twice: ' . $c);
                    }
                }
            }
        }
    }
}
