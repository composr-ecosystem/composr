<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    usergroup_intersection
 */

/**
 * Hook class.
 */
class Hook_implicit_usergroups_usergroup_intersection
{
    /**
     * Finds the group IDs it is bound to.
     *
     * @return array A list of usergroup IDs.
     */
    public function get_bound_group_ids() : array
    {
        return [203]; // Change this to the ID of the target usergroup
    }

    public function get_intersected_group_ids()
    {
        return [12, 2]; // Change this to the IDs of the usergroups we're doing the intersection of
    }

    protected function _where()
    {
        $db = $GLOBALS['FORUM_DB'];
        $sql = '';
        foreach ($this->get_intersected_group_ids() as $group_id) {
            if ($sql != '') {
                $sql .= ' AND ';
            }
            $sql .= '(m_primary_group=' . strval($group_id) . ' OR EXISTS(SELECT * FROM ' . $db->get_table_prefix() . 'f_group_members WHERE gm_member_id=m.id AND gm_group_id=' . strval($group_id) . '))';
        }
        return $sql;
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
        if (!addon_installed('usergroup_intersection')) {
            return [];
        }

        return list_to_map('id', $GLOBALS['FORUM_DB']->query('SELECT * FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m WHERE ' . $this->_where(), $max, $start));
    }

    /**
     * Finds a count of the members in the group.
     *
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @return integer count
     */
    public function get_member_list_count(int $group_id) : int
    {
        if (!addon_installed('usergroup_intersection')) {
            return 0;
        }

        return $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m WHERE ' . $this->_where());
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
        if (!addon_installed('usergroup_intersection')) {
            return false;
        }

        if ($member_id == get_member()) {
            $groups_in = cns_get_members_groups($member_id, false, true, false);
            foreach ($this->get_intersected_group_ids() as $group_id) {
                if (!array_key_exists($group_id, $groups_in)) {
                    return false;
                }
            }
            return true;
        }

        $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m WHERE (' . $this->_where() . ') AND id=' . strval($member_id);
        return ($GLOBALS['FORUM_DB']->query_value_if_there($sql) !== null);
    }
}
