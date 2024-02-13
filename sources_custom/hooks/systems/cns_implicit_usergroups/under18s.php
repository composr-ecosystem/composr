<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    under18s
 */

/**
 * Hook class.
 */
class Hook_implicit_usergroups_under18s
{
    /**
     * Finds the group IDs it is bound to.
     *
     * @return array A list of usergroup IDs
     */
    public function get_bound_group_ids() : array
    {
        require_code('cns_groups');
        $probation_group_id = get_probation_group(); // Customise as required
        if ($probation_group_id === null) {
            $probation_group_id = db_get_first_id(); // Guests then
        }
        return [$probation_group_id];
    }

    protected function _where()
    {
        $eago = intval(date('Y')) - 18;
        return 'm_dob_year>' . strval($eago) . ' OR m_dob_year=' . strval($eago) . ' AND (m_dob_month>' . date('m') . ' OR m_dob_month=' . date('m') . ' AND m_dob_day>=' . date('d') . ')';
    }

    /**
     * Finds all members in the group.
     *
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @param  ?integer $max Return up to this many entries for members (null: no limit)
     * @param  integer $start Return members after this offset
     * @return ?array The list of members as a map between member ID and member row (null: unsupported by hook)
     */
    public function get_member_list(int $group_id, ?int $max = null, int $start = 0) : ?array
    {
        if (!addon_installed('under18s')) {
            return [];
        }

        return list_to_map('id', $GLOBALS['FORUM_DB']->query('SELECT * FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . $this->_where(), $max, $start));
    }

    /**
     * Finds a count of the members in the group.
     *
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @return integer count
     */
    public function get_member_list_count(int $group_id) : int
    {
        if (!addon_installed('under18s')) {
            return 0;
        }

        return $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . $this->_where());
    }

    /**
     * Finds whether the member is within the implicit usergroup.
     *
     * @param  MEMBER $member_id The member ID
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @param  ?boolean $is_exclusive Return-by-reference if the member should *only* be in this usergroup (null: initially unset)
     * @return boolean Whether they are
     */
    public function is_member_within(int $member_id, int $group_id, ?bool &$is_exclusive = null) : bool
    {
        if (!addon_installed('under18s')) {
            return false;
        }

        if ($member_id == get_member()) {
            // IDEA: #3830 Support timezones, decide age based on user's own timezone

            $eago = intval(date('Y')) - 18;
            $row = $GLOBALS['FORUM_DRIVER']->get_member_row($member_id);
            $dob_year = $row['m_dob_year'];
            $dob_month = $row['m_dob_month'];
            $dob_day = $row['m_dob_day'];
            return $dob_year > $eago || $dob_year == $eago && ($dob_month > intval(date('m')) || $dob_month == intval(date('m')) && $dob_day >= intval(date('d')));
        }

        $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE (' . $this->_where() . ') AND id=' . strval($member_id);
        return ($GLOBALS['FORUM_DB']->query_value_if_there($sql) !== null);
    }
}
