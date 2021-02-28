<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class standard_dir_files_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        disable_php_memory_limit();
    }

    public function testPHPBlocking()
    {
        $min_version = 7;
        $max_version = 8;

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', 0, true, true, ['htaccess']);
        sort($files);
        $types = [];
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            if (strpos($c, '<IfModule mod_php') !== false) {
                for ($i = 1; $i < $min_version; $i++) {
                    $this->assertTrue(strpos($c, '<IfModule mod_php' . strval($i)) === false, 'No need to reference mod_php for a version of PHP not supported in ' . $path);
                }
                for ($i = $min_version; $i <= $max_version; $i++) {
                    $this->assertTrue(strpos($c, '<IfModule mod_php' . strval($i)) !== false, 'We need to reference mod_php for a version of PHP we support in ' . $path);
                }
            }
        }
    }

    public function testHtaccessConsistency()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', 0, true, true, ['htaccess']);
        sort($files);
        $types = [];
        foreach ($files as $path) {
            // Exceptions
            if ($path == '.htaccess') { // Root file
                continue;
            }
            if (preg_match('#^(tracker|exports/backups|exports/static|exports/builds|uploads/website_specific/compo.sr/demonstratr/servers)/#', $path) != 0) {
                continue;
            }

            if (basename($path) == '.htaccess') {
                $md5 = md5(cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK));
                if (!array_key_exists($md5, $types)) {
                    $types[$md5] = [];
                }
                $types[$md5][] = $path;
            }
        }

        ksort($types);

        // To reset
        /*foreach (array_keys($types) as $type) {
            echo "\t\t\t'" . $type . "',\n";
        }*/

        $valid_hashes = [
            '296a0f42479e015438791d0b21e22a07' => true, // Many
            '8fbbec6b8fd8a4999a5b07f5ddcf5ea8' => true, // */pages/modules*/.htaccess
            '3184b8b93e2d9b02dea0c4ec3133ee9c' => true, // */pages/html/EN/.htaccess
            '8a7c42d7083b00b153df228e1700c60a' => true, // */pages/html_custom/EN/.htaccess
            '61e312cb9d1db877826e8aa77c282b2a' => true, // _tests/simpletest/test/site/.htaccess
            'f30780cfeab05516183f1b42e174b700' => true, // _tests/simpletest/test/site/protected/.htaccess
            'de9b5b7778090cf4376839b6aebb9f45' => true, // adminzone/.htaccess
            'e829b8bdcef68c92b0926288106048b6' => true, // data*/images/.htaccess, uploads/.htaccess
            '8a55e7d3c6651736659f3bc5959c16dd' => true, // data_custom/.htaccess
            '362eb392e7da973c77733262cf1d0e90' => true, // sources/.htaccess
            '4751dc3cdd5d93c11fbc7b5bc86d8a71' => true, // themes/*/images*/.htaccess
            '9bc9716b414d96e6800c5b2fe70b15a1' => true, // themes/*/templates_cached/.htaccess
            'de500d1e1a3c5fa182fcc6d9a7656d79' => true, // uploads/*/.htaccess
            'e8c3e39b09dac4a56f032a37762351fe' => true, // uploads/incoming/.htaccess
            '35524c96fbfc2361a6dff117f3a19bc8' => true, // uploads/website_specific/compo.sr/.htaccess
        ];
        foreach ($types as $hash => $file_paths) {
            $this->assertTrue(array_key_exists($hash, $valid_hashes), 'Invalid .htaccess file: ' . serialize($file_paths) . ' with hash of ' . $hash);
            unset($valid_hashes[$hash]);
        }
        if ($this->debug) {
            var_dump($valid_hashes);
        }
    }

    public function testStandardDirFiles()
    {
        $this->do_dir(get_file_base(), '');
    }

    protected function do_dir($dir, $dir_stub)
    {
        $contents_count = 0;

        require_code('files');

        $dh = opendir($dir);
        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if (should_ignore_file((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, IGNORE_FLOATING | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS)) {
                    continue;
                }

                // Exceptions
                if ($dir_stub == '') {
                    if (in_array($file, ['tracker'])) {
                        continue;
                    }
                }

                if (is_dir($dir . '/' . $file)) {
                    $this->do_dir($dir . '/' . $file, (($dir_stub == '') ? '' : ($dir_stub . '/')) . $file);
                } else {
                    $contents_count++;
                }
            }
            closedir($dh);
        }

        if ($contents_count > 0) {
            if (
                (preg_match('#^data/ckeditor(/|$)#', $dir_stub) == 0) && // We do not bother for CKEditor, it is none interesting and they do not ship these files themselves - and we want upgrading to be easy
                (preg_match('#^uploads/website_specific/test(/|$)#', $dir_stub) == 0) && // LEGACY: Not from v10 test XML DB
                (preg_match('#^caches/guest_pages(/|$)#', $dir_stub) == 0) && // LEGACY: Not from v10 static cache dir
                (preg_match('#^_tests/codechecker(/|$)#', $dir_stub) == 0) && // Not in codechecker (we need to call CQC)
                (preg_match('#^uploads/filedump/xxx(/|$)#', $dir_stub) == 0) // Created for a test
            ) {
                if (
                    (!file_exists($dir . '/index.php')) // Not in a zone (needs to run as default)
                ) {
                    $ok = file_exists($dir . '/index.html');
                    $msg = 'touch "' . $dir . '/index.html"';
                    $git_msg = ' ; git add -f "' . $dir . '/index.html"';
                    if ($this->debug) {
                        if (!$ok) {
                            echo $msg . "\n";
                        }
                    } else {
                        $this->assertTrue($ok, $msg . $git_msg);
                    }
                }
            }

            if (
                (preg_match('#^caches/guest_pages(/|$)#', $dir_stub) == 0) && // LEGACY: Not from v10 static cache dir
                (preg_match('#^_tests/assets(/|$)#', $dir_stub) == 0) && // Needs to be web-executable
                (!file_exists($dir . '/index.php')) && // Not in a zone (needs to run)
                (!file_exists($dir . '/html_custom')) && // Not in an HTML directory (want to be able to call by hand)
                (!file_exists($dir . '/EN')) && // Not in a pages directory (as parent of HTML directory)
                (preg_match('#^uploads(/|$)#', $dir_stub) == 0) && // Not from uploads (we need to download from)
                (preg_match('#/data(/|$|_)#', $dir) == 0) && // Not from data (scripts need to run)
                (preg_match('#themes($|/[^/]*($|/(images|images_custom|templates_cached)(/|$)))#', $dir_stub) == 0) && // Not from themes (we need to download from)
                (preg_match('#^exports(/|$)#', $dir_stub) == 0) && // Not in exports (we need to download from)
                (preg_match('#^_tests/codechecker(/|$)#', $dir_stub) == 0) && // Not in codechecker (we need to call CQC)
                (preg_match('#^mobiquo(/smartbanner(/images)?)?$#', $dir_stub) == 0) && // Not in mobiquo (we need to call Tapatalk)
                (preg_match('#^sources_custom/composr_mobile_sdk(/|$)#', $dir_stub) == 0) // composr_mobile_sdk may need to be callable
            ) {
                if (strpos($dir, '/uploads/') !== false) {
                    $best_htaccess = 'uploads/downloads/.htaccess';
                } else {
                    $best_htaccess = 'sources_custom/.htaccess';
                }
                $ok = file_exists($dir . '/.htaccess');
                $msg = 'cp "' . get_file_base() . '/' . $best_htaccess . '" "' . $dir . '/.htaccess"';
                $git_msg = ' ; git add "' . $dir . '/.htaccess"';
                if ($this->debug) {
                    if (!$ok) {
                        echo $msg . "\n";
                    }
                } else {
                    $this->assertTrue($ok, $msg . $git_msg);
                }
            }
        }
    }

    public function testParallelHookDirs()
    {
        foreach (['systems', 'blocks', 'modules'] as $dir) {
            $a = [];
            $_dir = get_file_base() . '/sources/hooks/' . $dir;
            $dh = opendir($_dir);
            while (($file = readdir($dh)) !== false) {
                if ($file == '.DS_Store') {
                    continue;
                }

                if (is_file($_dir . '/' . $file . 'index.html')) {
                    $a[] = $file;
                }
            }
            closedir($dh);
            sort($a);

            $b = [];
            $_dir = get_file_base() . '/sources_custom/hooks/' . $dir;
            $dh = opendir($_dir);
            while (($file = readdir($dh)) !== false) {
                if ($file == '.DS_Store') {
                    continue;
                }

                if (is_file($_dir . '/' . $file . 'index.html')) {
                    $b[] = $file;
                }
            }
            closedir($dh);
            sort($b);

            $diff = array_diff($a, $b);
            $this->assertTrue(empty($diff), 'Missing in sources_custom/hooks/' . $dir . ': ' . serialize($diff));

            $diff = array_diff($b, $a);
            $this->assertTrue(empty($diff), 'Missing in sources/hooks/' . $dir . ': ' . serialize($diff));
        }
    }
}
