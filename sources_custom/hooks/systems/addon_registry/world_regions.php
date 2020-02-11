<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    world_regions
 */

/**
 * Hook class.
 */
class Hook_addon_registry_world_regions
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
        return 'Development';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Extends the regions system so that USA states and British regions can be targeted. Further countries may also be implemented, as the system is extended over time.';
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
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/world_regions.php',
            'sources_custom/locations.php',
            'sources_custom/locations/.htaccess',
            'sources_custom/locations/index.html',
            'sources_custom/locations/uk.php',
            'sources_custom/locations/us.php',
            'sources_custom/hooks/systems/symbols/STATE_CODE_TO_NAME.php',
            'sources_custom/hooks/systems/symbols/STATE_NAME_TO_CODE.php',
        ];
    }
}
