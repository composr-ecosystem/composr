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
class js_lang_references_test_set extends cms_test_case
{
    public function testLangReferences()
    {
        $core_ini_files_contents = '';
        foreach ([
            'lang/EN/global.ini',
            'lang_custom/EN/global.ini',
            'lang/EN/critical_error.ini',
            'lang_custom/EN/critical_error.ini',
        ] as $path) {
            if (is_file(get_file_base() . '/' . $path)) {
                $core_ini_files_contents .= cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
            }
        }

        foreach (['javascript', 'javascript_custom'] as $subdir) {
            $path = get_file_base() . '/themes/default/' . $subdir;
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (cms_strtolower_ascii(substr($file, -3)) == '.js') {
                    $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                    $matches = [];
                    $num_matches = preg_match_all('#\{\!(\w+)[\},;^\*]#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $str = $matches[1][$i];
                        $this->assertTrue(strpos($core_ini_files_contents, "\n" . $str . '=') !== false, $file . '/' . $str . ' needs to have explicit file referencing');
                    }
                }
            }
            closedir($dh);
        }
    }
}
