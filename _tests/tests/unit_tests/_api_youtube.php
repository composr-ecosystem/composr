<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class _api_youtube_test_set extends cms_test_case
{
    public function testYouTubeApi()
    {
        $this->load_key_options('google_apis_', 'youtube__'); // We have to use a prefix on here because Google deactivates YouTube quota if left unused too long and has a horrible process to re-enable it

        require_code('hooks/modules/video_syndication/youtube');
        $ob = new Hook_video_syndication_youtube();

        // Simple non-destructive Health Check
        require_code('health_check');
        $this->run_health_check('API connections', 'YouTube', CHECK_CONTEXT__TEST_SITE, true);

        $this->assertTrue($ob->is_active(), 'YouTube video syndication provider does not register as active');

        $local_id = 123456789;

        $local_video = [
            'local_id' => $local_id,
            'title' => 'Test',
            'description' => 'Test description',
            'mtime' =>time(),
            'tags' => ['test', 'Music'],
            'url' => get_custom_base_url() . '/_tests/assets/media/early_cinema.mp4',
            '_raw_url' => get_custom_base_url() . '/_tests/assets/media/early_cinema.mp4',
            'thumb_url' => get_custom_base_url() . '/_tests/assets/images/exifrotated.jpg',
            'validated' => false,
        ];
        $remote_video = $ob->upload_video($local_video);
        $this->assertTrue($remote_video !== null, 'Cannot upload video');
        if ($remote_video === null) {
            return;
        }
        $this->assertTrue($remote_video['title'] == 'Test', 'Video did not upload as expected');

        // Store the DB mapping for the transcoding
        $transcoding_id = 'youtube_' . $remote_video['remote_id'];
        $GLOBALS['SITE_DB']->query_delete('video_transcoding', [
            't_local_id' => $local_video['local_id'],
        ]);
        $GLOBALS['SITE_DB']->query_insert('video_transcoding', [
            't_id' => $transcoding_id,
            't_local_id' => $local_video['local_id'],
            't_local_id_field' => 'id',
            't_error' => '',
            't_url' => $local_video['_raw_url'],
            't_table' => 'videos',
            't_url_field' => 'url',
            't_orig_filename_field' => '',
            't_width_field' => 'video_width',
            't_height_field' => 'video_height',
            't_output_filename' => '',
        ]);

        $remote_videos = $ob->get_remote_videos($local_id);
        $remote_video = $remote_videos[$remote_video['remote_id']];
        $this->assertTrue($remote_video['title'] == 'Test', 'Video did not come up when listing for it');

        $remote_video = $ob->change_remote_video($remote_video, ['title' => 'Test 2'], false);
        $this->assertTrue($remote_video['title'] == 'Test 2', 'Video did not edit as expected');

        $success = $ob->leave_comment($remote_video, 'This is a test comment');
        $this->assertTrue($success, 'Error leaving comment');

        $success = $ob->delete_remote_video($remote_video);
        $this->assertTrue($success, 'Error deleting video');

        $remote_videos = $ob->get_remote_videos($local_id);
        $this->assertTrue(empty($remote_videos), 'Video was not apparently deleted');
    }
}
