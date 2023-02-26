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
class js_icon_use_test_set extends cms_test_case
{
    public function testLangReferences()
    {
        foreach (['javascript', 'javascript_custom'] as $subdir) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (cms_strtolower_ascii(substr($file, -3)) == '.js') {
                    $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                    $matches = [];
                    $num_matches = preg_match_all('#\$cms\.ui\.setIcon\(\w+, \'([^\']+)\'(, \'\{\$IMG;,\{\$\?,\{\$THEME_OPTION,use_monochrome_icons\},icons_monochrome,icons\}/([^\']+)\}\'\))?#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $icon = $matches[1][$i];
                        $url_part = $matches[2][$i];
                        $icon_in_url = $matches[3][$i];

                        $this->assertTrue($url_part != '', 'Incorrect or missing URL component to setIcon call in ' . $file . ' for ' . $icon);

                        if ($url_part != '') {
                            $this->assertTrue($icon == $icon_in_url, 'Mismatched URL component to setIcon call in ' . $file . ' for ' . $icon);
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}
