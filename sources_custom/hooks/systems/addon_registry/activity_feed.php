<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_addon_registry_activity_feed
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
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
        return 'New Features';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Warburton / Chris Graham / Paul / Naveen';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'base64.js is from http://www.webtoolkit.info'
        );
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
        return 'Displays a self-updating feed of logged site activity, with options to filter the contents. Also includes a block for entering new activities directly into the feed, allowing a "status update" functionality.

These blocks are put on the member profile tabs by default, but may also be called up on other areas of the site.

If the chat addon is installed, "status" posts can be restricted to only show for buddies.

If the Facebook of Twitter addons are installed then the system can syndicate out activities to the user\'s Twitter and Facebook followers.

The blocks provided are [tt]main_activities[/tt] and the status entry box is called [tt]main_activities_state[/tt].

[code="Comcode"][block="Goings On" max="20" grow="0" mode="all"]main_activities[/block][/code]
...will show a feed with a title "Goings On" containing the last 20 activities, old activities will "fall off the bottom" (grow="0") as new ones are loaded via AJAX and there is no filtering on what is shown. (mode="all").

[code="Comcode"][block="Say Something"]main_activities_state[/block][/code]
...will show a status update box with the title "Say Something".';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'sup_facebook',
        );
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'all_icons',
            ),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/spare/activity.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/privacy/activity_feed.php',
            'sources_custom/hooks/systems/addon_registry/activity_feed.php',
            'sources_custom/hooks/systems/notifications/activity.php',
            'sources_custom/hooks/systems/rss/activities.php',
            'data_custom/activities_updater.php',
            'data_custom/activities_removal.php',
            'data_custom/activities_handler.php',
            'data_custom/latest_activity.bin',
            'lang_custom/EN/activities.ini',
            'sources_custom/blocks/main_activities_state.php',
            'sources_custom/blocks/main_activities.php',
            'sources_custom/activities_submission.php',
            'sources_custom/hooks/systems/activities/activities.php',
            'themes/default/javascript_custom/base64.js',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITIES_STATE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITIES.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITIES_XML.tpl',
            'themes/default/templates_custom/ACTIVITY.tpl',
            'themes/default/templates_custom/CNS_MEMBER_PROFILE_ACTIVITIES.tpl',
            'themes/default/css_custom/activities.css',
            'sources_custom/hooks/systems/profiles_tabs/activities.php',
            'sources_custom/hooks/systems/profiles_tabs/posts.php',
            'sources_custom/hooks/systems/config/syndicate_site_activity_default.php',
            'sources_custom/activities.php',
            'themes/default/javascript_custom/activity_feed.js',
            'sources_custom/hooks/systems/syndication/.htaccess',
            'sources_custom/hooks/systems/syndication/index.html',
        );
    }
}
