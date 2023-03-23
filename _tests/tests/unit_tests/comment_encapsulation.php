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
class comment_encapsulation_test_set extends cms_test_case
{
    public function testEncapsulation()
    {
        require_code('files2');

        foreach (['javascript' => 'js', 'javascript_custom' => 'js', 'css' => 'css', 'css_custom' => 'css'] as $subdir => $suffix) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $files = get_directory_contents($path, '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, [$suffix]);
            foreach ($files as $file) {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                if (strpos($c, '/*{$,parser hint: pure}*/') !== false) {
                    continue;
                }

                $c = preg_replace('#/\*.*\*/#Us', '', $c);

                $matches = [];
                $num_matches = preg_match_all('#\{\+#', $c, $matches, PREG_OFFSET_CAPTURE);
                for ($i = 0; $i < $num_matches; $i++) {
                    $data = $matches[0][$i][0];
                    $offset = $matches[0][$i][1];

                    $line = substr_count(substr($c, 0, $offset), "\n") + 1;

                    $this->assertTrue(false, 'Missing comment encapsulation in themes/default/' . $subdir . '/' . $file . ' on line ' . strval($line));
                }
            }
        }
    }
}
