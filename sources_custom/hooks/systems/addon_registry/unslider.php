<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    unslider
 */

/**
 * Hook class.
 */
class Hook_addon_registry_unslider
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
        return '11.0.1'; // addon_version_auto_update c18f5dfcf72d5f8967ab203b0bc151e8
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
        return 'Graphical';
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
        return [
            '"@idiot"',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'WTFPL License';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'A customisable slider for displaying custom HTML or media.

The addon contains a new block, main_unslider. The block is based on the Unslider jQuery plugin. It includes a number of slides and lets you move between them, either on a timer or manually. The contents of each slide is sourced from a specially-named Composr page.

Here is an example of the block Comcode:
[code][block pages="slide1,slide2,slide3,slide4,slide5,slide6" width="100%" height="350px" buttons="1" delay="3000" speed="500" keypresses="0"]main_unslider[/block][/code]

The [tt]pages[/tt] parameter refers to suffixes on a standard naming scheme. [tt]slide1[/tt] is the page [tt]_unslider_slide1[/tt].

These six example slides/pages are bundled with the addon. 1-4 use background images and simple Comcode, 5 uses a YouTube video (but any video code should work), 6 uses an image. You will see that the image and video are auto-sized to fit the slider perfectly (taking precedence over the normal size of the video).

If the [tt]delay[/tt] is set to blank or 0, it will disable automatic (timed) transition between slides. You may wish to do this if you include a video slide, although if the user hovers the mouse over the slider it will block automatic transitions also.';
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
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/unslider.ini',
            'pages/comcode_custom/EN/_unslider_slide1.txt',
            'pages/comcode_custom/EN/_unslider_slide2.txt',
            'pages/comcode_custom/EN/_unslider_slide3.txt',
            'pages/comcode_custom/EN/_unslider_slide4.txt',
            'pages/comcode_custom/EN/_unslider_slide5.txt',
            'pages/comcode_custom/EN/_unslider_slide6.txt',
            'sources_custom/blocks/main_unslider.php',
            'sources_custom/hooks/systems/addon_registry/unslider.php',
            'themes/default/css_custom/unslider.css',
            'themes/default/images_custom/unslider_backgrounds/index.html',
            'themes/default/images_custom/unslider_backgrounds/shop.jpg',
            'themes/default/images_custom/unslider_backgrounds/subway.jpg',
            'themes/default/images_custom/unslider_backgrounds/sunset.jpg',
            'themes/default/images_custom/unslider_backgrounds/wood.jpg',
            'themes/default/javascript_custom/unslider.js',
            'themes/default/templates_custom/BLOCK_MAIN_UNSLIDER.tpl',
        ];
    }
}
