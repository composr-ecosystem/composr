<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class scripts_test_set extends cms_test_case
{
    public function testReferences()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            $num_matches = preg_match_all('#find_script\(\'(\w+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $script = $matches[1][$i];
                $this->assertTrue(file_exists(preg_replace('#^' . preg_quote(get_base_url() . '/') . '#', get_file_base() . '/', find_script($script))), 'Could not find ' . $script);
            }
        }
    }
}
