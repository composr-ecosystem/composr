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
class broken_includes_test_set extends cms_test_case
{
    protected $files;

    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');

        $this->files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
        $this->files[] = 'install.php';
    }

    public function testRequireCode()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.php') {
                continue;
            }

            if ($path == 'data_custom/execute_temp.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#require_code\(\'([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];

                if (substr($dependency, -4) == '.php') {
                    continue;
                }

                if (in_array($dependency, ['user_sync__customise'])) { // Exceptions
                    continue;
                }

                $okay = file_exists(get_file_base() . '/sources/' . $dependency . '.php') || file_exists(get_file_base() . '/sources_custom/' . $dependency . '.php');
                $this->assertTrue($okay, 'Could not find target of require_code, ' . $dependency);
            }
        }
    }

    public function testRequireLang()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#require_lang\(\'([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                $this->assertTrue($okay, 'Could not find target of require_lang, ' . $dependency);
            }
        }
    }

    public function testRequireLangB()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#do_lang(_tempcode)?\(\'([^\']+):([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[2][$i];
                $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                $this->assertTrue($okay, 'Could not find target of PHP implicit lang include, ' . $dependency);
            }
        }
    }

    public function testRequireLangC()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.tpl') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
            $matches = [];
            $num_matches = preg_match_all('#\{\!(\w+):(\w+)\}#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                $this->assertTrue($okay, 'Could not find target of Tempcode implicit lang include, ' . $dependency);
            }
        }
    }

    public function testRequireCSS()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#require_css\(\'([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/themes/default/css/' . $dependency . '.css') || file_exists(get_file_base() . '/themes/default/css_custom/' . $dependency . '.css');
                $this->assertTrue($okay, 'Could not find target of require_css, ' . $dependency);
            }
        }
    }

    public function testRequireCSSB()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.tpl') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
            $matches = [];
            $num_matches = preg_match_all('#\{\$REQUIRE_CSS,(\w+)\}#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/themes/default/css/' . $dependency . '.css') || file_exists(get_file_base() . '/themes/default/css_custom/' . $dependency . '.css');
                $this->assertTrue($okay, 'Could not find target of Tempcode CSS include, ' . $dependency);
            }
        }
    }

    public function testRequireJavascript()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.php') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#require_javascript\(\'([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/themes/default/javascript/' . $dependency . '.js') || file_exists(get_file_base() . '/themes/default/javascript_custom/' . $dependency . '.js');
                $this->assertTrue($okay, 'Could not find target of require_javascript, ' . $dependency);
            }
        }
    }

    public function testRequireJavascriptB()
    {
        foreach ($this->files as $path) {
            if (substr($path, -4) != '.tpl') {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
            $matches = [];
            $num_matches = preg_match_all('#\{\$REQUIRE_JAVASCRIPT,(\w+)\}#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $dependency = $matches[1][$i];
                $okay = file_exists(get_file_base() . '/themes/default/javascript/' . $dependency . '.js') || file_exists(get_file_base() . '/themes/admin/javascript/' . $dependency . '.js') || file_exists(get_file_base() . '/themes/default/javascript_custom/' . $dependency . '.js');
                $this->assertTrue($okay, 'Could not find target of require_javascript, ' . $dependency);
            }
        }
    }
}
