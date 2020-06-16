<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

// IDEA: #4276 Also sync Composr categories to YouTube playlists

// Note we unfortunately cannot sync allow_rating and allow_comments. This is because Google did not include this in the v3 API (https://issuetracker.google.com/issues/35174729).

/**
 * Hook class.
 */
class Hook_video_syndication_youtube
{
    public $_access_token = null;

    public function uninstall()
    {
    }

    public function get_service_title()
    {
        return 'YouTube';
    }

    public function recognises_as_remote($url)
    {
        return ((preg_match('#^https?://www\.youtube\.com/watch\?v=([\w\-]+)#', $url) != 0) || (preg_match('#^http://youtu\.be/([\w\-]+)#', $url) != 0));
    }

    public function is_active()
    {
        if (!addon_installed('gallery_syndication')) {
            return false;
        }

        $youtube_client_id = get_option('google_apis_client_id');
        if ($youtube_client_id == '') {
            return false;
        }

        $youtube_client_secret = get_option('google_apis_client_secret');
        if ($youtube_client_secret == '') {
            return false;
        }

        $youtube_developer_key = get_option('google_apis_api_key');
        if ($youtube_developer_key == '') {
            return false;
        }

        $refresh_token = get_value('youtube_refresh_token', null, true);
        if ($refresh_token == '') {
            return false;
        }

        return ($youtube_client_id != '');
    }

    public function get_remote_videos($local_id = null, $standard_format = true, $max_rows = null)
    {
        $videos = [];

        if (($max_rows !== null) && ($max_rows < 1)) {
            return [];
        }

        if ($local_id === null) {
            $remote_id = null;
        } else {
            // This code is a bit annoying. Ideally we'd do a remote tag search, but YouTube's API is lagged here, and only works for listed videos. We'll therefore look at our local mappings.
            $transcoding_id = $GLOBALS['SITE_DB']->query_value_if_there('SELECT t_id FROM ' . get_table_prefix() . 'video_transcoding WHERE t_local_id=' . strval($local_id) . ' AND t_id LIKE \'' . db_encode_like('youtube\_%') . '\'');
            if ($transcoding_id === null) {
                return []; // Not uploaded yet
            }

            $remote_id = preg_replace('#^youtube_#', '', $transcoding_id);
        }

        if ($remote_id !== null) { // Searching for a specific linked video
            try {
                $url = 'https://www.googleapis.com/youtube/v3/videos';
                $params = [
                    'part' => 'snippet,status',
                    'id' => $remote_id,
                ];
                $result = $this->_http($url, $params);

                if (!empty($result['items'])) {
                    if ($standard_format) {
                        $detected_video = $this->process_remote_video($result['items'][0]);
                        if ($detected_video !== null) {
                            $remote_id = $detected_video['remote_id'];
                            $videos[$remote_id] = $detected_video;
                        }
                    } else {
                        $videos[] = $result['items'][0];
                    }
                }
            } catch (Exception $e) {
                $this->convert_exception_to_attached_message($url, $e);
                return null;
            }
        } else { // Listing all linked videos
            try {
                // First we need to find the 'videos' playlist
                $url = 'https://www.googleapis.com/youtube/v3/channels';
                $params = [
                    'part' => 'contentDetails',
                    'mine' => true,
                ];
                $result = $this->_http($url, $params);
                $playlist_id = $result['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

                $next_page_token = null;
                do {
                    $url = 'https://www.googleapis.com/youtube/v3/playlistItems';
                    $params = [
                        'part' => 'snippet,status',
                        'maxResults' => ($max_rows === null) ? 50 : min(50, $max_rows - count($videos)),
                        'playlistId' => $playlist_id,
                    ];
                    if ($next_page_token !== null) {
                        $params['pageToken'] = $next_page_token;
                    }
                    $result = $this->_http($url, $params);

                    foreach ($result['items'] as $remote_video) {
                        if ($standard_format) {
                            $detected_video = $this->process_remote_video($remote_video);
                            if ($detected_video !== null) {
                                $remote_id = $detected_video['remote_id'];
                                if (!array_key_exists($remote_id, $videos)) { // If new match
                                    $videos[$remote_id] = $detected_video;

                                    if (count($videos) >= $max_rows) {
                                        break 2;
                                    }
                                }
                            }
                        } else {
                            $videos[] = $remote_video;

                            if (count($videos) >= $max_rows) {
                                break 2;
                            }
                        }
                    }

                    if (empty($result['nextPageToken'])) {
                        $next_page_token = null;
                    } else {
                        $next_page_token = $result['nextPageToken'];
                    }
                } while ($next_page_token !== null);
            } catch (Exception $e) {
                $this->convert_exception_to_attached_message($url, $e);
                return null;
            }
        }

        return $videos;
    }

    protected function process_remote_video($remote_video)
    {
        $snippet = $remote_video['snippet'];

        $categories = $this->get_remote_categories();

        // Find bound ID, and real tags, from remote tags and remote category
        $local_id = null;
        $tags = [];
        $category_id = $snippet['categoryId'];
        if (array_key_exists($category_id, $categories)) {
            $tags[] = $categories[$category_id];
        }
        $tags = array_merge($tags, $snippet['tags']);
        foreach ($tags as $k) {
            $matches = [];
            if (preg_match('#^sync(\d+)$#', $k, $matches) != 0) {
                $local_id = intval($matches[1]);
                array_unshift($tags, $k);
            }
        }

        // Find highest resolution thumbnail
        $best_width = null;
        foreach ($snippet['thumbnails'] as $thumbnail) {
            if (($best_width === null) || ($thumbnail['width'] > $best_width)) {
                $thumb_url = $thumbnail['url'];
                $best_width = $thumbnail['width'];
            }
        }

        $detected_video = [
            'local_id' => $local_id,
            'remote_id' => $remote_video['id'],

            'title' => $snippet['title'],
            'description' => $snippet['description'],
            'mtime' => strtotime($snippet['publishedAt']),
            'tags' => $tags,
            'url' => null,
            'thumb_url' => $thumb_url,
            'validated' => ($remote_video['status']['privacyStatus'] == 'public'),
        ];

        if ($local_id !== null) {
            return $detected_video; // else we ignore remote videos that aren't bound to local ones
        }

        return null;
    }

    public function upload_video($video)
    {
        cms_disable_time_limit();

        // Tests before we do anything
        list($file_path, $is_temp_file, $mime_type) = $this->url_to_file_path($video['url']);
        if (!is_file($file_path)) {
            return null;
        }

        // Upload metadata
        try {
            $url = 'https://www.googleapis.com/upload/youtube/v3/videos';
            $params = [
                'part' => 'snippet,status',
            ];
            $json = $this->generate_video_json($video, true);
            $metadata_http_result = $this->_http_lowlevel($url, $params, 'POST', $json, 1000.0, [], null, 'video/*');

            // Error?
            if ($metadata_http_result->message != '200') {
                if (empty($metadata_http_result->data)) {
                    throw new Exception(($metadata_http_result->message_b === null) ? do_lang('UNKNOWN') : static_evaluate_tempcode($metadata_http_result->message_b));
                }

                $metadata_result = @json_decode($metadata_http_result->data, true);

                if (is_array($metadata_result)) {
                    throw new Exception(@strval($metadata_result['error']['message']), @strval($metadata_result['error']['code']));
                } else {
                    throw new Exception($metadata_http_result->data);
                }
            }
        } catch (Exception $e) {
            // Cleanup
            if ($is_temp_file) {
                @unlink($file_path);
            }

            $this->convert_exception_to_attached_message($url, $e);
            return null;
        }

        // Upload actual video file
        try {
            $url = $metadata_http_result->download_url;
            $params = [];
            $result = $this->_http($url, $params, 'PUT', null, 10000.0, [], $file_path, $mime_type, false);
        } catch (Exception $e) {
            $this->convert_exception_to_attached_message($url, $e);
            return null;
        } finally {
            // Cleanup
            if ($is_temp_file) {
                @unlink($file_path);
            }
        }

        // Upload thumbnail
        if ($video['thumb_url'] != '') {
            list($file_path, $is_temp_file) = $this->url_to_file_path($video['thumb_url']);
            if (is_file($file_path)) {
                try {
                    require_code('images');
                    $temppath_a = cms_tempnam();
                    $temppath_b = $temppath_a;
                    convert_image($file_path, $temppath_b, 1280, 720, null, false, 'png', true);
                    if (($temppath_a == $temppath_b) && (is_file($temppath_b))) { // Did indeed manage to make a PNG file
                        try {
                            $url = 'https://www.googleapis.com/upload/youtube/v3/thumbnails/set';
                            $params = ['videoId' => $result['snippet']['id']];
                            $result = $this->_http($url, $params, 'POST', null, 1000.0, [], $temppath_b, 'image/png', false);
                        } catch (Exception $e) {
                            // We do not care if this fails
                        }
                    }
                } catch (Exception $e) {
                    $this->convert_exception_to_attached_message($url, $e);
                    return null;
                } finally {
                    // Cleanup
                    if ($is_temp_file) {
                        @unlink($file_path);
                    }
                    @unlink($temppath_b);
                }
            }
        }

        return $this->process_remote_video($result);
    }

    protected function url_to_file_path($url)
    {
        $is_temp_file = false;

        if (substr($url, 0, strlen(get_custom_base_url())) != get_custom_base_url()) {
            $temppath = cms_tempnam();
            $tempfile = fopen($temppath, 'wb');
            http_get_contents($url, ['convert_to_internal_encoding' => true, 'byte_limit' => 1024 * 1024 * 1024 * 5, 'write_to_file' => $tempfile]);

            $is_temp_file = true;

            $video_path = $temppath;
        } else {
            $video_path = preg_replace('#^' . preg_quote(get_custom_base_url() . '/') . '#', get_custom_file_base() . '/', $url);
        }

        require_code('mime_types');
        $mime_type = get_mime_type(get_file_extension($url), false);

        return [$video_path, $is_temp_file, $mime_type];
    }

    public function change_remote_video($video, $changes, $unbind = false)
    {
        if (!empty($changes['url'])) { // Oh, if URL changes we'll need to actually delete existing one and put up a new one (this is all that YouTube allows).
            $this->upload_video($changes + $video/*PHP has weird overwrite precedence with + operator, opposite to the intuitive ordering*/); // Put up a new one

            $changes['validated'] = false; // Let the existing one unvalidate, flow on...
        }

        $json = $this->generate_video_json($changes + $video/*PHP has weird overwrite precedence with + operator, opposite to the intuitive ordering*/, !$unbind);

        try {
            $url = 'https://www.googleapis.com/youtube/v3/videos';
            $params = [
                'id' => $video['remote_id'],
                'part' => 'snippet,status',
            ];
            $result = $this->_http($url, $params, 'PUT', $json);
        } catch (Exception $e) {
            $this->convert_exception_to_attached_message($url, $e);
            return null;
        }
        return $this->process_remote_video($result);
    }

    public function delete_remote_video($video)
    {
        try {
            $url = 'https://www.googleapis.com/youtube/v3/videos';
            $params = ['id' => $video['remote_id']];
            $result = $this->_http($url, $params, 'DELETE');
        } catch (Exception $e) {
            $this->convert_exception_to_attached_message($url, $e);
            return false;
        }
        return true;
    }

    public function leave_comment($video, $comment)
    {
        try {
            $url = 'https://www.googleapis.com/youtube/v3/comments';
            $params = ['part' => 'snippet'];
            $request = [
                'snippet' => [
                    'textOriginal' => $comment,
                    'videoId' => $video['remote_id'],
                ],
            ];
            $json = json_encode($request);
            $result = $this->_http($url, $params, 'POST', $json);
        } catch (Exception $e) {
            $this->convert_exception_to_attached_message($url, $e);
            return false;
        }
        return true;
    }

    protected function generate_video_json($video, $bind = true)
    {
        // Match to a category using remote list
        $category_id = 1;
        $possible_categories = $this->get_remote_categories();
        foreach ($possible_categories as $_category_id => $_tag) { // Try to bind to one of our tags. Already-bound-remote-category intentionally will be on start of tags list, so automatically maintained through precedence.
            foreach ($video['tags'] as $i => $tag) {
                if ($_tag == $tag) {
                    $category_id = $_category_id;
                    unset($video['tags'][$i]);
                    break 2;
                }
            }
        }

        $_tags = $video['tags'];
        if (($bind) && (!empty($video['local_id']))) {
            $_tags[] = 'sync' . strval($video['local_id']);
        }

        $request = [
            'snippet' => [
                'title' => $video['title'],
                'description' => $video['description'],
                'tags' => $_tags,
                'categoryId' => $category_id,
            ],
            'status' => [
                'privacyStatus' => $video['validated'] ? 'public' : 'unlisted',
            ],
        ];
        return json_encode($request);
    }

    public function get_remote_categories()
    {
        static $categories = [];

        if (empty($categories)) {
            $country = get_value('youtube_primary_country', 'US', true);
            try {
                $url = 'https://www.googleapis.com/youtube/v3/videoCategories';
                $params = ['part' => 'snippet', 'regionCode' => $country];
                $_categories = $this->_http($url, $params, 'GET', null, 6.0, [], null, 'application/json', true, true);
            } catch (Exception $e) {
                $this->convert_exception_to_attached_message($url, $e);
                return [1 => do_lang('GENERAL')];
            }
            foreach ($_categories['items'] as $category) {
                if ($category['snippet']['assignable']) {
                    $categories[$category['id']] = $category['snippet']['title'];
                }
            }
        }

        return $categories;
    }

    protected function _connect()
    {
        require_code('oauth');

        // Read in settings. If unset, we won't get a token - but we will return to allow anonymous calls
        $client_id = get_option('google_apis_client_id');
        if ($client_id == '') {
            return true;
        }
        $client_secret = get_option('google_apis_client_secret');
        if ($client_secret == '') {
            return true;
        }
        $refresh_token = get_value('youtube_refresh_token', null, true);
        if (empty($refresh_token)) {
            return true;
        }

        $endpoint = 'https://accounts.google.com/o/oauth2';
        $auth_url = $endpoint . '/token';
        $this->_access_token = refresh_oauth2_token('youtube');

        return ($this->_access_token !== null);
    }

    protected function _http($url, $params = [], $http_verb = 'GET', $request_body = null, $timeout = 6.0, $extra_headers = [], $file_to_upload = null, $content_type = 'application/json', $text = true, $support_caching = false)
    {
        $http_result = $this->_http_lowlevel($url, $params, $http_verb, $request_body, $timeout, $extra_headers, $file_to_upload, $content_type, $text, $support_caching);

        if ($support_caching) {
            list($data, , , , $message, $message_b) = $http_result;
        } else {
            $data = $http_result->data;
            $message = $http_result->message;
            $message_b = $http_result->message_b;
        }

        if (empty($data)) {
            throw new Exception(($message_b === null) ? do_lang('UNKNOWN') : static_evaluate_tempcode($message_b));
        }

        $result = @json_decode($data, true);

        if (is_array($result)) {
            if (isset($result['error'])) {
                throw new Exception(@strval($result['error']['message']), @strval($result['error']['code']));
            }
        } else {
            throw new Exception($data);
        }

        return $result;
    }

    protected function _http_lowlevel($url, $params = [], $http_verb = 'GET', $request_body = null, $timeout = 6.0, $extra_headers = [], $file_to_upload = null, $content_type = 'application/json', $text = true, $support_caching = false)
    {
        if ($this->_access_token === null) {
            if (!$this->_connect()) {
                return null;
            }
        }

        $youtube_developer_key = get_option('google_apis_api_key');
        if ($youtube_developer_key != '') {
            $params['key'] = $youtube_developer_key;
        }

        if (!empty($params)) {
            $full_url = $url . '?' . http_build_query($params);
        } else {
            $full_url = $url;
        }

        if ($this->_access_token !== null) {
            $extra_headers['Authorization'] = 'Bearer ' . $this->_access_token;
        }

        $files = null;
        if ($file_to_upload !== null) {
            require_code('mime_types');
            $mime_type = get_mime_type(get_file_extension($file_to_upload), false);
            $files = [$mime_type => $file_to_upload];
        }

        if (($request_body !== null) && ($text)) {
            require_code('character_sets');
            $request_body = convert_to_internal_encoding($request_body, get_charset(), 'utf-8');
        }

        $options = [
            'trigger_error' => false,
            'post_params' => ($request_body === null) ? null : [$request_body],
            'timeout' => $timeout,
            'raw_post' => $request_body !== null,
            'files' => $files,
            'extra_headers' => $extra_headers,
            'http_verb' => $http_verb,
            'raw_content_type' => $content_type,
            'ignore_http_status' => true,
        ];
        if ($text) {
            $options['convert_to_internal_encoding'] = true;
        }

        if ($support_caching) {
            $http_result = cache_and_carry('cms_http_request', [$full_url, $options]);
        } else {
            $http_result = cms_http_request($full_url, $options);
        }

        return $http_result;
    }

    protected function convert_exception_to_attached_message($url, $e)
    {
        require_lang('gallery_syndication_youtube');
        $error_msg = do_lang_tempcode('YOUTUBE_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html($url));
fatal_exit($error_msg);//TODO
        require_code('failure');
        relay_error_notification($error_msg->evaluate());
        attach_message($error_msg, 'warn', false, true);
    }
}
