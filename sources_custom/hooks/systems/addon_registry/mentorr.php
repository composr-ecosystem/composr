<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_mentorr
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
        return 'New Features';
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
        return 'Assign trusted members as mentors who will help new members. As a bonus the mentor will get the same amount of points, which his new friend gain during the first week.

There will be configurable mentors usergroup, from which random member will be selected and made friend to newly joined members (it will make them buddies and also will create a Private Topic between them explaining the automatic friendship).

To set the mentor group go to Admin Zone > Setup > Configuration > Member and forum options. At the bottom of the page choose the mentor user group from the drop down list. Go to the Usergroups display page for mentors and assign the users to the mentors usergroup.

New users should then be assigned a mentor/buddy who will receive an equal amount of points the new user receives for the first week. The system will also create a private topic between the 2 members explaining what has happened.';
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
                'Conversr',
                'chat',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/mentorr.php',
            'sources_custom/points2.php',
            'sources_custom/hooks/systems/upon_query/add_mentor.php',
            'lang_custom/EN/mentorr.ini',
            'sources_custom/hooks/systems/config/mentor_usergroup.php',
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
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('members_mentors');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('members_mentors', array(
                'id' => '*AUTO',
                'member_id' => '*INTEGER',
                'mentor_id' => '*INTEGER',
            ));
        }
    }
}
