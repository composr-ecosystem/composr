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
    You might consider combining this qualification with account_age or sessions so members do not immediately earn the achievement for having a clean record.

    Supported parameters for this qualification:
    1) count     -- the member must have *less* than this number of formal warnings for this qualification to be satisfied (not specified: 1)
    2) days      -- the number of days in the past to consider when counting formal warnings [persist is not supported if this is specified] (not specified: indefinite / since joining)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_no_warnings
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

        if (!addon_installed('cns_warnings')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return [
            'supports_persist' => (!isset($params['days'])),
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

        if (!addon_installed('cns_warnings')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        $count = isset($params['count']) ? intval($params['count']) : 1;
        $days = isset($params['days']) ? intval($params['days']) : null;

        $where_map = ['w_member_id' => $member_id, 'w_is_warning' => 1];
        $extra_where = '';
        if ($days !== null) {
            $extra_where .= ' AND w_time>=' . strval(time() - ($days * 24 * 60 * 60));
        } elseif ($last_time !== null) {
            $extra_where .= ' AND w_time>' . strval($last_time);
        }
        $warnings = $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'COUNT(*)', $where_map, $extra_where);

        if ($warnings >= $count) {
            return [0, 1];
        }

        return [1, 1];
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

        require_lang('achievements');

        $count = isset($params['count']) ? intval($params['count']) : 1;
        $days = isset($params['days']) ? intval($params['days']) : null;

        $conditions = do_lang_tempcode('ACHIEVEMENT_NO_WARNINGS_REQUIREMENT_COUNT', escape_html(integer_format($count)));
        if ($days !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_NO_WARNINGS_REQUIREMENT_DAYS', escape_html(integer_format($days))));
        }

        if ($count_done > 0) {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_SATISFIED');
        } else {
            $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS_NOT_SATISFIED');
        }

        return do_lang_tempcode('ACHIEVEMENT_NO_WARNINGS_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
