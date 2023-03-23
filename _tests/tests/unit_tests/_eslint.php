<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _eslint_test_set extends cms_test_case
{
    public function testESLint()
    {
        cms_set_time_limit(120);

        if (strpos(shell_exec('npx eslint -v'), 'v') === false) {
            $this->assertTrue(false, 'eslint not available');
            return;
        }

        $current_path = '?';
        $path = get_file_base() . '/themes/default/';
        $result = shell_exec('eslint ' . escapeshellarg($path) . ' 2>&1');
        if (!empty($result)) {
            foreach (explode("\n", $result) as $line) {
                if (substr($line, 0, strlen($path)) == $path) {
                    $current_path = substr($line, strlen(get_file_base()) + 1);
                }

                $matches = [];
                if (preg_match('#^\s*(\d+):\d+\s+\w+\s+(.*)$#', $line, $matches) != 0) {
                    $this->assertTrue(false, $current_path . ':' . $matches[1] . ' -- ' . $matches[2]);
                }
            }
        }
    }
}
