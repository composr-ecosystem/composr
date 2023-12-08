<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

/**
 * Hook class.
 */
class Hook_cron_gallery_syndication
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        $value = get_value('last_gallery_syndication');
        if ($value !== 'in_progress') {
            if ((is_null($value)) || (intval($value) < time() - 60 * 60)) {
                set_value('last_gallery_syndication', 'in_progress');

                require_code('gallery_syndication');

                // Sync of videos that were explicitly deferred
                $videos = $GLOBALS['SITE_DB']->query('SELECT t_local_id,t_id FROM ' . get_table_prefix() . 'video_transcoding WHERE t_id LIKE \'' . db_encode_like('sync\_defer\_%') . '\'');
                foreach ($videos as $video) {
                    sync_video_syndication($video['t_local_id'], substr($video['t_id'], -strlen('__new_upload')) == '__new_upload', substr($video['t_id'], -strlen('__reupload')) == '__reupload');
                }

                // Main sync of everything
                sync_video_syndication();

                set_value('last_gallery_syndication', strval(time()));
            }
        }
    }
}
