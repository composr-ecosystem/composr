<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_addon_registry_facebook_support
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
        // Best to just categorise properly as it's not bundled 
        //return is_maintained('facebook') ? 'Third Party Integration' : 'Development';
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen / Naveen / Chris';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [
            'Class by Facebook Inc.',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Substantial {$IS_MAINTAINED,facebook,Facebook integration} for your Composr website.

Features:
 - User\'s can log in to your site using their Facebook profile (for Conversr-sites only)
 - News and calendar actions can be syndicated to a Facebook group/page
 - User\'s can syndicate all their site activity to their own Facebook accounts
 - New Facebook Page block (allows users to like your site, shows those that have, and view page posts)
 - New Facebook \'Like button\' block (linked into the main_screen_actions block by default)
 - New Facebook Comments block

For this addon to work you need to configure Composr\'s Facebook configuration settings, which includes getting a Facebook app ID.

Please be aware that this addon overrides some common templates to add Facebook functionality to them, such as [tt]LOGIN_SCREEN.tpl[/tt] and [tt]BLOCK_SIDE_PERSONAL_STATS_NO.tpl[/tt].

The documentation for this addon is covered in a [url="' . get_brand_base_url() . '/docs/sup_facebook.htm"]dedicated tutorial[/url].
For a demo, see this [url="video tutorial"]https://www.youtube.com/watch?v=HUZ_O5io0F0[/url].
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return ['sup_facebook'];
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
                'PHP curl extension',
                'PHP sessions extension',
                'SSL',
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
    public function get_default_icon()
    {
        return 'themes/default/images/icons/links/facebook.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'adminzone/pages/minimodules_custom/facebook_oauth.php',
            'facebook_connect.php',
            'lang_custom/EN/facebook.ini',
            'sources_custom/blocks/main_facebook_comments.php',
            'sources_custom/blocks/main_facebook_like.php',
            'sources_custom/blocks/main_facebook_page.php',
            'sources_custom/cns_field_editability.php',
            'sources_custom/cns_members.php',
            'sources_custom/facebook_connect.php',
            'sources_custom/hooks/modules/members/facebook.php',
            'sources_custom/hooks/systems/addon_registry/facebook_support.php',
            'sources_custom/hooks/systems/config/facebook_allow_signups.php',
            'sources_custom/hooks/systems/config/facebook_appid.php',
            'sources_custom/hooks/systems/config/facebook_auto_syndicate.php',
            'sources_custom/hooks/systems/config/facebook_member_syndicate_to_page.php',
            'sources_custom/hooks/systems/config/facebook_secret_code.php',
            'sources_custom/hooks/systems/config/facebook_sync_avatar.php',
            'sources_custom/hooks/systems/config/facebook_sync_dob.php',
            'sources_custom/hooks/systems/config/facebook_sync_email.php',
            'sources_custom/hooks/systems/config/facebook_sync_username.php',
            'sources_custom/hooks/systems/config/facebook_syndicate.php',
            'sources_custom/hooks/systems/config/facebook_uid.php',
            'sources_custom/hooks/systems/login_providers/facebook.php',
            'sources_custom/hooks/systems/page_groupings/facebook.php',
            'sources_custom/hooks/systems/startup/facebook.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_ACCESS_TOKEN.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_FINISHING_PROFILE.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_LOGGED_OUT.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_UID.php',
            'sources_custom/hooks/systems/symbols/USER_FB_CONNECT.php',
            'sources_custom/hooks/systems/syndication/facebook.php',
            'sources_custom/users_active_actions.php',
            'sources_custom/users.php',
            'themes/default/javascript_custom/facebook_support.js',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_COMMENTS.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_LIKE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_PAGE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_SCREEN_ACTIONS.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS_NO.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS.tpl',
            'themes/default/templates_custom/BLOCK_TOP_LOGIN.tpl',
            'themes/default/templates_custom/CNS_GUEST_BAR.tpl',
            'themes/default/templates_custom/FACEBOOK_FOOTER.tpl',
            'themes/default/templates_custom/LOGIN_SCREEN.tpl',
            'themes/default/templates_custom/MEMBER_FACEBOOK.tpl',

            'sources_custom/facebook/composer.json',
            'sources_custom/facebook/composer.lock',
            'sources_custom/facebook/.htaccess',
            'sources_custom/facebook/index.html',
            'sources_custom/facebook/vendor/autoload.php',
            'sources_custom/facebook/vendor/composer/autoload_classmap.php',
            'sources_custom/facebook/vendor/composer/autoload_files.php',
            'sources_custom/facebook/vendor/composer/autoload_namespaces.php',
            'sources_custom/facebook/vendor/composer/autoload_psr4.php',
            'sources_custom/facebook/vendor/composer/autoload_real.php',
            'sources_custom/facebook/vendor/composer/autoload_static.php',
            'sources_custom/facebook/vendor/composer/ClassLoader.php',
            'sources_custom/facebook/vendor/composer/.htaccess',
            'sources_custom/facebook/vendor/composer/index.html',
            'sources_custom/facebook/vendor/composer/installed.json',
            'sources_custom/facebook/vendor/composer/LICENSE',
            'sources_custom/facebook/vendor/facebook/graph-sdk/CODE_OF_CONDUCT.md',
            'sources_custom/facebook/vendor/facebook/graph-sdk/composer.json',
            'sources_custom/facebook/vendor/facebook/graph-sdk/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/LICENSE',
            'sources_custom/facebook/vendor/facebook/graph-sdk/phpcs.xml.dist',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Authentication/AccessToken.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Authentication/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Authentication/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Authentication/OAuth2Client.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/autoload.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookAuthenticationException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookAuthorizationException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookClientException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookOtherException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookResponseException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookResumableUploadException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookSDKException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookServerException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookThrottleException.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Exceptions/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookApp.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookBatchRequest.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookBatchResponse.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookClient.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Facebook.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookRequest.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FacebookResponse.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/FacebookFile.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/FacebookResumableUploader.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/FacebookTransferChunk.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/FacebookVideo.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/FileUpload/Mimetypes.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/Birthday.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/Collection.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphAchievement.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphAlbum.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphApplication.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphCoverPhoto.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphEdge.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphEvent.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphGroup.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphList.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphLocation.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphNodeFactory.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphNode.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphObjectFactory.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphObject.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphPage.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphPicture.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphSessionInfo.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/GraphUser.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/GraphNodes/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookCanvasHelper.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookJavaScriptHelper.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookPageTabHelper.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookRedirectLoginHelper.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookSignedRequestFromInputHelper.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Helpers/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/certs/DigiCertHighAssuranceEVRootCA.pem',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/certs/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/certs/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookCurlHttpClient.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookCurl.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookGuzzleHttpClient.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookHttpClientInterface.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookStreamHttpClient.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/FacebookStream.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/HttpClientsFactory.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/HttpClients/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/GraphRawResponse.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/RequestBodyInterface.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/RequestBodyMultipart.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Http/RequestBodyUrlEncoded.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/FacebookMemoryPersistentDataHandler.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/FacebookSessionPersistentDataHandler.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/PersistentDataFactory.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PersistentData/PersistentDataInterface.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/polyfills.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/McryptPseudoRandomStringGenerator.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/OpenSslPseudoRandomStringGenerator.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/PseudoRandomStringGeneratorFactory.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/PseudoRandomStringGeneratorInterface.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/PseudoRandomStringGeneratorTrait.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/RandomBytesPseudoRandomStringGenerator.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/PseudoRandomString/UrandomPseudoRandomStringGenerator.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/SignedRequest.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Url/FacebookUrlDetectionHandler.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Url/FacebookUrlManipulator.php',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Url/.htaccess',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Url/index.html',
            'sources_custom/facebook/vendor/facebook/graph-sdk/src/Facebook/Url/UrlDetectionInterface.php',
            'sources_custom/facebook/vendor/.htaccess',
            'sources_custom/facebook/vendor/index.html',
        ];
    }
}
