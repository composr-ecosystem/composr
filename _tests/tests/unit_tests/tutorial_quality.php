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
class tutorial_quality_test_set extends cms_test_case
{
    public $tutorials;

    public function setUp()
    {
        require_code('tutorials');

        $_GET['keep_tutorial_test'] = '1';

        $this->tutorials = list_tutorials();

        parent::setUp();
    }

    public function testHaveFullMetaData()
    {
        foreach ($this->tutorials as $tutorial_name => $tutorial) {
            if (is_numeric($tutorial_name)) {
                continue;
            }

            $this->assertTrue($tutorial['title'] != '', 'Title undefined for ' . $tutorial_name);
            $this->assertTrue($tutorial['author'] != '', 'Author undefined for ' . $tutorial_name);
            $this->assertTrue($tutorial['summary'] != '', 'Summary undefined for ' . $tutorial_name);
            $this->assertTrue($tutorial['icon'] != '', 'Icon undefined for ' . $tutorial_name);
            $this->assertTrue($tutorial['tags'] != array(), 'Tags undefined for ' . $tutorial_name);
            $this->assertTrue(array_intersect($tutorial['raw_tags'], array('novice', 'regular', 'expert')) != array(), 'No difficulty level defined for ' . $tutorial_name);
        }
    }

    public function testHasCorrectTitle()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, 0, 4) == 'sup_') {
                $this->assertTrue(strpos(file_get_contents($path . '/' . $f), 'Composr Supplementary: ') !== false, $f . ' has wrong title stub');
            }

            elseif (substr($f, 0, 4) == 'tut_') {
                $this->assertTrue(strpos(file_get_contents($path . '/' . $f), 'Composr Tutorial: ') !== false, $f . ' has wrong title stub');
            }
        }
    }

    public function skip_tutorial($f)
    {
        // Not subject to ocProducts coding standards
        if (in_array(basename($f, '.txt'), array('sup_youtube_channel_integration_block_addon_documentation'))) {
            return true;
        }

        return false;
    }

    public function testHasImage()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                if ($f == 'panel_top.txt') {
                    continue;
                }

                if (in_array(basename($f, '.txt'), array('sup_glossary', 'tut_addon_index'))) {
                    continue;
                }

                if ($this->skip_tutorial($f)) {
                    continue;
                }

                $c = file_get_contents($path . '/' . $f);

                $has_image = (strpos($c, '[media') !== false) || (strpos($c, '[img') !== false) || (strpos($c, '[code') !== false);

                $this->assertTrue($has_image, $f . ' has no images or code samples (pixabay.com has public domain no-attribution images)');
            }
        }
    }

    public function testImageDensity()
    {
        $data = array();

        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                if ($f == 'panel_top.txt') {
                    continue;
                }

                if ($this->skip_tutorial($f)) {
                    continue;
                }

                $c = file_get_contents($path . '/' . $f);

                $image_count = (substr_count($c, '[media')) + (substr_count($c, '[img')) + (substr_count($c, '[concepts')) + (substr_count($c, '[code')) + (substr_count($c, '[box')) + (substr_count($c, '{|')) + (substr_count($c, '<table'));
                $size = strlen($c);

                $data[] = array(
                    'file' => $f,
                    'image_count' => $image_count,
                    'size' => $size,
                    'ratio' => 100.0 * floatval($image_count) / floatval($size), // % of bytes that are images
                );
            }
        }

        foreach ($data as $d) {
            $f = $d['file'];

            // We'll make exceptions for a few wordy ones
            if (in_array(basename($f, '.txt'), array('sup_glossary', 'tut_addon_index', 'faq', 'atag'))) {
                continue;
            }

            $this->assertTrue($d['ratio'] > 0.014 || $d['image_count'] >= 4, $f . ': media to byte ratio too low, not good for visual-orientated readers');
        }

        /*sort_maps_by($data, 'ratio');

        header('Content-type: text/plain; charset=' . get_charset());
        @var_dump($data);*/
    }

    public function testHasStandardParts()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                if ($f == 'panel_top.txt') {
                    continue;
                }

                $c = remove_code_block_contents(file_get_contents($path . '/' . $f));

                $this->assertTrue(strpos($c, '{$SET,tutorial_add_date,') !== false, $f . ' has no defined add date');
                $this->assertTrue(strpos($c, '[block]main_tutorial_rating[/block]') !== false, $f . ' has no rating block');
                if (preg_match('#^sup\_#', $f) == 0 && substr_count($c, '[title="2"') > 1 && strpos($f, 'codebook') === false) {
                    $this->assertTrue(strpos($c, '[contents]decimal,lower-alpha[/contents]') !== false, $f . ' has no TOC');
                }
            }
        }
        closedir($dh);
    }
}
