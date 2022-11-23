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

/**
 * Hook class.
 */
class Hook_video_syndication_vimeo
{
    public $_vimeo_ob;
    public $_request_token = null;

    public function uninstall()
    {
    }

    public function __construct()
    {
        require_code('vimeo');

        $service_name = 'vimeo';

        // Initialise official client
        $this->_vimeo_ob = new phpVimeo(
            get_option($service_name . '_client_id'),
            get_option($service_name . '_client_secret'),
            get_value($service_name . '_access_token', null, true),
            get_value($service_name . '_access_token_secret', null, true)
        );
    }

    public function get_service_title()
    {
        return 'Vimeo';
    }

    public function recognises_as_remote($url)
    {
        $is_embed = (preg_match('#^https?://vimeo\.com/(.*/)?(\d+)#', $url) != 0);
        if ($is_embed) {
            return true;
        }

        $is_other = (preg_match('#^https?://vimeo\.com/#', $url) != 0);
        if ($is_other) {
            return true;
        }

        return false;
    }

    public function is_active()
    {
        $vimeo_client_id = get_option('vimeo_client_id');
        if ($vimeo_client_id == '') {
            return false;
        }

        $vimeo_client_secret = get_option('vimeo_client_secret');
        if ($vimeo_client_secret == '') {
            return false;
        }

        $token = get_value('vimeo_access_token', null, true);
        if ((is_null($token)) || ($token == '')) {
            return false;
        }

        return ($vimeo_client_id != '');
    }

    public function get_remote_videos($local_id = null, $transcoding_id = null)
    {
        $videos = array();

        if (!is_null($local_id)) {
            // This code is a bit annoying. Ideally we'd do a remote tag search (vimeo.videos.search), but Vimeo's API seems to be buggy/lagged here. We'll therefore look at our local mappings.
            $transcoding_id = $GLOBALS['SITE_DB']->query_value_if_there('SELECT t_id FROM ' . get_table_prefix() . 'video_transcoding WHERE t_local_id=' . strval($local_id) . ' AND t_id LIKE \'' . db_encode_like('vimeo\_%') . '\'');
            if (is_null($transcoding_id)) {
                return array(); // Not uploaded yet
            }

            $transcoding_id = preg_replace('#^vimeo_#', '', $transcoding_id);
        }

        $page = 1;
        do {
            $query_params = array();

            if (!is_null($transcoding_id)) {
                $query_params['video_id'] = preg_replace('#^vimeo_#', '', $transcoding_id);
                $api_method = 'vimeo.videos.getInfo';

                try {
                    $p = $this->_vimeo_ob->call($api_method, $query_params);
                } catch (VimeoAPIException $e) {
                    $p = false;
                }
                if ($p === false) {
                    break;
                }

                $detected_video = $this->_process_remote_video($p->video[0]);
                if (!is_null($detected_video)) {
                    $remote_id = $detected_video['remote_id'];
                    if ((!array_key_exists($remote_id, $videos)) || (!$videos[$remote_id]['validated'])) { // If new match, or last match was unvalidated (i.e. old version)
                        $videos[$remote_id] = $detected_video;
                    }
                }

                break;
            } else {
                $query_params['per_page'] = strval(50);
                $query_params['page'] = strval($page);
                $query_params['full_response'] = true;
                $query_params['user_id'] = $this->_request_token;
                $api_method = 'vimeo.videos.getUploaded';

                try {
                    $result = $this->_vimeo_ob->call($api_method, $query_params);
                } catch (VimeoAPIException $e) {
                    $result = false;
                }
                if ($result === false) {
                    return $videos;
                }
            }

            if (!isset($result->videos)) {
                break;
            }

            foreach ($result->videos->video as $p) {
                $detected_video = $this->_process_remote_video($p);
                if (!is_null($detected_video)) {
                    $remote_id = $detected_video['remote_id'];
                    if ((!array_key_exists($remote_id, $videos)) || (!$videos[$remote_id]['validated'])) { // If new match, or last match was unvalidated (i.e. old version)
                        $videos[$remote_id] = $detected_video;
                    }
                }
            }

            $page++;
        } while ((!is_null($local_id)) && (count($result['entry']) > 0));

        return $videos;
    }

    protected function _process_remote_video($p)
    {
        $detected_video = mixed();

        $remote_id = @strval($p->id);

        $add_date = strtotime(@strval($p->upload_date));
        $edit_date = isset($p->modified_date) ? strtotime(@strval($p->modified_date)) : $add_date;

        $allow_rating = null; // Specification of this not supported by Vimeo
        $allow_comments = null; // Specification of this not supported by Vimeo in API
        $validated = (@strval($p->privacy) != 'nobody');

        $got_best_video_type = false;

        foreach ($p->urls->url as $_url) {
            if ((@strval($_url->type) == 'video') && (!$got_best_video_type)) {
                $url = @strval($_url->_content); // Non-ideal, as is a link to vimeo.com
            }
            if (@strval($_url->type) == 'sd') { // Ideal because it lets us use jwplayer // But hmm, vimeo has not implemented yet https://vimeo.com/forums/api/topic:41030 !
                $url = @strval($_url->_content);
                $got_best_video_type = true;
            }
        }

        $category = null;
        $keywords = array();
        $bound_to_local_id = mixed();
        if (isset($p->tags)) {
            foreach ($p->tags->tag as $tag) {
                $matches = array();
                if (preg_match('#^sync(\d+)$#', @strval($tag->_content), $matches) != 0) {
                    $bound_to_local_id = intval($matches[1]);
                } else {
                    $keywords[] = @strval($tag->_content);
                }
            }
        }

        $detected_video = array(
            'bound_to_local_id' => $bound_to_local_id,
            'remote_id' => $remote_id,

            'title' => @strval($p->title),
            'description' => @strval($p->description),
            'mtime' => $edit_date,
            'tags' => $keywords,
            'url' => $url,
            'thumb_url' => @strval($p->thumbnails->thumbnail[1]->_content),
            'allow_rating' => $allow_rating,
            'allow_comments' => $allow_comments,
            'validated' => $validated,
        );

        if (!is_null($bound_to_local_id)) {
            return $detected_video; // else we ignore remote videos that aren't bound to local ones
        }

        return null;
    }

    public function upload_video($video)
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(10000);
        }
        list($file_path, $is_temp_file) = $this->_url_to_file_path($video['url']);
        try {
            $remote_id = $this->_vimeo_ob->upload($file_path, true, 2097152);

            if ($is_temp_file) {
                @unlink($file_path);
            }
            if ($remote_id === false) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }

            $this->_vimeo_ob->call('vimeo.videos.setDownloadPrivacy', array('video_id' => $remote_id, 'download' => false)); // If we want to allow downloading, we'll handle that locally. Most users won't want downloading.

            // Now do settings, which is like doing an immediate edit...

            // We change $video (which is a local video array) to be like a remote video array. This is because change_remote_video expects that.
            $video['remote_id'] = $remote_id;
            $video['bound_to_local_id'] = $video['local_id'];
            unset($video['local_id']);
            $video['url'] = null;
            // We pass whole $video as $changes; unchangable/irrelevant keys will be ignored, due to how change_remote_video is coded.
            $changes = $video;
            unset($changes['url']); // this is correct already of course
            $this->change_remote_video($video, $changes);
        } catch (VimeoAPIException $e) {
            require_lang('gallery_syndication_vimeo');
            $error_msg = do_lang_tempcode('VIMEO_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return null;
        }

        // Find live details
        $query_params = array();
        $query_params['video_id'] = $remote_id;
        $api_method = 'vimeo.videos.getInfo';
        $result = $this->_vimeo_ob->call($api_method, $query_params);
        if ($result === false) {
            return null;
        }
        $video = $this->_process_remote_video($result->video[0]);

        return $video;
    }

    protected function _url_to_file_path($url)
    {
        $is_temp_file = false;

        if (substr($url, 0, strlen(get_custom_base_url())) != get_custom_base_url()) {
            $temppath = cms_tempnam();
            $tempfile = fopen($temppath, 'wb');
            http_download_file($url, 1024 * 1024 * 1024 * 5, true, false, 'Composr', null, null, null, null, null, $tempfile);

            $is_temp_file = true;

            $video_path = $temppath;
        } else {
            $video_path = preg_replace('#^' . preg_quote(get_custom_base_url() . '/') . '#', get_custom_file_base() . '/', $url);
        }

        return array($video_path, $is_temp_file);
    }

    public function change_remote_video($video, $changes)
    {
        foreach (array_keys($changes) as $key) {
            try {
                switch ($key) {
                    case 'title':
                        $result = $this->_vimeo_ob->call('vimeo.videos.setTitle', array('video_id' => $video['remote_id'], 'title' => $changes['title']));
                        break;

                    case 'description':
                        $result = $this->_vimeo_ob->call('vimeo.videos.setDescription', array('video_id' => $video['remote_id'], 'description' => $changes['description']));
                        break;

                    case 'tags':
                        $result = $this->_vimeo_ob->call('vimeo.videos.clearTags', array('video_id' => $video['remote_id']));
                        $result = $this->_vimeo_ob->call('vimeo.videos.addTags', array('video_id' => $video['remote_id'], 'tags' => 'sync' . strval($video['bound_to_local_id']) . ',' . implode(',', $changes['tags'])));
                        break;

                    case 'validated':
                        $result = $this->_vimeo_ob->call('vimeo.videos.setPrivacy', array('video_id' => $video['remote_id'], 'privacy' => $changes['validated'] ? 'anybody' : 'nobody'));
                        break;

                    case 'url':
                        if (php_function_allowed('set_time_limit')) {
                            @set_time_limit(10000);
                        }
                        list($file_path, $is_temp_file) = $this->_url_to_file_path($changes['url']);
                        $remote_id = $this->_vimeo_ob->upload($file_path, true, 2097152, $video['remote_id']);
                        if ($is_temp_file) {
                            @unlink($file_path);
                        }
                        if ($remote_id === false) {
                            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
                        }
                        break;
                }
            } catch (VimeoAPIException $e) {
                require_lang('gallery_syndication_vimeo');
                $error_msg = do_lang_tempcode('VIMEO_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
                require_code('failure');
                relay_error_notification($error_msg->evaluate());
                attach_message($error_msg, 'warn');
            }
        }
    }

    public function unbind_remote_video($video)
    {
        try {
            $this->_vimeo_ob->call('vimeo.videos.clearTags', array('video_id' => $video['remote_id']));
            $this->_vimeo_ob->call('vimeo.videos.addTags', array('video_id' => $video['remote_id'], 'tags' => implode(',', $video['tags'])));
        } catch (VimeoAPIException $e) {
            require_lang('gallery_syndication_vimeo');
            $error_msg = do_lang_tempcode('VIMEO_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return false;
        }
        return true;
    }

    public function delete_remote_video($video)
    {
        try {
            $this->_vimeo_ob->call('vimeo.videos.delete', array('video_id' => $video['remote_id']));
        } catch (VimeoAPIException $e) {
            require_lang('gallery_syndication_vimeo');
            $error_msg = do_lang_tempcode('VIMEO_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return false;
        }
        return true;
    }

    public function leave_comment($video, $comment)
    {
        try {
            $this->_vimeo_ob->call('vimeo.videos.comments.addComment', array('video_id' => $video['remote_id'], 'comment_text' => $comment));
        } catch (VimeoAPIException $e) {
            require_lang('gallery_syndication_vimeo');
            $error_msg = do_lang_tempcode('VIMEO_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return false;
        }
        return true;
    }
}
