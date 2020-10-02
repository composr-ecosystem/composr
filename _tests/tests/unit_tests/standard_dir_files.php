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
class standard_dir_files_test_set extends cms_test_case
{
    public function setUp()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        parent::setUp();

        disable_php_memory_limit();
    }

    public function testHtaccessConsistency()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', true);
        sort($files);
        $types = array();
        foreach ($files as $path) {
            // Exceptions
            if ($path == '.htaccess') {
                continue;
            }
            if (preg_match('#^(tracker|exports/backups|exports/builds|themes/_unnamed_)/#', $path) != 0) {
                continue;
            }

            if (basename($path) == '.htaccess') {
                $md5 = md5(file_get_contents(get_file_base() . '/' . $path));
                if (!array_key_exists($md5, $types)) {
                    $types[$md5] = array();
                }
                $types[$md5][] = $path;
            }
        }

        ksort($types);

        // To reset
        /*foreach (array_keys($types) as $type) {
            echo "\t\t\t'" . $type . "',\n";
        }*/

        $valid_hashes = array(
            '040f254836ecefb94bebc44d91e391eb',
            '0e665ce3d0ae5f44e1a6affe3c7f5303',
            '296a0f42479e015438791d0b21e22a07',
            '3c3283f2b3f7d57a8bdf38ca126ff678',
            '44c2cb384e8efd1ab789978e00d6ea19',
            '45c31898af89e12147cf987481cae64b',
            '4afd84b898945e884f88b7f2cda376c0',
            '61b32927345080611fa4772255f4a70b',
            '97656c6f2c60873d55a421cd762fac00',
            'b4af30b08914c4a8240106cf7c614034',
            'c1bfa4b9b62eff28d2c697aff749bd76',
            'd565e2958abd06bfac42906ea7b4ea9d',
            'd90c4471fc2a552580896dd6dae99df7',
            'e584f07661e5fee9170ba1df153359ad',
            'ede82ed9879b9d6d011638ca5736bddd',
        );
        foreach ($types as $hash => $file_paths) {
            $this->assertTrue(in_array($hash, $valid_hashes), 'Invalid .htaccess file: ' . serialize($file_paths));
        }
    }

    public function testStandardDirFiles()
    {
        $this->do_dir(get_file_base());
    }

    private function do_dir($dir)
    {
        $contents = 0;

        require_code('files');

        if (($dh = opendir($dir)) !== false) {
            while (($file = readdir($dh)) !== false) {
                if (should_ignore_file(preg_replace('#^' . preg_quote(get_file_base() . '/', '#') . '#', '', $dir . '/') . $file, IGNORE_NONBUNDLED_VERY_SCATTERED | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_THEMES, 0)) {
                    continue;
                }

                if ($file == 'test-a') {
                    continue;
                }

                if (is_dir($dir . '/' . $file)) {
                    $this->do_dir($dir . '/' . $file);
                } else {
                    $contents++;
                }
            }
        }

        if ($contents > 0) {
            if (
                (!file_exists($dir . '/index.php')) &&
                (strpos($dir, 'ckeditor') === false) &&
                (strpos($dir, 'tracker/') === false) &&
                (strpos($dir, 'personal_dicts') === false) &&
                (strpos($dir, 'uploads/website_specific') === false)
            ) {
                $this->assertTrue(file_exists($dir . '/index.html'), 'touch "' . $dir . '/index.html" ; git add -f "' . $dir . '/index.html"');
            }

            if (
                (!file_exists($dir . '/index.php')) &&
                (!file_exists($dir . '/html_custom')) &&
                (!file_exists($dir . '/EN')) &&
                (strpos($dir, 'ckeditor') === false) &&
                (strpos($dir, 'tracker/') === false) &&
                (strpos($dir, 'uploads') === false) &&
                (preg_match('#/data(/|$|\_)#', $dir) == 0)
                && (strpos($dir, 'themes') === false) &&
                (strpos($dir, 'exports') === false)
            ) {
                if (strpos($dir, '/uploads/') !== false) {
                    $best_htaccess = 'uploads/downloads/.htaccess';
                } else {
                    $best_htaccess = 'sources/.htaccess';
                }
                $this->assertTrue(file_exists($dir . '/.htaccess'), 'cp "' . get_file_base() . '/' . $best_htaccess . '" "' . $dir . '/.htaccess" ; git add "' . $dir . '/.htaccess"');
            }
        }
    }

    public function testParallelHookDirs()
    {
        foreach (array('systems', 'blocks', 'modules') as $dir) {
            $a = array();
            $_dir = get_file_base() . '/sources/hooks/' . $dir;
            $dh = opendir($_dir);
            while (($f = readdir($dh)) !== false) {
                if ($f == '.DS_Store') {
                    continue;
                }

                if (is_file($_dir . '/' . $f . 'index.html')) {
                    $a[] = $f;
                }
            }
            closedir($dh);
            sort($a);

            $b = array();
            $_dir = get_file_base() . '/sources_custom/hooks/' . $dir;
            $dh = opendir($_dir);
            while (($f = readdir($dh)) !== false) {
                if ($f == '.DS_Store') {
                    continue;
                }

                if (is_file($_dir . '/' . $f . 'index.html')) {
                    $b[] = $f;
                }
            }
            closedir($dh);
            sort($b);

            $diff = array_diff($a, $b);
            $this->assertTrue(count($diff) == 0, 'Missing in sources_custom/hooks/' . $dir . ': ' . serialize($diff));

            $diff = array_diff($b, $a);
            $this->assertTrue(count($diff) == 0, 'Missing in sources/hooks/' . $dir . ': ' . serialize($diff));
        }
    }
}
