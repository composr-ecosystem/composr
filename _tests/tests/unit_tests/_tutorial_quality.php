<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _tutorial_quality_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND_crawl);
    }

    public function testValidComcode()
    {
        if (in_safe_mode()) {
            return;
        }

        require_code('comcode_check');

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                if ($this->debug) {
                    var_dump($file);
                }

                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_BOM);
                check_comcode($c); // This is quite slow
            }
        }
        closedir($dh);
    }
}
