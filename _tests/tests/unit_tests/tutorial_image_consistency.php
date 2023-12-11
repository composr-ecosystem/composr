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
class tutorial_image_consistency_test_set extends cms_test_case
{
    private $images;
    private $images_referenced;

    public function setUp()
    {
        require_code('images');
        require_code('tutorials');

        $this->images = array();
        $path = get_file_base() . '/data_custom/images/docs';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if ($f[0] == '.') {
                continue;
            }

            if (is_dir($path . '/' . $f)) {
                $dh2 = opendir($path . '/' . $f);
                while (($f2 = readdir($dh2)) !== false) {
                    if (is_image($f2)) {
                        $this->images[$f . '/' . $f2] = true;
                    }
                }
                closedir($dh2);
            } else {
                if (is_image($f)) {
                    $this->images[$f] = true;
                }
            }
        }
        closedir($dh);

        $this->images_referenced = array();
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                $contents = remove_code_block_contents(file_get_contents($path . '/' . $f));

                $matches = array();
                $num_matches = preg_match_all('#data_custom/images/docs/([^"\'\s]*\.(gif|jpg|jpeg|png))#', $contents, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $this->images_referenced[$matches[1][$i]] = $path . '/' . $f;
                }

                $matches = array();
                $num_matches = preg_match_all('#\[media[^\[\]]*]data_custom/images/docs/([^\[\]]*)\[/media\]#', $contents, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $this->images_referenced[$matches[1][$i]] = $path . '/' . $f;
                }
            }
        }
        closedir($dh);
    }

    public function testNoUnmatchedScreenshots()
    {
        foreach (array_keys($this->images_referenced) as $x) {
            $this->assertTrue(isset($this->images[$x]), 'Missing screenshot referenced in a doc: ' . $x);
        }
    }

    public function testNoMissingScreenshots()
    {
        $exceptions = array(
            'tut_install/install_step2_1.png',
            'tut_install/install_step3_1.png',
        );

        foreach (array_keys($this->images) as $x) {
            if (in_array($x, $exceptions)) {
                continue;
            }

            $this->assertTrue(isset($this->images_referenced[$x]), 'Unused screenshot: ' . $x);
        }
    }
}
