<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    alternate_emoticons
 */

/**
 * Hook class.
 */
class Hook_addon_registry_alternate_emoticons
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
        return '11'; // addon_version_auto_update 880897f6366b1f67fccba5c54bbef301
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
        return 'Philip Withnall';
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
        return 'Replaces most of the main emoticons which are included within Composr as standard.';
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
        return 'themes/default/images_custom/cns_emoticons/lol.png';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
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
            'themes/default/images_custom/cns_emoticons/index.html',
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
        ];
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
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'theme_images WHERE url LIKE \'themes/%/images/cns_emoticons/%\'');
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'theme_images WHERE url LIKE \'themes/%/images//cns_emoticons/%\'');

            if (class_exists('Self_learning_cache')) {
                Self_learning_cache::erase_smart_cache();
            }
        }
    }
}
