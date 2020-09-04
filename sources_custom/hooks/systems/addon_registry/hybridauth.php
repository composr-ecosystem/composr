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

/**
 * Hook class.
 */
class Hook_addon_registry_hybridauth
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'MIT License';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'This addon integrates Hybridauth, to add around 50 social network (etc) login options to your site (Facebook, Google, Apple, etc).

Hybridauth essentially implements the OAuth1, OAuth2, OpenID Connect, and OpenID standards, and proprietary APIs, necessary to unify all the different login integrations of different services.

For the full list of login options refer to the list of providers on the [url="Hybridauth website"]https://hybridauth.github.io/hybridauth/[/url].

The OAuth Redirect URI used will be [tt]http://yourbaseurl/data_custom/hybridauth.php[/tt]

Some Composr addons come with config options for OAuth that work with Hybridauth ([tt]facebook_support[/tt] does this). For these addons, it\'s therefore pretty easy to set up login integration. A little configuration on the service\'s end (e.g. configuring an app), and a little configuration on Composr\'s end.

To enable an integration that has no matching config options you need to set hidden options. These options are based on some naming conventions and the codenames of providers in the Hybridauth code.

To find the codename of a provider as recognised in the code, look at the filenames in [tt]sources_custom/hybridauth/src/Provider[/tt] (without the [tt].php[/tt], e.g. [tt]Yahoo[/tt]).

Set hidden options in Commandr like...
[code="Commandr"]
:set_value(\'hybridauth_Yahoo_key_id\', \'abcdef\');
:set_value(\'hybridauth_Yahoo_key_secret\', \'abcdef\');
[/code]

The [tt]id[/tt] and [tt]secret[/tt] values here are standard OAuth key parameters you would be provided by the service you are integrating (Yahoo in this case). i.e. You set up an app on Yahoo and it will give you these details.

For some services there may also be an API key, set like:
[code="Commandr"]
:set_value(\'hybridauth_Example_key_key\', \'abcdef\');
[/code]
For Twitter actually you set the [tt]key[/tt] and no [tt]id[/tt], because the integration is using OAuth1 not OAuth2.

And a scope to specify what service permissions you want, set like:
[code="Commandr"]
:set_value(\'hybridauth_Facebook_scope\', \'email,user_gender,user_birthday,user_location\');
[/code]
The actual value taken and whether spaces/commas are used as delimiters, depends on the provider. A safe low-pain default will be picked if you do not specify. The example above sets permissions on Facebook to gather a bit more, but would require you go through their approval process.

The [url="Apple provider actually takes different values"]https://hybridauth.github.io/providers/apple.html[/url]...
[code="Commandr"]
:set_value(\'hybridauth_Apple_key_id\', \'abcdef\');
:set_value(\'hybridauth_Apple_key_team_id\', \'abcdef\');
:set_value(\'hybridauth_Apple_key_key_id\', \'abcdef\');
:set_value(\'hybridauth_Apple_key_key_content\', \'abcdef\');
:set_value(\'hybridauth_Apple_key_key_file\', \'abcdef\');
[/code]

The [tt]MicrosoftGraph[/tt] provider may need a tenant value setting, you probably will want:
[code="Commandr"]
:set_value(\'hybridauth_MicrosoftGraph_tenant\', \'consumers\');
[/code]

The addon optimises the display of the following providers: Apple, Facebook, Google, MicrosoftGraph, Twitter. This is based on a pragmatic assessment of what is most likely to be popular. New [tt]sources_custom/hooks/systems/hybridauth[/tt] hooks may be added, or existing ones changed, to adjust this -- or you can actually override any setting in hidden options (as the naming of the hidden option parallels the settings in the hooks).
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return [
            'requires' => [
                'stats',
                'commandr',
                'PHP curl extension',
                'PHP sessions extension',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/menu/site_meta/user_actions/login.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/hybridauth.php',
            'lang_custom/EN/hybridauth.ini',
            'sources_custom/hooks/systems/hybridauth/google.php',
            'sources_custom/hooks/systems/hybridauth/apple.php',
            'sources_custom/hooks/systems/hybridauth/microsoft.php',
            'sources_custom/hooks/systems/hybridauth/_misc_overrides.php',
            'sources_custom/hooks/systems/hybridauth/index.html',
            'sources_custom/hooks/systems/hybridauth/.htaccess',
            'sources_custom/hooks/systems/config/hybridauth_sync_avatar.php',
            'sources_custom/hooks/systems/config/hybridauth_sync_email.php',
            'sources_custom/hooks/systems/config/hybridauth_sync_username.php',
            'sources_custom/hooks/systems/config/google_allow_signups.php',
            'sources_custom/cns_field_editability.php',
            'themes/default/templates_custom/HYBRIDAUTH_BUTTON.tpl',
            'themes/default/css_custom/hybridauth.css',
            'sources_custom/hooks/systems/symbols/HYBRIDAUTH_BUTTONS.php',
            'sources_custom/hooks/systems/symbols/HYBRIDAUTH_BUTTONS_CSS.php',
            'data_custom/hybridauth.php',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS_NO.tpl',
            'themes/default/templates_custom/LOGIN_SCREEN.tpl',
            'themes/default/templates_custom/BLOCK_TOP_LOGIN.tpl',
            'sources_custom/users.php',
            'sources_custom/cns_members.php',
            'sources_custom/hybridauth.php',

            'sources_custom/hybridauth/autoload.php',
            'sources_custom/hybridauth/User/Contact.php',
            'sources_custom/hybridauth/User/Activity.php',
            'sources_custom/hybridauth/User/Profile.php',
            'sources_custom/hybridauth/Exception/InvalidArgumentException.php',
            'sources_custom/hybridauth/Exception/NotImplementedException.php',
            'sources_custom/hybridauth/Exception/InvalidAuthorizationCodeException.php',
            'sources_custom/hybridauth/Exception/AuthorizationDeniedException.php',
            'sources_custom/hybridauth/Exception/RuntimeException.php',
            'sources_custom/hybridauth/Exception/HttpRequestFailedException.php',
            'sources_custom/hybridauth/Exception/InvalidAccessTokenException.php',
            'sources_custom/hybridauth/Exception/InvalidOpenidIdentifierException.php',
            'sources_custom/hybridauth/Exception/InvalidAuthorizationStateException.php',
            'sources_custom/hybridauth/Exception/HttpClientFailureException.php',
            'sources_custom/hybridauth/Exception/ExceptionInterface.php',
            'sources_custom/hybridauth/Exception/InvalidOauthTokenException.php',
            'sources_custom/hybridauth/Exception/BadMethodCallException.php',
            'sources_custom/hybridauth/Exception/UnexpectedApiResponseException.php',
            'sources_custom/hybridauth/Exception/Exception.php',
            'sources_custom/hybridauth/Exception/UnexpectedValueException.php',
            'sources_custom/hybridauth/Exception/InvalidApplicationCredentialsException.php',
            'sources_custom/hybridauth/HttpClient/Util.php',
            'sources_custom/hybridauth/HttpClient/Guzzle.php',
            'sources_custom/hybridauth/HttpClient/Curl.php',
            'sources_custom/hybridauth/HttpClient/HttpClientInterface.php',
            'sources_custom/hybridauth/Provider/Yandex.php',
            'sources_custom/hybridauth/Provider/Steam.php',
            'sources_custom/hybridauth/Provider/Twitter.php',
            'sources_custom/hybridauth/Provider/BitBucket.php',
            'sources_custom/hybridauth/Provider/Reddit.php',
            'sources_custom/hybridauth/Provider/Telegram.php',
            'sources_custom/hybridauth/Provider/AOLOpenID.php',
            'sources_custom/hybridauth/Provider/Foursquare.php',
            'sources_custom/hybridauth/Provider/YahooOpenID.php',
            'sources_custom/hybridauth/Provider/WeChat.php',
            'sources_custom/hybridauth/Provider/TwitchTV.php',
            'sources_custom/hybridauth/Provider/OpenID.php',
            'sources_custom/hybridauth/Provider/Slack.php',
            'sources_custom/hybridauth/Provider/Amazon.php',
            'sources_custom/hybridauth/Provider/Spotify.php',
            'sources_custom/hybridauth/Provider/GitLab.php',
            'sources_custom/hybridauth/Provider/Dribbble.php',
            'sources_custom/hybridauth/Provider/Instagram.php',
            'sources_custom/hybridauth/Provider/GitHub.php',
            'sources_custom/hybridauth/Provider/Google.php',
            'sources_custom/hybridauth/Provider/Facebook.php',
            'sources_custom/hybridauth/Provider/QQ.php',
            'sources_custom/hybridauth/Provider/Mailru.php',
            'sources_custom/hybridauth/Provider/Strava.php',
            'sources_custom/hybridauth/Provider/StackExchangeOpenID.php',
            'sources_custom/hybridauth/Provider/WordPress.php',
            'sources_custom/hybridauth/Provider/StackExchange.php',
            'sources_custom/hybridauth/Provider/ORCID.php',
            'sources_custom/hybridauth/Provider/BlizzardEU.php',
            'sources_custom/hybridauth/Provider/Dropbox.php',
            'sources_custom/hybridauth/Provider/Authentiq.php',
            'sources_custom/hybridauth/Provider/MicrosoftGraph.php',
            'sources_custom/hybridauth/Provider/BlizzardAPAC.php',
            'sources_custom/hybridauth/Provider/DeviantArt.php',
            'sources_custom/hybridauth/Provider/Pinterest.php',
            'sources_custom/hybridauth/Provider/Tumblr.php',
            'sources_custom/hybridauth/Provider/WeChatChina.php',
            'sources_custom/hybridauth/Provider/Yahoo.php',
            'sources_custom/hybridauth/Provider/LinkedIn.php',
            'sources_custom/hybridauth/Provider/Disqus.php',
            'sources_custom/hybridauth/Provider/Odnoklassniki.php',
            'sources_custom/hybridauth/Provider/SteemConnect.php',
            'sources_custom/hybridauth/Provider/Discord.php',
            'sources_custom/hybridauth/Provider/WindowsLive.php',
            'sources_custom/hybridauth/Provider/PaypalOpenID.php',
            'sources_custom/hybridauth/Provider/Patreon.php',
            'sources_custom/hybridauth/Provider/Paypal.php',
            'sources_custom/hybridauth/Provider/Vkontakte.php',
            'sources_custom/hybridauth/Provider/Blizzard.php',
            'sources_custom/hybridauth/Provider/Apple.php',
            'sources_custom/hybridauth/Thirdparty/OpenID/LightOpenID.php',
            'sources_custom/hybridauth/Thirdparty/OpenID/README.md',
            'sources_custom/hybridauth/Thirdparty/OAuth/OAuthSignatureMethodHMACSHA1.php',
            'sources_custom/hybridauth/Thirdparty/OAuth/OAuthSignatureMethod.php',
            'sources_custom/hybridauth/Thirdparty/OAuth/OAuthUtil.php',
            'sources_custom/hybridauth/Thirdparty/OAuth/README.md',
            'sources_custom/hybridauth/Thirdparty/OAuth/OAuthRequest.php',
            'sources_custom/hybridauth/Thirdparty/OAuth/OAuthConsumer.php',
            'sources_custom/hybridauth/Thirdparty/readme.md',
            'sources_custom/hybridauth/Logger/Logger.php',
            'sources_custom/hybridauth/Logger/Psr3LoggerWrapper.php',
            'sources_custom/hybridauth/Logger/LoggerInterface.php',
            'sources_custom/hybridauth/Hybridauth.php',
            'sources_custom/hybridauth/Adapter/OAuth2.php',
            'sources_custom/hybridauth/Adapter/DataStoreTrait.php',
            'sources_custom/hybridauth/Adapter/OpenID.php',
            'sources_custom/hybridauth/Adapter/AbstractAdapter.php',
            'sources_custom/hybridauth/Adapter/AdapterInterface.php',
            'sources_custom/hybridauth/Adapter/OAuth1.php',
            'sources_custom/hybridauth/Storage/StorageInterface.php',
            'sources_custom/hybridauth/Storage/Session.php',
            'sources_custom/hybridauth/Data/Collection.php',
            'sources_custom/hybridauth/Data/Parser.php',
            'sources_custom/hybridauth/index.html',
        ];
    }
    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        // LEGACY: Transfer old facebook scheme to a Hybridauth provider
        $GLOBALS['FORUM_DB']->query_update('f_members', ['m_password_compat_scheme' => 'Facebook'], ['m_password_compat_scheme' => 'facebook']);
    }
}
