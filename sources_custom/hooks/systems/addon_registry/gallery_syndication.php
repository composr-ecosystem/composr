<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

/**
 * Hook class.
 */
class Hook_addon_registry_gallery_syndication
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
        return 'Development'; // TODO #3166 Turn back to 'Third Party Integration' once fixed
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
        return 'Common Public Attribution License';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Automatically syndicate out videos to YouTube, keeping them in sync, with the option to actually host the videos on these services via an embedded player (giving you free video transcoding and hosting).

For YouTube syndication you must first:
1) Configure the Google API API Key in the configuration (Admin Zone > Configuration > Setup > Composr API options > Google API)
2) Make sure that YouTube Data API is enabled on Google\'s end
3) Connect YouTube oAuth from Admin Zone > Setup > API access
4) [url="Increase your YouTube video length limit"]http://support.google.com/youtube/bin/answer.py?hl=en&answer=71673[/url].';
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
                'PHP simplexml extension',
                'galleries',
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
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/gallery_syndication.php',
            'sources_custom/hooks/systems/oauth/index.html',
            'sources_custom/hooks/systems/oauth/.htaccess',
            'sources_custom/hooks/systems/oauth/youtube.php',
            'lang_custom/EN/gallery_syndication.ini',
            'sources_custom/gallery_syndication.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
            'sources_custom/hooks/systems/cron/gallery_syndication.php',
            'sources_custom/hooks/systems/config/gallery_sync_selectcode.php',
            'sources_custom/hooks/systems/config/gallery_sync_orphaned_handling.php',
            'sources_custom/hooks/systems/config/video_sync_transcoding.php',
            'sources_custom/hooks/systems/health_checks/youtube.php',
        ];
    }
}
