<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    usergroup_field_match
 */

/**
 * Hook class.
 */
class Hook_implicit_usergroups_usergroup_field_match
{
    protected function _get_structure()
    {
        if (!function_exists('get_translated_text')) {
            return [];
        }

        static $out = null;
        if ($out !== null) {
            return $out;
        }

        $out = [];
        $_groups = persistent_cache_get('OPEN_GROUPS');
        if ($_groups === null) {
            $_groups = $GLOBALS['FORUM_DB']->query_select('f_groups', ['id', 'g_name'], ['g_open_membership' => 1]);
            persistent_cache_set('OPEN_GROUPS', $_groups);
        }
        $groups = [];
        foreach ($_groups as $g) {
            $groups[get_translated_text($g['g_name'], $GLOBALS['FORUM_DB'])] = $g['id'];
        }

        $list_cpfs = persistent_cache_get('LIST_CPFS');
        if ($list_cpfs === null) {
            $list_cpfs = $GLOBALS['FORUM_DB']->query_select('f_custom_fields', ['id', 'cf_default'], ['cf_type' => 'list']);
            persistent_cache_set('LIST_CPFS', $list_cpfs);
        }
        foreach ($list_cpfs as $c) {
            $values = explode('|', $c['cf_default']);
            foreach ($values as $v) {
                if (($v != '') && (isset($groups[$v]))) {
                    if (!isset($out[$groups[$v]])) {
                        $out[$groups[$v]] = [];
                    }
                    $out[$groups[$v]][] = [$c['id'], $v];    // group id => [ {CPF id, CPF value / group name} ]
                }
            }
        }

        return $out;
    }

    /**
     * Finds the group IDs it is bound to.
     *
     * @return array A list of usergroup IDs
     */
    public function get_bound_group_ids() : array
    {
        if (!addon_installed('usergroup_field_match')) {
            return [];
        }

        return array_keys($this->_get_structure());
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
        if (!addon_installed('usergroup_field_match')) {
            return [];
        }

        $out = [];

        $structure = $this->_get_structure();
        $for_group = $structure[$group_id];
        foreach ($for_group as $pairs) {
            $cpf_key = 'field_' . strval($pairs[0]);
            $_members = $GLOBALS['FORUM_DB']->query_select('f_member_custom_fields', ['mf_member_id'], [$cpf_key => $pairs[1]], '', $max, $start);
            foreach ($_members as $m) {
                $member_id = $m['mf_member_id'];
                $out[$member_id] = $GLOBALS['FORUM_DRIVER']->get_member_row($member_id);
            }
        }

        return $out;
    }

    /**
     * Finds a count of the members in the group.
     *
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @return integer count
     */
    public function get_member_list_count(int $group_id) : int
    {
        if (!addon_installed('usergroup_field_match')) {
            return 0;
        }

        $structure = $this->_get_structure();
        $for_group = $structure[$group_id];

        if (count($for_group) == 1) {
            $pairs = $for_group[0];
            $cpf_key = 'field_' . strval($pairs[0]);
            return $GLOBALS['FORUM_DB']->query_select_value('f_member_custom_fields', 'COUNT(*)', [$cpf_key => $pairs[1]]);
        }

        // Much more complex if multiple CPFs are mapped, we need to find all and de-dupe
        $out = [];

        foreach ($for_group as $pairs) {
            $cpf_key = 'field_' . strval($pairs[0]);
            $_members = $GLOBALS['FORUM_DB']->query_select('f_member_custom_fields', ['mf_member_id'], [$cpf_key => $pairs[1]]);
            foreach ($_members as $m) {
                $member_id = $m['mf_member_id'];
                $out[$member_id/*automatic de-dupe*/] = true;
            }
        }

        return count($out);
    }

    /**
     * Finds whether the member is within the implicit usergroup.
     *
     * @param  MEMBER $member_id The member ID
     * @param  GROUP $group_id The group ID to check (if only one group supported by the hook, can be ignored)
     * @return boolean Whether they are
     */
    public function is_member_within(int $member_id, int $group_id) : bool
    {
        if (!addon_installed('usergroup_field_match')) {
            return false;
        }

        static $cache = []; // So finding if member in each, is quick

        $structure = $this->_get_structure();
        $for_group = $structure[$group_id];

        foreach ($for_group as $pairs) {
            $cpf_key = 'field_' . strval($pairs[0]);

            if (isset($cache[$member_id][$cpf_key])) {
                $cpf_value_actual = $cache[$member_id][$cpf_key];
            } else {
                $cpf_value_actual = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_member_custom_fields', $cpf_key, ['mf_member_id' => $member_id]);
                $cache[$member_id][$cpf_key] = $cpf_value_actual;
            }

            if ($cpf_value_actual == $pairs[1]) {
                return true;
            }
        }

        return false;
    }
}
