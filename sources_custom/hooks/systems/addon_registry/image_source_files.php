<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    image_source_files
 */

/**
 * Hook class.
 */
class Hook_addon_registry_image_source_files
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
     * Get the version of the software this addon is for (used in generating the TAR filename).
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
        return 'Graphical';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Allen Ellis';
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
        return 'A few source files for graphics. Also dark versions of Composr\'s animated gif emoticons (although ideally people will have browsers that support APNG, so there should not be an issue in the future).';
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
            'data_custom/images/source_files/blank_emoticon.png',
            'data_custom/images/source_files/dark_emoticons/blink.gif',
            'data_custom/images/source_files/dark_emoticons/devil.gif',
            'data_custom/images/source_files/dark_emoticons/guitar.gif',
            'data_custom/images/source_files/dark_emoticons/index.html',
            'data_custom/images/source_files/dark_emoticons/lol.gif',
            'data_custom/images/source_files/dark_emoticons/ninja2.gif',
            'data_custom/images/source_files/dark_emoticons/nod.gif',
            'data_custom/images/source_files/dark_emoticons/reallybadday.gif',
            'data_custom/images/source_files/dark_emoticons/rockon.gif',
            'data_custom/images/source_files/dark_emoticons/rolleyes.gif',
            'data_custom/images/source_files/dark_emoticons/shake.gif',
            'data_custom/images/source_files/dark_emoticons/shutup.gif',
            'data_custom/images/source_files/index.html',
            'sources_custom/hooks/systems/addon_registry/image_source_files.php',
        ];
    }
}
