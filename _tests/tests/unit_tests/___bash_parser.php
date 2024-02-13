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

/*EXTRA FUNCTIONS: shell_exec*/

// php _tests/index.php ___bash_parser

/**
 * Composr test case class (unit testing).
 */
class ___bash_parser_test_set extends cms_test_case
{
    public function testValidCode()
    {
        require_code('files2');
        $php_path = find_php_path();
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (basename($path) == 'phpstub.php') {
                continue;
            }
            if (basename($path) == 'test_with_parse_error.php') { // Intentionally has an error in it
                continue;
            }

            cms_set_time_limit(5);

            // NB: php-no-ext bit works around bug in Windows version of PHP with slow startup. Make a ../php-no-ext/php.ini file with no extensions listed for loading
            $message = shell_exec($php_path . ' -l ' . cms_escapeshellarg(get_file_base() . '/' . $path) . ' -c ' . cms_escapeshellarg(get_file_base() . '/../php-no-ext'));

            if (is_cli()) {
                echo $message;
            }

            $this->assertTrue(strpos($message, 'No syntax errors detected') !== false, $message . ' (' . $path . ')');
        }
    }
}
