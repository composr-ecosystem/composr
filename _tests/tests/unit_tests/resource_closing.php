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
class resource_closing_test_set extends cms_test_case
{
    protected $files;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_sluggish);

        require_code('files2');

        $this->files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, array('php'));
        $this->files[] = 'install.php';

        $exceptions = array(
            'sources_custom/phpstub.php',
        );
        $exception_stubs = array(
            'sources_custom/aws/',
            'sources_custom/sabredav/',
            'sources_custom/spout/',
            'tracker/',
            'sources_custom/getid3/',
        );

        foreach ($this->files as $i => $path) {
            foreach ($exception_stubs as $stub) {
                if (in_array($path, $exceptions)) {
                    unset($this->files[$i]);
                    continue;
                }
                if (substr($path, 0, strlen($stub)) == $stub) {
                    unset($this->files[$i]);
                    continue;
                }
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
            $ok = in_array($type, array('Unknown', 'stream-context'));
            $this->assertTrue($ok, 'Unexpected resource left open of type, ' . $type);
        }
    }

    public function testFclose()
    {
        $exceptions = array(
            '_tests/codechecker/tests.php',
            '_tests/tests/unit_tests/http.php',
            'sources/calendar.php',
            'sources/comcode_cleanup.php',
            'sources/database.php',
            'sources/permissions.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
        );
        $exception_stubs = array(
        );
        $strict_order_exceptions = array(
        );

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
        }
    }

    public function testClosedir()
    {
        $exceptions = array(
        );
        $exception_stubs = array(
        );
        $strict_order_exceptions = array(
        );

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
        $exceptions = array(
        );
        $exception_stubs = array(
            'sources/hooks/systems/tasks/',
        );
        $strict_order_exceptions = array(
            'code_editor.php',
            'sources/global3.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
        );

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
        $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, false);

        $c = preg_replace('#' . preg_quote($open_code, '#') . '.*' . preg_quote($close_code, '#') . '#Us', '', $c);

        $ok = (strpos($c, $open_code) === false) || ((strpos($c, $close_code) !== false) && ($strict_order_exception));
        $this->assertTrue($ok, '"' . $open_code . '" expects a matching "' . $close_code . '" in ' . $path);
    }
}
