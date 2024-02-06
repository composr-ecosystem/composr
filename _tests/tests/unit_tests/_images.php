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
class _images_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

        require_code('images');
        require_code('images2');
    }

    public function testIsImage()
    {
        if (!$this->isRunningTest('testIsImage')) {
            return;
        }

        $this->assertTrue(is_image('test.png', IMAGE_CRITERIA_NONE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.ico', IMAGE_CRITERIA_NONE, /* $as_admin */ false));

        $this->assertTrue(is_image('test.png', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpg', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpeg', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpe', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        $this->assertTrue(is_image('test.gif', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        $this->assertTrue(!is_image('test.ico', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));
        // May not be in PHP build $this->assertTrue(is_image('test.webp', IMAGE_CRITERIA_GD_READ, /*$as_admin*/false));
        // May not be in PHP build $this->assertTrue(is_image('test.bmp', IMAGE_CRITERIA_GD_READ, /*$as_admin*/false));
        $this->assertTrue(!is_image('test.bin', IMAGE_CRITERIA_GD_READ, /* $as_admin */ false));

        $this->assertTrue(is_image('test.png', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpg', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpeg', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpe', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.gif', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        $this->assertTrue(!is_image('test.ico', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));
        // May not be in PHP build $this->assertTrue(is_image('test.webp', IMAGE_CRITERIA_GD_WRITE, /*$as_admin*/false));
        // May not be in PHP build $this->assertTrue(is_image('test.bmp', IMAGE_CRITERIA_GD_WRITE, /*$as_admin*/false));
        $this->assertTrue(!is_image('test.bin', IMAGE_CRITERIA_GD_WRITE, /* $as_admin */ false));

        $this->assertTrue(is_image('test.png', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpg', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpeg', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.jpe', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.gif', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.ico', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(!is_image('test.webp', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        // Won't be in valid_images if not in PHP build $this->assertTrue(is_image('test.bmp', IMAGE_CRITERIA_WEBSAFE, /*$as_admin*/false));
        $this->assertTrue(!is_image('test.bin', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));

        $this->assertTrue(!is_image('test.svg', IMAGE_CRITERIA_GD_READ, /* $as_admin */ true));
        $this->assertTrue(!is_image('test.svg', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ false));
        $this->assertTrue(is_image('test.svg', IMAGE_CRITERIA_WEBSAFE, /* $as_admin */ true));

        $this->assertTrue(is_image('test.png', IMAGE_CRITERIA_RASTER, /* $as_admin */ true));
        $this->assertTrue(!is_image('test.svg', IMAGE_CRITERIA_RASTER, /* $as_admin */ true));
        $this->assertTrue(!is_image('test.png', IMAGE_CRITERIA_VECTOR, /* $as_admin */ true));
        $this->assertTrue(is_image('test.svg', IMAGE_CRITERIA_VECTOR, /* $as_admin */ true));
    }

    public function testImageSizing()
    {
        if (!$this->isRunningTest('testImageSizing')) {
            return;
        }

        $this->assertTrue(is_array(cms_getimagesize(get_file_base() . '/themes/default/images/button1.png')));
        $this->assertTrue(is_array(cms_getimagesize_url(get_base_url() . '/themes/default/images/button1.png')));
        $this->assertTrue(!isset($GLOBALS['REQUIRED_CODE']['http'])); // Should have been able to do the above using the filesystem, via a URL->path conversion
        $this->assertTrue(is_array(cms_getimagesize_url('https://compo.sr/themes/composr_homesite/images_custom/composr_homesite/composr_full_logo.png')));
        $this->assertTrue(cms_getimagesize(get_file_base() . '/themes/default/images/not_here.png') === false);

        $svg_dets = cms_getimagesize(get_file_base() . '/themes/default/images/led/off.svg');
        $this->assertTrue($svg_dets[0] == 6);
    }

    public function testIsAnimated()
    {
        if (!$this->isRunningTest('testIsAnimated')) {
            return;
        }

        require_code('images_cleanup_pipeline');

        $this->assertTrue(is_animated_image(cms_file_get_contents_safe(get_file_base() . '/themes/default/images/under_construction_animated.gif', FILE_READ_LOCK), 'gif'));
        $this->assertTrue(!is_animated_image(cms_file_get_contents_safe(get_file_base() . '/themes/default/images/flags/ZM.gif', FILE_READ_LOCK), 'gif'));

        $this->assertTrue(is_animated_image(cms_file_get_contents_safe(get_file_base() . '/themes/default/images/cns_emoticons/rockon.gif.png', FILE_READ_LOCK), 'png'));
        $this->assertTrue(!is_animated_image(cms_file_get_contents_safe(get_file_base() . '/themes/default/images/video_thumb.png', FILE_READ_LOCK), 'png'));
    }

    public function testConvertImage()
    {
        $file_aspects = [
            16 => 9,
            9 => 16,
            4 => 3,
            3 => 4,
            1 => 1
        ];

        $file_type = get_param_string('file_type', null);
        if ($file_type === null) {
            $file_types = [
                'png',
                'jpg',
                'jpeg',
                'gif',
            ];
        } else {
            $file_types = [$file_type];
        }

        $additional_information = '';

        // Tests...

        foreach ($file_types as $extension) {
            foreach ($file_aspects as $width => $height) {
                // Grow tests
                if ($this->isRunningTest('1')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, $height * 2, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Double width and height of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('2')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, $height * 3, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Double width, 3x height of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('3')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 3, $height * 2, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Double height, 3x width of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('4')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width, $height, null, /*$only_make_smaller=*/false, $width, $height, $additional_information), 'Keep the same width and height of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('5')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, null, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Double width, ignore (null) height of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('6')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, $height * 2, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Double height, ignore (null) width of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('7')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 32, /*$only_make_smaller=*/false, (($width > $height) ? 32 : null), (($height > $width) ? 32 : null), $additional_information), 'Use box width 16 of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('8')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 8, /*$only_make_smaller=*/false, (($width > $height) ? 8 : null), (($height > $width) ? 8 : null), $additional_information), 'Use box width 4 of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('9')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 2, /*$only_make_smaller=*/false, (($width > $height) ? 2 : null), (($height > $width) ? 2 : null), $additional_information), 'Use box width 1 of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('10')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, $height * 2, null, /*$only_make_smaller=*/true, $width, $height, $additional_information), 'Double width and height with only make smaller = true of ' . strval($width) . 'x' . strval($height) . '.' . $extension . '. ' . $additional_information);
                }

                // Shrink tests
                if ($this->isRunningTest('11')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width, $height, null, /*$only_make_smaller=*/false, $width, $height, $additional_information), 'Half the width and height of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('12')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width, $height * 2, null, /*$only_make_smaller=*/false, $width, $height, $additional_information), 'Half width, keep height of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('13')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, $height, null, /*$only_make_smaller=*/false, $width, $height, $additional_information), 'Half height, keep width of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('14')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width * 2, $height * 2, null, /*$only_make_smaller=*/false, $width * 2, $height * 2, $additional_information), 'Keep the same width and height of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('15')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width, null, null, /*$only_make_smaller=*/false, $width, null, $additional_information), 'Half the width, ignore height of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('16')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, $height, null, /*$only_make_smaller=*/false, null, $height, $additional_information), 'Half the height, ignore width of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('17')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 16, /*$only_make_smaller=*/false, (($width > $height) ? 16 : null), (($height > $width) ? 16 : null), $additional_information), 'Use box width 16 of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('18')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 4, /*$only_make_smaller=*/false, (($width > $height) ? 4 : null), (($height > $width) ? 4 : null), $additional_information), 'Use box width 4 of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('19')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, null, null, 1, /*$only_make_smaller=*/false, (($width > $height) ? 1 : null), (($height > $width) ? 1 : null), $additional_information), 'Use box width 1 of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
                if ($this->isRunningTest('20')) {
                    $this->assertTrue($this->runDimensionTest(get_file_base() . '/_tests/assets/images/' . strval($width) . 'x' . strval($height) . '.' . $extension, $width, $height, null, /*$only_make_smaller=*/true, $width, $height, $additional_information), 'Half the width and height with only make smaller = true of ' . strval($width * 2) . 'x' . strval($height * 2) . '.' . $extension . '. ' . $additional_information);
                }
            }

            // Edge case: transparency and translucent (50% opacity) tests
            if ($extension === 'png' || $extension === 'gif') { // jpg and jpeg does not support transparency
                if ($this->isRunningTest('21')) {
                    $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent.' . $extension, 16, 16, 127, $additional_information), 'Increased 8x8 transparent.' . $extension . ' to 16x16 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
                }
                if ($this->isRunningTest('22')) {
                    $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent.' . $extension, 8, 8, 127, $additional_information), '8x8 transparent.' . $extension . '. Kept size the same. Tested for transparency on the left side and visible color on the right side. ' . $additional_information);
                }
                if ($this->isRunningTest('23')) {
                    $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent.' . $extension, 4, 4, 127, $additional_information), 'Decreased 8x8 transparent.' . $extension . ' to 4x4 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
                }
                if ($extension === 'png') { // Only png supports alpha channel transparency
                    if ($this->isRunningTest('24')) {
                        $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/translucent.' . $extension, 16, 16, 63, $additional_information), 'Increased 8x8 translucent.' . $extension . ' to 16x16 and tested for 50 percent opacity on the left side and 100 percent opacity on the right side. ' . $additional_information);
                    }
                    if ($this->isRunningTest('25')) {
                        $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/translucent.' . $extension, 8, 8, 63, $additional_information), '8x8 translucent.' . $extension . '. Kept size the same. Tested for 50 percent opacity on the left side and 100 percent opacity on the right side. ' . $additional_information);
                    }
                    if ($this->isRunningTest('26')) {
                        $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/translucent.' . $extension, 4, 4, 63, $additional_information), 'Decreased 8x8 translucent.' . $extension . ' to 4x4 and tested for 50 percent opacity on the left side and 100 percent opacity on the right side. ' . $additional_information);
                    }
                }
            }

            // Edge case: Quadrant color test
            if ($this->isRunningTest('27')) {
                $this->assertTrue($this->runQuadrantTest(get_file_base() . '/_tests/assets/images/quadrant.' . $extension, 16, 16, $additional_information), 'Increased 8x8 quadrant.' . $extension . ' to 16x16 and tested for quadrant colors (top->bottom, left->right) red, green, blue, white. ' . $additional_information);
            }
            if ($this->isRunningTest('28')) {
                $this->assertTrue($this->runQuadrantTest(get_file_base() . '/_tests/assets/images/quadrant.' . $extension, 8, 8, $additional_information), '8x8 quadrant.' . $extension . '. Kept size the same. Tested for quadrant colors (top->bottom, left->right) red, green, blue, white. ' . $additional_information);
            }
            if ($this->isRunningTest('29')) {
                $this->assertTrue($this->runQuadrantTest(get_file_base() . '/_tests/assets/images/quadrant.' . $extension, 4, 4, $additional_information), 'Decreased 8x8 quadrant.' . $extension . ' to 4x4 and tested for quadrant colors (top->bottom, left->right) red, green, blue, white. ' . $additional_information);
            }
        }

        // Edge Case: palette-alpha PNG transparency test
        if ($this->isRunningTest('30')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_alpha.png', 16, 16, 127, $additional_information), 'Increased 8x8 transparent_palette_alpha.png to 16x16 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }
        if ($this->isRunningTest('31')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_alpha.png', 8, 8, 127, $additional_information), '8x8 transparent_palette_alpha.png. Kept size the same. Tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }
        if ($this->isRunningTest('32')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_alpha.png', 4, 4, 127, $additional_information), 'Decreased 8x8 transparent_palette_alpha.png to 4x4 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }

        // Edge Case: palette-binary PNG transparency test
        /*
        PHP/GD loads this as 32-bit, so it doesn't actually work :(. That said, it was incredibly hard to produce this test image and I couldn't find any in the wild. No software worked with it either.
        if ($this->isRunningTest('33')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_binary.png', 16, 16, 127, $additional_information), 'Increased 8x8 transparent_palette_binary.png to 16x16 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }
        if ($this->isRunningTest('34')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_binary.png', 8, 8, 127, $additional_information), '8x8 transparent_palette_binary.png. Kept size the same. Tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }
        if ($this->isRunningTest('35')) {
            $this->assertTrue($this->runTransparencyTest(get_file_base() . '/_tests/assets/images/transparent_palette_binary.png', 4, 4, 127, $additional_information), 'Decreased 8x8 transparent_palette_binary.png to 4x4 and tested for transparency on the left side and visible color on the right side. ' . $additional_information);
        }
        */

        // Edge Case: EXIF rotation test via dimension test and pixel color test
        if (function_exists('exif_read_data')) {
            if ($this->isRunningTest('36')) {
                $this->assertTrue($this->runEXIFTest(get_file_base() . '/_tests/assets/images/exifrotated.jpg', 4896, 6528, 4896, 6528, 4850, 250, 205, 164, 85, 0, $additional_information), 'exifrotated.jpg EXIF rotation test (size and color). Doubled the original size. ' . $additional_information);
            }
            if ($this->isRunningTest('37')) {
                $this->assertTrue($this->runEXIFTest(get_file_base() . '/_tests/assets/images/exifrotated.jpg', 2448, 3264, 2448, 3264, 2425, 175, 205, 164, 85, 0, $additional_information), 'ExifRotated.jpg EXIF rotation test (size and color). Kept the original size. ' . $additional_information);
            }
            if ($this->isRunningTest('38')) {
                $this->assertTrue($this->runEXIFTest(get_file_base() . '/_tests/assets/images/exifrotated.jpg', 1224, 1632, 1224, 1632, 1212, 62, 206, 165, 86, 0, $additional_information), 'xxifrotated.jpg EXIF rotation test (size and color). Halved the original size. ' . $additional_information);
            }
        } else {
            $this->assertTrue(false, 'Skipping rotation tests as PHP does not have EXIF');
        }

        // Edge case: SVG image content tests
        if ($this->isRunningTest('39')) {
            $this->assertTrue($this->runSvgTest(get_file_base() . '/_tests/assets/images/tux.svg', 670, 788, $additional_information), 'tux.svg contents test. Double the original size. ' . $additional_information);
        }
        if ($this->isRunningTest('40')) {
            $this->assertTrue($this->runSvgTest(get_file_base() . '/_tests/assets/images/tux.svg', 335, 394, $additional_information), 'tux.svg contents test. Kept the original size. ' . $additional_information);
        }
        if ($this->isRunningTest('41')) {
            $this->assertTrue($this->runSvgTest(get_file_base() . '/_tests/assets/images/tux.svg', 167, 197, $additional_information), 'tux.svg contents test. Half the original size. ' . $additional_information);
        }
    }

    public function testConvertImagePlus()
    {
        $additional_information = '';

        $file_type = get_param_string('file_type', null);
        if ($file_type === null) {
            $file_types = [
                'png',
                'jpg',
                'jpeg',
                'gif',
            ];
        } else {
            $file_types = [$file_type];
        }

        $wheres = ['both', 'start', 'end', 'start_if_vertical', 'start_if_horizontal', 'end_if_vertical', 'end_if_horizontal'];

        $this->cleanupOutFiles();

        // Tests...

        foreach ($file_types as $extension) {
            // Basic box tests
            if ($this->isRunningTest('42')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '64x36', 64, 36, 'box', 'both', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Box algorithm where = both and two dimensions specified. ' . $additional_information);
            }
            if ($this->isRunningTest('43')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '96x', 96, 54, 'box', 'both', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Box algorithm where = both and only width specified. ' . $additional_information);
            }
            if ($this->isRunningTest('44')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, 'x45', 45, 25, 'box', 'both', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Box algorithm where = both and only height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('45')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, null, intval(get_option('thumb_width')), null, 'box', 'both', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Box algorithm where = both. null dimensions. ' . $additional_information);
            }

            // Basic width and height tests
            if ($this->isRunningTest('46')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '480x', 480, null, 'box', 'width', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Width algorithm where = both and only width specified. ' . $additional_information);
            }
            if ($this->isRunningTest('47')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, 'x360', 360, null, 'box', 'width', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Width algorithm where = both and only height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('48')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '640x360', 640, null, 'box', 'width', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Width algorithm where = both and both width and height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('49')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '240x', 240, null, 'box', 'height', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Height algorithm where = both and only width specified. ' . $additional_information);
            }
            if ($this->isRunningTest('50')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, 'x180', 180, null, 'box', 'height', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Height algorithm where = both and only height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('51')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '640x360', null, 360, 'box', 'height', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . '. Height algorithm where = both and both width and height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('52')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '480x', null, 480, 'box', 'width', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . '. Width algorithm where = both and only width specified. ' . $additional_information);
            }
            if ($this->isRunningTest('53')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/9x16.' . $extension, 'x360', null, 360, 'box', 'width', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . '. Width algorithm where = both and only height specified. ' . $additional_information);
            }
            if ($this->isRunningTest('54')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '240x', null, 240, 'box', 'height', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . '. Height algorithm where = both and only width specified. ' . $additional_information);
            }
            if ($this->isRunningTest('55')) {
                $this->assertTrue($this->runDimensionTestPlus(get_base_url() . '/_tests/assets/images/9x16.' . $extension, 'x180', null, 180, 'box', 'height', /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . '. Height algorithm where = both and only height specified. ' . $additional_information);
            }

            // Crop tests
            foreach ($wheres as $where) {
                // Down scaling crop
                if ($this->isRunningTest('56')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_8x8.' . $extension, '8x8', 8, 8, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('57')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_16x9.' . $extension, '16x9', 16, 9, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (equal aspect test), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('58')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_4x8.' . $extension, '4x8', 4, 8, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 4x8 (unequal aspect test, 16:9 => 1:2), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                // Same scaling crop
                if ($this->isRunningTest('59')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_8x8.' . $extension, '16x16', 16, 16, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x16 (unequal aspect test, 16:9 => 1:1), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('60')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_16x9.' . $extension, '32x18', 32, 18, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 32x18 (equal aspect test), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('61')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_4x8.' . $extension, '8x16', 8, 16, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 4x8 (unequal aspect test, 16:9 => 1:2), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                // Up scaling crop
                if ($this->isRunningTest('62')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_8x8.' . $extension, '32x32', 32, 32, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 32x32 (unequal aspect test, 16:9 => 1:1), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('63')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_16x9.' . $extension, '64x36', 64, 36, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 64x36 (equal aspect test), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('64')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_4x8.' . $extension, '32x64', 32, 64, 'crop', $where, /*$only_make_smaller=*/false, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 32x64 (unequal aspect test, 16:9 => 1:2), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
                if ($this->isRunningTest('65')) {
                    $this->assertTrue($this->runCropTest(get_base_url() . '/_tests/assets/images/crop_' . $where . '_32x18_4x8.' . $extension, '32x64', 32, 64, 'crop', $where, /*$only_make_smaller=*/true, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 32x64 (only_make_smaller = true test; this parameter should be ignored for cropping.), Crop algorithm where = ' . $where . '. ' . $additional_information);
                }
            }

            // Pad tests
            // Down-scaling where = both
            if ($this->isRunningTest('66')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'both', /*$only_make_smaller=*/false, 0, 1, 0, 2, 7, 7, 7, 6, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('67')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'both', /*$only_make_smaller=*/false, 2, 0, 3, 0, 6, 8, 6, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('68')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'both', /*$only_make_smaller=*/false, 0, 2, 0, 1, 4, 2, 4, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('69')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'both', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 4, 1, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('70')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'both', /*$only_make_smaller=*/false, 0, 1, 0, 2, 3, 3, 3, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('71')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'both', /*$only_make_smaller=*/false, 1, 0, 2, 0, 3, 3, 2, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('72')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('73')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'both', /*$only_make_smaller=*/false, 0, 5, 0, 6, 9, 10, 9, 9, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('74')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'both', /*$only_make_smaller=*/false, 2, 0, 3, 0, 7, 6, 6, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('75')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('76')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = both. ' . $additional_information);
            }
            // Same scaling where = both
            if ($this->isRunningTest('77')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('78')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('79')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = both. ' . $additional_information);
            }
            // Up-scaling where = both
            if ($this->isRunningTest('80')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('81')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'both', /*$only_make_smaller=*/false, 0, 2, 0, 3, 6, 7, 6, 6, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('82')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'both', /*$only_make_smaller=*/false, 11, 0, 12, 0, 21, 18, 20, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('83')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'both', /*$only_make_smaller=*/false, 2, 0, 3, 0, 14, 9, 13, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('84')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'both', /*$only_make_smaller=*/false, 0, 2, 0, 3, 9, 14, 9, 13, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('85')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'both', /*$only_make_smaller=*/false, 0, 4, 0, 5, 9, 11, 9, 10, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('86')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'both', /*$only_make_smaller=*/false, 4, 0, 5, 0, 11, 9, 10, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('87')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('88')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'both', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('89')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'both', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = both. ' . $additional_information);
            }
            // Down-scaling where = start
            if ($this->isRunningTest('90')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'start', /*$only_make_smaller=*/false, 0, 5, 0, 4, 8, 5, 8, 4, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('91')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'start', /*$only_make_smaller=*/false, 5, 0, 4, 0, 5, 8, 4, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('92')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'start', /*$only_make_smaller=*/false, 0, 2, 0, 1, 4, 2, 4, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('93')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'start', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 4, 1, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('94')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'start', /*$only_make_smaller=*/false, 0, 2, 0, 1, 3, 2, 3, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('95')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'start', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 3, 1, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('96')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('97')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'start', /*$only_make_smaller=*/false, 0, 5, 0, 4, 9, 5, 9, 4, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('98')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'start', /*$only_make_smaller=*/false, 5, 0, 4, 0, 5, 6, 4, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('99')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('100')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = start. ' . $additional_information);
            }
            // Same scaling where = start
            if ($this->isRunningTest('101')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('102')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('103')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = start. ' . $additional_information);
            }
            // Up-scaling where = start
            if ($this->isRunningTest('104')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('105')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'start', /*$only_make_smaller=*/false, 0, 5, 0, 4, 6, 5, 6, 4, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('106')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'start', /*$only_make_smaller=*/false, 10, 0, 9, 0, 10, 18, 9, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('107')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'start', /*$only_make_smaller=*/false, 12, 0, 11, 0, 12, 9, 11, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('108')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'start', /*$only_make_smaller=*/false, 0, 12, 0, 11, 9, 12, 9, 11, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('109')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'start', /*$only_make_smaller=*/false, 0, 7, 0, 6, 9, 7, 9, 6, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('110')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'start', /*$only_make_smaller=*/false, 7, 0, 6, 0, 7, 9, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('111')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('112')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'start', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = start. ' . $additional_information);
            }
            if ($this->isRunningTest('113')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = start. ' . $additional_information);
            }
            // Down-scaling where = end
            if ($this->isRunningTest('114')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'end', /*$only_make_smaller=*/false, 0, 4, 0, 5, 8, 4, 8, 5, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('115')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'end', /*$only_make_smaller=*/false, 4, 0, 5, 0, 4, 8, 5, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('116')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'end', /*$only_make_smaller=*/false, 0, 1, 0, 2, 4, 1, 4, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('117')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'end', /*$only_make_smaller=*/false, 1, 0, 2, 0, 1, 4, 2, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('118')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'end', /*$only_make_smaller=*/false, 0, 1, 0, 2, 3, 1, 3, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('119')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'end', /*$only_make_smaller=*/false, 1, 0, 2, 0, 1, 3, 2, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('120')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('121')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'end', /*$only_make_smaller=*/false, 0, 11, 0, 12, 9, 11, 9, 12, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('122')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'end', /*$only_make_smaller=*/false, 4, 0, 5, 0, 4, 6, 5, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('123')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('124')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = end. ' . $additional_information);
            }
            // Same scaling where = end
            if ($this->isRunningTest('125')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('126')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('127')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = end. ' . $additional_information);
            }
            // Up-scaling where = end
            if ($this->isRunningTest('128')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('129')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'end', /*$only_make_smaller=*/false, 0, 4, 0, 5, 6, 4, 6, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('130')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'end', /*$only_make_smaller=*/false, 22, 0, 23, 0, 22, 18, 23, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('131')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'end', /*$only_make_smaller=*/false, 4, 0, 4, 0, 4, 9, 5, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('132')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'end', /*$only_make_smaller=*/false, 0, 4, 0, 5, 9, 4, 9, 5, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('133')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'end', /*$only_make_smaller=*/false, 0, 9, 0, 10, 9, 9, 9, 10, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('134')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'end', /*$only_make_smaller=*/false, 9, 0, 10, 0, 9, 9, 10, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('135')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('136')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'end', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = end. ' . $additional_information);
            }
            if ($this->isRunningTest('137')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = end. ' . $additional_information);
            }
            // Down-scaling where = start_if_vertical
            if ($this->isRunningTest('138')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 5, 0, 4, 8, 5, 8, 4, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('139')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 7, 8, 6, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('140')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 2, 0, 1, 4, 2, 4, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('141')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 4, 1, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('142')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 2, 0, 1, 3, 2, 3, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('143')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 1, 0, 2, 0, 3, 3, 2, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('144')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('145')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 5, 0, 4, 9, 5, 9, 4, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('146')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 7, 6, 6, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('147')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('148')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            // Same scaling where = start_if_vertical
            if ($this->isRunningTest('149')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('150')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('151')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            // Up-scaling where = start_if_vertical
            if ($this->isRunningTest('152')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('153')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 5, 0, 4, 6, 5, 6, 4, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('154')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 11, 0, 12, 0, 21, 18, 20, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('155')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 14, 9, 13, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('156')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 12, 0, 11, 9, 12, 9, 11, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('157')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 0, 7, 0, 6, 9, 7, 9, 6, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('158')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, 4, 0, 5, 0, 11, 9, 10, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('159')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('160')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'start_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('161')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start_if_vertical', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = start_if_vertical. ' . $additional_information);
            }
            // Down-scaling where = start_if_horizontal
            if ($this->isRunningTest('162')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 8, 8, 8, 6, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('163')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 5, 0, 4, 0, 5, 8, 4, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('164')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 1, 4, 2, 4, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('165')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 4, 1, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('166')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 1, 0, 2, 3, 3, 3, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('167')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 3, 1, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('168')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('169')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 5, 0, 6, 9, 10, 9, 9, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('170')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 5, 0, 4, 0, 5, 6, 4, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('171')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('172')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            // Same scaling where = start_if_horizontal
            if ($this->isRunningTest('173')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('174')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('175')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            // Up-scaling where = start_if_horizontal
            if ($this->isRunningTest('176')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('177')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 6, 7, 6, 6, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('178')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 10, 0, 9, 0, 10, 18, 9, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('179')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 12, 0, 11, 0, 12, 9, 11, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('180')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 9, 14, 9, 13, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('181')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 0, 4, 0, 5, 9, 11, 9, 10, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('182')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, 7, 0, 6, 0, 7, 9, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('183')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('184')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('185')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'start_if_horizontal', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = start_if_horizontal. ' . $additional_information);
            }
            // Down-scaling where = end_if_vertical
            if ($this->isRunningTest('186')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 4, 0, 5, 8, 4, 8, 5, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('187')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 7, 8, 6, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('188')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 1, 0, 2, 4, 1, 4, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('189')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 2, 0, 1, 0, 2, 4, 1, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('190')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 2, 0, 3, 3, 2, 3, 3, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('191')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 1, 0, 2, 0, 3, 3, 2, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('192')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('193')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 11, 0, 12, 9, 11, 9, 12, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('194')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 7, 6, 6, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('195')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('196')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            // Same scaling where = end_if_vertical
            if ($this->isRunningTest('197')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('198')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('199')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            // Up-scaling where = end_if_vertical
            if ($this->isRunningTest('200')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('201')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 4, 0, 5, 6, 4, 6, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('202')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 11, 0, 12, 0, 21, 18, 20, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('203')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 2, 0, 3, 0, 14, 9, 13, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('204')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 4, 0, 5, 9, 4, 9, 5, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('205')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 0, 9, 0, 10, 9, 9, 9, 10, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('206')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, 4, 0, 5, 0, 11, 9, 10, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('207')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('208')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'end_if_vertical', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            if ($this->isRunningTest('209')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end_if_vertical', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = end_if_vertical. ' . $additional_information);
            }
            // Down-scaling where = end_if_horizontal
            if ($this->isRunningTest('210')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '8x8', 8, 8, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 8, 7, 8, 6, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 8x8 (unequal aspect test, 16:9 => 1:1), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('211')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '8x8', 8, 8, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 4, 0, 5, 0, 4, 8, 5, 8, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 8x8 (unequal aspect test, 9:16 => 1:1), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('212')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '4x3', 4, 3, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 1, 4, 2, 4, 1, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 4x3 (unequal aspect test, 16:9 => 4:3), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('213')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '3x4', 3, 4, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 1, 0, 2, 0, 1, 4, 2, 4, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 3x4 (unequal aspect test, 9:16 => 3:4), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('214')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '3x4', 3, 4, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 1, 0, 2, 3, 3, 3, 2, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 3x4 (unequal aspect + flip width/height test, 16:9 => 3:4), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('215')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '4x3', 4, 3, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 2, 0, 3, 0, 2, 3, 3, 3, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 4x3 (unequal aspect + flip width/height test, 9:16 => 4:3), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('216')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x9', 16, 9, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x9 (Equal aspect test, 16:9 => 16:9), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('217')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '9x16', 9, 16, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 5, 0, 6, 9, 10, 9, 9, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 9x16 (Reversed aspect test, 16:9 => 9:16), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('218')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, '8x6', 8, 6, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 4, 0, 5, 0, 4, 6, 5, 6, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => 8x6 (Reversed aspect test, 3:4 => 4:3), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('219')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/32x18.' . $extension, '16x', 16, 9, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 32x18 ' . $extension . ' => 16x (Unspecified dimension test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('220')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/6x8.' . $extension, 'x4', 3, 4, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 2, 3, $additional_information), 'convert_image_plus w/ 6x8 ' . $extension . ' => x4 (Unspecified dimension test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            // Same scaling where = end_if_horizontal
            if ($this->isRunningTest('221')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/2x2.' . $extension, '2x2', 2, 2, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 1, 1, $additional_information), 'convert_image_plus w/ 2x2 ' . $extension . ' => 2x2 (same dimensions test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('222')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '16x9', 16, 9, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 15, 8, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 16x9 (same dimensions test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('223')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '9x16', 9, 16, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 8, 15, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 9x16 (same dimensions test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            // Up-scaling where = end_if_horizontal
            if ($this->isRunningTest('224')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 7, 5, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (Equal aspect test, 4:3 => 4:3), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('225')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '6x8', 6, 8, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 6, 7, 6, 6, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 6x8 (Reversed aspect test, 4:3 => 3:4), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('226')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/9x16.' . $extension, '32x18', 32, 18, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 22, 0, 23, 0, 22, 18, 23, 18, $additional_information), 'convert_image_plus w/ 9x16 ' . $extension . ' => 32x18 (Reversed aspect test, 9:16 => 16:9), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('227')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '16x9', 16, 9, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 4, 0, 5, 0, 4, 9, 5, 9, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 16x9 (unequal aspect test, 4:3 => 16:9), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('228')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '9x16', 9, 16, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 2, 0, 3, 9, 14, 9, 13, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 9x16 (unequal aspect test, 3:4 => 9:16), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('229')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '9x16', 9, 16, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 0, 4, 0, 5, 9, 11, 9, 10, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 9x16 (unequal aspect + flip width/height test, 4:3 => 9:16), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('230')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, '16x9', 16, 9, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, 9, 0, 10, 0, 9, 9, 10, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => 16x9 (unequal aspect + flip width/height test, 3:4 => 16:9), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('231')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/16x9.' . $extension, '24x', 24, 13, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 23, 12, $additional_information), 'convert_image_plus w/ 16x9 ' . $extension . ' => 24x (Unspecified dimension + uneven (1.5) scale test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('232')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/3x4.' . $extension, 'x10', 7, 10, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/false, null, null, 0, 0, null, null, 6, 9, $additional_information), 'convert_image_plus w/ 3x4 ' . $extension . ' => x10 (Unspecified dimension + uneven (2.5) scale test), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }
            if ($this->isRunningTest('233')) {
                $this->assertTrue($this->runPadTest(get_base_url() . '/_tests/assets/images/4x3.' . $extension, '8x6', 8, 6, 'pad', 'end_if_horizontal', /*$only_make_smaller=*/true, null, null, 0, 0, null, null, 3, 2, $additional_information), 'convert_image_plus w/ 4x3 ' . $extension . ' => 8x6 (only_make_smaller = true test; this parameter should be ignored for padding), Pad algorithm where = end_if_horizontal. ' . $additional_information);
            }

            // Pad-Crop Tests (some simple ones just to confirm the pad vs crop algorithm works)
            if ($this->isRunningTest('234')) {
                $this->assertTrue($this->runPadCropTest(get_base_url() . '/_tests/assets/images/pad_crop_both_32x18_16x9.' . $extension, '32x9', 32, 9, 'pad_vert_crop_vert', 'both', /*$only_make_smaller=*/false, null, null, 8, 0, 7, 0, null, null, 24, 9, 25, 9, $additional_information), 'convert_image_plus w/ 32x18 and 16x9 object ' . $extension . ' => 32x9 pad_vert_crop_vert algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('235')) {
                $this->assertTrue($this->runPadCropTest(get_base_url() . '/_tests/assets/images/pad_crop_both_32x18_16x9.' . $extension, '32x9', 32, 9, 'pad_horiz_crop_horiz', 'both', /*$only_make_smaller=*/false, 0, 0, null, null, null, null, 31, 8, null, null, null, null, $additional_information), 'convert_image_plus w/ 32x18 and 16x9 object ' . $extension . ' => 32x9 pad_vert_crop_vert algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('236')) {
                $this->assertTrue($this->runPadCropTest(get_base_url() . '/_tests/assets/images/pad_crop_both_32x18_16x9.' . $extension, '32x36', 32, 36, 'pad_vert_crop_vert', 'both', /*$only_make_smaller=*/false, 0, 0, null, null, null, null, 31, 35, null, null, null, null, $additional_information), 'convert_image_plus w/ 32x18 and 16x9 object ' . $extension . ' => 32x9 pad_vert_crop_vert algorithm where = both. ' . $additional_information);
            }
            if ($this->isRunningTest('237')) {
                $this->assertTrue($this->runPadCropTest(get_base_url() . '/_tests/assets/images/pad_crop_both_32x18_16x9.' . $extension, '32x36', 32, 36, 'pad_horiz_crop_horiz', 'both', /*$only_make_smaller=*/false, null, null, null, null, 0, 0, null, null, null, null, 31, 35, $additional_information), 'convert_image_plus w/ 32x18 and 16x9 object ' . $extension . ' => 32x9 pad_vert_crop_vert algorithm where = both. ' . $additional_information);
            }
        }

        $this->cleanupOutFiles();
    }

    // Helper functions...

    protected function isRunningTest($name)
    {
        if ($this->only === null) {
            return true;
        }
        if ($this->only == $name) {
            return true;
        }
        $matches = [];
        if (preg_match('#^(\d+)\-(\d+)$#', $this->only, $matches) != 0) {
            $from = intval($matches[1]);
            $to = intval($matches[2]);
            if (($from <= intval($name)) && ($to >= intval($name))) {
                return true;
            }
        }
        return false;
    }

    protected function runDimensionTest($in_path, $convert_width, $convert_height, $box_size, $only_make_smaller, $expected_width, $expected_height, &$additional_information)
    {
        $additional_information = '';
        $out_path = cms_tempnam();

        if (!$this->convertImage($in_path, $out_path, $convert_width, $convert_height, $box_size, $only_make_smaller, $additional_information)) {
            return false;
        }

        if (!$this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runDimensionTestPlus($in_url, $dimensions, $expected_width, $expected_height, $algorithm, $where, $only_make_smaller, &$additional_information)
    {
        $additional_information = '';
        $out_path = $this->convertImagePlus($in_url, $dimensions, $algorithm, $where, $only_make_smaller, $additional_information);

        if ($out_path === false) {
            return false;
        }

        if (!$this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runTransparencyTest($in_path, $convert_width, $convert_height, $transparency, &$additional_information)
    {
        $additional_information = '';
        $out_path = cms_tempnam();

        if (!$this->convertImage($in_path, $out_path, $convert_width, $convert_height, null, false, $additional_information)) {
            return false;
        }

        $dimensions = $this->checkImageSize($out_path, null, null, $additional_information);
        if ($dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        if (!$this->checkColor($out_path, $image_resource, intval($dimensions[0] / 4), intval($dimensions[1] / 2), null, null, null, $transparency, 4, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runQuadrantTest($in_path, $convert_width, $convert_height, &$additional_information)
    {
        $additional_information = '';
        $out_path = cms_tempnam();

        if (!$this->convertImage($in_path, $out_path, $convert_width, $convert_height, null, false, $additional_information)) {
            return false;
        }

        $dimensions = $this->checkImageSize($out_path, null, null, $additional_information);
        if ($dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        // Find quadrant centres, biased a bit towards edges to avoid problems with blending
        $min_x = max(0, intval($dimensions[0] / 4) - 1);
        $min_y = max(0, intval($dimensions[1] / 4) - 1);
        $max_x = min($dimensions[0] - 1, intval($dimensions[0] * 0.75) + 1);
        $max_y = min($dimensions[1] - 1, intval($dimensions[1] * 0.75) + 1);

        // Test red quadrant in upper left corner
        if (!$this->checkColor($out_path, $image_resource, $min_x, $min_y, 255, 0, 0, 0, 60, $additional_information)) {
            return false;
        }

        // Test green quadrant in upper right corner
        if (!$this->checkColor($out_path, $image_resource, $max_x, $min_y, 0, 255, 0, 0, 60, $additional_information)) {
            return false;
        }

        // Test blue quadrant in lower left corner
        if (!$this->checkColor($out_path, $image_resource, $min_x, $max_y, 0, 0, 255, 0, 60, $additional_information)) {
            return false;
        }

        // Test white quadrant in lower right corner
        if (!$this->checkColor($out_path, $image_resource, $max_x, $max_y, 255, 255, 255, 0, 60, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runEXIFTest($in_path, $convert_width, $convert_height, $expected_width, $expected_height, $test_x, $test_y, $expected_red, $expected_green, $expected_blue, $expected_alpha, &$additional_information)
    {
        $additional_information = '';
        $out_path = cms_tempnam();

        if (!$this->convertImage($in_path, $out_path, $convert_width, $convert_height, null, false, $additional_information)) {
            return false;
        }

        $dimensions = $this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information);
        if ($dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        // Test specific pixel color
        if (!$this->checkColor($out_path, $image_resource, $test_x, $test_y, $expected_red, $expected_green, $expected_blue, $expected_alpha, 8, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runSvgTest($in_path, $convert_width, $convert_height, &$additional_information)
    {
        $additional_information = '';
        $out_path = cms_tempnam();

        $contents_original = $this->getImageContents($in_path, $additional_information);
        if ($contents_original === false) {
            return false;
        }

        if (!$this->convertImage($in_path, $out_path, $convert_width, $convert_height, null, false, $additional_information)) {
            return false;
        }

        $contents_modified = $this->getImageContents($out_path, $additional_information);
        if ($contents_modified === false) {
            return false;
        }

        if (!$this->checkImageStringsAreSame($contents_original, $contents_modified, $additional_information)) {
            return false;
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runCropTest($in_url, $dimensions, $expected_width, $expected_height, $algorithm, $where, $only_make_smaller, &$additional_information)
    {
        $additional_information = '';
        $out_path = $this->convertImagePlus($in_url, $dimensions, $algorithm, $where, $only_make_smaller, $additional_information);

        if ($out_path === false) {
            return false;
        }

        $image_dimensions = $this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information);
        if ($image_dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        // Crop pixel color test
        if ($algorithm === 'crop') {
            if (!$this->checkRedPatch($out_path, $image_resource, 0, 0, $image_dimensions, 128, $additional_information)) {
                return false;
            }
            if (!$this->checkRedPatch($out_path, $image_resource, $image_dimensions[0] - 1, $image_dimensions[1] - 1, $image_dimensions, 128, $additional_information)) {
                return false;
            }
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runPadTest($in_url, $dimensions, $expected_width, $expected_height, $algorithm, $where, $only_make_smaller, $black_x1, $black_y1, $red_x1, $red_y1, $black_x2, $black_y2, $red_x2, $red_y2, &$additional_information)
    {
        $additional_information = '';
        $out_path = $this->convertImagePlus($in_url, $dimensions, $algorithm, $where, $only_make_smaller, $additional_information);

        if ($out_path === false) {
            return false;
        }

        $image_dimensions = $this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information);
        if ($image_dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        // Pad pixel color test
        if ($algorithm === 'pad') {
            if ($black_x1 !== null && $black_y1 !== null) {
                if (!$this->checkBlackPatch($out_path, $image_resource, $black_x1, $black_y1, $image_dimensions, 8, $additional_information)) {
                    return false;
                }
            }
            if ($red_x1 !== null && $red_y1 !== null) {
                if (!$this->checkRedPatch($out_path, $image_resource, $red_x1, $red_y1, $image_dimensions, 8, $additional_information)) {
                    return false;
                }
            }
            if ($black_x2 !== null && $black_y2 !== null) {
                if (!$this->checkBlackPatch($out_path, $image_resource, $black_x2, $black_y2, $image_dimensions, 8, $additional_information)) {
                    return false;
                }
            }
            if ($red_x2 !== null && $red_y2 !== null) {
                if (!$this->checkRedPatch($out_path, $image_resource, $red_x2, $red_y2, $image_dimensions, 8, $additional_information)) {
                    return false;
                }
            }
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function runPadCropTest($in_url, $dimensions, $expected_width, $expected_height, $algorithm, $where, $only_make_smaller, $black_x1, $black_y1, $red_x1, $red_y1, $blue_x1, $blue_y1, $black_x2, $black_y2, $red_x2, $red_y2, $blue_x2, $blue_y2, &$additional_information)
    {
        $additional_information = '';
        $out_path = $this->convertImagePlus($in_url, $dimensions, $algorithm, $where, $only_make_smaller, $additional_information);

        if ($out_path === false) {
            return false;
        }

        $image_dimensions = $this->checkImageSize($out_path, $expected_width, $expected_height, $additional_information);
        if ($image_dimensions === false) {
            return false;
        }

        $file_contents = $this->getImageContents($out_path, $additional_information);
        if ($file_contents === false) {
            return false;
        }

        $image_resource = $this->createImageFromString($file_contents, $additional_information);
        if ($image_resource === false) {
            return false;
        }

        if ($black_x1 !== null && $black_y1 !== null) {
            if (!$this->checkBlackPatch($out_path, $image_resource, $black_x1, $black_y1, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }
        if ($red_x1 !== null && $red_y1 !== null) {
            if (!$this->checkRedPatch($out_path, $image_resource, $red_x1, $red_y1, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }
        if ($blue_x1 !== null && $blue_y1 !== null) {
            if (!$this->checkBluePatch($out_path, $image_resource, $blue_x1, $blue_y1, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }
        if ($black_x2 !== null && $black_y2 !== null) {
            if (!$this->checkBlackPatch($out_path, $image_resource, $black_x2, $black_y2, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }
        if ($red_x2 !== null && $red_y2 !== null) {
            if (!$this->checkRedPatch($out_path, $image_resource, $red_x2, $red_y2, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }
        if ($blue_x2 !== null && $blue_y2 !== null) {
            if (!$this->checkBluePatch($out_path, $image_resource, $blue_x2, $blue_y2, $image_dimensions, 8, $additional_information)) {
                return false;
            }
        }

        $this->outputDebugVisual($out_path, true);

        return true;
    }

    protected function cleanupOutFiles()
    {
        $dh = opendir(get_custom_file_base() . '/temp');
        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if (is_image($file, IMAGE_CRITERIA_GD_READ)) {
                    unlink(get_custom_file_base() . '/temp/' . $file);
                }
            }
            closedir($dh);
        }
    }

    protected function convertImage($in_path, &$out_path, $convert_width, $convert_height, $box_size, $only_make_smaller, &$additional_information)
    {
        foreach ([$in_path => true, preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', get_base_url() . '/', $in_path) => false] as $_source => $using_path) {
            $converted_url = convert_image($_source, $out_path, $convert_width, $convert_height, $box_size, true, null, $using_path, $only_make_smaller);
            if ($converted_url === null) {
                $additional_information = 'convert_image failed on ' . $_source . '.';
                return false;
            }
        }

        return true;
    }

    protected function convertImagePlus($in_url, $dimensions, $algorithm, $where, $only_make_smaller, &$additional_information)
    {
        $out_url = convert_image_plus($in_url, $dimensions, 'temp', null, null, $algorithm, $where, '#000000', $only_make_smaller);
        if ($out_url == '') {
            $additional_information = 'convert_image_plus failed on ' . $in_url;
            return false;
        }
        $out_path = convert_url_to_path($out_url);
        return $out_path;
    }

    protected function checkImageSize($out_path, $expected_width, $expected_height, &$additional_information)
    {
        list($image_width, $image_height) = getimagesize($out_path);
        if (($expected_width !== null && $image_width !== $expected_width) || ($expected_height !== null && $image_height !== $expected_height)) {
            $additional_information = 'Expected dimensions of the converted image to be ' . strval($expected_width) . 'x' . strval($expected_height) . '. Instead, the dimensions were ' . strval($image_width) . 'x' . strval($image_height) . '.';
            $this->outputDebugVisual($out_path);
            return false;
        }
        return [$image_width, $image_height];
    }

    protected function getImageContents($path, &$additional_information)
    {
        if ((filemtime($path) < time() - 10) && (substr($path, -4) != '.svg')) {
            $additional_information = 'Image is old, likely filesystem permission issue.';
            return false;
        }
        $file_contents = cms_file_get_contents_safe($path, FILE_READ_LOCK);
        if ($file_contents === false) {
            $additional_information = 'The contents of the generated image file could not be read.';
            return false;
        }
        return $file_contents;
    }

    protected function createImageFromString($file_contents, &$additional_information)
    {
        $image_resource = imagecreatefromstring($file_contents);
        if ($image_resource === false) {
            $additional_information = 'A PHP image resource could not be created from the string contents of the image.';
            return false;
        }
        return $image_resource;
    }

    protected function checkImageStringsAreSame($string1, $string2, &$additional_information)
    {
        if ($string1 !== $string2) {
            $additional_information = 'Expected the contents of the converted image to be exactly the same as the original image, but instead they were different.';
            return false;
        }
        return true;
    }

    protected function checkColor($out_path, $image_resource, $x, $y, $expected_red, $expected_green, $expected_blue, $expected_alpha, $tolerance, &$additional_information)
    {
        $pixel = imagecolorat($image_resource, $x, $y);
        $data = imagecolorsforindex($image_resource, $pixel);
        if (($expected_red !== null && abs($expected_red - $data['red']) >= $tolerance) || ($expected_green !== null && abs($expected_green - $data['green']) >= $tolerance) || ($expected_blue !== null && abs($expected_blue - $data['blue']) >= $tolerance) || ($expected_alpha !== null && abs($expected_alpha - $data['alpha']) >= ($tolerance / 2))) {
            $additional_information = 'Expected pixel ' . strval($x) . 'x' . strval($y) . ' to be rgba(' . strval($expected_red) . ', ' . strval($expected_green) . ', ' . strval($expected_blue) . ', ' . strval($expected_alpha) . ') +- rgba(' . strval($tolerance) . ', ' . strval($tolerance) . ', ' . strval($tolerance) . ', ' . float_to_raw_string($tolerance / 2) . '), but instead got rgba(' . strval($data['red']) . ', ' . strval($data['green']) . ', ' . strval($data['blue']) . ', ' . strval($data['alpha']) . ').';
            $this->outputDebugVisual($out_path);
            return false;
        }
        return true;
    }

    protected function checkRedPatch($out_path, $image_resource, $x, $y, $image_dimensions, $color_tolerance, &$additional_information)
    {
        $tolerance = intval(max($image_dimensions) * 0.1) + 1;
        $extension = get_file_extension($out_path);
        if ($extension === 'jpg' || $extension === 'jpeg') {
            $color_tolerance = $color_tolerance + ((255 - $color_tolerance) / (($image_dimensions[0] + $image_dimensions[1]) / 8));
        }

        for ($_x = max(0, $x - $tolerance); $_x <= min($image_dimensions[0] - 1, $x + $tolerance); $_x++) {
            for ($_y = max(0, $y - $tolerance); $_y <= min($image_dimensions[1] - 1, $y + $tolerance); $_y++) {
                if ($this->checkRed($image_resource, $_x, $_y, $color_tolerance, $additional_information)) {
                    return true;
                }
            }
        }

        $additional_information = 'Expected pixel ' . strval($x) . 'x' . strval($y) . ' (or any pixels within ' . strval($tolerance) . ') to have at least ' . strval(255 - $color_tolerance) . ' red, and more red than green and blue, but none of them were.';
        $this->outputDebugVisual($out_path);
        return false;
    }

    protected function checkRed($image_resource, $x, $y, $color_tolerance, &$additional_information)
    {
        $pixel = imagecolorat($image_resource, $x, $y);
        $data = imagecolorsforindex($image_resource, $pixel);
        if ($data['red'] <= (255 - $color_tolerance) || $data['red'] < $data['green'] || $data['red'] < $data['blue']) {
            return false;
        }
        return true;
    }

    protected function checkBlackPatch($out_path, $image_resource, $x, $y, $image_dimensions, $color_tolerance, &$additional_information)
    {
        $tolerance = intval(max($image_dimensions) * 0.1) + 1;
        $extension = get_file_extension($out_path);
        if ($extension === 'jpg' || $extension === 'jpeg') {
            $color_tolerance = $color_tolerance + ((255 - $color_tolerance) / (($image_dimensions[0] + $image_dimensions[1]) / 8));
        }

        for ($_x = max(0, $x - $tolerance); $_x <= min($image_dimensions[0] - 1, $x + $tolerance); $_x++) {
            for ($_y = max(0, $y - $tolerance); $_y <= min($image_dimensions[1] - 1, $y + $tolerance); $_y++) {
                if ($this->checkBlack($image_resource, $_x, $_y, $color_tolerance, $additional_information)) {
                    return true;
                }
            }
        }

        $additional_information = 'Expected pixel ' . strval($x) . 'x' . strval($y) . ' (or any pixels within ' . strval($tolerance) . ') to have no color channels greater than ' . strval($color_tolerance) . ', and no color channels more than ' . strval($color_tolerance) . ' apart from each other (black test).';
        $this->outputDebugVisual($out_path);
        return false;
    }

    protected function checkBlack($image_resource, $x, $y, $color_tolerance, &$additional_information)
    {
        $pixel = imagecolorat($image_resource, $x, $y);
        $data = imagecolorsforindex($image_resource, $pixel);
        if ($data['red'] >= $color_tolerance || $data['green'] >= $color_tolerance || $data['blue'] >= $color_tolerance || abs($data['red'] - $data['green']) >= $color_tolerance || abs($data['red'] - $data['blue']) >= $color_tolerance || abs($data['blue'] - $data['green']) >= $color_tolerance) {
            return false;
        }
        return true;
    }

    protected function checkBluePatch($out_path, $image_resource, $x, $y, $image_dimensions, $color_tolerance, &$additional_information)
    {
        $tolerance = intval(max($image_dimensions) * 0.1) + 1;
        $extension = get_file_extension($out_path);
        if ($extension === 'jpg' || $extension === 'jpeg') {
            $color_tolerance = $color_tolerance + ((255 - $color_tolerance) / (($image_dimensions[0] + $image_dimensions[1]) / 8));
        }

        for ($_x = max(0, $x - $tolerance); $_x <= min($image_dimensions[0] - 1, $x + $tolerance); $_x++) {
            for ($_y = max(0, $y - $tolerance); $_y <= min($image_dimensions[1] - 1, $y + $tolerance); $_y++) {
                if ($this->checkBlue($image_resource, $_x, $_y, $color_tolerance, $additional_information)) {
                    return true;
                }
            }
        }

        $additional_information = 'Expected pixel ' . strval($x) . 'x' . strval($y) . ' (or any pixels within ' . strval($tolerance) . ') to have at least ' . (strval(255 - $color_tolerance)) . ' blue, and more blue than green and red, but none of them were.';
        $this->outputDebugVisual($out_path);
        return false;
    }

    protected function checkBlue($image_resource, $x, $y, $color_tolerance, &$additional_information)
    {
        $pixel = imagecolorat($image_resource, $x, $y);
        $data = imagecolorsforindex($image_resource, $pixel);
        if ($data['blue'] <= (255 - $color_tolerance) || $data['blue'] < $data['green'] || $data['blue'] < $data['red']) {
            return false;
        }
        return true;
    }

    protected function outputDebugVisual($out_path, $worked = false)
    {
        $debug_level = get_param_integer('debug', 0);
        if (($debug_level != 0) && ((!$worked) || ($debug_level == 2))) {
            echo '<br style="clear: both" /><br />';
            require_code('mime_types');
            if (is_file($out_path)) {
                $value = 'data:' . get_mime_type(get_file_extension($out_path), false) . ';base64,' . base64_encode(cms_file_get_contents_safe($out_path, FILE_READ_LOCK));
                echo '<img style="float: left; width: 100px; padding-right: 1em;" src="' . escape_html($value) . '" />';
            } else {
                echo '<span style="float: left; width: 100px; padding-right: 1em;">MISSING</span>';
            }
        }
    }
}
