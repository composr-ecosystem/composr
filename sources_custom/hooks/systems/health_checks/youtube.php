<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    youtube_channel_integration_block
 */

/**
 * Hook class.
 */
class Hook_health_check_youtube extends Hook_Health_Check
{
    protected $category_label = 'API connections';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     * @param  boolean $show_unusable_categories Whether to include categories that might not be accessible for some reason
     * @return array A pair: category label, list of results
     */
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null, $show_unusable_categories = false)
    {
        if (($show_unusable_categories) || (($check_context != CHECK_CONTEXT__INSTALL) && (addon_installed('youtube_channel_integration_block')))) {
            require_code('oauth');
            if ((($show_unusable_categories) || ((get_option('google_apis_api_key') != '') && (get_oauth_refresh_token('youtube') !== null)))) {
                $this->process_checks_section('testYouTubeConnection', 'YouTube', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass, $urls_or_page_links, $comcode_segments);
            }
        }

        return [$this->category_label, $this->results];
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     */
    public function testYouTubeConnection($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            return;
        }

        $youtube_api_key = get_option('google_apis_api_key');

        // Direct querying used by block...

        $channel_name = 'ocportal';
        $channel = @json_decode(http_get_contents('https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=' . urlencode($channel_name) . '&fields=items(contentDetails(relatedPlaylists(uploads)))&key=' . urlencode($youtube_api_key), ['convert_to_internal_encoding' => true]));
        $playlist_id = $channel->items[0]->contentDetails->relatedPlaylists->uploads;
        $playlist_items = json_decode(http_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2Cstatus&playlistId=' . urlencode($playlist_id) . '&fields=items(snippet(title%2CchannelId%2CchannelTitle%2Cdescription%2Cthumbnails%2CpublishedAt%2CresourceId(videoId))%2Cstatus(privacyStatus))%2CpageInfo(totalResults)&key=' . urlencode($youtube_api_key), ['convert_to_internal_encoding' => true]));
        $this->assertTrue(array_key_exists(0, $playlist_items->items), 'Could not search for any video on a public YouTube channel');
    }
}
