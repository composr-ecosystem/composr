<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: classTTConnection|member_acl*/

/**
 * Composr API helper class.
 */
class CMSAccountWrite
{
    protected const SIGN_IN_OKAY_TOKEN = null;
    protected const SIGN_IN_OKAY_REGISTER = null;
    protected const SIGN_IN_REGISTER_USERNAME_OCCUPIED = '1';
    protected const SIGN_IN_USERNAME_NEEDED_NOT_EMAIL = '2';
    protected const SIGN_IN_EMAIL_WRONG = '3';
    //protected const SIGN_IN_USERNAME_NO_EXIST='2'; Defined in Tapatalk API but cannot happen as we would register in this case
    protected const SIGN_IN_SSO_FAILED = '4';
    protected const SIGN_IN_REGISTER_NEEDS_PASSWORD = '6';
    protected const SIGN_IN_USERNAME_NEEDED = '7';
    protected const SIGN_IN_USERNAME_OR_EMAIL_NEEDED = '8';
    //protected const SIGN_IN_ACCOUNT_DELETED='9'; Actually we'll let it carry through
    protected const SIGN_IN_REGISTER_OTHER_ERROR = '10';

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
    public function sign_in(string $token, string $code, string $email, string $username, string $password, array $custom_fields) : array
    {
        cms_verify_parameters_phpdoc();

        // Find whether the member exists
        if (!empty($username)) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if (!cms_empty_safe($member_id)) {
                $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id);
            }
        } elseif (!empty($email)) {
            // Do we need a username?
            if (get_option('one_per_email_address') == '0') {
                return [
                    'status' => self::SIGN_IN_USERNAME_NEEDED_NOT_EMAIL, // We can't login with e-mail, as there may be multiple accounts with the same e-mail address
                    'register' => false,
                    'member_id' => null,
                    'result_text' => do_lang('SIGN_IN_USERNAME_NEEDED_NOT_EMAIL'),
                ];
            }

            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email);
            if ($member_id !== null) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
            }
        } else {
            // Nothing to look up with or add against even though password provided
            switch (get_option('one_per_email_address')) {
                case '0':
                    return [
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_USERNAME_NEEDED'),
                    ];

                case '1':
                    return [
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_USERNAME_OR_EMAIL_NEEDED'),
                    ];

                case '2':
                    return [
                        'status' => self::SIGN_IN_USERNAME_NEEDED,
                        'register' => false,
                        'member_id' => null,
                        'result_text' => do_lang('SIGN_IN_EMAIL_NEEDED'),
                    ];
            }
        }
        $exists = $member_id !== null; // At this point either $exists and $username and $email is set, or !$exists

        // Do SSO
        $connection = new classTTConnection();
        $key = get_option('tapatalk_api_key');
        $test = $connection->signinVerify($token, $code, get_base_url(), $key, !$exists);
        if ($test['result']) {
            // Check email matches
            if (($email != '') && (isset($test['email'])) && ($test['email'] != $email)) {
                return [
                    'status' => self::SIGN_IN_EMAIL_WRONG,
                    'register' => false,
                    'member_id' => $member_id, // Does let it throug though
                    'result_text' => do_lang('SIGN_IN_EMAIL_WRONG'),
                ];
            }

            // SSO passed
            if ($exists) {
                require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
                $acl_object = new CMSMemberACL();
                $acl_object->set_auth($member_id);

                return [
                    'status' => self::SIGN_IN_OKAY_TOKEN,
                    'register' => false,
                    'member_id' => $member_id,
                    'result_text' => null,
                ];
            } else {
                // Register (which may pass or fail)
                return $this->join($username, $email, $password, $custom_fields, false);
            }
        }

        // SSO failed
        return [
            'status' => self::SIGN_IN_SSO_FAILED,
            'register' => false,
            'member_id' => null,
            'result_text' => do_lang('SIGN_IN_SSO_FAILED'),
        ];
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
    public function register(string $username, string $password, string $email, string $token, string $code, array $custom_fields) : array
    {
        cms_verify_parameters_phpdoc();

        $result = $this->join($username, $email, $password, $custom_fields, true);

        if ($result['member_id'] === null) {
            warn_exit($result['result_text']);
        }

        return [
            'preview_topic_id' => $result['preview_topic_id'],
        ];
    }

    /**
     * Join.
     *
     * @param  ID_TEXT $username Username
     * @param  EMAIL $email E-mail address
     * @param  string $password Password
     * @param  array $custom_fields Map of custom fields
     * @param  boolean $confirm_if_enabled Whether we need to do an e-mail confirm
     * @return array Details of join status, containing status/member_id/data
     */
    private function join(string $username, string $email, string $password, array $custom_fields, bool $confirm_if_enabled) : array
    {
        cms_verify_parameters_phpdoc();

        // Do we have password for a registration?
        if ($password === null) {
            return [
                'status' => self::SIGN_IN_REGISTER_NEEDS_PASSWORD,
                'register' => false,
                'member_id' => null,
                'result_text' => do_lang('SIGN_IN_REGISTER_NEEDS_PASSWORD'),
            ];
        }

        if ($GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', ['m_username' => $username]) !== null) {
            return [
                'status' => self::SIGN_IN_REGISTER_USERNAME_OCCUPIED,
                'register' => false,
                'member_id' => null,
                'result_text' => do_lang('SIGN_IN_REGISTER_USERNAME_OCCUPIED'),
                'preview_topic_id' => null,
            ];
        }

        require_code('cns_join');
        cns_require_all_forum_stuff();
        list($message, $member_id) = cns_join_actual(
            false,
            false,
            true,
            false,
            $username,
            $email,
            $password,
            $custom_fields,
            ['email_confirm_join' => $confirm_if_enabled ? '2' : '0', 'is_on_coppa' => '0']
        );

        if ($member_id === null) {
            return [
                'status' => self::SIGN_IN_REGISTER_OTHER_ERROR,
                'register' => false,
                'member_id' => null,
                'result_text' => strip_html($message->evaluate()),
                'preview_topic_id' => null,
            ];
        }

        $preview_topic_id = get_option('rules_topic_id');

        require_once(COMMON_CLASS_PATH_ACL . '/member_acl.php');
        $acl_object = new CMSMemberACL();
        $acl_object->set_auth($member_id);

        return [
            'status' => self::SIGN_IN_OKAY_REGISTER,
            'register' => true,
            'member_id' => $member_id,
            'result_text' => null,
            'preview_topic_id' => cms_empty_safe($preview_topic_id) ? null : intval($preview_topic_id),
        ];
    }

    /**
     * Initiate lost password process.
     *
     * @param  string $username Username
     * @param  string $token Session token
     * @param  string $code Session code
     * @return array Details of result status, containing result/verified[/result_text]
     */
    public function forget_password(string $username, string $token, string $code) : array
    {
        cms_verify_parameters_phpdoc();

        if (!empty($code)) {
            // Can we do it via verification?
            $verified = false;
            $key = get_option('tapatalk_api_key');
            if ($key != '') {
                $boardurl = get_base_url();
                $verification_url = 'http://directory.tapatalk.com/au_reg_verify.php?token=' . urlencode($token) . '&code=' . urlencode($code) . '&key=' . urlencode($key) . '&url=' . urlencode($boardurl);
                $response = http_get_contents($verification_url, ['convert_to_internal_encoding' => true, 'trigger_error' => false]);
                if (!empty($response)) {
                    $result = json_decode($response, true);
                    $verified = $result['result'];
                }
                if ($verified) {
                    return [
                        'result' => true,
                        'verified' => $verified,
                    ];
                }
            }
        }

        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        if (($member_id !== null) && (!is_guest($member_id))) {
            // Has to go through full process...

            $result = $this->lost_password($member_id);

            if (!$result['status']) {
                return [
                    'result' => false,
                    'result_text' => $result['data'],
                    'verified' => false,
                ];
            }

            return [
                'result' => true,
                'result_text' => $result['data'],
                'verified' => false,
            ];
        }

        return [
            'result' => false,
            'result_text' => do_lang('MEMBER_NO_EXIST'),
            'verified' => false,
        ];
    }

    /**
     * Initiate lost password process (helper method).
     *
     * @param  MEMBER $member_id Member
     * @return array Details of result status, containing status/data
     */
    private function lost_password(int $member_id) : array
    {
        cms_verify_parameters_phpdoc();

        cns_require_all_forum_stuff();
        require_code('cns_lost_password');
        require_lang('cns_lost_password');

        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, false, USERNAME_DEFAULT_BLANK);
        $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id);

        // Basic validation
        if ($username == '') { // Should not be possible
            return [
                'status' => false,
                'data' => do_lang('PASSWORD_RESET_ERROR_NO_ACCOUNT_GIVEN'),
            ];
        }

        $password_reset_privacy = get_option('password_reset_privacy');

        // Check we are allowed to do a reset
        $error_msg = has_lost_password_error($member_id);
        if ($error_msg !== null) {
            switch ($password_reset_privacy) {
                case 'disclose':
                    return [
                        'status' => false,
                        'data' => $error_msg->evaluate(),
                    ];

                case 'silent':
                case 'email':
                    require_code('mail');
                    $subject = do_lang('LOST_PASSWORD_RESET_ERROR_SUBJECT', get_site_name());
                    $message = '[semihtml]' . $error_msg->evaluate() . '[/semihtml]';
                    dispatch_mail($subject, $message, [$email], null, '', '', ['bypass_queue' => true]);

                    return [$email, null];
            }
        }

        $password_reset_process = get_password_reset_process();

        // Save new code
        $code = generate_and_save_password_reset_code($password_reset_process, $member_id);

        // Logging
        log_it('LOST_PASSWORD_INITIALISE', strval($member_id), $username);

        // Send confirm mail
        send_lost_password_reset_code($password_reset_process, $member_id, $code);

        // Generate message
        $mailed_message = lost_password_mailed_message($password_reset_process, $email);

        // Return
        return [
            'status' => true,
            'data' => $mailed_message->evaluate(),
        ];
    }

    /**
     * Update member password, based on giving old password.
     *
     * @param  string $old_password Old password
     * @param  string $new_password New password
     */
    public function update_password__old_to_new(string $old_password, string $new_password)
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
        if ($member_id === null) {
            warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
        }

        $this->update_member_password($member_id, $new_password);
    }

    /**
     * Update member password, based on already being logged in.
     *
     * @param  string $new_password New password
     * @param  string $token Session token
     * @param  string $code Session code
     */
    public function update_password__for_session(string $new_password, string $token, string $code)
    {
        cms_verify_parameters_phpdoc();

        $key = get_option('tapatalk_api_key');

        $connection = new classTTConnection();
        $test = $connection->TTVerify($token, $code, get_base_url(), $key);
        if ($test['verified']) {
            $email_address = $test['TTEmail'];
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email_address);
            if ($member_id === null) {
                warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
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
    private function update_member_password(int $member_id, string $password)
    {
        $ip_address = get_ip_address();
        $salt = '';
        $password_compatibility_scheme = '';

        if ((get_value('disable_password_hashing') === '1')) {
            $password_compatibility_scheme = 'plain';
            $salt = '';
        }

        if (($salt == '') && ($password_compatibility_scheme == '')) {
            require_code('crypt');
            $salt = get_secure_random_string();
            $password_salted = ratchet_hash($password, $salt);
        } else {
            $password_salted = $password;
        }

        $map = [
            'm_pass_salt' => $salt,
            'm_pass_hash_salted' => $password_salted,
            'm_ip_address' => $ip_address,
            'm_login_key' => '',
        ];
        $GLOBALS['FORUM_DB']->query_update('f_members', $map, ['id' => $member_id], '', 1);
    }

    /**
     * Update e-mail address.
     *
     * @param  string $password Password
     * @param  EMAIL $new_email E-mail address
     */
    public function update_email(string $password, string $new_email)
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
        if ($member_id === null) {
            warn_exit(do_lang_tempcode('MEMBER_BAD_PASSWORD'));
        }

        $map = [
            'm_email_address' => $new_email,
            'm_ip_address' => get_ip_address(),
        ];
        $GLOBALS['FORUM_DB']->query_update('f_members', $map, ['id' => get_member()], '', 1);
    }
}
