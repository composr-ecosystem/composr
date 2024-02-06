<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class tutorial_image_consistency_test_set extends cms_test_case
{
    protected $images;
    protected $images_referenced;
    protected $images_referenced_by_tutorial;

    public function setUp()
    {
        require_code('images');
        require_code('tutorials');

        $this->images = [];
        $path = get_file_base() . '/data_custom/images/docs';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (is_dir($path . '/' . $file)) {
                $dh2 = opendir($path . '/' . $file);
                while (($file2 = readdir($dh2)) !== false) {
                    if (is_image($file2, IMAGE_CRITERIA_WEBSAFE)) {
                        $this->images[$file . '/' . $file2] = true;
                    }
                }
                closedir($dh2);
            } else {
                if (is_image($file, IMAGE_CRITERIA_WEBSAFE)) {
                    $this->images[$file] = true;
                }
            }
        }
        closedir($dh);

        $this->images_referenced = [];
        $this->images_referenced_by_tutorial = [];
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $tutorial = basename($file, '.txt');

                $c = remove_code_block_contents(cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM));

                $matches = [];
                $num_matches = preg_match_all('#data_custom/images/docs/([^"\'\s]*\.(gif|jpg|jpeg|png))#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $this->images_referenced[$matches[1][$i]] = $path . '/' . $file;
                }

                $matches = [];
                $num_matches = preg_match_all('#\[media[^\[\]]*]data_custom/images/docs/([^\[\]]*)\[/media\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $dir = dirname($matches[1][$i]);
                    if (!isset($this->images_referenced_by_tutorial[$tutorial])) {
                        $this->images_referenced_by_tutorial[$tutorial] = [];
                    }
                    $this->images_referenced_by_tutorial[$tutorial][] = $dir;

                    $this->images_referenced[$matches[1][$i]] = $path . '/' . $file;
                }
            }
        }
        closedir($dh);
    }

    public function testNoWrongDirs()
    {
        foreach ($this->images_referenced_by_tutorial as $tutorial => $files) {
            foreach ($files as $dir) {
                $this->assertTrue(($dir == $tutorial) || ($dir == '.'), 'Image from wrong directory referenced for ' . $tutorial . ' (' . $dir . ')');
            }
        }
    }

    public function testNoUnmatchedScreenshots()
    {
        foreach (array_keys($this->images_referenced) as $x) {
            $this->assertTrue(isset($this->images[$x]), 'Missing screenshot referenced in a doc: ' . $x);
        }
    }

    public function testNoMissingScreenshots()
    {
        $exceptions = [
            'tut_install/install_step2_1.png',
            'tut_install/install_step3_1.png',
        ];

        foreach (array_keys($this->images) as $x) {
            if (in_array($x, $exceptions)) {
                continue;
            }

            $this->assertTrue(isset($this->images_referenced[$x]), 'Unused screenshot: ' . $x);
        }
    }
}
