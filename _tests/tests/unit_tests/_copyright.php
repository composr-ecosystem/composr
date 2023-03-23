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

/*
To change date across files (update years as required)...

find . -type f \( -iname \*.php -o -iname \*.css -o -iname \*.bundle -o -iname \*.pre -o -iname \*.txt -o -iname \*.example -o -iname \*.java \) -not -path "./exports/*" -not -path "./build/*" -exec sed -i "s/, 2004-2021/, 2004-2023/g" '{}' \;
*/

/**
 * Composr test case class (unit testing).
 */
class _copyright_test_set extends cms_test_case
{
    public function testCodeCopyrightDates()
    {
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['php', 'css']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $code = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            if (preg_match('#Copyright \(c\) ocProducts, 2004-(\d+)#', $code, $matches) != 0) {
                $this->assertTrue(intval($matches[1]) >= intval(date('Y')), 'Old copyright date for ' . $path . ' (replace the whole PHP header, to ensure consistency)');
            }
        }
    }
}
