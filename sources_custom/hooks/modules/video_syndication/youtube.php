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
class Hook_video_syndication_youtube
{
    public $_access_token = null;

    public function uninstall()
    {
    }

    public function __construct()
    {
        require_code('xml');
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
        $youtube_client_id = get_option('youtube_client_id');
        if ($youtube_client_id == '') {
            return false;
        }

        $youtube_client_secret = get_option('youtube_client_secret');
        if ($youtube_client_secret == '') {
            return false;
        }

        $youtube_developer_key = get_option('youtube_developer_key');
        if ($youtube_developer_key == '') {
            return false;
        }

        $refresh_token = get_value('youtube_refresh_token', null, true);
        if ($refresh_token == '') {
            return false;
        }

        return ($youtube_client_id != '');
    }

    public function get_remote_videos($local_id = null, $transcoding_id = null)
    {
        $videos = array();

        if (!is_null($local_id)) {
            // This code is a bit annoying. Ideally we'd do a remote tag search, but YouTube's API is lagged here, and only works for listed videos. We'll therefore look at our local mappings.
            $transcoding_id = $GLOBALS['SITE_DB']->query_value_if_there('SELECT t_id FROM ' . get_table_prefix() . 'video_transcoding WHERE t_local_id=' . strval($local_id) . ' AND t_id LIKE \'' . db_encode_like('youtube\_%') . '\'');
            if (is_null($transcoding_id)) {
                return array(); // Not uploaded yet
            }

            $transcoding_id = preg_replace('#^youtube_#', '', $transcoding_id);
        }

        $start = 1;
        do {
            if (!is_null($transcoding_id)) {
                /* EDIT: Actually we looked at transcoding table instead and lookup individual video and process as such
                $query_params['category'] = 'sync' . strval($local_id); // Covers {http://gdata.youtube.com/schemas/2007/developertags.cat} and {http://gdata.youtube.com/schemas/2007/keywords.cat}
                $xml = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads', $query_params);

                if (!isset($parsed->entry)) // Annoying! YouTube search index takes time and doesn't consider unlisted. We therefore need to search much harder.
                {
                    unset($query_params['category']);
                    $xml = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads', $query_params);
                }
                */

                $xml = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads/' . $transcoding_id, array());

                if ($xml === null) {
                    break;
                }

                $p = simplexml_load_string($xml);

                $detected_video = $this->_process_remote_video($p);

                if (!is_null($detected_video)) {
                    $remote_id = $detected_video['remote_id'];
                    if ((!array_key_exists($remote_id, $videos)) || (!$videos[$remote_id]['validated'])) { // If new match, or last match was unvalidated (i.e. old version)
                        $videos[$remote_id] = $detected_video;
                    }
                }

                break; // Done
            }

            $query_params = array('max-results' => strval(50), 'start-index' => strval($start));
            $xml = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads', $query_params);

            if ($xml === null) {
                break;
            }
            $parsed = simplexml_load_string($xml);

            if (!isset($parsed->entry)) {
                break;
            }

            foreach ($parsed->entry as $p) {
                $detected_video = $this->_process_remote_video($p);

                if (!is_null($detected_video)) {
                    $remote_id = $detected_video['remote_id'];
                    if ((!array_key_exists($remote_id, $videos)) || (!$videos[$remote_id]['validated'])) { // If new match, or last match was unvalidated (i.e. old version)
                        $videos[$remote_id] = $detected_video;
                    }
                }
            }

            $start += 50;
        } while (count($parsed['entry']) > 0);

        return $videos;
    }

    protected function _process_remote_video($p)
    {
        $_remote_id = $p->xpath('//media:group/yt:videoid');
        $remote_id = @strval($_remote_id[0]);

        $add_date = strtotime(@strval($p->published[0]));
        $edit_date = isset($p->updated[0]) ? strtotime(@strval($p->updated[0])) : $add_date;

        $allow_rating = mixed();
        $allow_comments = mixed();
        $validated = true;
        foreach ($p->xpath('//yt:accessControl') as $a) {
            if (@strval($a['action']) == 'rate') {
                $allow_rating = (@strval($a['permission']) == 'allowed');
            }
            if (@strval($a['action']) == 'comment') {
                $allow_comments = (@strval($a['permission']) == 'allowed');
            }
            if (@strval($a['action']) == 'list') {
                $validated = (@strval($a['permission']) == 'allowed');
            }
        }
        $bound_to_local_id = mixed();
        $category = mixed();

        $_keywords = $p->xpath('//media:group/media:keywords');
        $keywords = explode(', ', @strval($_keywords[0]));

        // Find category and bound ID
        foreach ($p->xpath('//media:group/media:category') as $k) {
            if (@strval($k['scheme']) == 'http://gdata.youtube.com/schemas/2007/developertags.cat') {
                $matches = array();
                if (preg_match('#^sync(\d+)$#', @strval($k), $matches) != 0) {
                    $bound_to_local_id = intval($matches[1]);
                }
            } elseif (@strval($k['scheme']) == 'http://gdata.youtube.com/schemas/2007/categories.cat') {
                $category = @strval($k);
                array_unshift($keywords, $category);
            }
        }

        // Maybe bound ID was explicitly put in via keywords (takes precedence, as this is the one thing that can be re-edited [dev ID is locked at initial upload])
        foreach ($keywords as $i => $k) {
            $matches = array();
            if (preg_match('#^sync(\d+)$#', $k, $matches) != 0) {
                $bound_to_local_id = intval($matches[1]);
                unset($keywords[$i]);
            }
        }

        $_description = $p->xpath('//media:group/media:description');

        $_thumbnail = $p->xpath('//media:group/media:thumbnail');

        $_player = $p->xpath('//media:group/media:player');

        $detected_video = array(
            'bound_to_local_id' => $bound_to_local_id,
            'remote_id' => $remote_id,

            'title' => @strval($p->title),
            'description' => isset($_description[0]) ? @strval($_description[0]) : '',
            'mtime' => $edit_date,
            'tags' => $keywords,
            'url' => @strval($_thumbnail[0]['url']),
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
        global $HTTP_DOWNLOAD_URL;

        $extra_headers = array('Slug' => basename($video['url']));

        $xml = $this->_generate_video_xml($video/*PHP has weird overwrite precedence with + operator, opposite to the intuitive ordering*/, true);

        list($file_path, $is_temp_file, $mime_type) = $this->_url_to_file_path($video['url']);
        if (!is_file($file_path)) {
            return null;
        }

        $api_url = 'https://uploads.gdata.youtube.com/resumable/feeds/api/users/default/uploads';

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(10000);
        }
        try {
            $test = $this->_http($api_url, array(), 'POST', $xml, 1000.0, $extra_headers/*, $file_path*/);
            $response = $this->_http($HTTP_DOWNLOAD_URL, null, 'PUT', null, 10000.0, $extra_headers, $file_path, $mime_type);

            if ($is_temp_file) {
                @unlink($file_path);
            }

            $parsed = simplexml_load_string($response);

            if (isset($parsed->error)) {
                throw new Exception(@strval($parsed->error->internalReason), @strval($parsed->error->code));
            }
        } catch (Exception $e) {
            if ($is_temp_file) {
                @unlink($file_path);
            }

            require_lang('gallery_syndication_youtube');
            $error_msg = do_lang_tempcode('YOUTUBE_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return null;
        }

        return $this->_process_remote_video($parsed);
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

        require_code('mime_types');
        $mime_type = get_mime_type(get_file_extension($url), false);

        return array($video_path, $is_temp_file, $mime_type);
    }

    public function change_remote_video($video, $changes)
    {
        if (array_key_exists('url', $changes)) { // Oh, if URL changes we'll need to actually unvalidate existing one and put up a new one (this is all that YouTube allows).
            $this->upload_video($changes + $video/*PHP has weird overwrite precedence with + operator, opposite to the intuitive ordering*/); // Put up a new one.

            $changes['validated'] = false; // Let the existing one unvalidate, flow on...
        }

        $xml = $this->_generate_video_xml($changes + $video/*PHP has weird overwrite precedence with + operator, opposite to the intuitive ordering*/, false);

        try {
            $response = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads/' . $video['remote_id'], null, 'PUT', $xml);

            $parsed = simplexml_load_string($response);

            if (isset($parsed->error)) {
                throw new Exception(@strval($parsed->error->internalReason), @strval($parsed->error->code));
            }
        } catch (Exception $e) {
            require_lang('gallery_syndication_youtube');
            $error_msg = do_lang_tempcode('YOUTUBE_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return null;
        }
    }

    public function unbind_remote_video($video)
    {
        // No-op for youtube, can't be done via YouTube Data API. Fortunately we don't really need this method.
        return false;
    }

    public function delete_remote_video($video)
    {
        try {
            $response = $this->_http('https://gdata.youtube.com/feeds/api/users/default/uploads/' . $video['remote_id'], null, 'DELETE');

            $parsed = simplexml_load_string($response);

            if (isset($parsed->error)) {
                throw new Exception(@strval($parsed->error->internalReason), @strval($parsed->error->code));
            }
        } catch (Exception $e) {
            require_lang('gallery_syndication_youtube');
            $error_msg = do_lang_tempcode('YOUTUBE_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return false;
        }
        return true;
    }

    public function leave_comment($video, $comment)
    {
        $xml = trim('
            <' . '?xml version="1.0" encoding="utf-8"?' . '>
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:yt="http://gdata.youtube.com/schemas/2007">
                    <content>' . xmlentities($comment) . '</content>
            </entry>
        ');

        try {
            $response = $this->_http('https://gdata.youtube.com/feeds/api/videos/' . $video['remote_id'] . '/comments', array(), 'POST', $xml);

            $parsed = simplexml_load_string($response);

            if (isset($parsed->error)) {
                throw new Exception(@strval($parsed->error->internalReason), @strval($parsed->error->code));
            }
        } catch (Exception $e) {
            require_lang('gallery_syndication_youtube');
            $error_msg = do_lang_tempcode('YOUTUBE_ERROR', escape_html(strval($e->getCode())), $e->getMessage(), escape_html(get_site_name()));
            require_code('failure');
            relay_error_notification($error_msg->evaluate());
            attach_message($error_msg, 'warn');
            return false;
        }

        return true;
    }

    protected function _generate_video_xml($video, $is_initial)
    {
        // Match to a category using remote list
        $remote_list_xml = http_download_file('http://gdata.youtube.com/schemas/2007/categories.cat');
        $remote_list_parsed = simplexml_load_string($remote_list_xml);
        $category = 'People';
        foreach ($remote_list_parsed->category as $c) { // Try to bind to one of our tags. Already-bound-remote-category intentionally will be on start of tags list, so automatically maintained through precedence.
            foreach ($video['tags'] as $i => $tag) {
                if (($c['term'] == $tag) && (isset($c['assignable'][0]))) {
                    $category = $tag;
                    unset($video['tags'][$i]);
                    break 2;
                }
            }
        }

        // Now generate the XML...
        $xml = '
            <' . '?xml version="1.0"?' . '>
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://gdata.youtube.com/schemas/2007">
                    <media:group>
                            <media:title type="plain">' . xmlentities($video['title']) . '</media:title>
                            <media:description type="plain">' . xmlentities($video['description']) . '</media:description>';
        if ($category !== null) {
            $xml .= '
                            <media:category scheme="http://gdata.youtube.com/schemas/2007/categories.cat">' . xmlentities($category) . '</media:category>';
        }
        $xml .= '
                            <media:keywords>' . xmlentities(implode(', ', $video['tags'])) . ', sync' . strval($video['local_id']) . '</media:keywords>';
        if ($is_initial) {
            $xml .= '
                            <media:category scheme="http://gdata.youtube.com/schemas/2007/developertags.cat">sync' . xmlentities(strval($video['local_id'])) . '</media:category>';
        }
        $xml .= '
                    </media:group>
                    <yt:accessControl action="rate" permission="' . ($video['allow_rating'] ? 'allowed' : 'denied') . '" />
                    <yt:accessControl action="comment" permission="' . ($video['allow_comments'] ? 'allowed' : 'denied') . '" />
                    <yt:accessControl action="list" permission="' . ($video['validated'] ? 'allowed' : 'denied') . '" />
                    <updated>' . date('c', $video['mtime']) . '</updated>
            </entry>
        ';

        return trim($xml);
    }

    protected function _connect()
    {
        require_code('oauth');

        // Read in settings. If unset, we won't get a token - but we will return to allow anonymous calls
        $client_id = get_option('youtube_client_id');
        if ($client_id == '') {
            return true;
        }
        $client_secret = get_option('youtube_client_secret');
        if ($client_secret == '') {
            return true;
        }
        $refresh_token = get_value('youtube_refresh_token', null, true);
        if ((is_null($refresh_token)) || ($refresh_token == '')) {
            return true;
        }

        $endpoint = 'https://accounts.google.com/o/oauth2';
        $auth_url = $endpoint . '/token';
        $this->_access_token = refresh_oauth2_token('youtube', $auth_url, $client_id, $client_secret, $refresh_token, $endpoint);

        return !is_null($this->_access_token);
    }

    protected function _http($url, $params, $http_verb = 'GET', $xml = null, $timeout = 6.0, $extra_headers = null, $file_to_upload = null, $content_type = 'application/atom+xml')
    {
        $youtube_developer_key = get_option('youtube_developer_key');

        if (is_null($this->_access_token)) {
            if (!$this->_connect()) {
                return null;
            }
        }

        if (!empty($params)) {
            $full_url = $url . '?strict=1&v=2.1&' . http_build_query($params);
        } else {
            $full_url = $url;
        }

        if (is_null($extra_headers)) {
            $extra_headers = array();
        }

        if (!is_null($this->_access_token)) {
            $extra_headers['Authorization'] = 'Bearer ' . $this->_access_token;
        }

        if ($youtube_developer_key != '') {
            $extra_headers['X-GData-Key'] = 'key=' . $youtube_developer_key;
        }

        $files = mixed();
        if (!is_null($file_to_upload)) {
            require_code('mime_types');
            $mime_type = get_mime_type(get_file_extension($file_to_upload), false);
            $files = array($mime_type => $file_to_upload);
        }

        $response = http_download_file($full_url, null, false, true, 'Composr', is_null($xml) ? null : array($xml), null, null, null, null, null, null, null, $timeout, !is_null($xml), $files, $extra_headers, $http_verb, $content_type);

        if (is_null($response)) {
            global $HTTP_MESSAGE_B;
            throw new Exception(is_null($HTTP_MESSAGE_B) ? do_lang('UNKNOWN') : static_evaluate_tempcode($HTTP_MESSAGE_B));
        }

        return $response;
    }
}
