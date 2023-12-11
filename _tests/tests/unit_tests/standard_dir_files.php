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

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { // Windows comes out with different hashes
            $valid_hashes = array(
                'a37601228b335ab973d9b6a891e22173', // uploads/incoming/.htaccess
                'f2e4764b5f1f516f2a8f09fc3d861a66', // uploads/*/.htaccess
                '6df8752284886eee788dd3d1f3cfb5d3', // */pages/modules*/.htaccess
                '4eb44a47deb8ed9ce56d3fe7d7b4ab89', // data*/images/.htaccess, uploads/.htaccess
                'e78c651a22dea4ad388b386baa620b8a', // */pages/html*/EN/.htaccess
                '13dadee3af952ab7364f6927d3ba8a8b', // sources/.htaccess
                '707a815fffff53299f7c1982979d2fe1', // adminzone/.htaccess
                'acbb6d78741153bcca7026981618edbc', // themes/*/templates_cached/.htaccess
                'cf3920250ae53c394438269d8aee4ba0', // Many
                'ee01cae1920097d811835d3f75c861c6', // exports/static/.htaccess
                '0b19fd04af894968b228a3fbea3f3dab', // themes/*/images*/.htaccess
                '3d1ed1d9bce19d897fe7b2f59c7179e8', // uploads/website_specific/compo.sr/.htaccess
                'd8bfc8b93dd0ba8dbf98a595be8d318e', // data_custom/.htaccess
            );
        } else {
            $valid_hashes = array(
                'de3253ec2280f4da1a3bc966c113f369', // uploads/incoming/.htaccess
                'e239621b461039678b9096251869efb4', // uploads/*/.htaccess
                '8fbbec6b8fd8a4999a5b07f5ddcf5ea8', // */pages/modules*/.htaccess
                '3c3283f2b3f7d57a8bdf38ca126ff678', // data*/images/.htaccess, uploads/.htaccess
                '44c2cb384e8efd1ab789978e00d6ea19', // */pages/html*/EN/.htaccess
                '45c31898af89e12147cf987481cae64b', // sources/.htaccess
                '61b32927345080611fa4772255f4a70b', // adminzone/.htaccess
                'e0cc4033fbb4bf22b3f001bbcae33bfd', // themes/*/templates_cached/.htaccess
                'c1bfa4b9b62eff28d2c697aff749bd76', // Many
                'd565e2958abd06bfac42906ea7b4ea9d', // exports/static/.htaccess
                '1be57737eab0844f0d01a6a0adcb4b0f', // themes/*/images*/.htaccess
                'e584f07661e5fee9170ba1df153359ad', // uploads/website_specific/compo.sr/.htaccess
                'ede82ed9879b9d6d011638ca5736bddd', // data_custom/.htaccess
            );
        }
        foreach ($types as $hash => $file_paths) {
            $this->assertTrue(in_array($hash, $valid_hashes), 'Invalid .htaccess file: ' . serialize($file_paths) . ', hash ' . $hash);
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
