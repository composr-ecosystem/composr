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
class lang_ini_size_test_set extends cms_test_case
{
    public function testMaxSize()
    {
        $path = get_file_base() . '/lang/' . fallback_lang();
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.ini') {
                $contents = file_get_contents($path . '/' . $f);
                $this->assertTrue(substr_count($contents, "\n") < 980, $f . ' is too big'); // default max_input_vars=1000
            }
        }
        closedir($dh);
    }
}
