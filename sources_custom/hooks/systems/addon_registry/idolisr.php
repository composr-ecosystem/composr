<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    idolisr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_idolisr
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
        return 'Chris Graham';
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
        return 'Show the top performing members in a community. The addon adds a [tt]main_stars[/tt] block that ranks members on how many points they have been given in a certain category (also it changes the points module to allow selection of such categories when giving points). It also adds a block to show recent points transfers. Finally, it adds a line to member\'s profile screens that says how many topics they have created, and how many they have replied to, to give a reflection of whether they help more than they ask or vice-versa.

Usage:
[code="Comcode"][block max="10"]side_recent_points[/block][/code]
and
[code="Comcode"][block="Helpful soul"]main_stars[/block][/code]The [tt]POINTS_GIVE[/tt] ([tt]themes/default/templates_custom[/tt]) template contains hard-coded HTML that defines each kind of points category that can be used. It is likely you will want to put out one an instance of the [tt]main_stars[/tt] block for each category (using the syntax demonstrated above).';
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
                'JavaScript enabled',
                'points',
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
            'sources_custom/hooks/systems/addon_registry/idolisr.php',
            'sources_custom/hooks/modules/members/idolisr.php',
            'sources_custom/miniblocks/main_stars.php',
            'sources_custom/miniblocks/side_recent_points.php',
            'themes/default/templates_custom/POINTS_GIVE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_STARS.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_RECENT_POINTS.tpl',
        );
    }
}
