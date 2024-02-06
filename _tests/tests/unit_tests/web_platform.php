<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class web_platform_test_set extends cms_test_case
{
    public function testNoBadRegexp()
    {
        $c = cms_file_get_contents_safe(get_file_base() . '/web.config', FILE_READ_LOCK | FILE_READ_BOM);
        $this->assertTrue(strpos($c, '\\_') === false, 'Apache allows any character to be escaped, IIS only allows ones that must be');
    }

    public function testNoBadComments()
    {
        $c = cms_file_get_contents_safe(get_file_base() . '/web.config', FILE_READ_LOCK);
        $this->assertTrue(strpos($c, '<--') === false, 'Comments must be <!-- in web.config');
    }

    public function testNoDuplicateNames()
    {
        $c = cms_file_get_contents_safe(get_file_base() . '/web.config', FILE_READ_LOCK);
        $matches = [];
        $names = [];
        $num_matches = preg_match_all('#name="([^"]*)"#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $names[] = $matches[1][$i];
        }
        $this->assertTrue($names == array_unique($names), 'Names in web.config must be unique');
    }
}
