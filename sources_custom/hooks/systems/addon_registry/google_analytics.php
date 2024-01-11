<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_analytics
 */

/**
 * Hook class.
 */
class Hook_addon_registry_google_analytics
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
        // Best to just categorise properly as it's not bundled
        //return is_maintained('google_analytics') ? 'Third Party Integration' : 'Development';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Adds Google Analytics to the stats addon.

Setup instructions...
[list="1"]
[*] Set up oAuth for Google Analytics at Admin Zone > Setup > Setup API Access.
[*] Find the numeric ID of the Google Analytics property you need to integrate with (e.g. 12345678), then configure the "Google Analytics View ID" option at Admin Zone > Setup > Configuration > Site options > Logging. Note this number has nothing to do with the identifier that starts [tt]UA[/tt].
[/list]

The addon provides a [tt]main_staff_google_analytics[/tt] block for showing analytic data of your choice (suggested for adding to the Admin Zone dashboard). You can use the default [tt]main_staff_stats_graph[/tt] but this doesn\'t come with tabbing support, and the [tt]main_staff_google_analytics[/tt] tabbing support has special code required to make tabbing work with Google Analytics\'s JavaScript-based graph rendering.
There are [i]metric[/i] and [i]days[/i] parameters you may want to use for the block:
 - [b]metric[/b] is a comma-separated list of any of the following: [tt]hits[/tt], [tt]speed[/tt], [tt]browsers[/tt], [tt]device_types[/tt], [tt]screen_sizes[/tt], [tt]countries[/tt], [tt]languages[/tt], [tt]referrers[/tt], [tt]referrers_social[/tt], [tt]referral_mediums[/tt], [tt]popular_pages[/tt], [tt]keywords[/tt]. [tt]keywords[/tt] is only available if Google Search Console is configured. Tabs will be used if multiple metrics are requested. If you don\'t specify this parameter it will default to a reasonable selection.
 - [b]days[/b] is a number, the number of days to show data for (initially). If you don\'t specify this parameter it will default to 31.

You can always see all metrics from Admin Zone > Audit > Site statistics > Google Analytics simplified view.
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
        return 'themes/default/images/icons/buttons/search.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/google_analytics.php',
            'lang_custom/EN/google_analytics.ini',
            'sources_custom/google_analytics.php',
            'sources_custom/hooks/modules/admin_stats/google_analytics.php',
            'sources_custom/hooks/modules/admin_stats/.htaccess',
            'sources_custom/hooks/modules/admin_stats/index.html',
            'sources_custom/miniblocks/main_staff_google_analytics.php',
            'themes/default/templates_custom/GOOGLE_ANALYTICS.tpl',
            'themes/default/templates_custom/GOOGLE_ANALYTICS_TABS.tpl',
            'themes/default/templates_custom/GOOGLE_TIME_PERIODS.tpl',
            'themes/default/javascript_custom/google_analytics.js',
            'sources_custom/hooks/systems/config/ga_property_view_id.php',
            'sources_custom/hooks/systems/trusted_sites/google_analytics.php',
        ];
    }
}
