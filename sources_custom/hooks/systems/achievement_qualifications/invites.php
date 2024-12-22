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
    Supported parameters for this qualification:
    1) count        -- The number of invites a member must make to mark this qualification as satisfied (not specified: 3)
    2) taken_only   -- If 1, then only invites which actually resulted in a join are counted [persist is not supported if this is specified] (not specified: 0)
    3) days         -- Only count invites which were sent out in the last number of days [persist is not supported if this is specified] (not specified: no filter)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_invites
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

        if (!addon_installed('recommend')) {
            return null;
        }

        return [
            'supports_persist' => (!isset($params['days']) && isset($params['taken_only'])),
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

        if (!addon_installed('recommend')) {
            return null;
        }

        // Read in options
        $count_required = isset($params['count']) ? intval($params['count']) : 3;
        $taken_only = isset($params['taken_only']) ? ($params['taken_only'] == '1') : false;
        $days = isset($params['days']) ? intval($params['days']) : null;

        // Build query
        $where_map = ['i_invite_member' => $member_id];
        $extra_where = '';
        if ($taken_only) {
            $where_map['i_taken'] = 1;
        }
        if ($days !== null) {
            $extra_where .= ' AND i_time>=' . strval(time() - ($days * 24 * 60 * 60));
        } elseif (($last_time !== null) && ($taken_only === false)) {
            $extra_where .= ' AND i_time>' . strval($last_time);
        }

        // Get results
        $count_done = $GLOBALS['FORUM_DB']->query_select_value('f_invites', 'COUNT(*)', $where_map, $extra_where);

        return [$count_done, $count_required];
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

        if (!addon_installed('recommend')) {
            return null;
        }

        require_lang('achievements');

        // Read in options
        $taken_only = isset($params['taken_only']) ? ($params['taken_only'] == '1') : false;
        $days = isset($params['days']) ? intval($params['days']) : null;

        $conditions = new Tempcode();
        if ($taken_only) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_INVITES_REQUIREMENT_TAKEN_ONLY'));
        }
        if ($days !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_INVITES_REQUIREMENT_DAYS', escape_html(integer_format($days))));
        }

        // Progress
        $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));

        // Finalise
        return do_lang_tempcode('ACHIEVEMENT_INVITES_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
