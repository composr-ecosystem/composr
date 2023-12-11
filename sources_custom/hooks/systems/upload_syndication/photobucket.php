<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    image_syndication
 */

/**
 * Hook class.
 */
class Hook_upload_syndication_photobucket
{
    public $_api = null;
    public $_logged_in = false;

    public function get_label()
    {
        return 'Photobucket';
    }

    public function get_file_handling_types()
    {
        return CMS_UPLOAD_IMAGE | CMS_UPLOAD_VIDEO;
    }

    public function is_enabled()
    {
        return get_option('photobucket_client_id') != '' && get_option('photobucket_client_secret') != '';
    }

    public function happens_always()
    {
        return false;
    }

    public function get_reference_precedence()
    {
        return UPLOAD_PRECEDENCE_HIGH;
    }

    protected function _get_api()
    {
        if (is_null($this->_api)) {
            safe_ini_set('ocproducts.type_strictness', '0');
            require_code('photobucket/PBAPI');
            $api = new PBAPI(get_option('photobucket_client_id'), get_option('photobucket_client_secret'));
            $api->setResponseParser('phpserialize');
            $this->_api = $api;
        }
        return $this->_api;
    }

    public function is_authorised()
    {
        return $this->_login();
    }

    protected function _login()
    {
        $req_key = get_value('photobucket_oauth_key__' . strval(get_member()), null, true);
        if ($req_key === null) {
            return false; // No receive_authorisation() started yet
        }

        if ($this->_logged_in) {
            return true;
        }

        $api = $this->_get_api();

        // Request the 'access' token, that may have been authorised for us already against a stored 'request' token [stored from receive_authorisation() at the start of the oAuth chain, before the user was sent to go approve it]
        if (($req_key !== null) && (substr($req_key, 0, 4) == 'req_')) {
            return $this->_get_access_token_then_login($api, $req_key);
        }

        $api->setOAuthToken(
            $req_key,
            get_value('photobucket_oauth_secret__' . strval(get_member()), null, true)
        );
        if (get_value('photobucket_oauth_subdomain__' . strval(get_member()), null, true) !== null) {
            $api->setSubdomain(get_value('photobucket_oauth_subdomain__' . strval(get_member()), null, true));
        }

        // Did it work (requires valid 'access' token, not a temporary 'request' token)
        try {
            $api->user();
            $api->get();
            $user = $api->getParsedResponse(true);
        } catch (PBAPI_Exception $e) {
            return false;
        }
        $this->_logged_in = true;
        return true;
    }

    // Get access token from request token.
    protected function _get_access_token_then_login($api, $req_key)
    {
        $api->reset(true, true, true, true);

        $api->setOAuthToken(
            $req_key,
            get_value('photobucket_oauth_secret__' . strval(get_member()), null, true)
        );
        $api->login('access');
        $api->post();

        try {
            $api->loadTokenFromResponse();
        } catch (PBAPI_Exception $e) {
            require_lang('video_syndication_photobucket');
            attach_message(do_lang_tempcode('PHOTOBUCKET_ERROR', escape_html($e->getCode()), escape_html($e->getMessage()), escape_html(get_site_name())), 'warn');

            // Tidy out old incomplete request tokens
            if (is_null(get_value_newer_than('photobucket_oauth_key__' . strval(get_member()), time() - 60 * 60, true))) {
                set_value('photobucket_oauth_key__' . strval(get_member()), null, true);
                set_value('photobucket_oauth_secret__' . strval(get_member()), null, true);
            }

            return false; // Maybe our 'request' token had a stale authorisation, or was never authorised -- so we fail and receive_authorisation() would need calling
        }
        $token = $api->getOAuthToken();
        set_value('photobucket_oauth_key__' . strval(get_member()), $token->getKey(), true); // Replace request token with access token
        set_value('photobucket_oauth_secret__' . strval(get_member()), $token->getSecret(), true);
        set_value('photobucket_oauth_username__' . strval(get_member()), $api->getUsername(), true);
        set_value('photobucket_oauth_subdomain__' . strval(get_member()), $api->getSubdomain(), true);

        $api->reset(true, true, true, true); // Don't let a previous stale 'request' token we've set in _login() block us from getting a new one
        return $this->_login();
    }

    // This is called from data/upload_syndication_auth.php, which is called to start the oAuth chain
    public function receive_authorisation()
    {
        if ($this->is_authorised()) {
            return true;
        }

        $success = $this->_login();
        if ($success) {
            return true;
        }

        // Generate a 'request' token and go do oAuth with it
        $api = $this->_get_api();
        $api->reset(true, true, true, true); // Don't let a previous stale 'request' token we've set in _login() block us from getting a new one
        $api->login('request');
        $api->post();
        $api->loadTokenFromResponse();
        $token = $api->getOAuthToken();
        set_value('photobucket_oauth_key__' . strval(get_member()), $token->getKey(), true); // Request token for now
        set_value('photobucket_oauth_secret__' . strval(get_member()), $token->getSecret(), true);
        $api->goRedirect('login', '&oauth_callback=' . urlencode(get_self_url(true))); // Request an 'access' token to be generated for us. Unfortunately the callback doesn't seem to work, but it's not truly needed anyway.
        exit();

        return false;
    }

    public function syndicate($url, $filepath, $filename, $title, $description)
    {
        require_lang('video_syndication_photobucket');

        // Fix to correct file extensions
        $filename = preg_replace('#\.f4v#', '.flv', $filename);
        $filename = preg_replace('#\.m2v#', '.mpg', $filename);
        $filename = preg_replace('#\.mpv2#', '.mpg', $filename);
        $filename = preg_replace('#\.mp2#', '.mpg', $filename);
        $filename = preg_replace('#\.m4v#', '.mp4', $filename);
        $filename = preg_replace('#\.ram#', '.rm', $filename);
        require_code('files');
        $filetype = get_file_extension($filename);
        if (in_array($filetype, array('ogg', 'ogv', 'webm', 'pdf'))) {
            if (!$this->happens_always()) {
                attach_message(do_lang_tempcode('PHOTOBUCKET_BAD_FILETYPE', escape_html($filetype)), 'warn');
            }
            return null;
        }

        try {
            $api = $this->_get_api();
            $success = $this->_login();

            $remote_gallery_name = preg_replace('#[^A-Za-z\d\_\- ]#', '', get_site_name());
            $call_params = array(
                'name' => $remote_gallery_name,
            );
            $username = get_value('photobucket_oauth_username__' . strval(get_member()), null, true);
            $api->album($username, $call_params);
            $api->post();
            try {
                $response = $api->getParsedResponse(true);
            } catch (PBAPI_Exception $e1) {
                if ($e1->getCode() != 116) {// If it's not an expected possible "Album already exists: <albumname>"
                    throw $e1;
                }
            }

            require_code('images');
            $api->reset(true, true, true);
            $call_params = array(
                'type' => is_image($filename) ? 'image' : 'video',
                'uploadfile' => '@' . $filepath, // We can't do by URL unfortunately; that requires a business deal to be arranged, it's not allowed to the general public (for abuse reasons) http://web.archive.org/web/20120119180111/http://photobucket.com/developer/forum?read,2,319
                'filename' => $filename,
                'title' => $title,
                'description' => $description,
            );
            $api->album($username . '/' . $remote_gallery_name);
            $api->upload($call_params);
            $api->post();
            $response = $api->getParsedResponse(true);

            return $response['url'];
        } catch (PBAPI_Exception $e2) {
            attach_message(do_lang_tempcode('PHOTOBUCKET_ERROR', escape_html($e2->getCode()), escape_html($e2->getMessage()), escape_html(get_site_name())), 'warn');
        }

        return null;
    }
}
