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
class path_references_test_set extends cms_test_case
{
    public function testPathReferences()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $regexps = [
            '#\'([^\':/]+/[^\':]+\.\w+)\'#',
            '#\"([^\":/]+/[^\":]+\.\w+)\'#',
        ];

        require_code('third_party_code');
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php', 'tpl']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            $missing_paths = [];
            foreach ($regexps as $regexp) {
                $num_matches = preg_match_all($regexp, $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $_path = urldecode($matches[1][$i]);
                    if (!file_exists(get_file_base() . '/' . $_path)) {
                        // We can't test if every reference exists, as we don't know what is not supposed to exist automatically (for many reasons) -- but we can find case sensitivity issues
                        $this->assertTrue(!file_exists(get_file_base() . '/' . cms_strtolower_ascii($_path)) && !file_exists(get_file_base() . '/' . cms_strtoupper_ascii($_path)), $_path . ' has case sensitivity issues');
                        $missing_paths[$path] = $_path;
                    }
                }
            }
            if (count($missing_paths) > 0) {
                $this->dump($missing_paths, 'Possible missing file references [file containing reference => reference]:');
            }
        }
    }
}
