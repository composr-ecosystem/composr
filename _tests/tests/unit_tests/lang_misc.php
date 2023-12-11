<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class lang_misc_test_set extends cms_test_case
{
    private $lang_file_mapping = array();

    public function testLangStringsWork()
    {
        $dir = get_file_base() . '/lang_custom/EX';
        $path = $dir . '/global.ini';
        @mkdir($dir, 0777);
        file_put_contents($path, "[strings]\nSETTINGS=Foo");

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

        @unlink($path);
        @rmdir($dir);
    }

    public function testUnbalancedSmartQuotes()
    {
        require_code('lang2');
        require_code('lang_compile');

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
        require_code('lang_compile');

        disable_php_memory_limit();
        require_all_lang();

        $lang_files = get_lang_files();
        foreach (array_keys($lang_files) as $lang_file) {
            $map = get_lang_file_map(fallback_lang(), $lang_file, false) + get_lang_file_map(fallback_lang(), $lang_file, true);
            foreach (array_keys($map) as $key) {
                $this->lang_file_mapping[$key] = $lang_file;
            }
        }

        $files = $this->do_dir(get_file_base(), '', 'php');
        foreach ($files as $file) {
            if ($file == get_file_base() . '/_tests/tests/unit_tests/lang_inline_editing.php') {
                continue;
            }

            $c = file_get_contents($file);
            $this->process_file_for_references($c, $file);
        }
        $files = $this->do_dir(get_file_base(), '', 'tpl');
        foreach ($files as $file) {
            $c = file_get_contents($file);
            $this->process_file_for_references($c, $file);
        }
        $files = $this->do_dir(get_file_base() . '/themes', 'themes', 'js');
        foreach ($files as $file) {
            $c = file_get_contents($file);
            if (strpos($c, '/*{$,Parser hint: pure}*/') === false) {
                $this->process_file_for_references($c, $file);
            }
        }
        $files = $this->do_dir(get_file_base(), '', 'txt');
        foreach ($files as $file) {
            $c = file_get_contents($file);
            $this->process_file_for_references($c, $file);
        }
        $files = $this->do_dir(get_file_base(), '', 'xml');
        foreach ($files as $file) {
            $c = file_get_contents($file);
            $this->process_file_for_references($c, $file);
        }

        $c = file_get_contents(get_file_base() . '/install.php');
        $this->process_file_for_references($c, get_file_base() . '/install.php');
    }

    private function process_file_for_references($c, $file)
    {
        $matches = array();

        if ((empty($_GET['only'])) || ($_GET['only'] == 'do_lang_tempcode')) {
            $num_matches = preg_match_all('#do\_lang\_tempcode\(\'([^\']*)\'[\),]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'do_lang_tempcode', $file);
                $this->check_includes($c, $str, $file);
            }
        }

        if ((empty($_GET['only'])) || ($_GET['only'] == 'do_lang')) {
            $num_matches = preg_match_all('#do\_lang\(\'([^\']*)\'[\),]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'do_lang', $file);
                $this->check_includes($c, $str, $file);
            }
        }

        if ((empty($_GET['only'])) || ($_GET['only'] == 'log_it')) {
            $num_matches = preg_match_all('#log\_it\(\'([^\']*)\'[\),]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'log_it', $file);
                $this->check_includes($c, $str, $file);
            }
        }

        if ((empty($_GET['only'])) || ($_GET['only'] == 'do_notification_lang')) {
            $num_matches = preg_match_all('#do\_notification\_lang\(\'([^\']*)\'[\),]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'do_notification_lang', $file);
                $this->check_includes($c, $str, $file);
            }
        }

        if ((empty($_GET['only'])) || ($_GET['only'] == 'get_screen_title')) {
            $num_matches = preg_match_all('#get_screen_title\(\'([^\']*)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'get_screen_title', $file);
                $this->check_includes($c, $str, $file);
            }
        }

        if ((empty($_GET['only'])) || ($_GET['only'] == 'Tempcode')) {
            $num_matches = preg_match_all('#[^\\\\]\{\!([\w:]+)[^\}]*\}#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $str = $matches[1][$i];
                $this->process_str_reference($str, 'Tempcode', $file);
            }
        }
    }

    private function check_includes($c, $str, $file)
    {
        if (get_param_integer('deep', 0) == 0) { // Pass deep=1 if you are okay with false-positives
            return;
        }

        if (isset($this->lang_file_mapping[$str])) {
            $lang_file = $this->lang_file_mapping[$str];
            if (!in_array($lang_file, array('global', 'critical_error', 'cns'))) {
                $require_lang = 'require_lang(\'' . $lang_file . '\')';
                $ok = strpos($c, $require_lang) !== false;
                if (!$ok) {
                    $ok = strpos($c, 'require_all_lang') !== false;
                }

                $error_message = 'Cannot find ' . $require_lang . ' in ' . $file . ', caused by ' . $str . ' lang string';
                $this->assertTrue($ok, $error_message);
            }
        }
    }

    private function process_str_reference($str, $type, $file)
    {
        $this->assertTrue(do_lang($str, null, null, null, null, false) !== null, 'Cannot find referenced lang string ' . $str . ' for a ' . $type . ' case in ' . $file);

        if (strpos($str, ':') !== false) {
            list($lang_file, $just_str) = explode(':', $str, 2);
            $map = get_lang_file_map(fallback_lang(), $lang_file, false) + get_lang_file_map(fallback_lang(), $lang_file, true);
            $this->assertTrue(isset($map[$just_str]), 'Cannot find referenced lang string ' . $str . ' for a ' . $type . ' case (has implicit include) in ' . $file);
        }
    }

    private function do_dir($dir, $dir_stub, $ext)
    {
        $files = array();

        if (($dh = opendir($dir)) !== false) {
            while (($file = readdir($dh)) !== false) {
                if (($file[0] != '.') && (!should_ignore_file((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                    if (is_file($dir . '/' . $file)) {
                        if (substr($file, -strlen($ext) - 1, strlen($ext) + 1) == '.' . $ext) {
                            $files[] = $dir . '/' . $file;
                        }
                    } elseif (is_dir($dir . '/' . $file)) {
                        $_files = $this->do_dir($dir . '/' . $file, (($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, $ext);
                        $files = array_merge($_files, $files);
                    }
                }
            }
            closedir($dh);
        }

        return $files;
    }
}
