<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Icon by leslienayibe',
            'Class by Tijs Verkoyen',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Syndicate site activities to Twitter.

Activity can be syndicated:
1) On a site level, for any news or calendar events added by the staff
2) By individual users to their own Twitter accounts (if they have authorised this via the activity tab in their profile)

Set up is a little tricky:
1) you need to set up an application on Twitter, with callback URLs of https://yourbaseurl/adminzone/twitter-oauth.htm and https://yourbaseurl/members/view.htm (or whatever the correct URLs are in your current URL Scheme)
2) you need to configure Composr to use the Twitter API via Admin Zone > Setup > Configuration > Composr APIs > Twitter syndication
3) you need to authorise the site via Admin Zone > Setup > Twitter syndication

Once configured then syndication will be an option when adding news posts or calendar events, and will happen automatically for any users who have set up their own authorisation.';
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
                'PHP CuRL extension',
            ),
            'recommends' => array('activity_feed'),
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
        return 'themes/default/images_custom/icons/48x48/menu/twitter.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/twitter.png',
            'themes/default/images_custom/icons/48x48/menu/twitter.png',
            'sources_custom/hooks/systems/addon_registry/twitter_support.php',
            'sources_custom/twitter.php',
            'adminzone/pages/minimodules_custom/twitter_oauth.php',
            'lang_custom/EN/twitter.ini',
            'sources_custom/hooks/systems/syndication/twitter.php',
            'sources_custom/hooks/systems/page_groupings/twitter.php',
            'sources_custom/hooks/systems/config/twitter_api_key.php',
            'sources_custom/hooks/systems/config/twitter_api_secret.php',
        );
    }
}
