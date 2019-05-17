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
class missing_colour_equations_test_set extends cms_test_case
{
    public function testMissingColourEquations()
    {
        require_code('files2');

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

        $files = get_directory_contents(get_file_base() . '/themes/default/css', get_file_base() . '/themes/default/css', null, false, true, array('css'));
        foreach ($files as $path) {
            if (in_array(basename($path), $dont_check)) {
                continue;
            }

            $c = file_get_contents($path);
            $matches = array();
            $count = preg_match_all('/^.+(\#[0-9A-Fa-f]{3,6})(.*)$/m', $c, $matches);
            for ($i = 0; $i < $count; $i++) {
                if (strpos($matches[0][$i], '{$') === false) {
                    $line = substr_count(substr($c, 0, strpos($c, $matches[0][$i])), "\n") + 1;
                    $this->assertTrue(false, 'Missing colour equation in ' . $path . ':' . strval($line) . ' for ' . $matches[1][$i]);
                }
            }
        }
    }
}
