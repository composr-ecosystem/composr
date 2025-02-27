<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_addon_registry_cns_tapatalk
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
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update 89051d469ad5dd93aff7b23fd6741249
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
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        // Best to just categorise properly as it's not bundled
        //return is_maintained('tapatalk') ? 'Community' : 'Development';
        return 'Community';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Core Development Team';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Parts of the implementation are copyright to Quoord systems (the Tapatalk company). See mobiquo/license_agreement.txt',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return '[url="Tapatalk"]http://tapatalk.com[/url] is a popular third-party forum app for smartphones (available on all major mobile platforms). The Composr {$IS_MAINTAINED,tapatalk,Tapatalk addon} allows the Tapatalk app to connect seamlessly to Composr forums. The addon does this by providing an implementation of the Tapatalk API.

Tapatalk is free. It has optional advertisements and they will share advertising revenue with you. Tapatalk provide commercial customisation options also, for customisation of high-end forums ("BYO").

Some configuration options can be found within the normal Composr configuration module.

[title="2"]Is Tapatalk right for me?[/title]

Tapatalk asks users to separately register for Tapatalk, and then the individual forum(s) that they are subscribing to.

It will also recommend similar forums to your users which could potentially be competitors to yours.

Therefore please decide if you accept this or not before going down the Tapatalk route. It is not for everybody.

If you use the BYO service you can avoid this issue.

Mobile development is costly, which is why Tapatalk is a great thing for many site owners.

Composr provides a mobile mode out-of-the-box, which smartphones will use. However, mobile websites are significantly inferior to native mobile apps for a number of reasons, but particularly: responsiveness of user interface, bandwidth requirements, better access to phone capabilities. Mobile website capabilities are improving, but the gap will never close substantially.

[title="2"]Setup[/title]

First install the addon.

A Tapatalk site also needs setting up on Tapatalk\'s website, so that you have an API key. You also need to fill that API key in the Composr configuration.

When testing functionality you may wish to leave your site in Incognito mode so that it doesn\'t come up in searches.

[title="2"]Limitations[/title]

As Tapatalk is a third-party product, there are a number of limitations we need to work within. This section is thorough, but don\'t think that Tapatalk is really limited. The functionality made available via Tapatalk is very well tuned for mobile usage, and only on relatively rare occasions (or when wanting a decent screen and keyboard!) will users have to go to the normal Composr forum implementation.

The limitations...

The addon only works for users of Conversr. If you use a third-party forum with Composr then Tapatalk needs to connect to that forum independently of Composr. This will work for many popular forums which Tapatalk also supports.

The addon has not been tested with multi-site-network installs of Composr. It assumes your Composr website is standalone.

This addon assumes you are using MySQL (or MariaDB). It may potentially work on other database systems, but we have no plans to test it on them currently.

Your website is assumed to only be running a single language. We may have bugs if you are translating content into multiple languages.

You should keep your forum quantity to a reasonable number because Tapatalk\'s APIs may not work efficiently if you have 100s of forums.

Tapatalk does not support the following Composr forum features:
 - Multi-moderations
 - Having both photos or avatars (precedence is on avatars)
 - Normal signatures (there is Tapatalk signature support, but it works via cut-down signatures saved into posts via the Tapatalk app -- normal Composr signatures won\'t display [except in cut-down form when viewing users directly], and shouldn\'t due to space considerations)
 - Forced rule acceptance when creating private topics with another member (the recipients rules)
 - Complex HTML posts (posts are simplified down); for example, image-links are specifically prohibited, and no HTML requiring CSS or JavaScript will function
 - Post Templates
 - Topic Polls (we will say when there is a poll, but won\'t show the results and can\'t allow voting)
 - Rank images (including anything non-bundled addons inject, such as achievements or karma)
 - Per-forum rules
 - Join-rules (although you can workaround it by configuring a topic the new member is directed to after joining)
 - Browsing usergroups and usergroup memberships
 - Threaded topics (but we will show post quoting instead)
 - Topics cannot exist in the root forum (there is no root forum in Tapatalk; so if there are any topics in there then a new virtual forum will be shown to encapsulate them)
 - Marking posts as emphasised
 - Creating inline private posts ("whispers")
 - CAPTCHA for guests while joining or making topics/posts (we therefore have a separate Tapatalk option to disable guest access, to avoid potential spam)
 - Parental Consent
 - Inline creation of introduction message while joining the site
 - Categorisation of private topics (except the \'trash\' category, which is automatically used for deletion)
 - Browsing post-history
 - Not all custom field input types are supported, and only required ones will show on the join form
 - Full account editing
 - Special rendering for comment topics or support tickets

We support almost everything in Tapatalk, but there are just a few exceptions:
 - Per-forum icons (we don\'t have this in Composr)
 - Topic prefixes (we achieve these via multi-moderations in Composr, but those are not supported)
 - Naming emoticons (we don\'t have this in Composr and feel this is bloat)
 - Stubs for deleted and moved topics (we don\'t have this in Composr and feel these are messy -- we send out notifications to users instead)
 - Soft deletion of posts and topics (we achieve these via multi-moderations/post-history in Composr, but those are not supported) -- manually move to Trash instead
 - Ignoring users within topics (it\'s probably a bad idea because topics would get very confusing)
 - "Active members" (we felt it would end up a bit arbitrary and unnecessary -- there\'s no clear understood definition of what an active member is)
 - Password protected forums (we have an acceptance message with a corresponding question in Composr -- but there\'s no way to display the message, so we cannot map it directly as a password feature -- and pure password protection is better replaced with proper use of forum permissions anyway)
 - You cannot view a private topic that you\'ve already left
 - You cannot leave a private topic that you are an original participant in

[title="2"]Special notes[/title]

A Tapatalk "thank" (thanking a post) works by giving a configured number of points to a member. Tapatalk itself doesn\'t have support for Composr points, but this is a nice simple way of giving them that works well for a streamlined smartphone interface.

Tapatalk may use different terminology to Composr:
 - "Notifications" are "Subscriptions" in Tapatalk
 - "Validation" is "Approving" in Tapatalk
 - "Joining" is "Signing up" in Tapatalk
 - "Private topics" is "Private Conversations" in Tapatalk
 - "Emoticons" is "Smileys" in Tapatalk

Attachments in Tapatalk are restricted to images, and shown separately to the post bodies.

You cannot do post rating in Tapatalk, but you can like a post. This is the same as rating a post maximally. Actually in Composr by default we implement the same, but Composr provides both mechanisms as options.

Content which is not validated is not shown inline like it is in Composr (to posters or those with permission to view it). It is shown from special moderation areas.

Redirect forums are supported, if they point to an external URL. Jumps between forums are not supported (such redirect forums will be skipped).

Forum groupings are supported via extended nesting. If all the forums on a single level are in the same grouping then we skip this additional nesting.

[title="2"]Developer notes[/title]

The mobiquo folder is where most of the code is kept. This is the default folder name for a Tapatalk implementation.

Tapatalk\'s API is documented on Tapatalk\'s website.

The Tapatalk implementation is organised by API category. The API functions (aka methods, aka endpoints) call read/write classes. The classes generally are Tapatalk-independent, while the direct API functions convert XML-RPC inputs and formulate things into the XML-RPC and precise structure that Tapatalk requires.

To help with security we are using Composr\'s [tt]cms_verify_parameters_phpdoc[/tt] function to check API calls. We also check input method signatures if in development mode (i.e. running from Git); see [tt]server_define.php for this[/tt].

Tapatalk uses XML-RPC for communication. A simple test harness and testing framework is included for programmers. [tt]call_mobiquo_test[/tt] is used to call a test in the framework.

Composr has been configured to generate any errors in Tapatalk\'s XML-RPC format, via the enabling and catching/handling of Exceptions.

For live debugging you may wish to consider using [url="Charles Proxy"]http://www.charlesproxy.com/download/[/url]. Charles Proxy lets you log and filter all requests coming from a mobile device. You do need to make sure the mobile device is on the same wi-fi network as your development machine, and configure the mobile device to use your development machine in its proxy settings.

At the time of writing Tapatalk cannot produce web-links to Composr posts/topics/forums, as this is hard-coded into the Tapatalk client for all forum software they support, rather than coded up via API implementations like we have made.

[title="2"]Push notifications[/title]

Notifications are sent out to Tapatalk users based on various actions within Composr. Quotes, mentions, new private topics, and private topic replies, result in notifications to the members involved. New topics result in Tapatalk notifications for any members who have set any kind of Composr notification on the associated forum. Topic replies are the same, but recognise when Composr notifications were set on the associated topic too.

Tapatalk notifications relay through Tapatalk\'s own servers. Tapatalk knows which Composr member IDs are associated with which device IDs. We only relay out notifications for members who have signed into Tapatalk at least once.



For a demo, see this [url="video tutorial"]https://www.youtube.com/watch?v=SAGuj2mgkRo[/url].
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'tut_mobile',
        ];
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
                'Conversr',
                'cns_forum',
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
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/tapatalk.ini',
            'mobiquo/api/.htaccess',
            'mobiquo/api/account.php',
            'mobiquo/api/attachment.php',
            'mobiquo/api/forum.php',
            'mobiquo/api/index.html',
            'mobiquo/api/misc.php',
            'mobiquo/api/moderation.php',
            'mobiquo/api/post.php',
            'mobiquo/api/private_conversation.php',
            'mobiquo/api/private_message.php',
            'mobiquo/api/push.php',
            'mobiquo/api/search.php',
            'mobiquo/api/social.php',
            'mobiquo/api/subscription.php',
            'mobiquo/api/topic.php',
            'mobiquo/api/user.php',
            'mobiquo/avatar.php',
            'mobiquo/include/.htaccess',
            'mobiquo/include/common_functions.php',
            'mobiquo/include/forum_functions.php',
            'mobiquo/include/index.html',
            'mobiquo/include/mobiquo_functions.php',
            'mobiquo/include/permission_functions.php',
            'mobiquo/include/pm_functions.php',
            'mobiquo/include/server_define.php',
            'mobiquo/include/test_functions.php',
            'mobiquo/index.html',
            'mobiquo/lib/.htaccess',
            'mobiquo/lib/TapatalkBasePush.php',
            'mobiquo/lib/TapatalkPush.php',
            'mobiquo/lib/classTTCipherEncrypt.php',
            'mobiquo/lib/classTTConnection.php',
            'mobiquo/lib/index.html',
            'mobiquo/lib/mobiquo.php',
            'mobiquo/lib/mobiquo_json.php',
            'mobiquo/lib/mobiquo_post.php',
            'mobiquo/lib/mobiquo_xmlrpc.php',
            'mobiquo/lib/xmlrpc.php',
            'mobiquo/lib/xmlrpcs.php',
            'mobiquo/license_agreement.txt',
            'mobiquo/mbqClass/.htaccess',
            'mobiquo/mbqClass/acl/.htaccess',
            'mobiquo/mbqClass/acl/index.html',
            'mobiquo/mbqClass/acl/member_acl.php',
            'mobiquo/mbqClass/index.html',
            'mobiquo/mbqClass/read/.htaccess',
            'mobiquo/mbqClass/read/account_read.php',
            'mobiquo/mbqClass/read/attachment_read.php',
            'mobiquo/mbqClass/read/board_stats.php',
            'mobiquo/mbqClass/read/forum_read.php',
            'mobiquo/mbqClass/read/index.html',
            'mobiquo/mbqClass/read/moderation_read.php',
            'mobiquo/mbqClass/read/pm_read.php',
            'mobiquo/mbqClass/read/post_read.php',
            'mobiquo/mbqClass/read/pt_read.php',
            'mobiquo/mbqClass/read/search_read.php',
            'mobiquo/mbqClass/read/social_read.php',
            'mobiquo/mbqClass/read/subscription_read.php',
            'mobiquo/mbqClass/read/topic_read.php',
            'mobiquo/mbqClass/read/user_read.php',
            'mobiquo/mbqClass/write/.htaccess',
            'mobiquo/mbqClass/write/account_write.php',
            'mobiquo/mbqClass/write/attachment_write.php',
            'mobiquo/mbqClass/write/forum_write.php',
            'mobiquo/mbqClass/write/index.html',
            'mobiquo/mbqClass/write/moderation_write.php',
            'mobiquo/mbqClass/write/pm_write.php',
            'mobiquo/mbqClass/write/post_write.php',
            'mobiquo/mbqClass/write/pt_write.php',
            'mobiquo/mbqClass/write/social_write.php',
            'mobiquo/mbqClass/write/subscription_write.php',
            'mobiquo/mbqClass/write/topics_write.php',
            'mobiquo/mbqClass/write/user_write.php',
            'mobiquo/mobiquo.php',
            'mobiquo/smartbanner/README.md',
            'mobiquo/smartbanner/app.php',
            'mobiquo/smartbanner/appbanner.css',
            'mobiquo/smartbanner/appbanner.js',
            'mobiquo/smartbanner/head.inc.php',
            'mobiquo/smartbanner/images/android-h-bg.jpg',
            'mobiquo/smartbanner/images/android-v-bg.jpg',
            'mobiquo/smartbanner/images/close.png',
            'mobiquo/smartbanner/images/index.html',
            'mobiquo/smartbanner/images/ipad-h-bg.jpg',
            'mobiquo/smartbanner/images/ipad-v-bg.jpg',
            'mobiquo/smartbanner/images/iphone-h-bg.jpg',
            'mobiquo/smartbanner/images/iphone-v-bg.jpg',
            'mobiquo/smartbanner/images/logo.png',
            'mobiquo/smartbanner/images/pad-h-bg.jpg',
            'mobiquo/smartbanner/images/pad-v-bg.jpg',
            'mobiquo/smartbanner/images/star.png',
            'mobiquo/smartbanner/images/tapatalk-banner-logo.png',
            'mobiquo/smartbanner/images/wp-h-bg.jpg',
            'mobiquo/smartbanner/images/wp-v-bg.jpg',
            'mobiquo/smartbanner/images/wrt-h-bg.jpg',
            'mobiquo/smartbanner/images/wrt-v-bg.jpg',
            'mobiquo/smartbanner/index.html',
            'mobiquo/tapatalkdetect.js',
            'mobiquo/tests/.htaccess',
            'mobiquo/tests/account.php',
            'mobiquo/tests/attachment.php',
            'mobiquo/tests/forum.php',
            'mobiquo/tests/index.html',
            'mobiquo/tests/misc.php',
            'mobiquo/tests/moderation.php',
            'mobiquo/tests/post.php',
            'mobiquo/tests/private_conversation.php',
            'mobiquo/tests/private_message.php',
            'mobiquo/tests/push.php',
            'mobiquo/tests/search.php',
            'mobiquo/tests/social.php',
            'mobiquo/tests/subscription.php',
            'mobiquo/tests/topic.php',
            'mobiquo/tests/user.php',
            'mobiquo/upload.php',
            'sources_custom/hooks/systems/addon_registry/cns_tapatalk.php',
            'sources_custom/hooks/systems/comcode_preparse/tapatalk.php',
            'sources_custom/hooks/systems/config/after_edit_mark_unread.php',
            'sources_custom/hooks/systems/config/days_to_keep__tapatalk_log.php',
            'sources_custom/hooks/systems/config/enable_guest_access.php',
            'sources_custom/hooks/systems/config/mark_as_edited.php',
            'sources_custom/hooks/systems/config/points_for_thanking.php',
            'sources_custom/hooks/systems/config/rules_topic_id.php',
            'sources_custom/hooks/systems/config/tapatalk_api_key.php',
            'sources_custom/hooks/systems/config/tapatalk_enable_sync_user.php',
            'sources_custom/hooks/systems/config/tapatalk_joining.php',
            'sources_custom/hooks/systems/config/tapatalk_promote_from_website.php',
            'sources_custom/hooks/systems/logs/tapatalk.php',
            'sources_custom/hooks/systems/startup/tapatalk.php',
            'sources_custom/hooks/systems/upon_query/tapatalk_push.php',
            'themes/default/text_custom/TAPATALK_POST_WRAPPER.txt',
        ];
    }
}
