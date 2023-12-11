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
class web_platform_test_set extends cms_test_case
{
    public function testNoBadRegexp()
    {
        $c = file_get_contents(get_file_base() . '/web.config');
        $this->assertTrue(strpos($c, '\\_') === false, 'Apache allows any character to be escaped, IIS only allows ones that must be');
    }

    public function testNoBadComments()
    {
        $c = file_get_contents(get_file_base() . '/web.config');
        $this->assertTrue(strpos($c, '<--') === false, 'Comments must be <!--');
    }

    public function testNoDuplicateNames()
    {
        $c = file_get_contents(get_file_base() . '/web.config');
        $matches = array();
        $names = array();
        $num_matches = preg_match_all('#name="([^"]*)"#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $names[] = $matches[1][$i];
        }
        $this->assertTrue($names == array_unique($names), 'Names in web.config must be unique');
    }
}
