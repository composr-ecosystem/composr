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
        return 'This addon integrates Hybridauth, to add many social network (etc) login options to your site (Facebook, Google, Apple, etc). It also may be used as an admin backend to configuring content integration with such services, with included support for Atom feed generation and content display.

Hybridauth essentially implements the OAuth1, OAuth2, OpenID Connect, and OpenID standards, and proprietary APIs, necessary to unify all the different login integrations of different services.

Hybridauth supports many providers (around 50), and likely will support more in the future. For the purposes of this addon we can confirm the following common ones work well:
 - [tt]BitBucket[/tt] (*)
 - [tt]Discord[/tt]
 - [tt]Dropbox[/tt]
 - [tt]Facebook[/tt]
 - [tt]GitHub[/tt]
 - [tt]GitLab[/tt]
 - [tt]Google[/tt]
 - [tt]MicrosoftGraph[/tt] (*) (this is Microsoft login, either of your account with Microsoft, or of a private Active Directory installation)
 - [tt]Reddit[/tt]
 - [tt]Twitter[/tt]
 - [tt]Vkontakte[/tt]
 - [tt]Yahoo[/tt]
* These do not support avatars/photos, which you might care about when deciding what login options to feature.

For the full list of login options and integration notes refer to the list of providers on the [url="Hybridauth website"]https://hybridauth.github.io/hybridauth/[/url], and the code comments in the [tt]sources_custom/hybridauth/Provider[/tt] files.
We expect any Hybridauth provider will work with Composr, but we have not tested or optimised any not listed in this documentation.

[title="2"]Setting up providers[/title]

[title="3"]On the provider\'s end[/title]

The first thing you do is create an \'app\' on the developers section of the provider\'s website.
The vast majority of providers work via OAuth2.
The actual steps vary from provider-to-provider, but for most you will end up with an OAuth ID and an OAuth secret.
The OAuth Redirect URI used will be [tt]http://yourbaseurl/data_custom/hybridauth.php[/tt]. You will probably need to set it up on the app for security reasons.

[title="3"]On the Composr end[/title]

You configure a provider by editing XML in Admin Zone > Setup > Hybridauth Configuration. The XML defined here is mapped automatically to Hybridauth configuration settings as well as whatever settings Composr requires for integration. The XML structure is based on some naming conventions, including the Hybridauth filenames of providers (listed above).

Here is the structure of the basic configuration:
[code="XML"]
<hybridauth>
    <SomeProvider>
        <composr-config allow_signups="true" />
        <keys-config id="ExampleOAuthId" secret="ExampleOAuthSecret" />
    </SomeProvider>

    <AnotherProvider>
        <composr-config allow_signups="true" />
        <keys-config id="ExampleOAuthId" secret="ExampleOAuthSecret" />
    </AnotherProvider>

    ...
</hybridauth>
[/code]

E.g.:
[code="XML"]
<hybridauth>
    <Facebook>
        <composr-config allow_signups="true" />
        <keys-config id="abc" secret="def" />
    </Facebook>

    <Twitter>
        <composr-config allow_signups="true" />
        <keys-config id="abc" secret="def" />
    </Twitter>
</hybridauth>
[/code]

The [tt]id[/tt] and [tt]secret[/tt] values here are standard OAuth2 key parameters. This is of course assuming the provider works via OAuth2, but most do.

[title="2"]Provider-specific notes[/title]

[title="3"]Apple (untested)[/title]

You may wonder why Apple is not on the list of tested providers. This is supported by Hybridauth but you will need to set up and upload special key files, along with extra PHP software dependencies for Firebase. It likely is not worth the extra effort for you given Apple users likely also have Facebook accounts.

The [url="Apple provider actually takes different values"]https://hybridauth.github.io/providers/apple.html[/url]:
[code="XML"]
<hybridauth>
    ...
    <Apple>
        <composr-config allow_signups="true" />
        <keys-config id="abc" team-id="def" key-id="ghi" key-file="jkl" key-content="mno" />
    </Apple>
    ...
</hybridauth>
[/code]
See the code comments in [tt]sources_custom/hybridauth/Provider/Apple.php[/tt] for clearer details on these config settings.

[title="3"]Facebook[/title]

If you have the [tt]facebook_support[/tt] addon installed then there are friendly configuration options for setting up OAuth2 and enabling login. No XML attributes need setting (but you can do that instead if you prefer, and they take precedence).

Facebook has a wide variety of extra fields, but only if you go through a special approval process and extend the configured scope, e.g.:
[code="XML"]
<hybridauth>
    ...
    <Facebook>
        <hybridauth-config scope="email,user_gender,user_birthday,user_location" />
    </Facebook>
    ...
</hybridauth>
[/code]

[title="3"]Google[/title]

There are friendly configuration options for setting up OAuth2 and enabling login. No XML attributes need setting.

[title="3"]Twitter[/title]

If you have the [tt]twitter_support[/tt] addon installed then there are friendly configuration options for setting up OAuth2 and enabling login. No XML attributes need setting.

Twitter is using OAuth1 instead of OAuth2. Set XML like:
[code="XML"]
<hybridauth>
    ...
    <Twitter>
        <composr-config allow_signups="true" />
        <keys-config key="abc" secret="def" />
    </Twitter>
    ...
</hybridauth>
[/code]

Twitter apps need to go through an approval process.

[title="3"]LinkedIn (untested)[/title]

You may wonder why LinkedIn is not on the list. LinkedIn apps need to go through an approval process and we imagine most users will not want to make the effort. Hybridauth does support it.

[title="3"]MicrosoftGraph[/title]

Setting up of [tt]MicrosoftGraph[/tt] on Microsoft\'s end is a bit complicated. You need to create and configure an "Azure Active Directory" resource on the [url="Azure Portal"]https://portal.azure.com/[/url].
There is an extra [tt]tenant[/tt] option that relates to the "Supported account types" choice you made. You probably will need:
[code="XML"]
<hybridauth>
    ...
    <MicrosoftGraph>
        <composr-config allow_signups="true" />
        <keys-config id="abc" secret="def" />
        <hybridauth-config tenant="consumers" />
    </MicrosoftGraph>
    ...
</hybridauth>
[/code]

[title="3"]Pinterest (untested)[/title]

You may wonder why Pinterest is not on the list. Pinterest is not currently accepting new apps. Hybridauth does support it.

[title="3"]StackExchange (suboptimal)[/title]

You may wonder why StackExchange is not on the list. StackExchange does not allow transfer of e-mail address, which is important for most sites. Hybridauth does support it.

There is an extra [tt]site[/tt] option that relates to the particular StackExchange site you want to use. It must be set. For example:
[code="XML"]
<hybridauth>
    ...
    <MicrosoftGraph>
        <composr-config allow_signups="true" />
        <keys-config id="abc" secret="def" />
        <hybridauth-config site="stackoverflow.com" />
    </MicrosoftGraph>
    ...
</hybridauth>
[/code]

[title="3"]Vkontakte[/title]

The terminology displayed on Vkontakte\'s end is a little different:
 - App ID is the OAuth2 ID.
 - Secure key is OAuth2 secret.

[title="2"]Button display[/title]

All the providers mentioned in this documentation are guaranteed to have a nice button icon bundled with Composr, and a human-friendly label. Others may or may not.

You can customise the button display for any provider via more options:
[code="XML"]
<hybridauth>
    ...
    <SomeProvider>
        <composr-config allow_signups="true" label="Some label" prominent_button="true" button_precedence="5" background_color="FF0000" text_color="FFFFFF" icon="links/microsoft" />
        <keys-config id="ExampleOAuthId" secret="ExampleOAuthSecret" />
    </SomeProvider>
    ...
</hybridauth>
[/code]

Some notes:
 - The [tt]button-precedence[/tt] allows manual sorting (lower numbers are higher precedence)
 - The icon is any theme image path under [tt]icons/[/tt]

[title="2"]Admin integration[/title]

As well as member login, there is also the ability for the admin to establish a log in for other integrations.

The settings are configured in the same way as member login. However, if you need them different to member logins (maybe setting an extended scope, for example), you can set them under an [tt]<admin>[/tt] node in the XML:
[code="Commandr"]
<hybridauth>
    ...
    <Facebook>
        <hybridauth-config scope="email,user_gender,user_birthday,user_location" />
        <admin>
            <hybridauth-config scope="email,user_posts,pages_manage_posts,pages_show_list,manage_pages,publish_pages,user_videos,pages_read_engagement" default_page_id="111785054060070" />
        </admin>
    </Facebook>
    ...
</hybridauth>
[/code]

After configuring XML you establish a log in from Admin Zone > Setup > Setup API access.

Out of the box the following integrations exist, for providers supporting the Hybridauth Atom API. At the time of writing:
 - Facebook
 - Instagram
 - Twitter

[title="3"]Atom feed display[/title]

[tt]https://yourbaseurl/data_custom/hybridauth_admin_atom.php?provider=<Provider>[/tt] will generate an Atom feed for a provider.

There are a couple of extra GET parameters to filter the feed:
 - [tt]includeContributedContent=0|1[/tt] -- whether to include 3rd party content posted on the provider feed (if relevant)
 - [tt]categoryFilter=<categoryFilter>[/tt] -- pass a category ID to filter to a specific category (what categories are depends on the provider; for Facebook blank is the personal feed and a numeric value is for a Facebook page you administer)

[title="3"]Content display[/title]

The [tt]main_hybridauth_admin_atoms[/tt] block allows you to display content from a provider in a way similar to the [tt]main_rss[/tt] or [tt]main_news[/tt] blocks.
A lot of data is passed into the templates for a high degree of flexibility.
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
                'PHP XML extension',
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
            'themes/default/css_custom/_hybridauth_button.css',
            'sources_custom/hooks/systems/symbols/HYBRIDAUTH_BUTTONS.php',
            'sources_custom/hooks/systems/symbols/HYBRIDAUTH_BUTTONS_CSS.php',
            'data_custom/hybridauth.php',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS_NO.tpl',
            'themes/default/templates_custom/LOGIN_SCREEN.tpl',
            'themes/default/templates_custom/CNS_JOIN_STEP2_SCREEN.tpl',
            'themes/default/templates_custom/BLOCK_TOP_LOGIN.tpl',
            'themes/default/templates_custom/CNS_GUEST_BAR.tpl',
            'sources_custom/users.php',
            'sources_custom/cns_members.php',
            'sources_custom/hybridauth.php',
            'sources_custom/hooks/systems/login_providers/hybridauth.php',
            'sources_custom/users_active_actions.php',
            'sources_custom/hooks/systems/startup/hybridauth.php',
            'adminzone/modules/minimodules_custom/admin_hybridauth.php',
            'sources_custom/hooks/systems/page_groupings/hybridauth.php',

            'sources_custom/hooks/systems/cron/hybridauth_admin.php',
            'sources_custom/hybridauth_admin.php',
            'sources_custom/hybridauth_admin_storage.php',
            'data_custom/hybridauth_admin.php',
            'sources_custom/hooks/systems/oauth_screen_sup/hybridauth_admin.php',
            'data_custom/hybridauth_admin_atom.php',
            'sources_custom/hybridauth/Adapter/AtomInterface.php',
            'sources_custom/hybridauth/Atom/.htaccess',
            'sources_custom/hybridauth/Atom/Atom.php',
            'sources_custom/hybridauth/Atom/AtomFeedBuilder.php',
            'sources_custom/hybridauth/Atom/AtomHelper.php',
            'sources_custom/hybridauth/Atom/Category.php',
            'sources_custom/hybridauth/Atom/Enclosure.php',
            'sources_custom/hybridauth/Atom/Author.php',
            'sources_custom/hybridauth/Atom/Filter.php',
            'sources_custom/hybridauth/Atom/index.html',
            'sources_custom/blocks/main_hybridauth_admin_atoms.php',
            'themes/default/templates_custom/BLOCK_MAIN_HYBRIDAUTH_ADMIN_ATOMS.tpl',

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
