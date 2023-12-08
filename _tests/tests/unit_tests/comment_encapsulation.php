<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class comment_encapsulation_test_set extends cms_test_case
{
    public function testEncapsulation()
    {
        require_code('files2');

        foreach (array('javascript' => '.js', 'javascript_custom' => '.js', 'css' => '.css', 'css_custom' => '.css') as $subdir => $suffix) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $files = get_directory_contents($path);
            foreach ($files as $file) {
                if (substr($file, -4) == $suffix) {
                    $contents = file_get_contents($path . '/' . $file);

                    $matches = array();
                    $num_matches = preg_match_all('#\{\+#', $contents, $matches, PREG_OFFSET_CAPTURE);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $data = $matches[0][$i][0];
                        $offset = $matches[0][$i][1];

                        $line = substr_count(substr($contents, 0, $offset), "\n") + 1;

                        $this->assertTrue(substr($contents, $offset - 2, 2) == '/*', 'Missing comment encapsulation in themes/default/' . $subdir . '/' . $file . ' on line ' . strval($line));
                    }
                }
            }
        }
    }
}
