<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class media_test_set extends cms_test_case
{
    public function setUp()
    {
        require_code('media_renderer');
        require_code('images');

        parent::setUp();
    }

    public function testVimeoThumbnail()
    {
        $test_url = 'https://vimeo.com/channels/staffpicks/264037633';
        require_code('hooks/systems/media_rendering/vimeo');
        $ob = new Hook_media_rendering_vimeo();
        $thumb_url = $ob->get_video_thumbnail($test_url);
        $this->assertTrue($thumb_url !== null);
        if ($thumb_url !== null) {
            $test = cms_getimagesizefromstring(http_download_file($thumb_url));
            $this->assertTrue(is_array($test) && is_integer($test[0]) && is_integer($test[1]));
        }
    }

    public function testYouTubeThumbnail()
    {
        $test_url = 'https://www.youtube.com/watch?v=C1bLJieqbhk';
        require_code('hooks/systems/media_rendering/youtube');
        $ob = new Hook_media_rendering_youtube();
        $thumb_url = $ob->get_video_thumbnail($test_url);
        $this->assertTrue($thumb_url !== null);
        if ($thumb_url !== null) {
            $test = cms_getimagesizefromstring(http_download_file($thumb_url));
            $this->assertTrue(is_array($test) && is_integer($test[0]) && is_integer($test[1]));
        }
    }

    /* Facebook removed non-authenticated access
    public function testFacebookThumbnail()
    {
        $test_url = 'https://www.facebook.com/CollegeHumor/videos/10154300448557807/';
        require_code('hooks/systems/media_rendering/video_facebook');
        $ob = new Hook_media_rendering_video_facebook();
        $thumb_url = $ob->get_video_thumbnail($test_url);
        $this->assertTrue($thumb_url !== null);
        if ($thumb_url !== null) {
            $test = cms_getimagesizefromstring(http_download_file($thumb_url));
            $this->assertTrue(is_array($test) && is_integer($test[0]) && is_integer($test[1]));
        }
    }*/
}
