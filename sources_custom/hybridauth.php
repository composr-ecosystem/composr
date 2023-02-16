<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/*EXTRA FUNCTIONS: session_.+*/

function init__hybridauth()
{
    require_code('hybridauth/autoload');
}

function initiate_hybridauth_session_state()
{
    @session_write_close();
    $options = [
        'name' => 'hybridauth',
        'gc_maxlifetime' => strval(60 * 60 * 24 * 365 * 2),  // 2 year server-side cookie lifetime
        'cookie_lifetime' => strval(60 * 60 * 24 * 365 * 2),  // 2 year client-side lifetime
        'cookie_httponly' => 'On',
        //'cookie_samesite' => 'Strict', // LEGACY Only works on PHP 7.3+ otherwise gives an error
    ];
    if (strpos(get_base_url(), 'https://') !== false) {
        $options['cookie_secure'] = 'On';
    }
    @session_start($options);
}

function initiate_hybridauth()
{
    $providers = enumerate_hybridauth_providers();

    global $CSP_NONCE;

    $_providers = [];
    foreach ($providers as $provider => $info) {
        $_providers[$provider] = [
            'enabled' => $info['enabled'],
            'nonce' => $CSP_NONCE,
            'keys' => $info['keys'],
        ] + $info['other_parameters'];
    }

    $config = [
        'providers' => $_providers,
        'callback' => find_script('hybridauth'),
    ];

    return new Hybridauth\Hybridauth($config);
}

function is_hybridauth_special_type($special_type)
{
    return array_key_exists($special_type, enumerate_hybridauth_providers());
}

// If $alternate_config is null then this is for login buttons
// Otherwise likely $alternate_config = 'admin' for most kinds of site-level integration
function enumerate_hybridauth_providers($alternate_config = null)
{
    static $providers_cache = [];
    if (isset($providers_cache[$alternate_config])) {
        return $providers_cache[$alternate_config];
    }

    $config_structure = [];

    // Retrieve Hybridauth's list of providers
    $all_available_providers = [];
    require_code('files2');
    $files = get_directory_contents(get_file_base() . '/sources_custom/hybridauth/Provider', '', IGNORE_ACCESS_CONTROLLERS, false, true, ['php']);
    sort($files);
    foreach ($files as $i => $file) {
        $provider = basename($file, '.php');
        $all_available_providers[] = $provider;
    }

    // Imply some data from hidden options
    foreach ($all_available_providers as $provider) {
        foreach (['id', 'secret', 'team-id', 'key-id', 'key-file', 'key-content'] as $setting) {
            $test = get_value('Hybridauth:' . $provider . ':' . $setting);
            if (!empty($test)) {
                if (!isset($config_structure[$provider])) {
                    $config_structure[$provider] = [
                        'composr-config' => [],
                        'keys-config' => [],
                        'hybridauth-config' => [],
                        'alternate_configs' => [],
                    ];
                }
                $config_structure[$provider]['keys-config'][$setting] = $test;
            }
        }
    }

    // Load up XML
    require_code('xml');
    $xml_path = get_custom_file_base() . '/data_custom/xml_config/hybridauth.xml';
    if (is_file($xml_path)) {
        $xml_contents = cms_file_get_contents_safe($xml_path, FILE_READ_LOCK | FILE_READ_BOM);
    } else {
        $xml_contents = '<hybridauth></hybridauth>';
    }
    require_code('failure');
    $te = throwing_errors();
    if (!$te) {
        set_throw_errors(true);
    }
    try {
        $parsed = new CMS_simple_xml_reader($xml_contents);
        list(, , , $root_children) = $parsed->gleamed;
    } catch (CMSException $e) {
        if (running_script('index')) {
            require_code('site');
            attach_message('Hybridauth: ' . $e->getMessage(), 'warn');
        }
        $root_children = [];
    }
    if (!$te) {
        set_throw_errors(false);
    }

    // Go over XML data
    foreach ($root_children as $root_child) {
        list($provider, , , $children) = $root_child;

        if ((!isset($config_structure[$provider])) && (!empty($children))) {
            $config_structure[$provider] = [
                'composr-config' => [],
                'keys-config' => [],
                'hybridauth-config' => [],
                'alternate_configs' => [],
            ];
        }

        if ($children !== null) {
            foreach ($children as $child) {
                list($subtag, $subattributes, , $subchildren) = $child;
                switch ($subtag) {
                    case 'composr-config':
                    case 'keys-config':
                    case 'hybridauth-config':
                        $config_structure[$provider][$subtag] = $subattributes;
                        break;

                    // Alternate config, e.g. 'admin'
                    default:
                        if (is_array($subchildren)) {
                            foreach ($subchildren as $subchildren_child) {
                                list($subsubtag, $subsubattributes, , ) = $subchildren_child;
                                switch ($subsubtag) {
                                    case 'composr-config':
                                    case 'keys-config':
                                    case 'hybridauth-config':
                                        if (!isset($config_structure[$provider]['alternate_configs'][$subtag])) {
                                            $config_structure[$provider]['alternate_configs'][$subtag] = [
                                                'composr-config' => [],
                                                'keys-config' => [],
                                                'hybridauth-config' => [],
                                            ];
                                        }
                                        $config_structure[$provider]['alternate_configs'][$subtag][$subsubtag] = $subsubattributes;
                                        break;
                                }
                            }
                        }
                        break;
                }
            }
        }
    }

    $providers = [];

    // Retrieve expanded info Composr provides
    $provider_expanded_info = [];
    $hooks = find_all_hook_obs('systems', 'hybridauth', 'Hook_hybridauth_');
    foreach ($hooks as $hook_ob) {
        $provider_expanded_info += $hook_ob->info();
    }

    // Set up a skeleton structure of info for all available Hybridauth providers
    foreach ($all_available_providers as $i => $provider) {
        $providers[$provider] = [
            'enabled' => null,

            'label' => $provider,

            // Prominence options. These could be dynamic, e.g. for countries/languages where a service is not popular, do not show a prominent button and/or lower the priority
            'prominent_button' => false, // Basically if it shows in login blocks (as opposed to just the full login screen)
            'button_precedence' => 30 + $i, // 1=most prominent, 100=least prominent

            'background_colour' => '000000',
            'text_colour' => 'FFFFFF',
            'icon' => null,

            'syndicate_from' => '',
            'syndicate_from_by_default' => '',
            'remote_hosting' => false,

            'keys' => [
            ],

            'other_parameters' => [
            ],

            'alternate_configs' => [
            ],
        ];
        if (isset($provider_expanded_info[$provider])) {
            $providers[$provider] = $provider_expanded_info[$provider] + $providers[$provider];
        }
    }

    // Expand any holes in the skeleton structure with defaults
    foreach ($providers as $provider => &$info) {
        if (isset($config_structure[$provider])) {
            if (($alternate_config !== null) && (isset($config_structure[$provider]['alternate_configs'][$alternate_config]))) {
                $config_override = $config_structure[$provider]['alternate_configs'][$alternate_config];
            } else {
                $config_override = ['composr-config' => [], 'keys-config' => [], 'hybridauth-config' => []];
            }
            $config = [];
            foreach (['composr-config', 'keys-config', 'hybridauth-config'] as $config_section) {
                $config[$config_section] = $config_override[$config_section] + $config_structure[$provider][$config_section];
            }
        } else {
            $config = ['composr-config' => [], 'keys-config' => [], 'hybridauth-config' => []];
        }

        $info['keys'] = $config['keys-config'] + $info['keys'];
        $info['other_parameters'] = $config['hybridauth-config'] + $info['other_parameters'];

        if (isset($config_structure[$provider])) {
            $info['alternate_configs'] = array_keys($config_structure[$provider]['alternate_configs']);
        }

        if ($alternate_config !== null) {
            $enabled = !empty($info['keys']);
        } else {
            $enabled = $info['enabled'];
            if ($enabled === null) {
                $enabled = (!empty($info['keys'])) && (isset($config['composr-config']['allow_signups'])) && ($config['composr-config']['allow_signups'] == 'true');
            }
        }
        $info['enabled'] = $enabled;

        if (!empty($config['composr-config']['label'])) {
            $info['label'] = $config['composr-config']['label'];
        }

        if (!empty($config['composr-config']['prominent_button'])) {
            $info['prominent_button'] = ($config['composr-config']['prominent_button'] == 'true');
        }
        if (!empty($config['composr-config']['button_precedence'])) {
            $info['button_precedence'] = intval($config['composr-config']['button_precedence']);
        }

        if (!empty($config['composr-config']['background_colour'])) {
            $info['background_colour'] = ltrim($config['composr-config']['background_colour'], '#');
        }
        if (!empty($config['composr-config']['text_colour'])) {
            $info['text_colour'] = ltrim($config['composr-config']['text_colour'], '#');
        }
        if (empty($config['composr-config']['icon'])) {
            if ($info['icon'] === null) {
                if (is_file(get_file_base() . '/themes/default/images/icons/links/' . cms_strtolower_ascii($provider) . '.svg')) {
                    $icon = 'links/' . cms_strtolower_ascii($provider);
                } elseif (is_file(get_file_base() . '/themes/default/images_custom/icons/links/' . cms_strtolower_ascii($provider) . '.svg')) {
                    $icon = 'links/' . cms_strtolower_ascii($provider);
                } else {
                    $icon = 'menu/site_meta/user_actions/login';
                }
                $info['icon'] = $icon;
            }
        } else {
            $icon = $config['composr-config']['icon'];
            if (substr($icon, 0, 6) == 'icons/') {
                $icon = substr($icon, 6);
            }
            $info['icon'] = $icon;
        }

        $info['syndicate_from'] = isset($config['composr-config']['syndicate_from']) ? $config['composr-config']['syndicate_from'] : '';
        $info['syndicate_from_by_default'] = isset($config['composr-config']['syndicate_from_by_default']) ? $config['composr-config']['syndicate_from_by_default'] : '';

        $info['remote_hosting'] = isset($config['composr-config']['remote_hosting']) ? ($config['composr-config']['remote_hosting'] == 'true') : false;
    }

    sort_maps_by($providers, 'button_precedence');

    $providers_cache[$alternate_config] = $providers;

    return $providers;
}

function hybridauth_handle_authenticated_account($provider, $user_profile)
{
    require_lang('cns');
    require_code('cns_members');
    require_code('cns_groups');
    require_code('cns_members2');
    require_code('cns_members_action');
    require_code('cns_members_action2');
    require_code('character_sets');

    // Convert all null to empty string
    $user_profile->identifier = $user_profile->identifier ?? '';
    $user_profile->email = $user_profile->email ?? '';
    $user_profile->emailVerified = $user_profile->emailVerified ?? '';
    $user_profile->displayName = $user_profile->displayName ?? '';
    $user_profile->firstName = $user_profile->firstName ?? '';
    $user_profile->lastName = $user_profile->lastName ?? '';
    $user_profile->photoURL = $user_profile->photoURL ?? '';
    $user_profile->language = $user_profile->language ?? '';
    $user_profile->profileURL = $user_profile->profileURL ?? '';
    $user_profile->description = $user_profile->description ?? '';
    $user_profile->gender = $user_profile->gender ?? '';
    $user_profile->webSiteURL = $user_profile->webSiteURL ?? '';
    $user_profile->phone = $user_profile->phone ?? '';
    $user_profile->address = $user_profile->address ?? '';
    $user_profile->city = $user_profile->city ?? '';
    $user_profile->region = $user_profile->region ?? '';
    $user_profile->zip = $user_profile->zip ?? '';
    $user_profile->country = $user_profile->country ?? '';

    // Get basic details from Hybridauth's $user_profile...

    $id = $user_profile->identifier;

    $email_address = $user_profile->email;
    if (empty($email_address)) {
        $email_address = $user_profile->emailVerified;
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

    $username = preg_replace('/#.*$/', '', $user_profile->displayName);
    if (empty($username)) {
        $username = trim($user_profile->firstName . ' ' . $user_profile->lastName);
    }
    if (empty($username)) {
        warn_exit(do_lang_tempcode('HYBRIDAUTH_NO_USERNAME', escape_html($provider)));
    }
    $username = convert_to_internal_encoding($username, 'utf-8');

    $photo_url = $user_profile->photoURL;

    $language = cms_strtoupper_ascii(preg_replace('#^([^_\-]*).*$#', '\1', $user_profile->language));
    if (empty($language)) {
        $language = null;
    }

    $dob_day = $user_profile->birthDay;
    $dob_month = $user_profile->birthMonth;
    $dob_year = $user_profile->birthYear;

    // And some things that may go into CPFs...

    $profile_url = $user_profile->profileURL;

    $about = convert_to_internal_encoding($user_profile->description, 'utf-8');

    $gender = convert_to_internal_encoding(cms_ucfirst_ascii($user_profile->gender), 'utf-8');

    $firstname = convert_to_internal_encoding($user_profile->firstName, 'utf-8');
    $lastname = convert_to_internal_encoding($user_profile->lastName, 'utf-8');

    $website = $user_profile->webSiteURL;

    $mobile_phone_number = $user_profile->phone;

    $street_address = convert_to_internal_encoding($user_profile->address, 'utf-8');
    $city = convert_to_internal_encoding($user_profile->city, 'utf-8');
    $state = convert_to_internal_encoding($user_profile->region, 'utf-8');
    $post_code = convert_to_internal_encoding($user_profile->zip, 'utf-8');
    $country = convert_to_internal_encoding($user_profile->country, 'utf-8');

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
        $member_id = $member_row['id'];
    } else {
        $member_id = null;
        $member_row = null;
    }

    /*
    if ($member_id !== null) { // Useful for debugging
        require_code('cns_members_action2');
        cns_delete_member($member_id);
        $member_id = null;
    }
    */

    // Save the data
    if ($member_row === null) {
        $is_new_account = true;
        $member_id = hybridauth_create_authenticated_account($provider, $id, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year);
    } else {
        $is_new_account = false;
        $member_id = hybridauth_update_authenticated_account($provider, $id, $member_row, $email_address, $username, $photo_url, $language, $dob_day, $dob_month, $dob_year);
    }
    hybridauth_set_cpfs($provider, $is_new_account, $member_id, $profile_url, $about, $gender, $firstname, $lastname, $website, $mobile_phone_number, $street_address, $city, $state, $post_code, $country);

    return $member_id;
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
        check_for_spam(post_param_string('username', $username, INPUT_FILTER_POST_IDENTIFIER), $email_address, false);
    }

    $username = post_param_string('username', $username, INPUT_FILTER_POST_IDENTIFIER); // User may have customised username
    if (!empty($_custom_fields)) { // Was not auto-generated, so needs to be checked
        cns_check_name_valid($username);
    }
    $member_id = cns_member_external_linker($provider, $username, $id, false, $email_address, $dob_day, $dob_month, $dob_year, null, $language, $photo_url, '');

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
                $changes['field_' . strval($composr_field_id)] = convert_to_internal_encoding($value, 'utf-8');
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
