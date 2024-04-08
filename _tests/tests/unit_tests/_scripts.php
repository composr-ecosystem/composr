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
class _scripts_test_set extends cms_test_case
{
    public function testReferences()
    {
        disable_php_memory_limit();

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php', 'tpl', 'js', 'xml', 'txt']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];

            if (substr($path, -4) == '.php') {
                $num_matches = preg_match_all('#find_script\(\'(\w+)\'#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $script = $matches[1][$i];
                    $this->assertTrue(file_exists(preg_replace('#^' . preg_quote(get_base_url() . '/') . '#', get_file_base() . '/', find_script($script))), 'Could not find ' . $script);
                }
            }

            if (substr($path, -4) == '.tpl' || substr($path, -3) == '.js' || substr($path, -4) == '.xml' || substr($path, -4) == '.txt') {
                $num_matches = preg_match_all('#\{\$FIND_SCRIPT(_NOHTTP)?[^\s\w,]?,(\w+)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $script = $matches[2][$i];
                    $this->assertTrue(file_exists(preg_replace('#^' . preg_quote(get_base_url() . '/') . '#', get_file_base() . '/', find_script($script))), 'Could not find ' . $script);
                }
            }
        }
    }
}
