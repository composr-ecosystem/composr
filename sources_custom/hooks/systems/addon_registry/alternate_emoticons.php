<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    alternate_emoticons
 */

/**
 * Hook class.
 */
class Hook_addon_registry_alternate_emoticons
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
        return 'Philip Withnall';
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
        return 'Replaces most of the main emoticons which are included within Composr as standard.';
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
        return 'themes/default/images_custom/cns_emoticons/lol.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/alternate_emoticons.php',
            'themes/default/images_custom/cns_emoticons/angry.png',
            'themes/default/images_custom/cns_emoticons/blink.gif',
            'themes/default/images_custom/cns_emoticons/blush.png',
            'themes/default/images_custom/cns_emoticons/cheeky.png',
            'themes/default/images_custom/cns_emoticons/confused.gif',
            'themes/default/images_custom/cns_emoticons/cool.png',
            'themes/default/images_custom/cns_emoticons/cry.png',
            'themes/default/images_custom/cns_emoticons/cyborg.gif',
            'themes/default/images_custom/cns_emoticons/dry.png',
            'themes/default/images_custom/cns_emoticons/grin.png',
            'themes/default/images_custom/cns_emoticons/kiss.png',
            'themes/default/images_custom/cns_emoticons/lol.png',
            'themes/default/images_custom/cns_emoticons/mellow.png',
            'themes/default/images_custom/cns_emoticons/nerd.png',
            'themes/default/images_custom/cns_emoticons/ph34r.gif',
            'themes/default/images_custom/cns_emoticons/rolleyes.gif',
            'themes/default/images_custom/cns_emoticons/sad.png',
            'themes/default/images_custom/cns_emoticons/sarcy.png',
            'themes/default/images_custom/cns_emoticons/shocked.png',
            'themes/default/images_custom/cns_emoticons/sick.png',
            'themes/default/images_custom/cns_emoticons/smile.png',
            'themes/default/images_custom/cns_emoticons/thumbs.png',
            'themes/default/images_custom/cns_emoticons/upsidedown.png',
            'themes/default/images_custom/cns_emoticons/whistle.png',
            'themes/default/images_custom/cns_emoticons/wink.png',
            'themes/default/images_custom/cns_emoticons/wub.png',
        );
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'theme_images WHERE path LIKE \'themes/%/images/cns_emoticons/%\'');
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'theme_images WHERE path LIKE \'themes/%/images//cns_emoticons/%\'');

            if (class_exists('Self_learning_cache')) {
                Self_learning_cache::erase_smart_cache();
            }
        }
    }
}
