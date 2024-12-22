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
    1) count        -- The number of points required to have been received for this qualification to be satisfied (not specified: 1,000)
    2) rank_only    -- If 1, then consider only points which count towards rank points (not specified: 1)
    3) earned_only  -- If 1, then we only consider points which have been earned (given from the system) and not from other members; -1 has the opposite effect, only points received from other members (not specified: 0)
    4) rows_only    -- If 1, then we do not want to count points but rather number of transactions instead (not specified: 0)
    5) type         -- Only consider point transactions for the given type, usually a content type (not specified: no filter)
    6) subtype      -- Only consider point transactions for the given subtype, usually an action keyword (not specified: no filter)
    7) type_id      -- Only consider point transactions for the given type ID, usually an ID of the specified type (not specified: no filter)
    8) days         -- Only consider points received in the last given number of days [persist is not supported if this is specified] (not specified: no filter)
*/

/**
 * Hook class.
 */
class Hook_achievement_qualifications_points_received
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

        if (!addon_installed('points')) {
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

        if (!addon_installed('points')) {
            return null;
        }

        // Read in options
        $count_required = isset($params['count']) ? intval($params['count']) : 1000;
        $rank_only = isset($params['rank_only']) ? ($params['rank_only'] == '1') : true;
        $earned_only = isset($params['earned_only']) ? $params['earned_only'] : '0';
        $rows_only = isset($params['rows_only']) ? ($params['rows_only'] == '1') : false;
        $type = isset($params['type']) ? $params['type'] : null;
        $subtype = isset($params['subtype']) ? $params['subtype'] : null;
        $type_id = isset($params['type_id']) ? $params['type_id'] : null;
        $days = isset($params['days']) ? intval($params['days']) : null;

        require_code('points');

        // Optimisation; we might just be able to pull from total rank points
        $just_use_rank_points = (($rank_only) && ($type === null) && ($subtype === null) && ($type_id === null) && ($days === null) && ($last_time === null) && ($earned_only == '0') && (!$rows_only));
        if ($just_use_rank_points) {
            return [points_rank($member_id), $count_required];
        }

        // Build our WHERE query
        $extra_where = '';
        $secondary_member = null;
        if ($rank_only) {
            $extra_where .= ' AND is_ranked=1';
        }
        if ($earned_only == '1') {
            $secondary_member = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        }
        if ($earned_only == '-1') {
            $extra_where .= ' AND sending_member<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
        }
        if ($type !== null) {
            $extra_where .= ' AND ' . db_string_equal_to('t_type', $type);
        }
        if ($subtype !== null) {
            $extra_where .= ' AND ' . db_string_equal_to('t_subtype', $subtype);
        }
        if ($type_id !== null) {
            $extra_where .= ' AND ' . db_string_equal_to('t_type_id', $type_id);
        }
        if ($days !== null) {
            $extra_where .= ' AND date_and_time>=' . strval(time() - ($days * 24 * 60 * 60));
        } elseif ($last_time !== null) {
            $extra_where .= ' AND date_and_time>' . strval($last_time);
        }

        $points_info = points_ledger_calculate(LEDGER_TYPE_RECEIVED, $member_id, $secondary_member, $extra_where);
        list($rows, $points, $gift_points) = $points_info['received'];

        if ($rows_only) {
            return [$rows, $count_required];
        }

        return [($points + $gift_points), $count_required];
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

        if (!addon_installed('points')) {
            return null;
        }

        require_lang('achievements');

        // Read in options
        $rank_only = isset($params['rank_only']) ? ($params['rank_only'] == '1') : true;
        $earned_only = isset($params['earned_only']) ? $params['earned_only'] : '0';
        $rows_only = isset($params['rows_only']) ? ($params['rows_only'] == '1') : false;
        $type = isset($params['type']) ? $params['type'] : null;
        $subtype = isset($params['subtype']) ? $params['subtype'] : null;
        $type_id = isset($params['type_id']) ? $params['type_id'] : null;
        $days = isset($params['days']) ? intval($params['days']) : null;

        require_lang('achievements');

        $conditions = new Tempcode();

        // Rank only
        if ($rank_only) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_RANK_ONLY'));
        }

        // Earned
        if ($earned_only == '1') {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_EARNED_ONLY'));
        }
        if ($earned_only == '-1') {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_NOT_EARNED'));
        }

        // Rows
        if ($rows_only) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_ROWS_ONLY'));
        }

        // Types
        if ($type !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_TYPE', escape_html($type)));
        }
        if ($subtype !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_SUBTYPE', escape_html($subtype)));
        }
        if ($type_id !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_TYPE_ID', escape_html($type_id)));
        }

        // Days
        if ($days !== null) {
            $conditions->attach(do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT_DAYS', escape_html(integer_format($days))));
        }

        // Progress
        $progress = do_lang_tempcode('ACHIEVEMENT_PROGRESS', escape_html(integer_format($count_done)), escape_html(integer_format($count_required)));

        return do_lang_tempcode('ACHIEVEMENT_POINTS_RECEIVED_REQUIREMENT', protect_from_escaping($conditions), protect_from_escaping($progress));
    }
}
