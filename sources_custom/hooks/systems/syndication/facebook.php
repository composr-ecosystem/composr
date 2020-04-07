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

        $appid = get_option('facebook_appid');
        $app_secret = get_option('facebook_secret_code');
        if (($appid == '') || ($app_secret == '')) {
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
        require_code('facebook_connect');

        global $FACEBOOK_CONNECT;
        if ($FACEBOOK_CONNECT === null) {
            fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_lang('facebook');

        $code = get_param_string('code', '', INPUT_FILTER_GET_COMPLEX);

        if ($code == '') {
            $scope = [];
            if ($member_id === null) {
                $scope[] = 'manage_pages';
                $scope[] = 'publish_pages';
            }

            $helper = $FACEBOOK_CONNECT->getRedirectLoginHelper();

            $oauth_redir_url = $helper->getLoginUrl($oauth_url->evaluate(), $scope);
            require_code('site2');
            redirect_exit($oauth_redir_url);
        }

        if (get_param_string('error_reason', null, INPUT_FILTER_GET_COMPLEX) !== null) { // oauth happened and ERROR!
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(get_param_string('error_reason', false, INPUT_FILTER_GET_COMPLEX))), 'warn', false, true);
            return false;
        }

        // oAuth apparently worked
        $helper = $FACEBOOK_CONNECT->getRedirectLoginHelper();
        try {
            $access_token = $helper->getAccessToken();
        } catch (Exception $e) {
            if (php_function_allowed('error_log')) {
                @error_log('Facebook returned an error: ' . $e->__toString());
            }
            $access_token = null;
        }
        if ($access_token === null) { // Actually it didn't
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(do_lang('UNKNOWN'))), 'warn', false, true);
            return false;
        }

        // Extend token
        $oauth2_client = $FACEBOOK_CONNECT->getOAuth2Client();
        if (!$access_token->isLongLived()) {
            try {
                $access_token_extended = $oauth2_client->getLongLivedAccessToken($access_token);
            } catch (Exception $e) {
                if (php_function_allowed('error_log')) {
                    @error_log('Facebook returned an error: ' . $e->__toString());
                }
                $access_token_extended = null;
            }
            if ($access_token_extended !== null) {
                $access_token = $access_token_extended;
            }
        }

        // For website, not user
        if ($member_id === null) {
            // Auto-detect facebook_uid option
            if (get_option('facebook_uid') == '') {
                require_code('config2');
                $facebook_uid = facebook_get_current_user_id($access_token);
                if ($facebook_uid !== null) {
                    set_option('facebook_uid', $facebook_uid);
                }
            }
        }

        // Save token
        $save_to = 'facebook_oauth_token';
        if ($member_id !== null) {
            $save_to .= '__' . strval($member_id);
        }
        set_value($save_to, $access_token->__toString(), true);
        $facebook_syndicate_to_page = get_param_string('facebook_syndicate_to_page', null);
        if ($facebook_syndicate_to_page !== null) {
            set_value('facebook_syndicate_to_page__' . strval($member_id), $facebook_syndicate_to_page, true);
        }

        // Take member back to page that implicitly shows their results
        if (get_page_name() != 'facebook_oauth') {
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
        require_code('facebook_connect');

        global $FACEBOOK_CONNECT;
        if ($FACEBOOK_CONNECT === null) {
            return false;
        }

        $access_token = get_value('facebook_oauth_token', null, true);
        if ($access_token === null) {
            return false;
        }

        if (get_option('facebook_uid') == '') {
            return false; // No configured target
        }

        if (($this->auth_is_set(get_member())) && (get_option('facebook_uid') == facebook_get_current_user_id($access_token))) {
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

    protected function _send($access_token, $row, $post_to_uid = 'me', $member_id = null, $silent_warn = false)
    {
        require_code('facebook_connect');

        global $FACEBOOK_CONNECT;
        if ($FACEBOOK_CONNECT === null) {
            return false;
        }

        require_lang('facebook');

        // Prepare message
        list($message) = render_activity($row, false);
        $name = $row['a_label_1'];
        require_code('character_sets');
        $name = convert_to_internal_encoding($name, get_charset(), 'utf-8');
        $link = ($row['a_page_link_1'] == '') ? '' : page_link_to_url($row['a_page_link_1'], true);
        $message = strip_html($message->evaluate());
        $message = convert_to_internal_encoding($message, get_charset(), 'utf-8');

        // Send message
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
        try {
            $ret = $FACEBOOK_CONNECT->post('/' . $post_to_uid . '/feed', $attachment, $access_token);
        } catch (Exception $e) {
            if (($member_id !== null) && (!has_interesting_post_fields()) && (running_script('index')) && (!headers_sent())) {
                $this->auth_set($member_id, get_self_url());
            }

            if (php_function_allowed('error_log')) {
                @error_log('Facebook returned an error: ' . $e->__toString());
            }

            if (!$silent_warn) {
                attach_message($e->getMessage(), 'warn', false, true);
            }
        }

        return true;
    }
}
