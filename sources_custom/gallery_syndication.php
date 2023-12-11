<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
    if (is_null($local_id)) { // If being asked to do a full sync
        if ($num_local_videos > 1000) {
            return; // Too much, we won't do complex two-way syncs
        }
    }

    $local_videos = get_local_videos($local_id);

    if ((($consider_deferring) || ($num_local_videos > 1000)) && (cron_installed()) && (!is_null($local_id)) && (count($local_videos) == 1)) {
        foreach ($local_videos as $local_video) {
            $GLOBALS['SITE_DB']->query_insert('video_transcoding', array(
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
            ), false, true);
        }
        if ($consider_deferring) {
            return;
        }
    }

    $hooks = find_all_hooks('modules', 'video_syndication');
    $services = array();
    foreach (array_keys($hooks) as $hook) {
        require_code('hooks/modules/video_syndication/' . filter_naughty_harsh($hook));

        $ob = object_factory('Hook_video_syndication_' . filter_naughty_harsh($hook));

        $services[] = $ob;
    }

    foreach ($services as $ob) {
        $exists_remote = array();

        if ($ob->is_active()) {
            // What is already on remote server
            $remote_videos = $new_upload ?/*no remote search needed*/
                array() : $ob->get_remote_videos($local_id);

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

    if (!is_null($local_id)) {
        $GLOBALS['SITE_DB']->query_delete('video_transcoding', array(
            't_id' => 'sync_defer_' . strval($local_id) . ($new_upload ? '__new_upload' : '') . ($reupload ? '__reupload' : ''),
        ));
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

    if (!is_null($local_id)) {
        $where .= ' AND v.id=' . strval($local_id);
    }

    $rows = $GLOBALS['SITE_DB']->query('SELECT v.* FROM ' . get_table_prefix() . 'videos v WHERE ' . $where, null, null, false, true, array('title' => 'SHORT_TRANS', 'description' => 'LONG_TRANS'));
    $videos = array();
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
            $tree = collapse_2d_complexity('name', 'parent_id', $GLOBALS['SITE_DB']->query_select('galleries', array('name', 'parent_id')));
        } else {
            $tree = array(); // Pull in dynamically via individual queries
        }
    }

    $categories = array();
    $parent_id = $row['cat'];
    while ($parent_id != '') {
        array_push($categories, $parent_id);
        if (array_key_exists($parent_id, $tree)) {
            $parent_id = $tree[$parent_id];
        } else {
            $parent_id = $GLOBALS['SITE_DB']->query_select_value_if_there('galleries', 'parent_id', array('name' => $parent_id));
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

    return array(
        'local_id' => $row['id'],
        'title' => get_translated_text($row['title']),
        'description' => strip_comcode(get_translated_text($row['description'])),
        'mtime' => is_null($row['edit_date']) ? $row['add_date'] : $row['edit_date'],
        'tags' => $tags,
        'url' => $url,
        '_raw_url' => $row['url'],
        'thumb_url' => $row['thumb_url'],
        'allow_rating' => ($row['allow_rating'] == 1),
        'allow_comments' => ($row['allow_comments'] >= 1),
        'validated' => ($row['validated'] == 1),
    );
}

function _sync_remote_video($ob, $video, $local_videos, $orphaned_handling, $reupload, $services)
{
    if (array_key_exists($video['bound_to_local_id'], $local_videos)) {
        $local_video = $local_videos[$video['bound_to_local_id']];

        // Handle changes
        $changes = array();
        foreach (array('title', 'description', 'tags', 'allow_rating', 'allow_comments', 'validated') as $property) {
            if ((!is_null($video[$property])) && ($video[$property] != $local_video[$property])) {
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
                $changes += array('url' => $local_video['url']);
            }
        }
        if ($changes != array()) {
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
                $ob->change_remote_video($video, array('validated' => false));
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
    if (is_null($remote_video)) {
        return; // Didn't work, can't do anything further
    }

    $service_name = preg_replace('#^video\_syndication\_#', '', get_class($ob));
    $service_title = $ob->get_service_title();

    $local_video_url = build_url(array('page' => 'galleries', 'type' => 'video', 'id' => $local_video['local_id']), get_module_zone('galleries'), null, false, false, true);
    $_local_video_url = $local_video_url->evaluate();
    $_local_video_url_cleaned = preg_replace('#^http://#', '', $_local_video_url); // Useful if URLs are not permitted

    require_lang('gallery_syndication');
    $comment = do_lang('VIDEO_SYNC_INITIAL_COMMENT', $service_title, get_site_name(), array($_local_video_url, $_local_video_url_cleaned));

    if ($comment != '') {
        $ob->leave_comment($remote_video, $comment);
    }

    // Store the DB mapping for the transcoding
    $transcoding_id = $service_name . '_' . $remote_video['remote_id'];
    $GLOBALS['SITE_DB']->query_insert('video_transcoding', array(
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
    ));

    if (get_option('video_sync_transcoding') == $service_name) {
        require_lang('galleries');
        attach_message(do_lang_tempcode('TRANSCODING_IN_PROGRESS'), 'inform');

        // Actually carry over the DB mapping as an actual transcoding
        require_code('transcoding');
        store_transcoding_success($transcoding_id, $remote_video['url']);

        // Now copy over thumbnail, if applicable
        if ((find_theme_image('video_thumb', true) === $local_video['thumb_url']) || ($local_video['thumb_url'] == '')) { // Is currently on default thumb (i.e. none explicitly chosen)
            require_code('galleries2');
            $thumb_url = create_video_thumb($remote_video['url']);
            $GLOBALS['SITE_DB']->query_update('videos', array('thumb_url' => $thumb_url), array('id' => $local_video['local_id']), '', 1);
        }
    }
}
