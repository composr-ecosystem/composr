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
class _tutorial_quality_test_set extends cms_test_case
{
    protected $tutorials;

    public function setUp()
    {
        parent::setUp();

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        require_code('tutorials');

        $_GET['keep_tutorial_test'] = '1';

        $this->tutorials = list_tutorials();
    }

    public function testValidComcode()
    {
        if (in_safe_mode()) {
            return;
        }

        require_code('comcode_check');

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file); // TODO #3467
                check_comcode($c); // This is quite slow
            }
        }
        closedir($dh);
    }

    public function testHaveFullMetaData()
    {
        if (in_safe_mode()) {
            return;
        }

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
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, 0, 4) == 'sup_') {
                $this->assertTrue(strpos(cms_file_get_contents_safe($path . '/' . $file), 'Composr Supplementary: ') !== false, $file . ' has wrong title stub'); // TODO #3467
            }

            elseif (substr($file, 0, 4) == 'tut_') {
                $this->assertTrue(strpos(cms_file_get_contents_safe($path . '/' . $file), 'Composr Tutorial: ') !== false, $file . ' has wrong title stub'); // TODO #3467
            }
        }
        closedir($dh);
    }

    public function testHasNoIncorrectLinking()
    {
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($file === '.') || ($file === '..')) {
                continue;
            }

            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            $c = cms_file_get_contents_safe($path . '/' . $file); // TODO #3467

            $c = str_replace('[page="docs:"]', '', $c);
            $c = str_replace('[page="docs:tutorials"]', '', $c);

            $this->assertTrue(strpos($c, '[page="_SELF:') === false, $file . ' uses _SELF linking, should use _SEARCH linking');
            $this->assertTrue(strpos($c, '[page="docs:') === false, $file . ' uses docs-zone linking, should use _SEARCH linking');
        }
        closedir($dh);
    }

    protected function skip_tutorial($file)
    {
        // Not subject to ocProducts coding standards
        if (in_array(basename($file, '.txt'), array('sup_youtube_channel_integration_block_addon_documentation'))) {
            return true;
        }

        return false;
    }

    public function testHasImage()
    {
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                if ($file == 'panel_top.txt') {
                    continue;
                }

                if (in_array(basename($file, '.txt'), array('sup_glossary', 'tut_addon_index'))) {
                    continue;
                }

                if ($this->skip_tutorial($file)) {
                    continue;
                }

                $c = cms_file_get_contents_safe($path . '/' . $file); // TODO #3467

                $has_image = (strpos($c, '[media') !== false) || (strpos($c, '[img') !== false) || (strpos($c, '[code') !== false);

                $this->assertTrue($has_image, $file . ' has no images or code samples (pixabay.com has public domain no-attribution images)');
            }
        }
        closedir($dh);
    }

    public function testImageDensity()
    {
        if (in_safe_mode()) {
            return;
        }

        $data = array();

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                if ($file == 'panel_top.txt') {
                    continue;
                }

                if ($this->skip_tutorial($file)) {
                    continue;
                }

                $c = cms_file_get_contents_safe($path . '/' . $file); // TODO #3467

                $image_count = (substr_count($c, '[media')) + (substr_count($c, '[img')) + (substr_count($c, '[concepts')) + (substr_count($c, '[code')) + (substr_count($c, '[box')) + (substr_count($c, '{|')) + (substr_count($c, '<table'));
                $size = strlen($c);

                $data[] = array(
                    'file' => $file,
                    'image_count' => $image_count,
                    'size' => $size,
                    'ratio' => 100.0 * floatval($image_count) / floatval($size), // % of bytes that are images
                );
            }
        }
        closedir($dh);

        foreach ($data as $d) {
            $file = $d['file'];

            // We'll make exceptions for a few wordy ones
            if (in_array(basename($file, '.txt'), array('sup_glossary', 'tut_addon_index', 'faq', 'atag'))) {
                continue;
            }

            $this->assertTrue($d['ratio'] > 0.014 || $d['image_count'] >= 4, $file . ': media to byte ratio too low, not good for visual-orientated readers');
        }

        /*sort_maps_by($data, 'ratio');

        header('Content-type: text/plain; charset=' . get_charset());
        @var_dump($data);*/
    }

    public function testHasStandardParts()
    {
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (($this->only !== null) && ($this->only != $file)) {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                if ($file == 'panel_top.txt') {
                    continue;
                }

                $c = remove_code_block_contents(cms_file_get_contents_safe($path . '/' . $file)); // TODO #3467

                $this->assertTrue(strpos($c, '{$SET,tutorial_add_date,') !== false, $file . ' has no defined add date');
                $this->assertTrue(strpos($c, '[block]main_tutorial_rating[/block]') !== false, $file . ' has no rating block');
                if ((preg_match('#^sup_#', $file) == 0) && (substr_count($c, '[title="2"') > 1) && (strpos($file, 'codebook') === false)) {
                    $this->assertTrue(strpos($c, '[contents]decimal,lower-alpha[/contents]') !== false, $file . ' has no TOC');
                }
            }
        }
        closedir($dh);
    }
}
