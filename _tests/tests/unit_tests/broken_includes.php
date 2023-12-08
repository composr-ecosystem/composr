<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        require_code('files');
        require_code('files2');
        $this->contents = get_directory_contents(get_file_base());
    }

    public function testRequireCode()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                if ($c == 'data_custom/execute_temp.php') {
                    continue;
                }

                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#require_code\(\'([^\']+)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];

                    if (substr($dependency, -4) == '.php') {
                        continue;
                    }

                    if (in_array($dependency, array('user_sync__customise'))) { // Exceptions
                        continue;
                    }

                    $okay = file_exists(get_file_base() . '/sources/' . $dependency . '.php') || file_exists(get_file_base() . '/sources_custom/' . $dependency . '.php');
                    $this->assertTrue($okay, 'Could not find target of require_code, ' . $dependency);
                }
            }
        }
    }

    public function testRequireLang()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#require_lang\(\'([^\']+)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                    $this->assertTrue($okay, 'Could not find target of require_lang, ' . $dependency);
                }
            }
        }
    }

    public function testRequireLangB()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#do_lang(_tempcode)?\(\'([^\']+):([^\']+)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[2][$i];
                    $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                    $this->assertTrue($okay, 'Could not find target of PHP implicit lang include, ' . $dependency);
                }
            }
        }
    }

    public function testRequireLangC()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.tpl') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#\{\!(\w+):(\w+)\}#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $dependency . '.ini') || file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $dependency . '.ini');
                    $this->assertTrue($okay, 'Could not find target of Tempcode implicit lang include, ' . $dependency);
                }
            }
        }
    }

    public function testRequireCSS()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#require_css\(\'([^\']+)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/themes/default/css/' . $dependency . '.css') || file_exists(get_file_base() . '/themes/default/css_custom/' . $dependency . '.css');
                    $this->assertTrue($okay, 'Could not find target of require_css, ' . $dependency);
                }
            }
        }
    }

    public function testRequireCSSB()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.tpl') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#\{\$REQUIRE_CSS,(\w+)\}#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/themes/default/css/' . $dependency . '.css') || file_exists(get_file_base() . '/themes/default/css_custom/' . $dependency . '.css');
                    $this->assertTrue($okay, 'Could not find target of Tempcode CSS include, ' . $dependency);
                }
            }
        }
    }

    public function testRequireJavascript()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#require_javascript\(\'([^\']+)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/themes/default/javascript/' . $dependency . '.js') || file_exists(get_file_base() . '/themes/default/javascript_custom/' . $dependency . '.js');
                    $this->assertTrue($okay, 'Could not find target of require_javascript, ' . $dependency);
                }
            }
        }
    }

    public function testRequireJavascriptB()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.tpl') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#\{\$REQUIRE_JAVASCRIPT,(\w+)\}#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dependency = $matches[1][$i];
                    $okay = file_exists(get_file_base() . '/themes/default/javascript/' . $dependency . '.js') || file_exists(get_file_base() . '/themes/default/javascript_custom/' . $dependency . '.js');
                    $this->assertTrue($okay, 'Could not find target of require_javascript, ' . $dependency);
                }
            }
        }
    }
}
