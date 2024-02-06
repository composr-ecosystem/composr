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
class gallery_media_defaults_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('galleries2');
    }

    public function testVideoDefaults()
    {
        list($video_width, $video_height, $video_length) = video_get_default_metadata('', '', 'foo.mp4');
        $this->assertTrue($video_width == intval(get_option('default_video_width')));
        $this->assertTrue($video_height == intval(get_option('default_video_height')));
        $this->assertTrue($video_length == 0);
        $this->assertTrue(video_get_default_thumb_url('', '', 'foo.mp4') == find_theme_image('video_thumb', true));

        list($video_width, $video_height, $video_length) = video_get_default_metadata('', '', 'foo.mp3');
        $this->assertTrue($video_width == DEFAULT_AUDIO_WIDTH);
        $this->assertTrue($video_height == DEFAULT_AUDIO_HEIGHT);
        $this->assertTrue($video_length == 0);
        $this->assertTrue(video_get_default_thumb_url('', '', 'foo.mp3') == find_theme_image('audio_thumb', true));

        list($video_width, $video_height, $video_length) = video_get_default_metadata('_tests/assets/media/early_cinema.mp4', '', 'foo.mp4');
        $this->assertTrue($video_width == 320);
        $this->assertTrue($video_height == 240);
        $this->assertTrue($video_length == 3);

        list($video_width, $video_height, $video_length) = video_get_default_metadata('_tests/assets/media/sine.mp3', '', 'foo.mp3');
        $this->assertTrue($video_width == DEFAULT_AUDIO_WIDTH);
        $this->assertTrue($video_height == DEFAULT_AUDIO_HEIGHT);
        $this->assertTrue($video_length == 1);

        $this->assertTrue(strpos(video_get_default_thumb_url('https://www.youtube.com/watch?v=gPCiIGJlm-4'), 'img.youtube.com') !== false);
    }
}
