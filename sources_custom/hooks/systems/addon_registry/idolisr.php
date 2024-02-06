<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
     * Get the version of Composr this addon is for (used in generating the TAR filename).
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
        return 'Chris Graham';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Show the top performing members in a community. The addon adds a [tt]main_stars[/tt] block that ranks members on how many points they have been given in a certain category (also it changes the points module to allow selection of such categories when giving points). It also adds a block to show recent points transfers. Finally, it adds a line to member\'s profile screens that says how many topics they have created, and how many they have replied to, to give a reflection of whether they help more than they ask or vice-versa.

Usage:
[code="Comcode"][block max="10"]side_recent_points[/block][/code]
and
[code="Comcode"][block="Helpful soul"]main_stars[/block][/code]The [tt]POINTS_SEND[/tt] ([tt]themes/default/templates_custom[/tt]) template contains hard-coded HTML that defines each kind of points category that can be used. It is likely you will want to put out one an instance of the [tt]main_stars[/tt] block for each category (using the syntax demonstrated above).';
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
            'requires' => [
                'points',
            ],
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
            'sources_custom/hooks/systems/addon_registry/idolisr.php',
            'sources_custom/hooks/modules/members/idolisr.php',
            'sources_custom/miniblocks/main_stars.php',
            'sources_custom/miniblocks/side_recent_points.php',
            'site/pages/modules_custom/points.php',
            'themes/default/templates_custom/POINTS_SEND.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_STARS.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_RECENT_POINTS.tpl',
            'themes/default/javascript_custom/idolisr.js',
            'sources_custom/points2.php',
            'sources_custom/hooks/systems/config/idolisr_roles.php',
            'lang_custom/EN/idolisr.ini',
        ];
    }
}
