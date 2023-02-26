<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_implicit_usergroups_patreon
{
    protected function tier_map()
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $map = mixed();
        $map = [];

        require_code('cns_groups');

        $group = get_option('patreon_group');
        if ($group != '') {
            $group_id = find_usergroup_id($group);
        } else {
            $group_id = null;
        }

        $tiers = explode("\n", get_option('patreon_tiers'));
        foreach ($tiers as $tier) {
            if (trim($tier) == '') {
                continue;
            }

            if (strpos($tier, '=') !== false) {
                $matches = [];
                preg_match('#^(.*?)\s*=\s*(.*)$#', $tier, $matches);
                $tier = $matches[1];
                $group = $matches[2];

                $tier_group_id = find_usergroup_id($group);
                if ($tier_group_id !== null) {
                    $map[$tier] = $tier_group_id;
                }
            } elseif ($group_id !== null) {
                $map[$tier] = $group_id;
            }
        }

        if ((empty($map)) && ($group_id !== null)) {
            $map = $group_id;
        }

        return $map;
    }

    /**
     * Finds the group IDs it is bound to.
     *
     * @return array A list of usergroup IDs
     */
    public function get_bound_group_ids() : array
    {
        if (!addon_installed('patreon')) {
            return [];
        }

        $group = get_option('patreon_group');
        if ($group == '') {
            return [];
        }

        $tier_map = $this->tier_map();
        if (is_integer($tier_map)) {
            return [$tier_map];
        }

        return array_values(array_unique($tier_map));
    }

    protected function _where($group_id)
    {
        $tier_map = $this->tier_map();

        if (is_integer($tier_map)) {
            return 'EXISTS (SELECT * FROM ' . get_table_prefix() . 'patreon_patrons WHERE p_member_id=id)';
        }


        $or_list = '';
        foreach ($tier_map as $tier => $_group_id) {
            if ($group_id === $_group_id) {
                if ($or_list != '') {
                    $or_list .= ' OR ';
                }
                $or_list .= db_string_equal_to('p_tier', $tier);
            }
        }
        if ($or_list == '') {
            return null; // Should never happen
        }

        return 'EXISTS (SELECT * FROM ' . get_table_prefix() . 'patreon_patrons WHERE p_member_id=id AND (' . $or_list . '))';
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
        if (!addon_installed('patreon')) {
            return [];
        }

        $where = $this->_where($group_id);
        if ($where === null) {
            return [];
        }
        return list_to_map('id', $GLOBALS['FORUM_DB']->query('SELECT * FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . $where, $max, $start));
    }

    /**
     * Finds a count of the members in the group.
     *
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @return integer count
     */
    public function get_member_list_count(int $group_id) : int
    {
        if (!addon_installed('patreon')) {
            return 0;
        }

        $where = $this->_where($group_id);
        if ($where === null) {
            return 0;
        }
        return $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . $where);
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
        if (!addon_installed('patreon')) {
            return false;
        }

        $tier_map = $this->tier_map();

        $tiers = $GLOBALS['SITE_DB']->query_select('patreon_patrons', ['p_tier'], ['p_member_id' => $member_id]);
        foreach ($tiers as $tier) {
            if ((isset($tier_map[$tier['p_tier']])) && ($tier_map[$tier['p_tier']] == $group_id)) {
                return true;
            }
        }

        return ($tiers !== null);
    }
}
