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
class image_compression_test_set extends cms_test_case
{
    public function testImageCompression()
    {
        // This test is not great, as some files just don't compress well. But it does pick up Photoshops terrible lack of compression and storage of metadata

        require_code('images');
        require_code('themes2');
        require_code('images_cleanup_pipeline');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            if ($theme == '_testing_') {
                continue;
            }

            foreach (array('images', 'images_custom') as $dir) {
                $base = get_file_base() . '/themes/' . $theme . '/' . $dir;
                require_code('files2');
                $files = get_directory_contents($base);
                foreach ($files as $file) {
                    if ((is_image($file)) && (substr($file, -4) != '.ico')) {
                        $filesize = filesize($base . '/' . $file) - 100/*overhead*/;

                        // Approximate base size
                        if (substr($file, -4) == '.gif') {
                            $filesize -= 800; // For the palette (not in all gifs, but needed for non-trivial ones)
                            $min_ratio = 0.8;
                            if (is_animated_image(file_get_contents($base . '/' . $file), 'gif')) {
                                continue; // Can't do animated gifs
                            }
                        } else {
                            $filesize -= 73;
                            $min_ratio = 0.31;
                        }
                        if ($filesize < 1) {
                            $filesize = 1;
                        }

                        list($width, $height) = cms_getimagesize($base . '/' . $file);
                        $area = $width * $height;
                        $ratio = (floatval($area) / floatval($filesize));

                        $this->assertTrue($ratio > $min_ratio, 'Poor compression density on ' . $file . ' theme image; ' . float_format($ratio) . '<=' . float_format($min_ratio));
                    }
                }
            }
        }
    }
}
