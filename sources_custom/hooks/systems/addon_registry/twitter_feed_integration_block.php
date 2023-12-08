<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    twitter_feed_integration_block
 */

/**
 * Hook class.
 */
class Hook_addon_registry_twitter_feed_integration_block
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Jason Verhagen';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Common Public Attribution License';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Integrate your Twitter feed into your web site, via a block.

First set up an app on Twitter, then use Comcode like:
[code="Comcode"]
[block consumer_key="xxx" consumer_secret="xxx" access_token="xxx" access_token_secret="xxx" screen_name="yourname"]twitter_feed[/block]
[/code]';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'twitter_support',
                'PHP CuRL Extension',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images_custom/twitter_feed/twitter_feed_icon.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/twitter_feed_integration_block.php',
            'lang_custom/EN/twitter_feed.ini',
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
            'sources_custom/hooks/systems/config/twitterfeed_use_twitter_support_config.php',
        );
    }
}
