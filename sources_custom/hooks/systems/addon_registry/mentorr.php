<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    mentorr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_mentorr
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
        return '11'; // addon_version_auto_update 1731956906
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
        return 'Community';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen Blaginov';
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
        return 'Assign trusted members as mentors who will help new members. As a bonus the mentor will get the same amount of points that his new friend gained during the first week.

There will be a configurable mentors usergroup, from which random members will be selected to be made friends with newly joined members (it will make them friends and also will create a Private Topic between them explaining the automatic friendship).

To set the mentor group go to Admin Zone > Setup > Configuration > Member and forum options. At the bottom of the page choose the mentor user group from the drop down list. Go to the Usergroups display page for mentors and assign the users to the mentors usergroup.

New users should then be assigned a mentor/friend who will receive an equal amount of points the new user receives for the first week. The system will also create a private topic between the 2 members explaining what has happened.';
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
                'Conversr',
                'chat',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/mentorr.ini',
            'sources_custom/hooks/systems/addon_registry/mentorr.php',
            'sources_custom/hooks/systems/config/mentor_usergroup.php',
            'sources_custom/hooks/systems/points_transact/mentorr.php',
            'sources_custom/hooks/systems/privacy/mentorr.php',
            'sources_custom/hooks/systems/upon_query/add_mentor.php',
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
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('members_mentors', [
                'id' => '*AUTO',
                'member_id' => '*MEMBER',
                'mentor_member_id' => '*MEMBER',
                'date_and_time' => 'TIME'
            ]);
        }

        if ($upgrade_from !== null && $upgrade_from < 11) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('members_mentors', 'date_and_time', 'TIME');

            // Database consistency fixes
            $GLOBALS['SITE_DB']->alter_table_field('members_mentors', 'mentor_id', '*MEMBER', 'mentor_member_id');
        }

        if ($upgrade_from === null || $upgrade_from < 11) {
            $GLOBALS['SITE_DB']->create_index('members_mentors', 'date_and_time', ['date_and_time']);
        }
    }
}
