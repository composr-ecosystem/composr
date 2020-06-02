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

// We don't use admin_oauth because we use our Facebook library's own oAuth functionality.

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('facebook_support', $error_msg)) {
    return $error_msg;
}

if (!function_exists('curl_init')) {
    warn_exit(do_lang_tempcode('NO_CURL_ON_SERVER'));
}
if (!function_exists('session_status')) {
    warn_exit('PHP session extension missing');
}

require_code('developer_tools');
destrictify();

require_lang('facebook');

$title = get_screen_title('FACEBOOK_OAUTH');

$facebook_appid = get_option('facebook_appid');

if ($facebook_appid == '') {
    $config_url = build_url(['page' => 'admin_config', 'type' => 'category', 'id' => 'COMPOSR_APIS', 'redirect' => protect_url_parameter(SELF_REDIRECT)], get_module_zone('admin_config'), [], false, false, false, 'group-FACEBOOK_SYNDICATION');
    $echo = redirect_screen($title, $config_url, do_lang_tempcode('FACEBOOK_SETUP_FIRST'));
    $echo->evaluate_echo();
    return;
}

require_code('hooks/systems/syndication/facebook');
$ob = new Hook_syndication_facebook();

$result = $ob->auth_set(null, get_self_url(false, false, ['oauth_in_progress' => 1]));

if ($result) {
    $out = do_lang_tempcode('FACEBOOK_OAUTH_SUCCESS');
} else {
    $out = do_lang_tempcode('SOME_ERRORS_OCCURRED');
}

$title->evaluate_echo();

$out->evaluate_echo();
