<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    psds
 */

/**
 * Hook class.
 */
class Hook_addon_registry_psds
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
        return 'Graphical';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Allen Ellis';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'The source PSD files for graphics that contain English text. Also dark versions of Composr\'s animated gif emoticons.';
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
            'requires' => array(),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/psds.php',
            'data_custom/images/psds/blank_emoticon.png',
            'data_custom/images/psds/chatcode.psd',
            'data_custom/images/psds/whisper.psd',
            'data_custom/images/psds/comcode.psd',
            'data_custom/images/psds/dark_emoticons/blink.gif',
            'data_custom/images/psds/dark_emoticons/devil.gif',
            'data_custom/images/psds/dark_emoticons/guitar.gif',
            'data_custom/images/psds/dark_emoticons/lol.gif',
            'data_custom/images/psds/dark_emoticons/ninja2.gif',
            'data_custom/images/psds/dark_emoticons/nod.gif',
            'data_custom/images/psds/dark_emoticons/reallybadday.gif',
            'data_custom/images/psds/dark_emoticons/rockon.gif',
            'data_custom/images/psds/dark_emoticons/rolleyes.gif',
            'data_custom/images/psds/dark_emoticons/shake.gif',
            'data_custom/images/psds/dark_emoticons/shutup.gif',
        );
    }
}
