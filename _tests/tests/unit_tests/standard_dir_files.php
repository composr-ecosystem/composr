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
class standard_dir_files_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);
    }

    public function testHtaccessConsistency()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_LANGS);
        sort($files);
        $types = [];
        foreach ($files as $path) {
            // Exceptions
            if ($path == '.htaccess') {
                continue;
            }
            if (preg_match('#^(tracker|exports/backups|exports/builds)/#', $path) != 0) {
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

        $this->assertTrue(array_keys($types) == [
            '296a0f42479e015438791d0b21e22a07',
            '3184b8b93e2d9b02dea0c4ec3133ee9c',
            '35524c96fbfc2361a6dff117f3a19bc8',
            '362eb392e7da973c77733262cf1d0e90',
            '4215242c301a30d66cd824e1ef0dd562',
            '54173c31cdac14469a93eaa292ebbb08',
            '8a55e7d3c6651736659f3bc5959c16dd',
            '8a7c42d7083b00b153df228e1700c60a',
            '8ce63a764e2f9e6ec2cca2aa511197dd',
            '97656c6f2c60873d55a421cd762fac00',
            'b4af30b08914c4a8240106cf7c614034',
            'de9b5b7778090cf4376839b6aebb9f45',
            'e829b8bdcef68c92b0926288106048b6',
        ]);
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
                } elseif ($dir_stub == '_tests/codechecker') {
                    if (in_array($file, ['netbeans'])) { // Auto-generated folder, should not be meddled with
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
                (!file_exists($dir . '/index.php')) // Not in a zone (needs to run as default)
            ) {
                $this->assertTrue(file_exists($dir . '/index.html'), 'touch "' . $dir . '/index.html" ; git add -f "' . $dir . '/index.html"');
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
                (preg_match('#^_tests/codechecker$#', $dir_stub) == 0) && // Not in codechecker (we need to call CQC)
                (preg_match('#^mobiquo(/smartbanner(/images)?)?$#', $dir_stub) == 0) && // Not in mobiquo (we need to call Tapatalk)
                (preg_match('#^sources_custom/composr_mobile_sdk(/|$)#', $dir_stub) == 0) // composr_mobile_sdk may need to be callable
            ) {
                if (strpos($dir, '/uploads/') !== false) {
                    $best_htaccess = 'uploads/downloads/.htaccess';
                } else {
                    $best_htaccess = 'sources_custom/.htaccess';
                }
                $this->assertTrue(file_exists($dir . '/.htaccess'), 'cp "' . get_file_base() . '/' . $best_htaccess . '" "' . $dir . '/.htaccess" ; git add "' . $dir . '/.htaccess"');
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
