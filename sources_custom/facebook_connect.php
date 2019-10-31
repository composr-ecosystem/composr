<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

function init__facebook_connect()
{
    if (!class_exists('Tempcode')) {
        return;
    }

    if (!function_exists('curl_version')) {
        return;
    }

    // Initialise Facebook Connect
    require_code('facebook/facebook');

    global $FACEBOOK_CONNECT;
    $FACEBOOK_CONNECT = null;
    $appid = get_option('facebook_appid');
    $appsecret = get_option('facebook_secret_code');
    $FACEBOOK_CONNECT = new Facebook(array('appId' => $appid, 'secret' => $appsecret));
}

// This is only called if we know we have a user logged into Facebook, who has authorised to our app
function handle_facebook_connection_login($current_logged_in_member, $quick_only = false)
{
    if (!class_exists('Tempcode')) {
        return null;
    }
    if (!function_exists('require_lang')) {
        return null;
    }

    if (is_guest($current_logged_in_member)) {
        $current_logged_in_member = null;

        // We are not a normal cookie login so Composr has loaded up a Guest session already in the expectation of keeping it. Unsetting it will force a rebind (existing session may be reused though)
        require_code('users_inactive_occasionals');
        set_session_id('');
    }

    // If already session-logged-in onto a Facebook account, or a non-Facebook account (i.e. has a log in on top), don't bother doing anything
    if (($current_logged_in_member !== null) && (!is_guest($current_logged_in_member))) {
        return $current_logged_in_member;
    }

    // Who is this user, from Facebook's point of view?
    global $FACEBOOK_CONNECT;
    $facebook_uid = $FACEBOOK_CONNECT->getUser();
    if ($facebook_uid === null) {
        return $current_logged_in_member;
    }
    try {
        $details = $FACEBOOK_CONNECT->api('/me', array('fields' => 'id,name,email,about,website,currency,first_name,last_name,gender,location,hometown,picture,timezone,locale,birthday'));
    } catch (Exception $e) {
        header('Facebook-Error: ' . escape_header($e->getMessage()));

        return $current_logged_in_member;
    }
    if (!is_array($details)) {
        return $current_logged_in_member;
    }
    $details2 = $FACEBOOK_CONNECT->api('/me', array('fields' => 'picture', 'type' => 'normal'));
    if (!is_array($details2)) { // NB: This can happen even if there is a Facebook session, if the session ID in the cookie has expired. In this case Guest will be the user until the frontend does a refresh
        return $current_logged_in_member;
    }
    $details = array_merge($details, $details2);
    if (!isset($details['name'])) {
        return $current_logged_in_member;
    }
    $username = $details['name'];
    $photo_url = array_key_exists('picture', $details) ? $details['picture'] : '';
    if (is_array($photo_url)) {
        $photo_url = $photo_url['data']['url'];
    }
    if ($photo_url != '') {
        $photo_url = 'https://graph.facebook.com/' . strval($facebook_uid) . '/picture?type=large'; // In case URL changes
    }
    $avatar_url = ($photo_url == '') ? null : $photo_url;
    $photo_thumb_url = '';
    if ($photo_url != '') {
        $photo_thumb_url = $photo_url;
    }
    $email_address = array_key_exists('email', $details) ? $details['email'] : '';
    $timezone = null;
    if (isset($details['timezone'])) {
        require_code('temporal');
        $timezone = convert_timezone_offset_to_formal_timezone($details['timezone']);
    }
    $language = null;
    if (isset($details['locale'])) {
        $language = strtoupper($details['locale']);
    }
    if ($language !== null) {
        if (!does_lang_exist($language)) {
            $language = preg_replace('#_.*$#', '', $language);
            if (!does_lang_exist($language)) {
                $language = '';
            }
        }
    }
    $dob = array_key_exists('birthday', $details) ? $details['birthday'] : '';
    $dob_day = null;
    $dob_month = null;
    $dob_year = null;
    if ($dob != '') {
        $_dob = explode('/', $dob);
        if (count($_dob) == 3) {
            $dob_day = intval($_dob[1]);
            $dob_month = intval($_dob[0]);
            $dob_year = intval($_dob[2]);
        }
    }

    // See if they have logged in before - i.e. have a synched account
    $member_row = $GLOBALS['FORUM_DB']->query_select('f_members', array('*'), array('m_password_compat_scheme' => 'facebook', 'm_pass_hash_salted' => $facebook_uid), 'ORDER BY m_join_time DESC,id DESC', 1);
    $member_id = array_key_exists(0, $member_row) ? $member_row[0]['id'] : null;
    if (is_guest($member_id)) {
        $member_id = null;
    }

    /*if ($member_id !== null) { // Useful for debugging
        require_code('cns_members_action2');
        cns_delete_member($member_id);
        $member_id = null;
    }*/

    if (($member_id !== null) && ($current_logged_in_member !== null) && (!is_guest($current_logged_in_member)) && ($current_logged_in_member != $member_id)) {
        return $current_logged_in_member; // User has an active login, and the Facebook account is bound to a DIFFERENT login. Take precedence to the other login that is active on top of this
    }

    // If logged in before using Facebook, do some synching
    if ($member_id !== null) {
        $last_visit_time = $member_id[0]['m_last_visit_time'];
        if ($timezone !== null) {
            if ((!is_numeric($member_row[0]['m_timezone_offset'])) && (tz_time(time(), $timezone) == tz_time(time(), $member_row[0]['m_timezone_offset']))) {
                $timezone = $member_row[0]['m_timezone_offset']; // If equivalent, don't change
            }
        }

        $update_map = array();

        // Username
        if (get_option('facebook_sync_username') == '1') {
            $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_username' => $username));
            if ($test === null) { // Make sure there's no conflict yet the name has changed
                $update_map['m_username'] = $username;
            }
        }

        // DOB
        if (get_option('facebook_sync_dob') == '1') {
            $update_map += array('m_dob_day' => $dob_day, 'm_dob_month' => $dob_month, 'm_dob_year' => $dob_year);
        }

        // Email
        if (get_option('facebook_sync_email') == '1') {
            if ($email_address != '') {
                $update_map['m_email_address'] = $email_address;
            }
        }

        // Avatar/photos
        if (get_option('facebook_sync_avatar') == '1') {
            $test = $member_row[0]['m_avatar_url'];
            if (($avatar_url !== null) && (($test == '') || (strpos($test, 'facebook') !== false) || (strpos($test, 'fbcdn') !== false))) {
                if ($timezone !== null) {
                    $update_map['m_timezone_offset'] = $timezone;
                }
                $update_map['m_avatar_url'] = $avatar_url;
                $update_map['m_photo_url'] = $photo_url;
                $update_map['m_photo_thumb_url'] = $photo_thumb_url;
            }
        }

        // Run update
        $GLOBALS['FORUM_DB']->query_update('f_members', $update_map, array('m_password_compat_scheme' => 'facebook', 'm_pass_hash_salted' => strval($facebook_uid)), '', 1);

        // Caching
        if ((array_key_exists('m_username', $update_map)) && ($username != $member_row[0]['m_username'])) {
            require_code('cns_members_action2');
            update_member_username_caching($member_id, $username);
        }
    }

    // Not logged in before using Facebook, so we need to create an account, or bind to the active Composr login if there is one
    $in_a_sane_place = (get_page_name() != 'login') && ((running_script('index')) || (running_script('execute_temp'))) && (!$quick_only); // If we're in some weird script, or the login module UI, it's not a sane place, don't be doing account creation yet
    if (($member_id === null) && ($in_a_sane_place)) {
        // Bind to existing Composr login?
        if ($current_logged_in_member !== null) {
            /* Won't work because Facebook is currently done in JS and cookies force this. If user wishes to cancel they must go to http://www.facebook.com/settings?tab=applications and remove the app, then run a lost password reset.
            if (post_param_integer('associated_confirm', 0) == 0) {
                $title = get_screen_title('LOGIN_FACEBOOK_HEADER');
                $message = do_lang_tempcode('LOGGED_IN_SURE_FACEBOOK', escape_html($GLOBALS['FORUM_DRIVER']->get_username($current_logged_in_member)));
                $middle = do_template('CONFIRM_SCREEN', array(
                    '_GUID' => '3d80095b18cf57717d0b091cf3680252',
                    'TITLE' => $title,
                    'TEXT' => $message,
                    'HIDDEN' => form_input_hidden('associated_confirm', '1'),
                    'URL' => get_self_url_easy(),
                    'FIELDS' => '',
                ));
                $tpl = globalise($middle, null, '', true);
                $tpl->evaluate_echo();
                exit();
            }
            */

            $GLOBALS['FORUM_DB']->query_update('f_members', array('m_password_compat_scheme' => 'facebook', 'm_pass_hash_salted' => $facebook_uid), array('id' => $current_logged_in_member), '', 1);
            require_code('site');
            require_lang('facebook');
            attach_message(do_lang_tempcode('FACEBOOK_ACCOUNT_CONNECTED', escape_html(get_site_name()), escape_html($GLOBALS['FORUM_DRIVER']->get_username($current_logged_in_member)), array(escape_html($username))), 'inform');
            return $current_logged_in_member;
        }

        // If we're still here, we have to create a new account...
        // -------------------------------------------------------

        if (get_option('facebook_allow_signups') == '0') {
            require_lang('facebook');
            attach_message(do_lang_tempcode('FACEBOOK_SIGNUPS_DISABLED'), 'warn');
            return null;
        }

        $completion_form_submitted = (post_param_integer('finishing_profile', 0) == 1);

        require_code('cns_members_action2');

        // Ask Composr to finish off the profile from the information presented in the POST environment (a standard mechanism in Composr, for third party logins of various kinds)
        require_lang('cns');
        require_code('cns_members');
        require_code('cns_groups');
        require_code('cns_members2');
        require_code('cns_members_action');
        $_custom_fields = cns_get_all_custom_fields_match(
            cns_get_all_default_groups(true), // groups
            null, // public view
            null, // owner view
            null, // owner set
            null, // required
            null, // show in posts
            null, // show in post previews
            null, // special start
            true // show on join form
        );
        if ((!$completion_form_submitted) && (count($_custom_fields) != 0) && (get_option('finish_profile') == '1')) { // UI
            $GLOBALS['FACEBOOK_FINISHING_PROFILE'] = true;
            $middle = cns_member_external_linker_ask('facebook', $username, $email_address, $dob_day, $dob_month, $dob_year);
            $tpl = globalise($middle, null, '', true);
            $tpl->evaluate_echo();
            exit();
        } else { // Actualiser
            // If there's a conflicting username, we may need to change it (suffix a number)  [we don't do in code branch above, as cns_member_external_linker_ask already handles it]
            $username = get_username_from_human_name($username);

            // Check RBL's/stopforumspam
            $spam_check_level = get_option('spam_check_level');
            if (($spam_check_level == 'EVERYTHING') || ($spam_check_level == 'ACTIONS') || ($spam_check_level == 'GUESTACTIONS') || ($spam_check_level == 'JOINING')) {
                require_code('antispam');
                check_for_spam(post_param_string('username', $username), $email_address, false);
            }

            $username = post_param_string('username', $username); // User may have customised username
            if ((count($_custom_fields) != 0) && (get_value('no_finish_profile') !== '1')) { // Was not auto-generated, so needs to be checked
                cns_check_name_valid($username, null, null);
            }
            $member_id = cns_member_external_linker('facebook', $username, $facebook_uid, false, $email_address, $dob_day, $dob_month, $dob_year, $timezone, $language, $avatar_url, $photo_url, $photo_thumb_url);

            // Custom Profile Fields should be filled, as possible
            $changes = array();
            require_lang('cns_special_cpf');
            $mappings = array(
                'about' => do_lang('DEFAULT_CPF_about_NAME'),
                'website' => do_lang('DEFAULT_CPF_website_NAME'),
                'currency' => 'cms_currency',
                'first_name' => 'cms_firstname',
                'last_name' => 'cms_lastname',
                'gender' => do_lang('DEFAULT_CPF_gender_NAME'),
            );
            foreach ($mappings as $facebook_field => $composr_field_title) {
                if (!@cms_empty_safe($details[$facebook_field])) {
                    $composr_field_id = find_cms_cpf_field_id($composr_field_title);
                    if ($composr_field_id !== null) {
                        switch ($facebook_field) {
                            case 'user_currency':
                                $changes['field_' . strval($composr_field_id)] = $details[$facebook_field]['user_currency'];
                                break;

                            default:
                                if (!is_array($details[$facebook_field])) {
                                    $changes['field_' . strval($composr_field_id)] = $details[$facebook_field];
                                }
                                break;
                        }
                    }
                }
            }
            $facebook_field = 'location'; // Could also be 'hometown', but tends to get left outdated
            if (!@cms_empty_safe($details[$facebook_field])) {
                try {
                    $details3 = $FACEBOOK_CONNECT->api('/' . $details[$facebook_field], array('fields' => 'location'));

                    $mappings = array(
                        'latitude' => 'cms_latitude',
                        'longitude' => 'cms_longitude',
                        'city' => 'cms_city',
                        'state' => 'cms_state',
                        'country' => 'cms_country',
                    );
                    foreach ($mappings as $facebook_field => $composr_field_title) {
                        if (!@cms_empty_safe($details3[$facebook_field])) {
                            $composr_field_id = find_cms_cpf_field_id($composr_field_title);
                            if ($composr_field_id !== null) {
                                $changes['field_' . strval($composr_field_id)] = $details3[$facebook_field];
                            }
                        }
                    }
                } catch (Exception $e) {
                    header('Facebook-Error: ' . escape_header($e->getMessage()));
                }
            }
            if (!empty($changes)) {
                $GLOBALS['FORUM_DB']->query_update('f_member_custom_fields', $changes, array('mf_member_id' => $member_id), '', 1);
            }
        }
    }

    // Finalise the session
    if (($member_id !== null) && (!$quick_only)) {
        require_code('users_inactive_occasionals');
        create_session($member_id, 1, (isset($_COOKIE[get_member_cookie() . '_invisible'])) && ($_COOKIE[get_member_cookie() . '_invisible'] == '1')); // This will mark it as confirmed
    }

    // Store oAuth for syndication
    if (get_option('facebook_auto_syndicate') == '1') {
        if ($member_id !== null) {
            set_value('facebook_oauth_token__' . strval($member_id), $FACEBOOK_CONNECT->getAccessToken(), true);

            if (get_option('facebook_member_syndicate_to_page') == '1') {
                set_value('facebook_syndicate_to_page__' . strval($member_id), '1', true);
            }
        }
    }

    return $member_id;
}
