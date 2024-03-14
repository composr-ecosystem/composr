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
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN, true, true, ['htaccess']);
        sort($files);
        $types = [];
        foreach ($files as $path) {
            if (preg_match('#^themes/(_unnamed_)/#', $path) != 0) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            if (strpos($c, '<IfModule mod_php') !== false) {
                for ($i = 1; $i < $min_version; $i++) {
                    $this->assertTrue(strpos($c, '<IfModule mod_php' . strval($i)) === false, 'No need to reference mod_php for a version of PHP not supported in ' . $path);
                }
                for ($i = 8; $i < 100; $i++) {
                    $this->assertTrue(strpos($c, '<IfModule mod_php' . strval($i)) === false, 'No need to reference mod_php for PHP8+ ' . $path);
                }
                for ($i = $min_version; $i < 8; $i++) {
                    $this->assertTrue(strpos($c, '<IfModule mod_php' . strval($i)) !== false, 'We need to reference mod_php for a version of PHP we support in ' . $path);
                }
            }
        }
    }

    public function testHtaccessConsistency()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN, true, true, ['htaccess']);
        sort($files);
        $types = [];
        foreach ($files as $path) {
            // Exceptions
            if ($path == '.htaccess') { // Root file
                continue;
            }
            if (preg_match('#^(data/ckeditor|_tests/simpletest|tracker|exports/backups|exports/static|exports/builds|uploads/website_specific/composr.app/demonstratr/servers)/#', $path) != 0) { // Third party files
                continue;
            }
            if (preg_match('#^themes/(_unnamed_)/#', $path) != 0) {
                continue;
            }

            if (basename($path) == '.htaccess') {
                $md5 = md5(cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT));
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
            '35524c96fbfc2361a6dff117f3a19bc8' => true, // uploads/website_specific/composr.app/.htaccess (Deny access to some important files on the composr.app site)
            '8fbbec6b8fd8a4999a5b07f5ddcf5ea8' => true, // */pages/modules*/.htaccess (Allows access to module URLs, and then the recommended.htaccess rules will rewrite to correct URL - useful for IDE integration)
            '3184b8b93e2d9b02dea0c4ec3133ee9c' => true, // Many, */pages/html*/EN/.htaccess, */pages/comcode*/EN/.htaccess (Completely block all HTTP requests)
            'de9b5b7778090cf4376839b6aebb9f45' => true, // adminzone/.htaccess (Better help for Mod_Rewrite)
            '205c253d00d3eac70ce61ba26612b27f' => true, // data*/images/.htaccess, uploads/.htaccess (Long-life cache settings for non-changing files (images))
            '8a55e7d3c6651736659f3bc5959c16dd' => true, // data_custom/.htaccess (Block specific patterns of log and config files)
            'bb091e894176e79224ddf66e45558e53' => true, // themes/*/images*/.htaccess (Disable any kind of server-side CGI/scripting via blocking handlers; Disable JavaScript etc via HTTP headers; Long-life cache settings for non-changing files (images))
            '5b7e3044b5aac9ba5955612da8b21e29' => true, // themes/*/templates_cached/.htaccess (Disable any kind of server-side CGI/scripting via blocking handlers; Long-life cache settings for non-changing files (CSS/JS); Serve pre-compressed CSS/JS files if they exist and the client accepts Gzip or Brotli)
            'af733954322951529e9b3b9c52362352' => true, // uploads/*/.htaccess (Disable any kind of server-side CGI/scripting via blocking handlers; Disable JavaScript etc via HTTP headers)
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
                if (should_ignore_file((($dir_stub == '') ? '' : ($dir_stub . '/')) . $file, IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS)) {
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
                (preg_match('#^_tests/codechecker(/|$)#', $dir_stub) == 0) // Not in codechecker (we need to call CQC)
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
