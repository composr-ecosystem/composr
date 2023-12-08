<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    openid
 */

/**
 * Hook class.
 */
class Hook_login_provider_openid
{
    /**
     * Standard login provider hook.
     *
     * @param  ?MEMBER $member Member ID already detected as logged in (null: none). May be a guest ID.
     * @param  boolean $quick_only Whether to just do a quick check, don't establish new sessions
     * @return ?MEMBER Member ID now detected as logged in (null: none). May be a guest ID.
     */
    public function try_login($member, $quick_only = false)
    {
        // Some kind of OpenID provider
        try {
            require_code('openid');
            require_code('developer_tools');

            if (!isset($_REQUEST['openid_mode'])) {
                if (array_key_exists('openid_identifier', $_POST)) {
                    destrictify();

                    $openid = new LightOpenID;
                    $openid->identity = $_POST['openid_identifier'];
                    $openid->required = array(
                        'namePerson/friendly',
                        'namePerson',
                        'contact/email',
                        'birthDate',
                        'pref/language',
                        'media/image/default',
                    );
                    require_code('site2');
                    smart_redirect($openid->authUrl());
                    exit();
                }
            } elseif ($_GET['openid_mode'] == 'cancel') {
                destrictify();

                require_code('site');
                require_code('site2');
                attach_message('You cancelled your OpenID login, so you are not logged into the site.', 'inform');
            } else {
                destrictify();

                $openid = new LightOpenID();

                if ($openid->validate()) {
                    $attributes = $openid->getAttributes();

                    // If member already existed, no action needed - just create a session to existing record
                    $member = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_password_compat_scheme' => 'openid', 'm_pass_hash_salted' => $openid->identity));
                    if (!is_null($member)) {
                        require_code('users_inactive_occasionals');

                        if (!$quick_only) {
                            create_session($member, 1, (isset($_COOKIE[get_member_cookie() . '_invisible'])) && ($_COOKIE[get_member_cookie() . '_invisible'] == '1')); // This will mark it as confirmed
                        }

                        return $member;
                    }

                    require_code('cns_members');
                    require_code('cns_groups');
                    require_lang('cns');

                    if (((running_script('index')) || (running_script('execute_temp'))) && (!$quick_only)) {
                        require_code('cns_members_action');
                        require_code('cns_members_action2');

                        $email = '';
                        if (array_key_exists('contact/email', $attributes)) {
                            $email = $attributes['contact/email'];
                        }

                        $username = $openid->identity; // Yuck, we'll try and build on this
                        if (array_key_exists('namePerson/friendly', $attributes)) {
                            $username = $attributes['namePerson/friendly'];
                        } elseif (array_key_exists('namePerson', $attributes)) {
                            $username = $attributes['namePerson'];
                        } elseif ($email != '') {
                            $username = substr($email, 0, strpos($email, '@'));
                        }

                        if ($username != '') {
                            $_username = $username;
                            $i = 1;
                            do {
                                $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_username' => $_username));
                                if (!is_null($test)) {
                                    $i++;
                                    $_username = $username . ' (' . strval($i) . ')';
                                }
                            } while (!is_null($test));
                            $username = $_username;
                        }

                        $dob = '';
                        if (array_key_exists('birthDate', $attributes)) {
                            $dob = $attributes['birthDate'];
                        }
                        $dob_day = mixed();
                        $dob_month = mixed();
                        $dob_year = mixed();
                        if ($dob != '') {
                            $dob_bits = explode('-', $dob);
                            $dob_day = intval($dob_bits[2]);
                            $dob_month = intval($dob_bits[1]);
                            $dob_year = intval($dob_bits[0]);
                        }

                        $language = mixed();
                        if (array_key_exists('pref/language', $attributes)) {
                            if (file_exists(get_file_base() . '/lang_custom/' . $attributes['pref/language'])) {
                                $language = $attributes['pref/language'];
                            }
                        }

                        // Check RBL's/stopforumspam
                        $spam_check_level = get_option('spam_check_level');
                        if (($spam_check_level == 'EVERYTHING') || ($spam_check_level == 'ACTIONS') || ($spam_check_level == 'GUESTACTIONS') || ($spam_check_level == 'JOINING')) {
                            require_code('antispam');
                            check_rbls();
                            check_stopforumspam($username);
                        }

                        // Actually add member
                        require_code('config2');
                        set_option('maximum_password_length', '255');
                        $member = cns_member_external_linker($username, $openid->identity, 'openid', false, $email, $dob_day, $dob_month, $dob_year, null, $language);

                        $avatar = '';
                        if (array_key_exists('media/image/default', $attributes)) {
                            $avatar = $attributes['media/image/default'];
                        }
                        cns_member_choose_avatar($avatar, $member);
                    }

                    if ((!is_null($member)) && (!$quick_only)) {
                        require_code('users_inactive_occasionals');
                        create_session($member, 1, (isset($_COOKIE[get_member_cookie() . '_invisible'])) && ($_COOKIE[get_member_cookie() . '_invisible'] == '1')); // This will mark it as confirmed
                    }
                } else {
                    require_code('site');
                    require_code('site2');
                    attach_message('An unknown error occurred during OpenID login.', 'warn');
                }
            }
        } catch (ErrorException $e) {
            require_code('site');
            require_code('site2');
            attach_message($e->getMessage(), 'warn');
        }

        return $member;
    }
}
