<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'MIT License';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon integrates Hybridauth, providing a number of features:
1) Adds many social network (etc) login options to your site (Facebook, Google, Apple, etc)
2) Allows pulling in content from some services, with included support for Atom feed generation and content display using a block
3) Allows syndicating (pushing) content to some services
4) Allows syndicating (pushing) activities to some services (see the activity_feed addon)
5) Allows using YouTube as a video host (effectively, a transcoder)
6) Works as a media renderer (oEmbed-like)

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
 - [tt]YouTube[/tt]
* These do not support avatars/photos, which you might care about when deciding what login options to feature.

For the full list of login options and integration notes refer to the list of providers on the [url="Hybridauth website"]https://hybridauth.github.io/hybridauth/[/url], and the code comments in the [tt]sources_custom/hybridauth/Provider[/tt] files.
We expect any Hybridauth provider will work with Composr, but we have not tested or optimised any not listed in this documentation.

[title="2"]Setting up providers[/title]

[title="3"]On the provider\'s end[/title]

The first thing you do is create an \'app\' on the developers section of the provider\'s website.
The vast majority of providers work via OAuth2.
The actual steps vary from provider-to-provider, but for most you will end up with an OAuth ID and an OAuth secret.
The OAuth Redirect URI used will be both [tt]https://yourbaseurl/data_custom/hybridauth.php[/tt] and [tt]https://yourbaseurl/data_custom/hybridauth_admin.php[/tt]. You will probably need to set it up on the app for security reasons.

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

You may wonder why Pinterest is not on the list. Pinterest was not accepting new apps at the time of testing, although this may no longer be the case. Hybridauth does support it.

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

[title="3"]YouTube[/title]

The Google configuration options will also be used for YouTube, so don\'t need configuring in the XML.
E-mail login is not supported.
Video transcoding is supported, and documented further down.

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
            <hybridauth-config scope="email,user_posts,pages_show_list,pages_manage_posts,pages_read_user_content,user_videos,pages_read_engagement" default_page_id="111785054060070" />
        </admin>
    </Facebook>
    ...
</hybridauth>
[/code]

After configuring XML you establish a log in from Admin Zone > Setup > Setup API access by clicking the link under &ldquo;Connected&rdquo; for the provider.

Out of the box the following integrations exist, for providers supporting Hybridauth Atom API operations. At the time of writing:
 - Facebook
 - Instagram
 - Twitter
 - YouTube

Many providers have an app review process for certain features that broadly overlap with the admin integration here. However, as you are authorising against your own account (which added the app), usually app reviews will not actually be required.

[title="3"]Atom feed display[/title]

[tt]https://yourbaseurl/data_custom/hybridauth_admin_atom.php?provider=<Provider>[/tt] will generate an Atom feed for a provider.

There are a couple of extra GET parameters to filter the feed:
 - [tt]includeContributedContent=0|1[/tt] -- whether to include 3rd party content posted on the provider feed (if relevant)
 - [tt]categoryFilter=<categoryFilter>[/tt] -- pass a category ID to filter to a specific category (what categories are depends on the provider; for Facebook blank is the personal feed and a numeric value is for a Facebook page you administer)

[title="3"]Content display (pull)[/title]

The [tt]main_hybridauth_admin_atoms[/tt] block allows you to display content from a provider in a way similar to the [tt]main_rss[/tt] or [tt]main_news[/tt] blocks.
A lot of data is passed into the templates for a high degree of flexibility.

[title="3"]Syndication (push)[/title]

You can syndicate content (as much of the full content as the provider can handle, combined with a link back to the original content), as well as activities (logs of site actions with a link back to what the action happened on).

Out of the box the following integrations exist, for providers supporting Hybridauth Atom API write operations. At the time of writing:
 - Facebook
 - Twitter
 - YouTube

It needs to be enabled in the XML, each provider needs to specifies what addons/content-types it can syndicate from:
[code="XML"]
<hybridauth>
    ...
    <Twitter>
        <composr-config syndicate_from="news,image,video,activity_feed" syndicate_from_by_default="news" />
    </Twitter>
    ...
</hybridauth>
[/code]

In this example we are configuring Twitter to syndicate from:
 - The [tt]news[/tt] addon (content syndication)
 - The [tt]image[/tt] content-type (content syndication)
 - The [tt]video[/tt] content-type (content syndication)
 - The [tt]activity_feed[/tt] addon (this is how you enable activities syndication, which requires the non-bundled [tt]activity_feed[/tt] addon)

And we are saying that by default [tt]news[/tt] will be pre-selected for content syndication (others will need manually selecting on the content add/edit form).

At the time of writing, content syndication is supported for the following content-types:
 - [tt]catalogue_entry[/tt]
 - [tt]download[/tt]
 - [tt]event[/tt]
 - [tt]image[/tt] (from the [tt]galleries[/tt] addon)
 - [tt]news[/tt]
 - [tt]quiz[/tt]
 - [tt]video[/tt] (from the [tt]galleries[/tt] addon)

Note that if content syndication is supported for a content-type, activity syndication will be un-selected by default, to avoid unnecessary noise.

[title="4"]Remote hosting / Transcoding[/title]

For YouTube combined with gallery videos (only), you can choose to use YouTube as a host after syndication happens.
The video is uploaded to YouTube then the local video is altered to point to it, discarding the uploaded file.

This is done using the [tt]remote_hosting[/tt] configuration property, like:
[code="XML"]
<hybridauth>
    ...
    <YouTube>
        <composr-config remote_hosting="true" syndicate_from="video" syndicate_from_by_default="video" />
    </YouTube>
    ...
</hybridauth>
[/code]

[title="3"]Media renderer[/title]

URLs to content owned by a connected provider (e.g. a Facebook post) can be used with the media rendering system. For example, posting a link within Comcode will provide an embed box.

This works in two possible ways:
 - Loading atoms via URLs, and displaying in a media box
 - Doing oEmbed through the authenticated API (supported for Instagram and Facebook, as oEmbed needs API keys on these providers)

For Facebook oEmbed, an extra setting is needed, from some values that will be available from the main app...
[code="XML"]
<hybridauth>
    ...
    <Facebook>
        <keys-config client_token="..." />
    </Facebook>
    ...
</hybridauth>
[/code]
The main oAuth keys are not shown here as usually this will be done in the Composr configuration UI for Facebook.
The client token is under Settings > Advanced.

And also Instagram...
[code="XML"]
<hybridauth>
    ...
    <Instagram>
        <keys-config id="..." secret="..." client_token="..." />
    </Instagram>
    ...
</hybridauth>
[/code]
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                'stats',
                'commandr',
                'PHP curl extension',
                'PHP sessions extension',
                'PHP xml extension',
            ],
            'recommends' => [
                'activity_feed',
            ],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/menu/site_meta/user_actions/login.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hybridauth/Adapter/.htaccess',
            'sources_custom/hybridauth/Adapter/index.html',
            'sources_custom/hybridauth/Data/.htaccess',
            'sources_custom/hybridauth/Data/index.html',
            'sources_custom/hybridauth/Exception/.htaccess',
            'sources_custom/hybridauth/Exception/index.html',
            'sources_custom/hybridauth/HttpClient/.htaccess',
            'sources_custom/hybridauth/HttpClient/index.html',
            'sources_custom/hybridauth/Logger/.htaccess',
            'sources_custom/hybridauth/Logger/index.html',
            'sources_custom/hybridauth/Provider/.htaccess',
            'sources_custom/hybridauth/Provider/index.html',
            'sources_custom/hybridauth/Storage/.htaccess',
            'sources_custom/hybridauth/Storage/index.html',
            'sources_custom/hybridauth/Thirdparty/.htaccess',
            'sources_custom/hybridauth/Thirdparty/index.html',
            'sources_custom/hybridauth/Thirdparty/OAuth/.htaccess',
            'sources_custom/hybridauth/Thirdparty/OAuth/index.html',
            'sources_custom/hybridauth/Thirdparty/OpenID/.htaccess',
            'sources_custom/hybridauth/Thirdparty/OpenID/index.html',
            'sources_custom/hybridauth/User/.htaccess',
            'sources_custom/hybridauth/User/index.html',
            'sources_custom/hybridauth/.htaccess',

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
            'adminzone/pages/minimodules_custom/admin_hybridauth.php',
            'sources_custom/hooks/systems/page_groupings/hybridauth.php',
            'sources_custom/hooks/systems/actionlog/hybridauth.php',
            'sources_custom/hooks/systems/contentious_overrides/hybridauth.php',

            'data_custom/hybridauth_admin.php',
            'data_custom/hybridauth_admin_atom.php',
            'sources_custom/hybridauth_admin.php',
            'sources_custom/hybridauth_admin_storage.php',
            'sources_custom/blocks/main_hybridauth_admin_atoms.php',
            'sources_custom/hooks/systems/cron/hybridauth_admin.php',
            'sources_custom/hooks/systems/oauth_screen_sup/hybridauth_admin.php',
            'sources_custom/hooks/systems/syndication/hybridauth_admin.php',
            'sources_custom/hooks/systems/media_rendering/hybridauth_admin.php',
            'themes/default/templates_custom/BLOCK_MAIN_HYBRIDAUTH_ADMIN_ATOMS.tpl',
            'sources_custom/hooks/systems/oembed/hybridauth_admin.php',

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
            'sources_custom/hybridauth/autoload.php',
            'sources_custom/hybridauth/User/Contact.php',
            'sources_custom/hybridauth/User/Activity.php',
            'sources_custom/hybridauth/User/Profile.php',
            'sources_custom/hybridauth/Exception/InvalidArgumentException.php',
            'sources_custom/hybridauth/Exception/NotImplementedException.php',
            'sources_custom/hybridauth/Exception/InvalidAuthorizationCodeException.php',
            'sources_custom/hybridauth/Exception/AuthorizationDeniedException.php',
            'sources_custom/hybridauth/Exception/AccessDeniedException.php',
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
            'sources_custom/hybridauth/Provider/YouTube.php',
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
            'sources_custom/hybridauth/Provider/Medium.php',
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
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('hybridauth_content_map');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            // LEGACY: Transfer old facebook scheme to a Hybridauth provider
            if (get_forum_type() == 'cns') {
                $GLOBALS['FORUM_DB']->query_update('f_members', ['m_password_compat_scheme' => 'Facebook'], ['m_password_compat_scheme' => 'facebook']);
            }

            $GLOBALS['SITE_DB']->create_table('hybridauth_content_map', [
                'h_content_type' => '*ID_TEXT',
                'h_content_id' => '*ID_TEXT',
                'h_provider' => '*ID_TEXT',
                'h_provider_id' => 'SHORT_TEXT',
                'h_sync_time' => 'TIME',
            ]);
        }
    }
}
