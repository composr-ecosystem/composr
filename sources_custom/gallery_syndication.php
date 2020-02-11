<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

function init__gallery_syndication()
{
    define('SYNDICATION_ORPHANS__LEAVE', 1);
    define('SYNDICATION_ORPHANS__UNVALIDATE', 2);
    define('SYNDICATION_ORPHANS__DELETE', 3);

    if (function_exists('restrictify')) {
        restrictify();
    }
}

function sync_video_syndication($local_id = null, $new_upload = false, $reupload = false, $consider_deferring = false)
{
    $orphaned_handling = intval(get_option('gallery_sync_orphaned_handling'));

    $num_local_videos = $GLOBALS['SITE_DB']->query_select_value('videos', 'COUNT(*)');
    if ($local_id === null) { // If being asked to do a full sync
        if ($num_local_videos > 1000) {
            return; // Too much, we won't do complex two-way syncs
        }
    }

    $local_videos = get_local_videos($local_id);

    if ((($consider_deferring) || ($num_local_videos > 1000)) && (cron_installed()) && ($local_id !== null) && (count($local_videos) == 1)) {
        foreach ($local_videos as $local_video) {
            $GLOBALS['SITE_DB']->query_insert('video_transcoding', [
                't_id' => 'sync_defer_' . strval($local_id) . ($new_upload ? '__new_upload' : '') . ($reupload ? '__reupload' : ''),
                't_local_id' => $local_id,
                't_local_id_field' => 'id',
                't_error' => '',
                't_url' => $local_video['_raw_url'],
                't_table' => 'videos',
                't_url_field' => 'url',
                't_orig_filename_field' => '',
                't_width_field' => 'video_width',
                't_height_field' => 'video_height',
                't_output_filename' => '',
            ], false, true);
        }
        if ($consider_deferring) {
            return;
        }
    }

    $services = find_all_hook_obs('modules', 'video_syndication', 'Hook_video_syndication_');

    foreach ($services as $ob) {
        $exists_remote = [];

        if ($ob->is_active()) {
            // What is already on remote server
            $remote_videos = $new_upload ? /*no remote search needed*/[] : $ob->get_remote_videos($local_id);

            foreach ($remote_videos as $video) {
                if (!$GLOBALS['DEV_MODE']) {
                    if (get_value('handling_video_currently__' . strval($video['bound_to_local_id'])) === '1') {
                        continue; // Check lock
                    }
                    set_value('handling_video_currently__' . strval($video['bound_to_local_id']), '1'); // Set lock
                }

                _sync_remote_video($ob, $video, $local_videos, $orphaned_handling, $reupload, $services);
                $exists_remote[$video['bound_to_local_id']] = true;

                if (!$GLOBALS['DEV_MODE']) {
                    delete_value('handling_video_currently__' . strval($video['bound_to_local_id']));
                }
            }

            // What is there locally
            foreach ($local_videos as $video) {
                // Check this isn't a video that was manually uploaded, already remote - because we can't syndicate those
                foreach ($services as $_service) {
                    if ($_service->recognises_as_remote($video['url'])) {
                        continue 2;
                    }
                }

                if (!$GLOBALS['DEV_MODE']) {
                    if (get_value('handling_video_currently__' . strval($video['local_id'])) === '1') {
                        continue; // Check lock
                    }
                    set_value('handling_video_currently__' . strval($video['local_id']), '1'); // Set lock
                }

                if (!array_key_exists($video['local_id'], $exists_remote)) {
                    _sync_onlylocal_video($ob, $video);
                }

                if (!$GLOBALS['DEV_MODE']) {
                    delete_value('handling_video_currently__' . strval($video['local_id']));
                }
            }
        }
    }

    if ($local_id !== null) {
        $GLOBALS['SITE_DB']->query_delete('video_transcoding', [
            't_id' => 'sync_defer_' . strval($local_id) . ($new_upload ? '__new_upload' : '') . ($reupload ? '__reupload' : ''),
        ]);
    }
}

function get_local_videos($local_id = null)
{
    $filter = get_option('gallery_sync_selectcode');
    if ($filter == '') {
        $where = '1=1';
    } else {
        require_code('selectcode');
        $where = selectcode_to_sqlfragment($filter, 'v.id', 'galleries', 'parent_id', 'v.cat', 'name', true, false);
    }

    if ($local_id !== null) {
        $where .= ' AND v.id=' . strval($local_id);
    }

    $rows = $GLOBALS['SITE_DB']->query('SELECT v.* FROM ' . get_table_prefix() . 'videos v WHERE ' . $where, null, 0, false, true, ['title' => 'SHORT_TRANS', 'the_description' => 'LONG_TRANS']);
    $videos = [];
    foreach ($rows as $row) {
        $videos[$row['id']] = _get_local_video($row);
    }

    return $videos;
}

function _get_local_video($row)
{
    static $tree = null;
    if ($tree === null) {
        $num_galleries = $GLOBALS['SITE_DB']->query_select_value('galleries', 'COUNT(*)');
        if ($num_galleries < 500) {
            $tree = collapse_2d_complexity('name', 'parent_id', $GLOBALS['SITE_DB']->query_select('galleries', ['name', 'parent_id']));
        } else {
            $tree = []; // Pull in dynamically via individual queries
        }
    }

    $categories = [];
    $parent_id = $row['cat'];
    while ($parent_id != '') {
        array_push($categories, $parent_id);
        if (array_key_exists($parent_id, $tree)) {
            $parent_id = $tree[$parent_id];
        } else {
            $parent_id = $GLOBALS['SITE_DB']->query_select_value_if_there('galleries', 'parent_id', ['name' => $parent_id]);
            $tree[$parent_id] = $parent_id;
        }
    }
    array_pop($categories); // We don't need root category on there

    $url = $row['url'];
    if (url_is_local($url)) {
        $url = get_custom_base_url() . '/' . $url;
    }

    $tags = array_reverse($categories);
    list($keywords,) = seo_meta_get_for('video', strval($row['id']));
    if ($keywords != '') {
        $tags = array_unique(array_merge($tags, array_map('trim', explode(',', $keywords))));
    }

    return [
        'local_id' => $row['id'],
        'title' => get_translated_text($row['title']),
        'description' => strip_comcode(get_translated_text($row['the_description'])),
        'mtime' => ($row['edit_date'] === null) ? $row['add_date'] : $row['edit_date'],
        'tags' => $tags,
        'url' => $url,
        '_raw_url' => $row['url'],
        'thumb_url' => $row['thumb_url'],
        'allow_rating' => ($row['allow_rating'] == 1),
        'allow_comments' => ($row['allow_comments'] >= 1),
        'validated' => ($row['validated'] == 1),
    ];
}

function _sync_remote_video($ob, $video, $local_videos, $orphaned_handling, $reupload, $services)
{
    if (array_key_exists($video['bound_to_local_id'], $local_videos)) {
        $local_video = $local_videos[$video['bound_to_local_id']];

        // Handle changes
        $changes = [];
        foreach (['title', 'description', 'tags', 'allow_rating', 'allow_comments', 'validated'] as $property) {
            if (($video[$property] !== null) && ($video[$property] != $local_video[$property])) {
                $changes[$property] = $local_video[$property];
            }
        }
        if ($reupload) {
            foreach ($services as $_service) {
                if ($_service->recognises_as_remote($local_video['url'])) {
                    $reupload = false; // Actually, this isn't handleable as a reupload, the URL is on a remote service
                }
            }
            if ($reupload) {
                $changes += ['url' => $local_video['url']];
            }
        }
        if (!empty($changes)) {
            $ob->change_remote_video($video + $local_video, $changes);
        }
    } else {
        // Orphaned remotes
        switch ($orphaned_handling) {
            case SYNDICATION_ORPHANS__LEAVE:
                $ob->unbind_remote_video($video);
                break;

            case SYNDICATION_ORPHANS__UNVALIDATE:
                $ob->unbind_remote_video($video);
                $ob->change_remote_video($video, ['validated' => false]);
                break;

            case SYNDICATION_ORPHANS__DELETE:
                $ob->delete_remote_video($video);
                break;
        }
    }
}

function _sync_onlylocal_video($ob, $local_video)
{
    $remote_video = $ob->upload_video($local_video);
    if ($remote_video === null) {
        return; // Didn't work, can't do anything further
    }

    $service_name = preg_replace('#^video_syndication_#', '', get_class($ob));
    $service_title = $ob->get_service_title();

    $local_video_url = build_url(['page' => 'galleries', 'type' => 'video', 'id' => $local_video['local_id']], get_module_zone('galleries'), [], false, false, true);
    $_local_video_url = $local_video_url->evaluate();
    $_local_video_url_cleaned = preg_replace('#^http://#', '', $_local_video_url); // Useful if URLs are not permitted

    require_lang('gallery_syndication');
    $comment = do_lang('VIDEO_SYNC_INITIAL_COMMENT', $service_title, get_site_name(), [$_local_video_url, $_local_video_url_cleaned]);

    if ($comment != '') {
        $ob->leave_comment($remote_video, $comment);
    }

    // Store the DB mapping for the transcoding
    $transcoding_id = $service_name . '_' . $remote_video['remote_id'];
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

    if (get_option('video_sync_transcoding') == $service_name) {
        require_lang('galleries');
        attach_message(do_lang_tempcode('TRANSCODING_IN_PROGRESS'), 'inform');

        // Actually carry over the DB mapping as an actual transcoding
        store_transcoding_success($transcoding_id, $remote_video['url']);

        // Now copy over thumbnail, if applicable
        if ((find_theme_image('video_thumb', true) === $local_video['thumb_url']) || ($local_video['thumb_url'] == '')) { // Is currently on default thumb (i.e. none explicitly chosen)
            require_code('galleries2');
            $thumb_url = create_video_thumb($remote_video['url']);
            $GLOBALS['SITE_DB']->query_update('videos', ['thumb_url' => $thumb_url], ['id' => $local_video['local_id']], '', 1);
        }
    }
}

/**
 * Handle that a transcode has worked.
 *
 * @param  ID_TEXT $transcoder_id Transcoding ID
 * @param  URLPATH $new_url Transcoded URL
 */
function store_transcoding_success($transcoder_id, $new_url)
{
    // Stuff about the transcoding
    $descript_rows = $GLOBALS['SITE_DB']->query_select('video_transcoding', ['*'], [
        't_id' => $transcoder_id,
    ], '', 1);
    if (!array_key_exists(0, $descript_rows)) {
        return; // No match
    }
    $descript_row = $descript_rows[0];

    // The database for for what has been transcoded
    $rows = $GLOBALS['SITE_DB']->query_select($descript_row['t_table'], ['*'], [$descript_row['t_url_field'] => $descript_row['t_url']], '', 1, 0, false, []);
    if (!array_key_exists(0, $rows)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $row = $rows[0];

    // Deal with old file
    $old_file_path = get_custom_file_base() . '/' . rawurldecode($row['url']);
    if (url_is_local($descript_row['t_url'])) {
        $new_file_path = str_replace('/uploads/galleries/', '/uploads/galleries/pre_transcoding/', $old_file_path);
        if (file_exists(get_custom_file_base() . '/uploads/galleries/pre_transcoding')) { // Move the old media file to the archive directory
            @rename($old_file_path, $new_file_path);
            sync_file_move($old_file_path, $new_file_path);
        } else { // Delete old media
            @unlink($old_file_path);
            sync_file($old_file_path);
        }
    }

    // Update width/height, to what we specified we want to transcode to
    $ext = get_file_extension($descript_row['t_output_filename']);
    require_code('mime_types');
    $mime_type = get_mime_type($ext, true);
    if (substr($mime_type, 0, 6) != 'audio/') {
        if ($descript_row['t_width_field'] != '') {
            $row[$descript_row['t_width_field']] = intval(get_option('video_width_setting'));
        }
        if ($descript_row['t_height_field'] != '') {
            $row[$descript_row['t_height_field']] = intval(get_option('video_height_setting'));
        }
    } else {
        if ($descript_row['t_width_field'] != '') {
            $row[$descript_row['t_width_field']] = intval(get_option('video_width_setting'));
        }
        if ($descript_row['t_height_field'] != '') {
            $row[$descript_row['t_height_field']] = 20;
        }
    }

    // Update original filename
    if ($descript_row['t_orig_filename_field'] != '') {
        $row[$descript_row['t_orig_filename_field']] = preg_replace('#\..*$#', '.' . $ext, $row[$descript_row['t_orig_filename_field']]);
    }

    // Update URL to transcoded one
    $row[$descript_row['t_url_field']] = $new_url;

    // Update record to point to new file
    if ($descript_row['t_local_id_field'] !== null) {
        $GLOBALS['SITE_DB']->query_update($descript_row['t_table'], $row, [$descript_row['t_local_id_field'] => $descript_row['t_local_id']], '', 1);
    }
}
