<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    twitter_support
 */

// We don't use a regular oauth hook because we use our Twitter library's own oAuth functionality. It's oAuth1, while Composr's oAuth is only oAuth2 (very different).

class Hook_oauth_screen_sup_twitter
{
    public function get_services()
    {
        if (!addon_installed('twitter_support')) {
            return [];
        }

        $services = [];

        require_code('developer_tools');
        destrictify();

        require_code('twitter');
        require_lang('twitter');

        $api_key = get_option('twitter_api_key');
        $api_secret = get_option('twitter_api_secret');

        $configured = ($api_key != '') && ($api_secret != '');
        $config_url = build_url(['page' => 'admin_config', 'type' => 'category', 'id' => 'COMPOSR_APIS', 'redirect' => protect_url_parameter(SELF_REDIRECT)], get_module_zone('admin_config'), [], false, false, false, 'group-TWITTER');
        $connected = (get_value('twitter_oauth_token', null, true) !== null) && (get_value('twitter_oauth_token_secret', null, true) !== null);
        $url = get_self_url(false, false, ['oauth_step' => 1], false, true);

        $services[] = [
            'LABEL' => 'Twitter',
            'PROTOCOL' => 'oAuth1',
            'AVAILABLE' => function_exists('curl_init'),

            'CONFIGURED' => $configured,
            'CONFIG_URL' => $config_url,
            'CONNECTED' => $connected,
            'CONNECT_URL' => $url,
            'CLIENT_ID' => '',
            'CLIENT_SECRET' => $api_secret,
            'API_KEY' => $api_key,
            'REFRESH_TOKEN' => '',
        ];

        $oauth_step = get_param_integer('oauth_step', 0);

        if ($oauth_step != 0) {
            $twitter = new Twitter($api_key, $api_secret);

            switch ($oauth_step) {
                case 1:
                    $oauth_url = get_self_url(false, false, ['oauth_step' => 2], false, true);

                    $response = $twitter->oAuthRequestToken($oauth_url->evaluate());

                    require_code('site2');
                    redirect_exit(Twitter::SECURE_API_URL . '/oauth/authorize?oauth_token=' . urlencode($response['oauth_token']));

                    exit();

                    break;

                case 2:
                    $save_to = 'twitter_oauth_token';
                    $secret_save_to = 'twitter_oauth_token_secret';

                    if ((get_value_newer_than($save_to, time() - 5, true) === null) || (get_value_newer_than($secret_save_to, time() - 5, true) === null)) {
                        $response = $twitter->oAuthAccessToken(
                            get_param_string('oauth_token', false, INPUT_FILTER_GET_COMPLEX),
                            get_param_string('oauth_verifier', false, INPUT_FILTER_GET_COMPLEX)
                        );

                        if (isset($response['oauth_token'])) {
                            set_value($save_to, $response['oauth_token'], true);
                            set_value($secret_save_to, $response['oauth_token_secret'], true);

                            attach_message(do_lang_tempcode('TWITTER_OAUTH_SUCCESS'), 'inform');
                        } else {
                            attach_message(do_lang_tempcode('TWITTER_OAUTH_FAIL', escape_html($response['message'])), 'warn', false, true);
                        }
                    }

                    break;
            }
        }

        return $services;
    }
}
