<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    twitter_support
 */

// We don't use admin_oauth because we use our Twitter library's own oAuth functionality. It's oAuth1, while Composr's oAuth is only oAuth2 (very different).

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('twitter_support', $error_msg)) {
    return $error_msg;
}

if (!function_exists('curl_init')) {
    warn_exit(do_lang_tempcode('NO_CURL_ON_SERVER'));
}

require_code('developer_tools');
destrictify();

require_code('twitter');
require_lang('twitter');

$title = get_screen_title('TWITTER_OAUTH');

$api_key = get_option('twitter_api_key');
$api_secret = get_option('twitter_api_secret');

if ($api_key == '' || $api_secret == '') {
    $config_url = build_url(['page' => 'admin_config', 'type' => 'category', 'id' => 'COMPOSR_APIS', 'redirect' => protect_url_parameter(SELF_REDIRECT)], get_module_zone('admin_config'), [], false, false, false, 'group-TWITTER');
    $echo = redirect_screen($title, $config_url, do_lang_tempcode('TWITTER_SETUP_FIRST'));
    $echo->evaluate_echo();
    return;
}

try {
    $result = twitter_oauth(get_self_url(false, false, ['oauth_in_progress' => 1]));
} catch (Exception $e) {
    warn_exit($e->getMessage());
}

if ($result) {
    $out = do_lang_tempcode('TWITTER_OAUTH_SUCCESS');
} else {
    $out = do_lang_tempcode('SOME_ERRORS_OCCURRED');
}

$title->evaluate_echo();

$out->evaluate_echo();

function twitter_oauth($oauth_url)
{
    $api_key = get_option('twitter_api_key');
    $api_secret = get_option('twitter_api_secret');
    $twitter = new Twitter($api_key, $api_secret);

    if (get_param_integer('oauth_in_progress', 0) == 0) {
        $response = $twitter->oAuthRequestToken($oauth_url->evaluate());
        require_code('site2');
        redirect_exit(Twitter::SECURE_API_URL . '/oauth/authorize?oauth_token=' . urlencode($response['oauth_token']));
        exit();
    }

    $save_to = 'twitter_oauth_token';
    $secret_save_to = 'twitter_oauth_token_secret';

    if ((get_value_newer_than($save_to, time() - 10, true) === null) || (get_value_newer_than($secret_save_to, time() - 10, true) === null)) {
        $response = $twitter->oAuthAccessToken(get_param_string('oauth_token', false, INPUT_FILTER_GET_COMPLEX), get_param_string('oauth_verifier', false, INPUT_FILTER_GET_COMPLEX));

        if (!isset($response['oauth_token'])) {
            attach_message(do_lang_tempcode('TWITTER_OAUTH_FAIL', escape_html($response['message'])), 'warn', false, true);
            return false;
        }

        set_value($save_to, $response['oauth_token'], true);
        set_value($secret_save_to, $response['oauth_token_secret'], true);
    }

    return true;
}
