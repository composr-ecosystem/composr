<?php

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Jason L Verhagen (jlverhagen@tfo.net)
 * @package    youtube_channel_integration_block
 */

/**
 * Hook class.
 */
class Hook_addon_registry_youtube_channel_integration_block
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
        return '11'; // addon_version_auto_update c9d32f70b08e895d5d2b2a83ad1f52e4
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
        return 'Third Party Integration'; // Change to 'Development' if the integration breaks and is not fixed
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Jason Verhagen';
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
        return 'Creative Commons Attribution 3.0 Unported License (CC BY 3.0)';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Integrate YouTube channels into your web site. Specify a YouTube channel or user name and some other parameters and you can integrate videos and video info in your web site. The block can automatically update with new content as it is added to the YouTube channel.

You must first configure Google/YouTube API access:
1) Configure the Google API API Key in the configuration (Admin Zone > Configuration > Setup > Composr API options > Google API)
2) Make sure that YouTube Data API is enabled on Google\'s end
3) Connect YouTube oAuth from Admin Zone > Setup > API access
4) [url="Increase your YouTube video length limit"]http://support.google.com/youtube/bin/answer.py?hl=en&answer=71673[/url].
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
            'requires' => [],
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
        return 'themes/default/images_custom/youtube_channel_integration/youtube_channel_integration_icon.png';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/youtube_channel.ini',
            'sources_custom/blocks/youtube_channel.php',
            'sources_custom/hooks/systems/addon_registry/youtube_channel_integration_block.php',
            'sources_custom/hooks/systems/config/youtube_channel_block_update_time.php',
            'themes/default/images_custom/youtube_channel_integration/index.html',
            'themes/default/images_custom/youtube_channel_integration/star_empty.gif',
            'themes/default/images_custom/youtube_channel_integration/star_full.gif',
            'themes/default/images_custom/youtube_channel_integration/star_half.gif',
            'themes/default/images_custom/youtube_channel_integration/youtube_channel_integration_icon.png',
            'themes/default/templates_custom/BLOCK_YOUTUBE_CHANNEL.tpl',
            'themes/default/templates_custom/BLOCK_YOUTUBE_CHANNEL_VIDEO.tpl',
        ];
    }

    /**
     * Install the addon.
     *
     * @param  ?float $upgrade_major_minor From what major/minor version we are upgrading (null: new install)
     * @param  ?integer $upgrade_patch From what patch version of $upgrade_major_minor we are upgrading (null: new install)
     */
    public function install(?float $upgrade_major_minor = null, ?int $upgrade_patch = null)
    {
        // If old config option exists from older version of addon, remove it
        if ($upgrade_major_minor !== null) { // LEGACY
            delete_config_option('youtube_channel_block_update_time');
        }
    }
}
