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
class lang_misc_test_set extends cms_test_case
{
    protected $lang_file_mapping = [];

    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
    }

    public function testLangStringsWork()
    {
        if (($this->only !== null) && ($this->only != 'testLangStringsWork')) {
            return;
        }

        require_code('files');

        $dir = get_file_base() . '/lang_custom/EX';
        $path = $dir . '/global.ini';
        @mkdir($dir, 0777);
        cms_file_put_contents_safe($path, "[strings]\nSETTINGS=Foo");

        // Overridden
        $en = do_lang('SETTINGS', null, null, null, 'EN');
        $ex = do_lang('SETTINGS', null, null, null, 'EX');
        $this->assertTrue(!empty($en));
        $this->assertTrue(!empty($ex));
        $this->assertTrue($ex == 'Foo');
        $this->assertTrue($en != $ex);

        // Non-overridden
        $en = do_lang('ACTIVITY', null, null, null, 'EN');
        $ex = do_lang('ACTIVITY', null, null, null, 'EX');
        $this->assertTrue(!empty($en));
        $this->assertTrue(!empty($ex));
        $this->assertTrue($en == $ex);

        deldir_contents(get_file_base() . '/lang_custom/EX', false, true);
        @deldir_contents(get_file_base() . '/caches/lang/EX', false, true);
        @deldir_contents(get_file_base() . '/themes/default/templates_cached/EX/', false, true);
        @deldir_contents(get_file_base() . '/themes/admin/templates_cached/EX/', false, true);
    }

    public function testPluralisation()
    {
        if (($this->only !== null) && ($this->only != 'testPluralisation')) {
            return;
        }

        require_code('lang2');
        require_code('lang_compile');
        require_code('files2');

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $lang_file) {
            $map = get_lang_file_map(fallback_lang(), $lang_file, false, false) + get_lang_file_map(fallback_lang(), $lang_file, true, false);
            foreach ($map as $key => $value) {
                $this->assertTrue(preg_match('#\} \w+\(s\)#', $value) == 0, 'Do better pluralisation for ' . $key);
            }
        }
    }

    public function testUnknownReferences()
    {
        if (($this->only !== null) && ($this->only != 'testUnknownReferences')) {
            return;
        }

        require_code('lang_compile');
        require_code('files2');

        require_all_lang();

        $lang_files = get_lang_files();
        foreach (array_keys($lang_files) as $lang_file) {
            $map = get_lang_file_map(fallback_lang(), $lang_file, false) + get_lang_file_map(fallback_lang(), $lang_file, true);
            foreach (array_keys($map) as $key) {
                $this->lang_file_mapping[$key] = $lang_file;
            }
        }

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            if (in_array($path, [
                '_tests/tests/async_tests/lang_inline_editing.php',
            ])) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $this->process_file_for_references($c, $path, true);
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['tpl']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $this->process_file_for_references($c, $path);
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['js']);
        foreach ($files as $path) {
            if (preg_match('#^(data/ace|data/ckeditor|tracker|sources\_custom/openspout)/#', $path) != 0) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            if (strpos($c, '/*{$,parser hint: pure}*/') === false) {
                $this->process_file_for_references($c, $path);
            }
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['txt']);
        foreach ($files as $path) {
            // Exceptions
            if (in_array($path, [
                'docs/pages/comcode_custom/EN/tut_designer_themes.txt',
                'docs/pages/comcode_custom/EN/codebook_standards.txt',
                'docs/pages/comcode_custom/EN/codebook_standards_obscure.txt',
                'docs/pages/comcode_custom/EN/codebook_1.txt',
                'docs/pages/comcode_custom/EN/codebook_1b.txt',
                'docs/pages/comcode_custom/EN/codebook_2.txt',
                'docs/pages/comcode_custom/EN/tut_tempcode.txt',
            ])) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $this->process_file_for_references($c, $path);
        }
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['xml']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $this->process_file_for_references($c, $path);
        }

        $c = cms_file_get_contents_safe(get_file_base() . '/install.php', FILE_READ_LOCK);
        $this->process_file_for_references($c, get_file_base() . '/install.php');
    }

    protected function process_file_for_references($c, $path, $is_php = false)
    {
        $matches = [];

        if ($is_php) {
            if (strpos($c, '_lang') !== false) {
                if ((empty($this->only)) || ($this->only == 'do_lang_tempcode')) {
                    $num_matches = preg_match_all('#do_lang_tempcode\(\'([^\']*)\'[\),]#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $str = $matches[1][$i];
                        $this->process_str_reference($str, 'do_lang_tempcode', $path);
                        $this->check_includes($c, $str, $path);
                    }
                }

                if ((empty($this->only)) || ($this->only == 'do_lang')) {
                    $num_matches = preg_match_all('#do_lang\(\'([^\']*)\'[\),]#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $str = $matches[1][$i];
                        $this->process_str_reference($str, 'do_lang', $path);
                        $this->check_includes($c, $str, $path);
                    }
                }

                if ((empty($this->only)) || ($this->only == 'do_notification_lang')) {
                    $num_matches = preg_match_all('#do_notification_lang\(\'([^\']*)\'[\),]#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $str = $matches[1][$i];
                        $this->process_str_reference($str, 'do_lang', $path);
                        $this->check_includes($c, $str, $path);
                    }
                }
            }

            if ((empty($this->only)) || ($this->only == 'log_it')) {
                $num_matches = preg_match_all('#log_it\(\'([^\']*)\'[\),]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $str = $matches[1][$i];
                    $this->process_str_reference($str, 'log_it', $path);
                    $this->check_includes($c, $str, $path);
                }
            }

            if ((empty($this->only)) || ($this->only == 'get_screen_title')) {
                $num_matches = preg_match_all('#get_screen_title\(\'([^\']*)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $str = $matches[1][$i];
                    $this->process_str_reference($str, 'get_screen_title', $path);
                }
            }
        } else {
            if (strpos($c, '{!') !== false) {
                if ((empty($this->only)) || ($this->only == 'Tempcode')) {
                    $num_matches = preg_match_all('#[^\\\\]\{\!([\w:]+)[^\}]*\}#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $str = $matches[1][$i];
                        $this->process_str_reference($str, 'Tempcode', $path);
                    }
                }
            }
        }
    }

    protected function check_includes($c, $str, $path)
    {
        if (get_param_integer('deep', 0) == 0) { // Pass deep=1 if you are okay with false-positives
            return;
        }

        if (isset($this->lang_file_mapping[$str])) {
            $lang_file = $this->lang_file_mapping[$str];
            if (!in_array($lang_file, ['global', 'critical_error', 'cns'])) {
                $require_lang = 'require_lang(\'' . $lang_file . '\')';
                $ok = strpos($c, $require_lang) !== false;
                if (!$ok) {
                    $ok = strpos($c, 'require_all_lang') !== false;
                }

                $error_message = 'Cannot find ' . $require_lang . ' in ' . $path . ', caused by ' . $str . ' lang string';
                $this->assertTrue($ok, $error_message);
            }
        }
    }

    protected function process_str_reference($str, $type, $path)
    {
        $this->assertTrue(do_lang($str, null, null, null, null, false) !== null, 'Cannot find referenced lang string ' . $str . ' for a ' . $type . ' case in ' . $path);

        if (strpos($str, ':') !== false) {
            list($lang_file, $just_str) = explode(':', $str, 2);
            $map = get_lang_file_map(fallback_lang(), $lang_file, false) + get_lang_file_map(fallback_lang(), $lang_file, true);
            $this->assertTrue(isset($map[$just_str]), 'Cannot find referenced lang string ' . $str . ' for a ' . $type . ' case (has implicit include) in ' . $path);
        }
    }
}
