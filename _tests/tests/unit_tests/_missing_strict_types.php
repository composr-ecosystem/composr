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

/**
 * Composr test case class (unit testing).
 */
class _missing_strict_types_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }
    public function testStrictTypeDeclarations()
    {
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $code = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            if ((preg_match('#declare\(strict_types=1\)#', $code, $matches) == 0) && (preg_match('#Christopher Graham#', $code, $matches) != 0)/*FUDGE: Ignore third-party code but not all non-bundled addons*/) {
                $this->assertTrue(false, 'Missing declare(strict_types=1) in ' . $path);

                // Uncomment below and re-run the test to add in declaration. Then comment and re-run the test to confirm the fixes.
                /*
                $code = preg_replace('/<\?php/s', '<?php' . "\n" . 'declare(strict_types=1);' . "\n\n", $code);
                cms_file_put_contents_safe(get_file_base() . '/' . $path, $code, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_FAILURE_SILENT);
                */
            }
        }
    }
}
