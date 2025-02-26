<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

/**
 * Hook class.
 */
class Hook_addon_registry_achievements
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
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update f562d3a487b8652f616cf1ced3b55659
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
        return 'Patrick Schmalstig';
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
        return 'Achievements allows members to complete different qualifications (such as publishing a certain number of forum posts) to earn badges for their profile (and optionally points). Achievements are displayed on a member profile and their forum posts (Conversr only) for everyone to see. There are a wide number of criteria you can define for achievements, and more types can be created using hooks. The achievements system is configured through an XML file for maximum flexibility.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'sup_achievements'
        ];
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
            ],
            'recommends' => [
                'points',
                'System scheduler',
            ],
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
        return 'themes/default/images/icons/spare/popular.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/minimodules_custom/admin_achievements.php',
            'lang_custom/EN/achievements.ini',
            'site/pages/modules_custom/achievements.php',
            'sources_custom/achievements.php',
            'sources_custom/blocks/main_achievements.php',
            'sources_custom/hooks/systems/achievement_qualifications/.htaccess',
            'sources_custom/hooks/systems/achievement_qualifications/account_age.php',
            'sources_custom/hooks/systems/achievement_qualifications/achievements.php',
            'sources_custom/hooks/systems/achievement_qualifications/actionlog.php',
            'sources_custom/hooks/systems/achievement_qualifications/activity_feed.php',
            'sources_custom/hooks/systems/achievement_qualifications/attachments.php',
            'sources_custom/hooks/systems/achievement_qualifications/author.php',
            'sources_custom/hooks/systems/achievement_qualifications/awards.php',
            'sources_custom/hooks/systems/achievement_qualifications/bad_karma.php',
            'sources_custom/hooks/systems/achievement_qualifications/calendar_interests.php',
            'sources_custom/hooks/systems/achievement_qualifications/calendar_reminders.php',
            'sources_custom/hooks/systems/achievement_qualifications/chat_messages.php',
            'sources_custom/hooks/systems/achievement_qualifications/content.php',
            'sources_custom/hooks/systems/achievement_qualifications/daily_visits.php',
            'sources_custom/hooks/systems/achievement_qualifications/download.php',
            'sources_custom/hooks/systems/achievement_qualifications/ecom_transactions.php',
            'sources_custom/hooks/systems/achievement_qualifications/escrows.php',
            'sources_custom/hooks/systems/achievement_qualifications/filedump.php',
            'sources_custom/hooks/systems/achievement_qualifications/friends.php',
            'sources_custom/hooks/systems/achievement_qualifications/giftr.php',
            'sources_custom/hooks/systems/achievement_qualifications/group.php',
            'sources_custom/hooks/systems/achievement_qualifications/index.html',
            'sources_custom/hooks/systems/achievement_qualifications/invites.php',
            'sources_custom/hooks/systems/achievement_qualifications/karma.php',
            'sources_custom/hooks/systems/achievement_qualifications/karmic_influence.php',
            'sources_custom/hooks/systems/achievement_qualifications/leader_board.php',
            'sources_custom/hooks/systems/achievement_qualifications/member.php',
            'sources_custom/hooks/systems/achievement_qualifications/no_warnings.php',
            'sources_custom/hooks/systems/achievement_qualifications/page_access.php',
            'sources_custom/hooks/systems/achievement_qualifications/points_received.php',
            'sources_custom/hooks/systems/achievement_qualifications/points_used.php',
            'sources_custom/hooks/systems/achievement_qualifications/poll_votes.php',
            'sources_custom/hooks/systems/achievement_qualifications/privilege.php',
            'sources_custom/hooks/systems/achievement_qualifications/profile_views.php',
            'sources_custom/hooks/systems/achievement_qualifications/quiz_entries.php',
            'sources_custom/hooks/systems/achievement_qualifications/ratings.php',
            'sources_custom/hooks/systems/achievement_qualifications/staff.php',
            'sources_custom/hooks/systems/achievement_qualifications/topic_poll_votes.php',
            'sources_custom/hooks/systems/achievement_qualifications/topic_polls.php',
            'sources_custom/hooks/systems/achievement_qualifications/tracker.php',
            'sources_custom/hooks/systems/achievement_qualifications/tutorials.php',
            'sources_custom/hooks/systems/actionlog/achievements.php',
            'sources_custom/hooks/systems/addon_registry/achievements.php',
            'sources_custom/hooks/systems/contentious_overrides/achievements.php',
            'sources_custom/hooks/systems/cron/achievements.php',
            'sources_custom/hooks/systems/notifications/achievements.php',
            'sources_custom/hooks/systems/page_groupings/achievements.php',
            'sources_custom/hooks/systems/privacy/achievements.php',
            'themes/default/css_custom/achievements.css',
            'themes/default/templates_custom/ACHIEVEMENT_PROGRESS.tpl',
            'themes/default/templates_custom/ACHIEVEMENT_PROGRESS_QUALIFICATION.tpl',
            'themes/default/templates_custom/ACHIEVEMENT_QUALIFICATIONS_OR.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_ACHIEVEMENTS.tpl',
        ];
    }
}
