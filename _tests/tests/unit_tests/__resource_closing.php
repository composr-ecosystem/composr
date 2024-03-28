<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/*EXTRA FUNCTIONS: get_resources|get_resource_type*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// php _tests/index.php __resource_closing

/**
 * Composr test case class (unit testing).
 */
class resource_closing_test_set extends cms_test_case
{
    protected $files;

    public function setUp()
    {
        parent::setUp();

        if (!is_cli()) {
            warn_exit('This test should be run on the command line: php _tests/index.php __resource_closing.');
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');
        require_code('third_party_code');

        $this->files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $this->files[] = 'install.php';

        foreach ($this->files as $i => $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                unset($this->files[$i]);
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                'sources_custom/phpstub.php',
            ]);
            if (in_array($path, $exceptions)) {
                unset($this->files[$i]);
                continue;
            }
        }
    }

    public function testPHPReporting()
    {
        if (!function_exists('get_resources') || !function_exists('get_resource_type')) {
            return;
        }

        $rs = get_resources();
        foreach ($rs as $r) {
            $type = get_resource_type($r);
            $ok = in_array($type, ['Unknown', 'stream-context']) || $r === STDIN || $r === STDOUT || $r === STDERR;
            $this->assertTrue($ok, 'Unexpected resource left open of type, ' . $type);
        }
    }

    public function testFclose()
    {
        $exceptions = [
            '_tests/codechecker/tests.php',
            '_tests/tests/unit_tests/http.php',
            'sources/calendar.php',
            'sources/comcode_cleanup.php',
            'sources/database.php',
            'sources/permissions.php',
            'sources/files.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
        ];
        $exception_stubs = [
        ];
        $strict_order_exceptions = [
        ];

        foreach ($this->files as $path) {
            if (in_array($path, $exceptions)) {
                continue;
            }
            foreach ($exception_stubs as $stub) {
                if (substr($path, 0, strlen($stub)) == $stub) {
                    continue 2;
                }
            }

            $this->check_matching($path, 'fopen(', 'fclose(', in_array($path, $strict_order_exceptions));
            $this->check_matching($path, 'cms_fopen_text_read(', 'fclose(', in_array($path, $strict_order_exceptions));
            $this->check_matching($path, 'cms_fopen_text_write(', 'fclose(', in_array($path, $strict_order_exceptions));
            $this->check_matching($path, 'tar_open(', 'tar_close(', in_array($path, $strict_order_exceptions));
        }
    }

    public function testClosedir()
    {
        $exceptions = [
        ];
        $exception_stubs = [
        ];
        $strict_order_exceptions = [
        ];

        foreach ($this->files as $path) {
            if (in_array($path, $exceptions)) {
                continue;
            }
            foreach ($exception_stubs as $stub) {
                if (substr($path, 0, strlen($stub)) == $stub) {
                    continue 2;
                }
            }

            $this->check_matching($path, 'opendir(', 'closedir(', in_array($path, $strict_order_exceptions));
        }
    }

    public function testTempnamUnlink()
    {
        $exceptions = [
            'sources/files_spreadsheets_write.php',
            'sources_custom/files_spreadsheets_write__spout.php',
            'sources_custom/hybridauth/HttpClient/Guzzle.php',
        ];
        $exception_stubs = [
            'sources/hooks/systems/tasks/',
        ];
        $strict_order_exceptions = [
            'code_editor.php',
            'sources/global3.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
        ];

        foreach ($this->files as $path) {
            if (in_array($path, $exceptions)) {
                continue;
            }
            foreach ($exception_stubs as $stub) {
                if (substr($path, 0, strlen($stub)) == $stub) {
                    continue 2;
                }
            }

            $this->check_matching($path, 'cms_tempnam(', 'unlink(', in_array($path, $strict_order_exceptions));
        }
    }

    protected function check_matching($path, $open_code, $close_code, $strict_order_exception)
    {
        $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

        $c = preg_replace('#' . preg_quote($open_code, '#') . '.*' . preg_quote($close_code, '#') . '#Us', '', $c);

        $ok = (strpos($c, $open_code) === false) || ((strpos($c, $close_code) !== false) && ($strict_order_exception));
        $this->assertTrue($ok, '"' . $open_code . '" expects a matching "' . $close_code . '" in ' . $path);
    }
}
