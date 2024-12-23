<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update 0d204d63c249f73f9230352ee1e17e70
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
        return 'Community';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Warburton / Chris Graham / Paul / Naveen';
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
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Displays a self-updating feed of logged site activity, with options to filter the contents. Also includes a block for entering new activities directly into the feed, allowing a "status update" functionality.

These blocks are put on the member profile tabs by default, but may also be called up on other areas of the site.

If the chat addon is installed, "status" posts can be restricted to only show for buddies.

If the Hybridauth addon is installed then the system can syndicate out activities to configured social media destinations.

The blocks provided are [tt]main_activity_feed[/tt] and the status entry box is called [tt]main_activity_feed_state[/tt].

[code="Comcode"][block="Goings On" max="20" grow="0" mode="all"]main_activity_feed[/block][/code]
...will show a feed with a title "Goings On" containing the last 20 activities, old activities will "fall off the bottom" (grow="0") as new ones are loaded via AJAX and there is no filtering on what is shown. (mode="all").

[code="Comcode"][block="Say Something"]main_activity_feed_state[/block][/code]
...will show a status update box with the title "Say Something".';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'sup_facebook',
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
                //'core_all_icons',
            ],
            'recommends' => [
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
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/spare/activity.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/activity_feed_handler.php',
            'data_custom/activity_feed_removal.php',
            'data_custom/activity_feed_updater.php',
            'lang_custom/EN/activity_feed.ini',
            'sources_custom/activity_feed.php',
            'sources_custom/activity_feed_submission.php',
            'sources_custom/blocks/main_activity_feed.php',
            'sources_custom/blocks/main_activity_feed_state.php',
            'sources_custom/hooks/systems/addon_registry/activity_feed.php',
            'sources_custom/hooks/systems/config/syndicate_site_activity_default.php',
            'sources_custom/hooks/systems/notifications/activity_feed.php',
            'sources_custom/hooks/systems/privacy/activity_feed.php',
            'sources_custom/hooks/systems/profiles_tabs/activity_feed.php',
            'sources_custom/hooks/systems/profiles_tabs/posts.php',
            'sources_custom/hooks/systems/rss/activity_feed.php',
            'sources_custom/hooks/systems/syndication/activity_feed.php',
            'themes/default/css_custom/activity_feed.css',
            'themes/default/javascript_custom/activity_feed.js',
            'themes/default/templates_custom/ACTIVITY_FEED_ACTIVITY.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITY_FEED.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITY_FEED_STATE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACTIVITY_FEED_XML.tpl',
            'themes/default/templates_custom/CNS_MEMBER_PROFILE_ACTIVITY_FEED.tpl',
        ];
    }
}
