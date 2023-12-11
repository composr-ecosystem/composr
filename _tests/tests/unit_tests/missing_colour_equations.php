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
class missing_colour_equations_test_set extends cms_test_case
{
    public function testMissingColourEquations()
    {
        $dont_check = array(
            'commandr.css',
            'install.css',
            'widget_plupload.css',
            'widget_color.css',
            'widget_date.css',
            'widget_select2.css',
            'phpinfo.css',
            'jquery_ui.css',
        );

        $dh = opendir(get_file_base() . '/themes/default/css');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.css') {
                if (in_array($f, $dont_check)) {
                    continue;
                }

                $contents = file_get_contents(get_file_base() . '/themes/default/css/' . $f);
                $matches = array();
                $count = preg_match_all('/^.+(\#[0-9A-Fa-f]{3,6}).*$/m', $contents, $matches);
                for ($i = 0; $i < $count; $i++) {
                    if (strpos($matches[0][$i], '{$') === false) {
                        $line = substr_count(substr($contents, 0, strpos($contents, $matches[0][$i])), "\n") + 1;
                        $this->assertTrue(false, 'Missing colour equation in ' . $f . ':' . strval($line) . ' for ' . $matches[1][$i]);
                    }
                }
            }
        }
    }
}
