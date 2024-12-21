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
class git_conflicts_test_set extends cms_test_case
{
    public function testNoConflicts()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
        disable_php_memory_limit();

        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php', 'tpl', 'css', 'js', 'xml', 'txt', 'sh']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            if (in_array($path, [
                'sources/diff/Diff3.php', // because it does its own diffing of conflicts
                'sources/diff/Diff/ThreeWay.php', // because it does its own diffing of conflicts
                'tracker/vendor/guzzlehttp/guzzle/src/MessageFormatter.php', // Debug code
            ])) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $this->assertTrue(strpos($c, '<<<' . '<') === false, $path);
        }
    }
}
