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

/*
    This qualification uses ids as an *OR* condition; the member only needs to be in one of the specified usergroups to satisfy this qualification.
    If you want to set up a requirement for an achievement where a member must be in multiple usergroups, then use multiple group qualification specifications.

    Supported parameters for this qualification:
    1) ids          -- Required; a comma-delimited list of usergroup IDs where a member must be in *one or more* of them to satisfy this qualification
    2) exclude      -- If 1, then this qualification is treated as a negation; the member must not be in *any* of the specified usergroups for this qualification to be satisfied (not specified: 0)
    3) primary_only -- If 1, then we only consider the member's primary group and not their secondary ones (not specified: 0)
    4) text         -- Optional Comcode-supported text or language string to display as the requirement instead of the default "be in group" text
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_group
{
    /**
     * Get information about this qualification.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @return ?array Map of details (null: qualification is disabled)
     */
    public function info(int $member_id, array $params) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        return [
            'supports_persist' => true,
            'persist_progress_default' => false,
        ];
    }

    /**
     * Run calculations on this qualification to see how much it has been completed.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @param  ?TIME $last_time Only calculate results more recent than the given time (null: never calculated before, or not persisting progress)
     * @return ?array Double: the number accomplished, and the number needed for the qualification to be considered "complete" (null: qualification should be ignored)
     */
    public function run(int $member_id, array $params, ?int $last_time = null) : ?array
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        // Read in parameters
        $group_ids = array_map('intval', explode(',', $params['ids']));
        $exclude = isset($params['exclude']) ? ($params['exclude'] == '1') : false;
        $primary_only = isset($params['primary_only']) ? ($params['primary_only'] == '1') : false;

        // Read in usergroups
        require_code('cns_general');
        $need = ['primary_group'];
        if ($primary_only === false) {
            $need[] = 'secondary_groups';
        }
        $member_info = cns_read_in_member_profile($member_id, $need);
        $groups = [$member_info['primary_group']];
        if (isset($member_info['secondary_groups'])) {
            array_merge($groups, $member_info['secondary_groups']);
        }

        // Process if we are tracking that the member is not in any of the given groups
        if ($exclude) {
            foreach ($group_ids as $group_id) {
                if (in_array($group_id, $groups)) {
                    return [0, 1];
                }
            }

            return [1, 1];
        }

        // Otherwise process if they are in just one of them
        foreach ($group_ids as $group_id) {
            if (in_array($group_id, $groups)) {
                return [1, 1];
            }
        }

        return [0, 1];
    }

    /**
     * Convert information about the qualification into human-readable text where members can track their progress.
     *
     * @param  MEMBER $member_id The member we are viewing
     * @param  array $params Map of parameters which were specified on the XML for this qualification
     * @param  integer $count_done Count of items achieved for the qualification (from run)
     * @param  integer $count_required Count of items required for the qualification to be complete (from run)
     * @return ?Tempcode The text explaining this condition and the progress (null: hidden or disabled qualification)
     */
    public function to_text(int $member_id, array $params, int $count_done, int $count_required) : ?object
    {
        if (!addon_installed('achievements')) {
            return null;
        }

        // Read in parameters
        $group_ids = array_map('intval', explode(',', $params['ids']));
        $exclude = isset($params['exclude']) ? ($params['exclude'] == '1') : false;
        $primary_only = isset($params['primary_only']) ? ($params['primary_only'] == '1') : false;
        $text = isset($params['text']) ? $params['text'] : null;

        // Custom text requirement instead of default group text?
        if ($text !== null) {
            $ret = do_lang($text, null, null, null, null, false);
            if ($ret === null) {
                return comcode_to_tempcode($text, null, true);
            }
            return comcode_to_tempcode($ret, null, true);
        }

        require_lang('achievements');

        $conditions = new Tempcode();

        // Get group names for the condition
        require_code('cns_groups');
        $group_names = [];
        foreach ($group_ids as $group_id) {
            $group_names[] = cns_get_group_name($group_id);
        }
        $conditions->attach(do_lang_tempcode('ACHIEVEMENT_GROUP_REQUIREMENT_GROUPS', escape_html(implode(', ', $group_names))));

        // Only considering primary group?
        if ($primary_only) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_GROUP_REQUIREMENT_PRIMARY_ONLY'));
        }

        // Progress
        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        // Put it together
        if ($exclude) {
            return do_lang_tempcode('ACHIEVEMENT_NO_GROUP_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
        }
        return do_lang_tempcode('ACHIEVEMENT_GROUP_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
