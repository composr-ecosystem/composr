<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    youtube_channel_integration_block
 */

/**
 * Hook class.
 */
class Hook_addon_registry_youtube_channel_integration_block
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
        return 'Creative Commons Attribution 3.0 Unported License (CC BY 3.0)';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Integrate YouTube channels into your web site. Specify a YouTube channel or user name and some other parameters and you can integrate videos and video info in your web site. The block can automatically update with new content as it is added to the YouTube channel.';
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
                'PHP JSON Extension',
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
        return 'themes/default/images_custom/youtube_channel_integration/youtube_channel_integration_icon.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/youtube_channel_integration_block.php',
            'lang_custom/EN/youtube_channel.ini',
            'sources_custom/blocks/youtube_channel.php',
            'themes/default/images_custom/youtube_channel_integration/star_empty.gif',
            'themes/default/images_custom/youtube_channel_integration/star_full.gif',
            'themes/default/images_custom/youtube_channel_integration/star_half.gif',
            'themes/default/images_custom/youtube_channel_integration/youtube_channel_integration_icon.png',
            'themes/default/images_custom/youtube_channel_integration/index.html',
            'themes/default/templates_custom/BLOCK_YOUTUBE_CHANNEL.tpl',
            'themes/default/templates_custom/BLOCK_YOUTUBE_CHANNEL_STYLE.tpl',
            'sources_custom/hooks/systems/config/youtube_channel_block_api_key.php',
            'sources_custom/hooks/systems/config/youtube_channel_block_update_time.php',
        );
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        //if old config option exists from older version of addon, remove it
        if (get_option('channel_update_time', true) !== null) {
            delete_config_option('channel_update_time');
        }
    }
}
