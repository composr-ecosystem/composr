<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: json_decode|classTTConnection|member_acl*/

/**
 * Composr API helper class.
 */
class CMSAccountWrite
{
    const SIGN_IN_OKAY_TOKEN = null;
    const SIGN_IN_OKAY_REGISTER = null;
    const SIGN_IN_REGISTER_USERNAME_OCCUPIED = '1';
    const SIGN_IN_USERNAME_NEEDED_NOT_EMAIL = '2';
    const SIGN_IN_EMAIL_WRONG = '3';
    //const SIGN_IN_USERNAME_NO_EXIST='2';	Defined in Tapatalk API but cannot happen as we would register in this case
    const SIGN_IN_SSO_FAILED = '4';
    const SIGN_IN_REGISTER_NEEDS_PASSWORD = '6';
    const SIGN_IN_USERNAME_NEEDED = '7';
    const SIGN_IN_USERNAME_OR_EMAIL_NEEDED = '8';
    //const SIGN_IN_ACCOUNT_DELETED='9'; Actually we'll let it carry through
    const SIGN_IN_REGISTER_OTHER_ERROR = '10';

    /**
     * Log in via Tapatalk SSO / Join.
     *
     * @param  string $token Token to use for session
     * @param  string $code Code to use for session
     * @param  EMAIL $email E-mail address
     * @param  string $username Username
     * @param  string $password Password
     * @param  array $custom_fields Map of custom fields
     * @return array Details of login status, containing status/tapatalk_status/member_id/register[/result_text]
     */
    public function sign_in($token, $code, $email, $username, $password, $custom_fields)
    {
        cms_verify_parameters_phpdoc();

        // Find whether the member exists
        if (!empty($username)) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if (!empty($member_id)) {
                $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id);
            }
        } elseif (!empty($email)) {
            // Do we need a username?
            if (get_option('one_per_email_address') == '0') {
                return array(
                    'status' => self::SIGN_IN_USERNAME_NEEDED_NOT_EMAIL, // We can't login with e-mail, as there may be multiple accounts with the same e-mail address
                    'register' => false,
                    'member_id' => null,
                    'result_text' => do_lang('SIGN_IN_USERNAME_NEEDED_NOT_EMAIL'),
                );
            }

            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email);
            if (!is_null($member_id)) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
            }
        } else {
            // Nothing to look up with or add against even though password provided
            switch (get_option('one_per_email_address')) {
                case '0':
                    return array(
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_USERNAME_NEEDED'),
                    );

                case '1':
                    return array(
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_USERNAME_OR_EMAIL_NEEDED'),
                    );

                case '2':
                    return array(
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_EMAIL_NEEDED'),
                    );
            }
        }
        $exists = !is_null($member_id); // At this point either $exists and $username and $email is set, or !$exists

        // Do SSO
        $connection = new classTTConnection();
        $key = get_option('tapatalk_api_key');
        $test = $connection->signinVerify($token, $code, get_base_url(), $key, !$exists);
        if ($test['result']) {
            // Check email matches
            if (($email != '') && (isset($test['email'])) && ($test['email'] != $email)) {
                return array(
                    'status' => self::SIGN_IN_EMAIL_WRONG,
                    'register' => false,
                    'member_id' => $member_id, // Does let it throug though
                    'result_text' => do_lang('SIGN_IN_EMAIL_WRONG'),
                );
            }

            // SSO passed
            if ($exists) {
                require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
                $acl_object = new CMSMemberACL();
                $acl_object->set_auth($member_id);

                return array(
                    'status' => self::SIGN_IN_OKAY_TOKEN,
                    'register' => false,
                    'member_id' => $member_id,
                    'result_text' => null,
                );
            } else {
                // Register (which may pass or fail)
                return $this->join($username, $email, $password, $custom_fields, false);
            }
        }

        // SSO failed
        return array(
            'status' => self::SIGN_IN_SSO_FAILED,
            'register' => false,
            'member_id' => null,
            'result_text' => do_lang('SIGN_IN_SSO_FAILED'),
        );
    }

    /**
     * Join (old method, superseded by sign_in).
     *
     * @param  string $username Username
     * @param  string $password Password
     * @param  EMAIL $email E-mail address
     * @param  string $token Token to use for session
     * @param  string $code Code to use for session
     * @param  array $custom_fields Map of custom fields
     * @return array Details of join status, containing result[/result_text]
     */
    public function register($username, $password, $email, $token, $code, $custom_fields)
    {
        cms_verify_parameters_phpdoc();

        $result = $this->join($username, $email, $password, $custom_fields, true);

        if (is_null($result['member_id'])) {
            warn_exit($result['result_text']);
        }

        return array(
            'preview_topic_id' => $result['preview_topic_id'],
        );
    }

    /**
     * Join.
     *
     * @param  ID_TEXT $username  Username
     * @param  EMAIL $email E-mail address
     * @param  string $password Password
     * @param  array $custom_fields Map of custom fields
     * @param  boolean $confirm_if_enabled Whether we need to do an email confirm
     * @return array Details of join status, containing status/member_id/data
     */
    private function join($username, $email, $password, $custom_fields, $confirm_if_enabled)
    {
        cms_verify_parameters_phpdoc();

        // Do we have password for a registration?
        if (is_null($password)) {
            return array(
                'status' => self::SIGN_IN_REGISTER_NEEDS_PASSWORD,
                'register' => false,
                'member_id' => null,
                'result_text' => do_lang('SIGN_IN_REGISTER_NEEDS_PASSWORD'),
            );
        }

        if (!is_null($GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_username' => $username)))) {
            return array(
                'status' => self::SIGN_IN_REGISTER_USERNAME_OCCUPIED,
                'register' => false,
                'member_id' => null,
                'result_text' => do_lang('SIGN_IN_REGISTER_USERNAME_OCCUPIED'),
                'preview_topic_id' => null,
            );
        }

        require_code('cns_join');
        cns_require_all_forum_stuff();
        list($message, $member_id) = cns_join_actual(/*$captcha_if_enabled=*/
            false,/*$intro_message_if_enabled=*/
            false,/*$invites_if_enabled=*/
            true,/*$one_per_email_address_if_enabled=*/
            true,/*$confirm_if_enabled=*/
            $confirm_if_enabled,/*$validate_if_enabled=*/
            true,/*$coppa_if_enabled=*/
            false,/*$instant_login=*/
            false, $username, $email, $password, $custom_fields);

        if (is_null($member_id)) {
            return array(
                'status' => self::SIGN_IN_REGISTER_OTHER_ERROR,
                'register' => false,
                'member_id' => null,
                'result_text' => strip_html($message->evaluate()),
                'preview_topic_id' => null,
            );
        }

        $preview_topic_id = get_option('rules_topic_id');

        require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
        $acl_object = new CMSMemberACL();
        $acl_object->set_auth($member_id);

        return array(
            'status' => self::SIGN_IN_OKAY_REGISTER,
            'register' => true,
            'member_id' => $member_id,
            'result_text' => null,
            'preview_topic_id' => empty($preview_topic_id) ? null : intval($preview_topic_id),
        );
    }

    /**
     * Initiate lost password process.
     *
     * @param  string $username Username
     * @param  string $token Session token
     * @param  string $code Session code
     * @return array Details of result status, containing result/verified[/result_text]
     */
    public function forget_password($username, $token, $code)
    {
        cms_verify_parameters_phpdoc();

        if (!empty($code)) {
            // Can we do it via verification?
            $verified = false;
            $key = get_option('tapatalk_api_key');
            if ($key != '') {
                $boardurl = get_base_url();
                $verification_url = 'http://directory.tapatalk.com/au_reg_verify.php?token=' . urlencode($token) . '&code=' . urlencode($code) . '&key=' . urlencode($key) . '&url=' . urlencode($boardurl);
                $response = http_download_file($verification_url, null, false);
                if (!empty($response)) {
                    $result = json_decode($response, true);
                    $verified = $result['result'];
                }
                if ($verified) {
                    return array(
                        'result' => true,
                        'verified' => $verified,
                    );
                }
            }
        }

        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        if (($member_id !== null) && (!is_guest($member_id))) {
            // Has to go through full process...

            $result = $this->lost_password($member_id);

            if (!$result['status']) {
                return array(
                    'result' => false,
                    'result_text' => $result['data'],
                    'verified' => false,
                );
            }

            return array(
                'result' => true,
                'result_text' => $result['data'],
                'verified' => false,
            );
        }

        return array(
            'result' => false,
            'result_text' => do_lang('MEMBER_NO_EXIST'),
            'verified' => false,
        );
    }

    /**
     * Initiate lost password process (helper method).
     *
     * @param  MEMBER $member Member
     * @return array Details of result status, containing status/data
     */
    private function lost_password($member)
    {
        cms_verify_parameters_phpdoc();

        cns_require_all_forum_stuff();

        $username = $GLOBALS['FORUM_DRIVER']->get_username($member);
        $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member);

        // Basic validation
        if ($username == '') {
            return array(
                'status' => false,
                'data' => do_lang('PASSWORD_RESET_ERROR'),
            );
        }
        if ($email == '') {
            return array(
                'status' => false,
                'data' => do_lang('MEMBER_NO_EMAIL_ADDRESS_RESET_TO'),
            );
        }

        // Check we are allowed to do a reset
        if (($GLOBALS['FORUM_DRIVER']->get_member_row_field($member, 'm_password_compat_scheme') == '') && (has_privilege($member, 'disable_lost_passwords'))) {
            return array(
                'status' => false,
                'data' => do_lang('NO_RESET_ACCESS'),
            );
        }
        if ($GLOBALS['FORUM_DRIVER']->get_member_row_field($member, 'm_password_compat_scheme') == 'httpauth') {
            return array(
                'status' => false,
                'data' => do_lang('NO_PASSWORD_RESET_HTTPAUTH'),
            );
        }
        $is_ldap = cns_is_ldap_member($member);
        $is_httpauth = cns_is_httpauth_member($member);
        if (($is_ldap)/* || ($is_httpauth  Actually covered more explicitly above - over mock-httpauth, like Facebook, may have passwords reset to break the integrations)*/) {
            warn_exit(do_lang_tempcode('EXT_NO_PASSWORD_CHANGE'));
        }

        // Start the reset process by generating a reset code
        $code = mt_rand(0, mt_getrandmax());
        $GLOBALS['FORUM_DB']->query_update('f_members', array('m_password_change_code' => strval($code)), array('id' => $member), '', 1);
        log_it('LOST_PASSWORD', strval($member), strval($code));

        // Send confirm mail
        $zone = get_module_zone('lost_password');
        $_url = build_url(array('page' => 'lost_password', 'type' => 'step3', 'code' => $code, 'member' => $member), $zone, null, false, false, true);
        $url = $_url->evaluate();
        $_url_simple = build_url(array('page' => 'lost_password', 'type' => 'step3', 'code' => null, 'username' => null, 'member' => null), $zone, null, false, false, true);
        $url_simple = $_url_simple->evaluate();
        $message = do_lang('LOST_PASSWORD_TEXT', comcode_escape(get_site_name()), comcode_escape($username), array(comcode_escape($url), $url_simple, strval($member), strval($code)), get_lang($member));
        require_code('mail');
        mail_wrap(do_lang('LOST_PASSWORD', null, null, null, get_lang($member)), $message, array($email), $username, '', '', 3, null, false, null, false, false, false, 'MAIL', true);

        // Return
        return array(
            'status' => true,
            'data' => do_lang('RESET_CODE_MAILED'),
        );
    }

    /**
     * Update member password.
     *
     * @param  string $old_password Old password
     * @param  string $new_password New password
     */
    public function update_password__old_to_new($old_password, $new_password)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member());

        // Check old password
        require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
        $acl_object = new CMSMemberACL();
        $member_id = $acl_object->authenticate_credentials_and_set_auth($username, $old_password);
        if (is_null($member_id)) {
            warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
        }

        $this->update_member_password($member_id, $new_password);
    }

    /**
     * Update member password.
     *
     * @param  string $new_password New password
     * @param  string $token Session token
     * @param  string $code Session code
     */
    public function update_password__for_session($new_password, $token, $code)
    {
        cms_verify_parameters_phpdoc();

        $key = get_option('tapatalk_api_key');

        $connection = new classTTConnection();
        $test = $connection->TTVerify($token, $code, get_base_url(), $key);
        if ($test['verified']) {
            $email_address = $test['TTEmail'];
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email_address);
            if (is_null($member_id)) {
                warn_exit(do_lang_tempcode('MEMBER_NO_EXIST'));
            }

            $this->update_member_password($member_id, $new_password);
        } else {
            warn_exit('Could not verify request with Tapatalk');
        }
    }

    /**
     * Update member password (helper method).
     *
     * @param  MEMBER $member_id Member ID
     * @param  string $password Password
     */
    private function update_member_password($member_id, $password)
    {
        $ip_address = get_ip_address();
        $salt = '';
        $password_compatibility_scheme = '';

        if ((get_value('no_password_hashing') === '1')) {
            $password_compatibility_scheme = 'plain';
            $salt = '';
        }

        if (($salt == '') && ($password_compatibility_scheme == '')) {
            require_code('crypt');
            $salt = produce_salt();
            $password_salted = ratchet_hash($password, $salt);
        } else {
            $password_salted = $password;
        }

        $map = array(
            'm_pass_salt' => $salt,
            'm_pass_hash_salted' => $password_salted,
            'm_ip_address' => $ip_address,
        );
        $GLOBALS['FORUM_DB']->query_update('f_members', $map, array('id' => $member_id), '', 1);
    }

    /**
     * Update e-mail address.
     *
     * @param  string $password Password
     * @param  EMAIL $new_email E-mail address
     */
    public function update_email($password, $new_email)
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member());

        // Check old password
        require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
        $acl_object = new CMSMemberACL();
        $member_id = $acl_object->authenticate_credentials_and_set_auth($username, $password);
        if (is_null($member_id)) {
            warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
        }

        $map = array(
            'm_email_address' => $new_email,
            'm_ip_address' => get_ip_address(),
        );
        $GLOBALS['FORUM_DB']->query_update('f_members', $map, array('id' => get_member()), '', 1);
    }
}
