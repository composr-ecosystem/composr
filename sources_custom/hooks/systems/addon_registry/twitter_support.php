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

/**
 * Hook class.
 */
class Hook_addon_registry_twitter_support
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
        return 'Jason Verhagen & Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [
            'Class by Tijs Verkoyen',
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
        return 'Integrate your Twitter feed into your web site, via a block.

[list]
[*] Set up an app on Twitter
[*] Configure the Twitter settings in Composr (Admin Zone > Setup > Configuration > Composr API options > Twitter)
[*] Set up oAuth for Twitter (Admin Zone > Setup > Twitter authorisation)
[*] Use Comcode like:
[code="Comcode"]
[block screen_name="yourname"]twitter_feed[/block]
[/code]
[/list]';
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
                'PHP curl extension',
            ],
            'recommends' => [
                'activity_feed',
                'hybridauth',
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
        return 'themes/default/images/icons/links/twitter.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/twitter_support.php',
            'sources_custom/twitter.php',
            'lang_custom/EN/twitter.ini',
            'sources_custom/hooks/systems/config/twitter_api_key.php',
            'sources_custom/hooks/systems/config/twitter_api_secret.php',
            'sources_custom/hooks/systems/config/twitter_allow_signups.php',
            'sources_custom/hooks/systems/health_checks/twitter.php',
            'sources_custom/hooks/systems/hybridauth/twitter.php',
            'adminzone/pages/minimodules_custom/twitter_oauth.php',
            'sources_custom/hooks/systems/page_groupings/twitter.php',

            'sources_custom/blocks/twitter_feed.php',
            'themes/default/templates_custom/BLOCK_TWITTER_FEED.tpl',
            'themes/default/templates_custom/BLOCK_TWITTER_FEED_TWEET.tpl',
            'themes/default/images_custom/twitter_feed/bird_black_16.png',
            'themes/default/images_custom/twitter_feed/bird_black_32.png',
            'themes/default/images_custom/twitter_feed/bird_black_48.png',
            'themes/default/images_custom/twitter_feed/bird_blue_16.png',
            'themes/default/images_custom/twitter_feed/bird_blue_32.png',
            'themes/default/images_custom/twitter_feed/bird_blue_48.png',
            'themes/default/images_custom/twitter_feed/bird_gray_16.png',
            'themes/default/images_custom/twitter_feed/bird_gray_32.png',
            'themes/default/images_custom/twitter_feed/bird_gray_48.png',
            'themes/default/images_custom/twitter_feed/favorite.png',
            'themes/default/images_custom/twitter_feed/favorite_hover.png',
            'themes/default/images_custom/twitter_feed/favorite_on.png',
            'themes/default/images_custom/twitter_feed/twitter_feed_icon.png',
            'themes/default/images_custom/twitter_feed/index.html',
            'themes/default/images_custom/twitter_feed/reply.png',
            'themes/default/images_custom/twitter_feed/reply_hover.png',
            'themes/default/images_custom/twitter_feed/retweet.png',
            'themes/default/images_custom/twitter_feed/retweet_hover.png',
            'themes/default/images_custom/twitter_feed/retweet_on.png',
            'sources_custom/hooks/systems/config/twitterfeed_update_time.php',
            'themes/default/javascript_custom/twitter_feed.js',
        ];
    }
}
