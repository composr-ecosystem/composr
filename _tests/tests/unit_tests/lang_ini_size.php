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
class lang_ini_size_test_set extends cms_test_case
{
    public function testMaxSize()
    {
        $path = get_file_base() . '/lang/' . fallback_lang();
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.ini') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
                $this->assertTrue(substr_count($c, "\n") < 980, $file . ' is too big'); // default max_input_vars=1000
            }
        }
        closedir($dh);
    }
}
