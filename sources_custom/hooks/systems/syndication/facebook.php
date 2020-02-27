<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_syndication_facebook
{
    public function get_service_name()
    {
        return 'Facebook';
    }

    public function is_available()
    {
        if (!addon_installed('facebook_support')) {
            return false;
        }

        if (get_option('facebook_syndicate') == '0') {
            return false;
        }

        $appapikey = get_option('facebook_appid');
        $appsecret = get_option('facebook_secret_code');
        if (($appapikey == '') || ($appsecret == '')) {
            return false;
        }

        return true;
    }

    public function syndication_javascript_function_calls()
    {
        if (get_option('facebook_member_syndicate_to_page') == '0') {
            return '';
        }

        require_lang('facebook');
        require_javascript('facebook_support');

        return ['hookSyndicationFacebook_syndicationJavascript'];
    }

    public function auth_is_set($member_id)
    {
        $save_to = 'facebook_oauth_token';
        if ($member_id !== null) {
            $save_to .= '__' . strval($member_id);
        }
        $val = get_value($save_to, null, true);
        return !empty($val);
    }

    public function auth_set($member_id, $oauth_url)
    {
        require_lang('facebook');
        require_code('facebook_connect');
        global $FACEBOOK_CONNECT;

        $code = get_param_string('code', '', INPUT_FILTER_GET_COMPLEX);

        if ($code == '') {
            $scope = ['publish_actions'];
            if ($member_id === null) {
                $scope[] = 'manage_pages';
                $scope[] = 'publish_pages';
            }
            $oauth_redir_url = $FACEBOOK_CONNECT->getLoginUrl(['redirect_uri' => $oauth_url->evaluate(), 'scope' => $scope]);
            require_code('site2');
            redirect_exit($oauth_redir_url);
        }

        if (get_param_string('error_reason', null, INPUT_FILTER_GET_COMPLEX) !== null) { // oauth happened and ERROR!
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(get_param_string('error_reason', false, INPUT_FILTER_GET_COMPLEX))), 'warn', false, true);
            return false;
        }

        // oauth apparently worked
        $access_token = $FACEBOOK_CONNECT->getAccessToken();
        if ($access_token === null) { // Actually it didn't
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(do_lang('UNKNOWN'))), 'warn', false, true);
            return false;
        }

        if ($member_id === null) {
            /*$FACEBOOK_CONNECT->setExtendedAccessToken();
            $FACEBOOK_CONNECT->api('/oauth/access_token', 'POST',
                [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => get_option('facebook_appid'),
                    'client_secret' => get_option('facebook_secret_code'),
                    'fb_exchange_token' => $access_token
                ]
            );*/

            if (get_option('facebook_uid') == '') {
                require_code('config2');
                $facebook_uid = $FACEBOOK_CONNECT->getUser();
                set_option('facebook_uid', strval($facebook_uid));
            }
        }

        if ((strpos($access_token, '|') === false) || ($member_id === null)) { // If for users, not if application access token, which will happen on a refresh (as user token will not confirm twice)
            $save_to = 'facebook_oauth_token';
            if ($member_id !== null) {
                $save_to .= '__' . strval($member_id);
            }
            set_value($save_to, $access_token, true);

            $facebook_syndicate_to_page = get_param_string('facebook_syndicate_to_page', null);
            if ($facebook_syndicate_to_page !== null) {
                set_value('facebook_syndicate_to_page__' . strval($member_id), $facebook_syndicate_to_page, true);
            }
        }

        if (get_page_name() != 'facebook_oauth') { // Take member back to page that implicitly shows their results
            require_code('site2');
            $target_url = $oauth_url->evaluate();
            $target_url = preg_replace('#oauth_in_progress=\d+#', '', $target_url);
            $target_url = preg_replace('#syndicate_start__facebook=\d+#', '', $target_url);
            redirect_exit($target_url);
        }

        return true;
    }

    public function auth_unset($member_id)
    {
        $save_to = 'facebook_oauth_token';
        if ($member_id !== null) {
            $save_to .= '__' . strval($member_id);
        }
        set_value($save_to, null, true);
    }

    public function syndicate_user_activity($member_id, $row)
    {
        if (($this->is_available()) && ($this->auth_is_set($member_id))) {
            $page_syndicate = (get_option('facebook_member_syndicate_to_page') == '1' && get_option('facebook_uid') != '' && get_value('facebook_syndicate_to_page__' . strval($member_id), null, true) === '1');
            return $this->_send(
                get_value('facebook_oauth_token__' . strval($member_id), null, true),
                $row,
                $page_syndicate ? get_option('facebook_uid') : 'me',
                $member_id,
                $page_syndicate
            );
        }
        return false;
    }

    public function auth_is_set_site()
    {
        global $FACEBOOK_CONNECT;
        if (!isset($FACEBOOK_CONNECT)) {
            return false;
        }

        if (get_value('facebook_oauth_token', null, true) === null) {
            return false;
        }

        if (get_option('facebook_uid') == '') {
            return false; // No configured target
        }

        if (($this->auth_is_set(get_member())) && (get_option('facebook_uid') == strval($FACEBOOK_CONNECT->getUser()))) {
            return false; // Avoid double syndication, will already go to the user
        }

        return true;
    }

    public function syndicate_site_activity($row)
    {
        if (($this->is_available()) && ($this->auth_is_set_site())) {
            return $this->_send(
                get_value('facebook_oauth_token'),
                $row,
                get_option('facebook_uid')
            );
        }
        return false;
    }

    protected function _send($token, $row, $post_to_uid = 'me', $member_id = null, $silent_warn = false)
    {
        require_lang('facebook');
        require_code('facebook_connect');

        // Prepare message
        list($message) = render_activity($row, false);
        $name = $row['a_label_1'];
        require_code('character_sets');
        $name = convert_to_internal_encoding($name, get_charset(), 'utf-8');
        $link = ($row['a_page_link_1'] == '') ? '' : page_link_to_url($row['a_page_link_1'], true);
        $message = strip_html($message->evaluate());
        $message = convert_to_internal_encoding($message, get_charset(), 'utf-8');

        // Send message
        $appid = get_option('facebook_appid');
        $appsecret = get_option('facebook_secret_code');
        $fb = new Facebook(['appId' => $appid, 'secret' => $appsecret]);
        $fb->setAccessToken($token);

        $attachment = ['description' => $message];
        if (($name != '') && ($name != $message)) {
            $attachment['name'] = $name;
        }
        if ($link != '') {
            $attachment['link'] = $link;
        }
        if (count($attachment) == 1) {
            $attachment = ['message' => $message];
        }

        if ($post_to_uid == 'me') {
            $post_to_uid = $fb->getUser(); // May not be needed, but just in case
        }

        try {
            $ret = $fb->api('/' . $post_to_uid . '/feed', 'POST', $attachment);
        } catch (Exception $e) {
            if (($member_id !== null) && (!has_interesting_post_fields()) && (running_script('index')) && (!headers_sent())) {
                $this->auth_set($member_id, get_self_url());
            }

            header('Facebook-Error: ' . escape_header($e->getMessage()));

            if (!$silent_warn) {
                attach_message($e->getMessage(), 'warn', false, true);
            }
        }

        return true;
    }
}
