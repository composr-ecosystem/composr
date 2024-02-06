<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_addon_registry_karma
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
     * Get the version of Composr this addon is for.
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
        return 'New Features';
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
        return ['PDStig, LLC'];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'GNU General Public License v3.0';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Karma is a highly-configurable member reputation system for Conversr-based websites. Karma is a great addition to websites utilising crowdsourced moderation.

Each member has good karma and bad karma. Members can receive good karma for exceptional content or behaviour, and they can receive bad karma for poor content or behaviour. The combination of good and bad karma (good - bad) yields the overall karma score for a member.

Karma is displayed, depending on set privileges, as a bar under user avatars on their profile and forum posts. It can display the overall karma or a breakdown of good / bad karma. It utilises easy to understand colours: green is good, red is bad, and yellow is neutral / negates the other colour on the bar.

There are several options for configuring how members receive karma under Admin Zone > Setup > Configuration > Feature options. There are also options for specifying how much a member can influence the karma of other members (such as by account age or number of forum posts).

Site staff can manage the karma that was given to members through the Karma logs under Admin Zone > Audit > Karma. Staff can also reverse recent karma activity or assess bad karma through the warnings form under the new "Karma" section.

The Karma addon works with several other addons by default; these are listed under recommended addons.

Karma and its API can be further extended for additional functionality. For example, you could code an addon to restrict certain content or usergroups to members with a minimal karma score or those whose bad karma makes up no more than a certain percentage of their overall karma.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'sup_adding_a_member_reputation_system'
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
                'cns_forum', // We require Conversr to prevent conflicts or confusion with reputation systems used by other forums.
            ],
            'recommends' => [ // These addons will utilize Karma if they are installed
                'awards',
                'cns_warnings',
                'polls',
                'points',
                'giftr',
                'idolisr',
                'member_comments',
                'ecommerce',
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
        return 'themes/default/images/icons/feedback/like.svg'; // TODO: Use a different icon
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/karma.php',

            // Config hooks
            'sources_custom/hooks/systems/config/karma_influence_use_voting_power.php',
            'sources_custom/hooks/systems/config/karma_influence_account_age.php',
            'sources_custom/hooks/systems/config/karma_influence_forum_posts.php',
            'sources_custom/hooks/systems/config/karma_influence_lifetime_points.php',
            'sources_custom/hooks/systems/config/karma_influence_points.php',
            'sources_custom/hooks/systems/config/karma_influence_karma.php',
            'sources_custom/hooks/systems/config/karma_influence_additional.php',
            'sources_custom/hooks/systems/config/karma_influence_warnings.php',
            'sources_custom/hooks/systems/config/karma_influence_warnings_amount.php',
            'sources_custom/hooks/systems/config/karma_influence_multiplier.php',
            'sources_custom/hooks/systems/config/karma_awards.php',
            'sources_custom/hooks/systems/config/karma_likes.php',
            'sources_custom/hooks/systems/config/karma_dislikes.php',
            'sources_custom/hooks/systems/config/karma_points.php',
            'sources_custom/hooks/systems/config/karma_points_idolisr.php',
            'sources_custom/hooks/systems/config/karma_voting.php',
            'sources_custom/hooks/systems/config/karma_giftr.php',
            'sources_custom/hooks/systems/config/karma_threshold.php',

            // TODO: not yet implemented
            'sources_custom/hooks/systems/config/karma_member_comments.php',
            'sources_custom/hooks/systems/config/karma_ecommerce.php',

            // Compliance hooks
            'sources_custom/hooks/systems/privacy/karma.php',

            // Warnings hooks
            'sources_custom/hooks/systems/cns_warnings/karma.php',
            'sources_custom/hooks/systems/cns_warnings/karma_logs.php',

            // Commandr hooks
            'sources_custom/hooks/systems/commandr_commands/add_karma.php',
            'sources_custom/hooks/systems/commandr_commands/karma.php',
            'sources_custom/hooks/systems/commandr_commands/karmic_influence.php',

            // Other hooks
            'sources_custom/hooks/systems/actionlog/karma.php',
            'sources_custom/hooks/systems/cns_cpf_filter/karma.php',
            'sources_custom/hooks/systems/symbols/KARMA.php',
            'sources_custom/hooks/systems/points_transact/karma.php',
            'sources_custom/hooks/systems/page_groupings/karma.php',

            // Source files / API
            'lang_custom/EN/karma.ini',
            'sources_custom/karma.php',
            'sources_custom/karma2.php',

            // Overrides
            'sources_custom/hooks/systems/contentious_overrides/karma.php',

            // Templates / MV
            'adminzone/pages/modules_custom/admin_karma.php',
            'sources_custom/blocks/main_karma_graph.php',
            'themes/default/templates_custom/BLOCK_MAIN_KARMA_GRAPH.tpl',
            'themes/default/css_custom/karma.css',
        ];
    }
}
