<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

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

    public function is_available($member_id = null)
    {
        if (!addon_installed('facebook_support')) {
            return false;
        }

        if ($member_id !== null) {
            // Facebook removed this https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24
            return false;
        }

        if (get_option('facebook_uid') == '') {
            return false; // No page/group configured
        }

        $appid = get_option('facebook_appid');
        $app_secret = get_option('facebook_secret_code');
        if (($appid == '') || ($app_secret == '')) {
            return false;
        }

        return true;
    }

    public function auth_is_set($member_id)
    {
        $save_to = 'facebook_oauth_token';
        if ($member_id !== null) {
            // Facebook removed this https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24
            return false;
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
            $scope[] = 'manage_pages';
            $scope[] = 'publish_pages';

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

        // Save token
        $save_to = 'facebook_oauth_token';
        set_value($save_to, $access_token->__toString(), true);

        return true;
    }

    public function auth_unset($member_id)
    {
        $save_to = 'facebook_oauth_token';
        set_value($save_to, null, true);
    }

    public function syndicate_user_activity($member_id, $row)
    {
        // Facebook removed this https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24
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
        $attachment = ['message' => $message];
        if (($name != '') && ($name != $message)) {
            $attachment['name'] = $name;
        }
        if ($link != '') {
            $attachment['link'] = $link;
        }
        try {
            $ret = $FACEBOOK_CONNECT->post('/' . $post_to_uid . '/feed', $attachment, $access_token);
        } catch (Exception $e) {
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
