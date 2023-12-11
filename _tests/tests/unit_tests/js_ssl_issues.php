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
class js_ssl_issues_test_set extends cms_test_case
{
    public function testSSLIssues()
    {
        $templates = array();
        $path = get_file_base() . '/themes/default/javascript';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (strtolower(substr($f, -3)) == '.js') {
                $file = file_get_contents($path . '/' . $f);

                $matches = array();
                $num_matches = preg_match_all('#\{\$IMG[;\*]+,(\w+)\}(?!.*protocol.*$)(.*)$#m', $file, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $this->assertTrue(false, $f . '/' . $matches[1][$i] . ' not prepared for SSL');
                }
            }
        }
    }
}
