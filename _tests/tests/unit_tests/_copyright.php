<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/*
To change date across files (update years as required)...

find . -type f \( -iname \*.php -o -iname \*.css -o -iname \*.bundle -o -iname \*.pre -o -iname \*.txt -o -iname \*.example -o -iname \*.java \) -not -path "./exports/*" -not -path "./build/*" -exec sed -i "s/, 2004-2021/, 2004-2023/g" '{}' \;

You can also use the commented out code in the unit test.

*/

/**
 * Composr test case class (unit testing).
 */
class _copyright_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }
    public function testCodeCopyrightDates()
    {
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['php', 'css']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $code = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            if (preg_match('#Copyright \(c\) Christopher Graham, 2004-(\d+)#', $code, $matches) != 0) {
                $ok = intval($matches[1]) >= intval(date('Y'));
                $this->assertTrue($ok, 'Old copyright date for ' . $path . ' (replace the whole PHP header, to ensure consistency)');

                // Uncomment below and re-run the test to fix copyright dates. Then comment and re-run the test to confirm the fixes.
                /*
                if (!$ok) {
                    $code = preg_replace('/Copyright \(c\) Christopher Graham, 2004-(\d+)/s', 'Copyright (c) Christopher Graham, 2004-' . date('Y'), $code);
                    cms_file_put_contents_safe(get_file_base() . '/' . $path, $code, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_FAILURE_SILENT);
                }
                */
            }
        }
    }
}
