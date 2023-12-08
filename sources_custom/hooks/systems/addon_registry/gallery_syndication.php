<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
            'Vimeo class by Vimeo',
        );
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
        return 'Automatically syndicate out videos to YouTube or Vimeo, keeping them in sync, with the option to actually host the videos on these services via an embedded player (giving you free video transcoding and hosting).

Unfortunately YouTube support is [url="currently not functional"]https://compo.sr/tracker/view.php?id=3166[/url].';
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
                'PHP5.2',
                'PHP mhash extension',
                'PHP cURL extension',
                'galleries',
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/gallery_syndication.php',
            'adminzone/pages/minimodules_custom/vimeo_oauth.php',
            'adminzone/pages/minimodules_custom/youtube_oauth.php',
            'lang_custom/EN/gallery_syndication.ini',
            'lang_custom/EN/oauth.ini',
            'lang_custom/EN/gallery_syndication_youtube.ini',
            'lang_custom/EN/gallery_syndication_vimeo.ini',
            'sources_custom/gallery_syndication.php',
            'sources_custom/hooks/modules/video_syndication/vimeo.php',
            'sources_custom/hooks/modules/video_syndication/youtube.php',
            'sources_custom/hooks/systems/cron/gallery_syndication.php',
            'sources_custom/hooks/systems/page_groupings/gallery_syndication.php',
            'sources_custom/oauth.php',
            'sources_custom/vimeo.php',
            'sources_custom/hooks/systems/config/vimeo_client_id.php',
            'sources_custom/hooks/systems/config/vimeo_client_secret.php',
            'sources_custom/hooks/systems/config/youtube_client_id.php',
            'sources_custom/hooks/systems/config/youtube_client_secret.php',
            'sources_custom/hooks/systems/config/youtube_developer_key.php',
            'sources_custom/hooks/systems/config/video_sync_transcoding.php',
            'sources_custom/hooks/systems/config/gallery_sync_selectcode.php',
            'sources_custom/hooks/systems/config/gallery_sync_orphaned_handling.php',
        );
    }
}
