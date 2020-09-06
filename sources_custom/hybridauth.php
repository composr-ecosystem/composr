<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

function initiate_hybridauth()
{
    $providers = enumerate_hybridauth_providers();

    $_providers = [];
    foreach ($providers as $provider => $info) {
        $_providers[$provider] = [
            'enabled' => true,
            'keys' => $info['keys'],
        ] + $info['other_parameters'];
    }

    $keep = symbol_tempcode('KEEP');

    $config = [
        'providers' => $_providers,
        'callback' => find_script('hybridauth'),
    ];

    require_code('hybridauth/autoload');

    return new Hybridauth\Hybridauth($config);
}

function is_hybridauth_special_type($special_type)
{
    return array_key_exists($special_type, enumerate_hybridauth_providers());
}

function enumerate_hybridauth_providers()
{
    static $providers = null;
    if ($providers !== null) {
        return $providers;
    }

    $providers = [];

    // Retrieve expanded info Composr provides
    $provider_expanded_info = [];
    $hooks = find_all_hook_obs('systems', 'hybridauth', 'Hook_hybridauth_');
    foreach ($hooks as $hook_ob) {
        $provider_expanded_info += $hook_ob->info();
    }

    // Retrieve Hybridauth's list of providers and set up a skeleton structure of info for them
    require_code('files2');
    $files = get_directory_contents(get_file_base() . '/sources_custom/hybridauth/Provider', '', IGNORE_ACCESS_CONTROLLERS, false, true, ['php']);
    sort($files);
    foreach ($files as $i => $file) {
        $provider = basename($file, '.php');
        $providers[$provider] = [
            'enabled' => null,

            'label' => $provider,

            // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
            'prominent_button' => false, // Basically if it shows in login blocks (as opposed to just the full login screen)
            'button_precedence' => 30 + $i, // 1=most prominent, 100=least prominent

            'background_colour' => '000000',
            'text_colour' => 'FFFFFF',
            'icon' => null,

            'keys' => [
            ],

            'other_parameters' => [
            ],
        ];
        if (isset($provider_expanded_info[$provider])) {
            $providers[$provider] = $provider_expanded_info[$provider] + $providers[$provider];
        }
    }

    // This is a bit of a FUDGE, but we need to know a list of all possible parameters APIs may need, to load from hidden options
    $possible_key_parameters = [
        'id',
        'secret',
        'key',
        'team_id',
        'id',
        'content',
        'key_file',
    ];
    $possible_other_parameters = [
        'site',
        'api_key',
        'tenant',
        'scope',
    ];

    // Expand any holes in the skeleton structure with defaults
    foreach ($providers as $provider => &$info) {
        foreach ($possible_key_parameters as $i => $parameter) {
            $value = get_value('hybridauth_' . $provider . '_key_' . $parameter, empty($info['keys'][$parameter]) ? '' : $info['keys'][$parameter]);
            if (!empty($value)) {
                $info['keys'][$parameter] = $value;
            } else {
                unset($info['keys'][$i]);
            }
        }

        $enabled = $info['enabled'];
        if ($enabled === null) {
            $enabled = !empty($info['keys']);
        }
        if ($enabled === false) {
            unset($providers[$provider]);
            continue;
        }
        unset($info['enabled']);

        foreach ($possible_other_parameters as $i => $parameter) {
            $value = get_value('hybridauth_' . $provider . '_' . $parameter, empty($info['other_parameters'][$parameter]) ? '' : $info['other_parameters'][$parameter]);
            if (!empty($value)) {
                $info['other_parameters'][$parameter] = $value;
            } else {
                unset($info['other_parameters'][$i]);
            }
        }

        $value = get_value('hybridauth_' . $provider . '_' . 'label', null);
        if ($value !== null) {
            $info['label'] = $value;
        }

        $value = get_value('hybridauth_' . $provider . '_' . 'prominent_button', null);
        if ($value !== null) {
            $info['prominent_button'] = ($value == '1');
        }
        $value = get_value('hybridauth_' . $provider . '_' . 'button_precedence', null);
        if ($value !== null) {
            $info['button_precedence'] = intval($value);
        }

        $value = get_value('hybridauth_' . $provider . '_' . 'background_colour', null);
        if ($value !== null) {
            $info['background_colour'] = ltrim($value, '#');
        }
        $value = get_value('hybridauth_' . $provider . '_' . 'text_colour', null);
        if ($value !== null) {
            $info['text_colour'] = ltrim($value, '#');
        }
        $value = get_value('hybridauth_' . $provider . '_' . 'icon', null);
        if ($value === null) {
            if ($info['icon'] === null) {
                if (is_file(get_file_base() . '/themes/default/images/icons/links/' . cms_strtolower_ascii($provider) . '.svg')) {
                    $value = 'links/' . cms_strtolower_ascii($provider);
                } elseif (is_file(get_file_base() . '/themes/default/images_custom/icons/links/' . cms_strtolower_ascii($provider) . '.svg')) {
                    $value = 'links/' . cms_strtolower_ascii($provider);
                } else {
                    $value = 'menu/site_meta/user_actions/login';
                }
                $info['icon'] = $value;
            }
        } else {
            $info['icon'] = $value;
        }
    }

    sort_maps_by($providers, 'button_precedence');

    return $providers;
}

function hybridauth_handle_authenticated_account($provider, $userProfile)
{
    require_lang('cns');
    require_code('cns_members');
    require_code('cns_groups');
    require_code('cns_members2');
    require_code('cns_members_action');
    require_code('cns_members_action2');

    // Get basic details from Hybridauth's $userProfile...

    $id = $userProfile->identifier;

    $email_address = $userProfile->email;
    if (empty($email_address)) {
        $email_address = $userProfile->emailVerified;
    }
    if (get_option('one_per_email_address') == '1') {
        if (empty($email_address)) {
            warn_exit(do_lang_tempcode('HYBRIDAUTH_NO_EMAIL_ADDRESS', escape_html($provider)));
        }

        $existing_scheme = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_password_compat_scheme', ['m_email_address' => $email_address]);
        if (($existing_scheme !== null) && ($existing_scheme !== $provider)) {
            if (is_hybridauth_special_type($existing_scheme)) {
                warn_exit(do_lang_tempcode('HYBRIDAUTH_CONFLICTING_ACCOUNT_PROVIDER', escape_html($provider), escape_html($existing_scheme), escape_html($email_address)));
            }
            warn_exit(do_lang_tempcode('HYBRIDAUTH_CONFLICTING_ACCOUNT_NATIVE', escape_html($provider), escape_html($email_address)));
        }
    }

    $username = preg_replace('/#.*$/', '', $userProfile->displayName);
    if (empty($username)) {
        $username = trim($userProfile->firstName . ' ' . $userProfile->lastName);
    }
    if (empty($username)) {
        warn_exit(do_lang_tempcode('HYBRIDAUTH_NO_USERNAME', escape_html($provider)));
    }

    $photo_url = ($userProfile->photoURL === null) ? '' : $userProfile->photoURL;

    $language = cms_strtoupper_ascii(preg_replace('#^([^_\-]*).*$#', '\1', $userProfile->language));
    if (empty($language)) {
        $language = null;
    }

    $dob_day = $userProfile->birthDay;
    $dob_month = $userProfile->birthMonth;
    $dob_year = $userProfile->birthYear;

    // And some things that may go into CPFs...

    $profile_url = $userProfile->profileURL;

    $about = $userProfile->description;

    $gender = cms_ucfirst_ascii($userProfile->gender);

    $firstname = $userProfile->firstName;
    $lastname = $userProfile->lastName;

    $website = $userProfile->webSiteURL;

    $mobile_phone_number = $userProfile->phone;

    $street_address = $userProfile->address;
    $city = $userProfile->city;
    $state = $userProfile->region;
    $post_code = $userProfile->zip;
    $country = $userProfile->country;

    // We need to undo any kind of existing session, as we will be starting from a clean slate now
    require_code('users_inactive_occasionals');
    set_session_id('');

    // See if they have logged in before - i.e. have a synched account
    $member_rows = $GLOBALS['FORUM_DB']->query_select('f_members', ['*'], ['m_password_compat_scheme' => $provider, 'm_pass_hash_salted' => $id], 'ORDER BY m_join_time DESC,id DESC', 1);
    if (array_key_exists(0, $member_rows)) {
        $member_row = $member_rows[0];
        if (is_guest($member_row['id'])) {
            $member_row = null;
        }
    } else {
        $member_id = null;
        $member_row = null;
    }

    /*if ($member_id !== null) { // Useful for debugging
        require_code('cns_members_action2');
        cns_delete_member($member_id);
        $member_id = null;
    }*/

    // Save the data
    if ($member_row === null) {
        $is_new_account = true;
        $member_id = hybridauth_create_authenticated_account($provider, $id, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year);
    } else {
        $is_new_account = false;
        $member_id = hybridauth_update_authenticated_account($provider, $id, $member_row, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year);
    }
    hybridauth_set_cpfs($provider, $is_new_account, $member_id, $profile_url, $about, $gender, $firstname, $lastname, $website, $mobile_phone_number, $street_address, $city, $state, $post_code, $country);

    // Set log in
    hybridauth_log_in_authenticated_account($member_id);
}

function hybridauth_create_authenticated_account($provider, $id, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year)
{
    $completion_form_submitted = (post_param_integer('finishing_profile', 0) == 1);

    // Ask Composr to finish off the profile from the information presented in the POST environment (a standard mechanism in Composr, for third party logins of various kinds)
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
    if ((!$completion_form_submitted) && (!empty($_custom_fields)) && (get_option('finish_profile') == '1')) { // UI
        $middle = cns_member_external_linker_ask($provider, $username, $email_address, $dob_day, $dob_month, $dob_year);
        $tpl = globalise($middle, null, '', true);
        $tpl->evaluate_echo();
        exit();
    }

    // Actualiser...

    // If there's a conflicting username, we may need to change it (suffix a number)  [we don't do in code branch above, as cns_member_external_linker_ask already handles it]
    $username = get_username_from_human_name($username);

    // Check RBL's/stopforumspam
    $spam_check_level = get_option('spam_check_level');
    if (($spam_check_level == 'EVERYTHING') || ($spam_check_level == 'ACTIONS') || ($spam_check_level == 'GUESTACTIONS') || ($spam_check_level == 'JOINING')) {
        require_code('antispam');
        check_for_spam(post_param_string('username', $username), $email_address, false);
    }

    $username = post_param_string('username', $username); // User may have customised username
    if (!empty($_custom_fields)) { // Was not auto-generated, so needs to be checked
        cns_check_name_valid($username);
    }
    $member_id = cns_member_external_linker($provider, $username, $id, false, $email_address, $dob_day, $dob_month, $dob_year, null, $language, $photo_url, $photo_url, '');

    return $member_id;
}

function hybridauth_update_authenticated_account($provider, $id, $member_row, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year)
{
    $member_id = $member_row['id'];

    $update_map = [];

    // Email
    if (get_option('hybridauth_sync_email') == '1') {
        if (!empty($email_address)) {
            $update_map['m_email_address'] = $email_address;
        }
    }

    // Username
    if (get_option('hybridauth_sync_username') == '1') {
        $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', ['m_username' => $username]);
        if ($test === null) { // Make sure there's no conflict yet the name has changed
            $update_map['m_username'] = $username;
        }
    }

    // Avatar/photos
    if (get_option('hybridauth_sync_avatar') == '1') {
        if (!empty($photo_url)) {
            $test = $member_row['m_avatar_url'];
            if (($test == '') || (!url_is_local($test)) || (substr($test, 0, strlen(get_custom_base_url()) + 1) != get_custom_base_url() . '/')) {
                $update_map['m_avatar_url'] = $photo_url;
            }

            $test = $member_row['m_photo_url'];
            if (($test == '') || (!url_is_local($test)) || (substr($test, 0, strlen(get_custom_base_url()) + 1) != get_custom_base_url() . '/')) {
                $update_map['m_photo_url'] = $photo_url;
                $update_map['m_photo_thumb_url'] = '';
            }
        }
    }

    // Language
    if ((!empty($language)) && (does_lang_exist($language)) && ($member_row['m_language'] == '')) {
        $update_map += ['m_language' => $language];
    }

    // DOB
    if (($dob_day !== null) && ($dob_month !== null) && ($dob_year !== null) && ($member_row['m_dob_day'] === null)) {
        $update_map += ['m_dob_day' => $dob_day, 'm_dob_month' => $dob_month, 'm_dob_year' => $dob_year];
    }

    // Run update
    $GLOBALS['FORUM_DB']->query_update('f_members', $update_map, ['m_password_compat_scheme' => $provider, 'm_pass_hash_salted' => $id], '', 1);

    // Caching
    if ((array_key_exists('m_username', $update_map)) && ($username != $member_row['m_username'])) {
        require_code('cns_members_action2');
        update_member_username_caching($member_id, $username);
    }

    return $member_id;
}

function hybridauth_set_cpfs($provider, $is_new_account, $member_id, $profile_url, $about, $gender, $firstname, $lastname, $website, $mobile_phone_number, $street_address, $city, $state, $post_code, $country)
{
    $current = cns_get_custom_field_mappings($member_id);

    require_lang('cns_special_cpf');

    $mappings = [
        [find_cms_cpf_field_id(do_lang('DEFAULT_CPF_about_NAME')), $about],
        [find_cms_cpf_field_id(do_lang('DEFAULT_CPF_gender_NAME')), $gender],
        [find_cms_cpf_field_id('cms_firstname'), $firstname],
        [find_cms_cpf_field_id('cms_lastname'), $lastname],
        [find_cms_cpf_field_id(do_lang('DEFAULT_CPF_website_NAME')), $website],
        [find_cms_cpf_field_id('cms_mobile_phone_number'), $mobile_phone_number],
        [find_cms_cpf_field_id('cms_street_address'), $street_address],
        [find_cms_cpf_field_id('cms_city'), $city],
        [find_cms_cpf_field_id('cms_state'), $state],
        [find_cms_cpf_field_id('cms_post_code'), $post_code],
        [find_cms_cpf_field_id('cms_country'), $country],
    ];

    if (!empty($profile_url)) {
        // Try and find a CPF to write $profile_url into

        $composr_field_id = find_cms_cpf_field_id('cms_' . cms_strtolower_ascii($provider));
        if ($composr_field_id === null) {
            $composr_field_id = find_cms_cpf_field_id('cms_' . cms_strtolower_ascii($provider));
        }
        if ($composr_field_id === null) {
            $composr_field_id = find_cms_cpf_field_id('cms_im_' . cms_strtolower_ascii($provider));
        }
        if ($composr_field_id === null) {
            $composr_field_id = find_cms_cpf_field_id('cms_sn_' . cms_strtolower_ascii($provider));
        }
        $mappings[] = [$composr_field_id, $profile_url];
    }

    $changes = [];
    foreach ($mappings as $mapping) {
        list($composr_field_id, $value) = $mapping;

        if ($composr_field_id !== null) {
            if ((!empty($value)) && (($is_new_account) || ($current['field_' . strval($composr_field_id)] == ''))) {
                $changes['field_' . strval($composr_field_id)] = $value;
            }
        }
    }

    if (!empty($changes)) {
        $GLOBALS['FORUM_DB']->query_update('f_member_custom_fields', $changes, ['mf_member_id' => $member_id], '', 1);
    }
}

function hybridauth_log_in_authenticated_account($member_id)
{
    // Finalise the session
    require_code('users_inactive_occasionals');
    create_session($member_id, 1, (isset($_COOKIE[get_member_cookie() . '_invisible'])) && ($_COOKIE[get_member_cookie() . '_invisible'] == '1')); // This will mark it as confirmed
}
