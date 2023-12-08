<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    bantr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_bantr
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
        return 'Fun and Games';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen Blaginov';
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
        return 'Randomly creates Private Topics between two random members with an insult inside of it (and a brief explanation).

The insult is randomly selected from a list which also includes insults-comebacks. This list is configurable in the Admin Zone under Setup (Manage insults icon). You should separate the insults from comebacks by equal sign(=) and add any new insults on a new line (the same way this is done in Random quotes section).

The insulted member has to try and make the right reply, if they succeed then they will be awarded some site points which is configurable under Setup > Configuration > Points options. The default insults are based off the classic computer game, Monkey Island.';
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
                'Cron',
                'Conversr',
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
        return 'themes/default/images_custom/icons/48x48/menu/insults.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/insults.png',
            'themes/default/images_custom/icons/48x48/menu/insults.png',
            'sources_custom/hooks/systems/addon_registry/bantr.php',
            'adminzone/pages/comcode_custom/EN/insults.txt',
            'lang_custom/EN/insults.ini',
            'sources_custom/hooks/systems/cron/insults.php',
            'sources_custom/hooks/systems/page_groupings/insults.php',
            'sources_custom/hooks/systems/upon_query/insults.php',
            'text_custom/EN/insults.txt',
            'sources_custom/hooks/systems/config/insult_points.php',
        );
    }
}
