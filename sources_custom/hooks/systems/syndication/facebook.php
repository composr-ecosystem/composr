<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
        if ($member_id !== null) {
            // Facebook removed this https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24
            return false;
        }

        if (get_option('facebook_uid') == '0') {
            return false; // No page/group configured
        }

        $appapikey = get_option('facebook_appid');
        $appsecret = get_option('facebook_secret_code');
        if (($appapikey == '') || ($appsecret == '')) {
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
        if ($member_id !== null) {
            // Facebook removed this https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24
            return false;
        }

        require_lang('facebook');
        require_code('facebook_connect');
        global $FACEBOOK_CONNECT;

        $code = get_param_string('code', '', true);

        if ($code == '') {
            $scope = array();
            $scope[] = 'manage_pages';
            $scope[] = 'publish_pages';
            $oauth_redir_url = $FACEBOOK_CONNECT->getLoginUrl(array('redirect_uri' => $oauth_url->evaluate(), 'scope' => $scope));

            require_code('site2');
            smart_redirect($oauth_redir_url);
        }

        if (!is_null(get_param_string('error_reason', null))) { // oauth happened and ERROR!
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(get_param_string('error_reason'))), 'warn');
            return false;
        }

        // oauth apparently worked
        $access_token = $FACEBOOK_CONNECT->getAccessToken();
        if (is_null($access_token)) { // Actually it didn't
            attach_message(do_lang_tempcode('FACEBOOK_OAUTH_FAIL', escape_html(do_lang('UNKNOWN'))), 'warn');
            return false;
        }

        // Get long lived token
        $FACEBOOK_CONNECT->setExtendedAccessToken();
        $result = $FACEBOOK_CONNECT->api('/oauth/access_token', 'POST',
            array(
                'grant_type' => 'fb_exchange_token',
                'client_id' => get_option('facebook_appid'),
                'client_secret' => get_option('facebook_secret_code'),
                'fb_exchange_token' => $access_token
            )
        );
        $access_token = $result['access_token'];

        $save_to = 'facebook_oauth_token';
        set_value($save_to, $access_token, true);

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
        $link = ($row['a_page_link_1'] == '') ? '' : static_evaluate_tempcode(page_link_to_tempcode($row['a_page_link_1']));
        $message = strip_html($message->evaluate());
        $message = convert_to_internal_encoding($message, get_charset(), 'utf-8');

        // Send message
        $appid = get_option('facebook_appid');
        $appsecret = get_option('facebook_secret_code');
        $fb = new Facebook(array('appId' => $appid, 'secret' => $appsecret));
        $fb->setAccessToken($token);

        $attachment = array('message' => $message);
        if (($name != '') && ($name != $message)) {
            $attachment['name'] = $name;
        }
        if ($link != '') {
            $attachment['link'] = $link;
        }

        if ($post_to_uid == 'me') {
            $post_to_uid = $fb->getUser(); // May not be needed, but just in case
        }

        try {
            $ret = $fb->api('/' . $post_to_uid . '/feed', 'POST', $attachment);
        } catch (Exception $e) {
            header('Facebook-Error: ' . escape_header($e->getMessage()));

            if (!$silent_warn) {
                attach_message($e->getMessage(), 'warn');
            }
        }

        return true;
    }
}
