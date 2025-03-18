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
    1) count            -- The number of GitLab issues a member must open before this qualification is satisfied (not specified: 10)
    4) days             -- Only consider issues opened within this many days (not specified: no filter)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_gitlab_issues
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

        if (!addon_installed('cms_homesite_gitlab')) {
            return null;
        }

        if (!addon_installed('points')) { // We use point transactions to count these
            return null;
        }

        return [
            'supports_persist' => false,
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

        if (!addon_installed('cms_homesite_gitlab')) {
            return null;
        }

        if (!addon_installed('points')) { // We use point transactions to count these
            return null;
        }

        require_code('points');

        // Read in parameters
        $count_required = isset($params['count']) ? intval($params['count']) : 5;
        $days = isset($params['days']) ? intval($params['days']) : null;

        // Build query
        $sql = 'SELECT COUNT(*) AS num_issues FROM {prefix}points_ledger';
        $where = ' WHERE receiving_member={member_id} AND status={ledger_normal} AND ' . db_string_equal_to('t_type', 'gitlab') . ' AND ' . db_string_equal_to('t_subtype', 'issue');
        $where_params = ['member_id' => $member_id, 'ledger_normal' => LEDGER_STATUS_NORMAL];
        if ($days !== null) {
            $where_params['time_limit'] = time() - ($days * 24 * 60 * 60);
            $where .= ' AND date_and_time>={time_limit}';
        }

        // Get results
        $_count_done = $GLOBALS['SITE_DB']->query_parameterised($sql . $where, $where_params);
        $count_done = $_count_done[0]['num_issues'];

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

        if (!addon_installed('cms_homesite_gitlab')) {
            return null;
        }

        if (!addon_installed('points')) { // We use point transactions to count these
            return null;
        }

        require_lang('cms_homesite_gitlab');

        // Read in parameters
        $days = isset($params['days']) ? intval($params['days']) : null;

        // Conditions
        $conditions = new Tempcode();
        if ($days !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_GITLAB_ISSUES_REQUIREMENT_DAYS', escape_html(integer_format($days))));
        }

        // Progress
        $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));

        // Finalise
        return do_lang_tempcode('ACHIEVEMENT_GITLAB_ISSUES_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
