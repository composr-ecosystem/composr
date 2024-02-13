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
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            foreach (['images', 'images_custom'] as $dir) {
                $base = get_file_base() . '/themes/' . $theme . '/' . $dir;
                require_code('files2');
                $files = get_directory_contents($base, '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
                foreach ($files as $path) {
                    if ((!is_image($path, IMAGE_CRITERIA_WEBSAFE | IMAGE_CRITERIA_GD_READ | IMAGE_CRITERIA_RASTER)) || (substr($path, -8) == '.gif.png')) {
                        continue;
                    }

                    $filesize = filesize($base . '/' . $path) - 100/*overhead*/;

                    // Approximate base size
                    if (substr($path, -4) == '.gif') {
                        $filesize -= 800; // For the palette (not in all gifs, but needed for non-trivial ones)
                        $min_ratio = 0.8;
                        if (is_animated_image(cms_file_get_contents_safe($base . '/' . $path, FILE_READ_LOCK), get_file_extension($path))) {
                            continue; // Can't do animated gifs
                        }
                    } else {
                        $filesize -= 73;
                        $min_ratio = 0.31;
                    }
                    if ($filesize < 1) {
                        $filesize = 1;
                    }

                    list($width, $height) = cms_getimagesize($base . '/' . $path);
                    $area = $width * $height;
                    $ratio = (floatval($area) / floatval($filesize));
                    $this->assertTrue($ratio > $min_ratio, 'Poor compression density on ' . $path . ' theme image (' . strval($width) . 'x' . strval($height) . ' is ' . clean_file_size($filesize) . ')');
                }
            }
        }
    }
}
