<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    antispam_question
 */

/**
 * Hook class.
 */
class Hook_implicit_usergroups_antispam_question
{
    /*
    Add a CPF something like...

    Name = What is Composr a kind of?
    Description = This question is designed to reduce the number of spammers we have joining the site. It indicates you have at least some interest in what you're signing up to.
    Default value = Commercial Maintenance System|Commercial Management Solution|Contacts Maintenance System|Contacts Management Solution|Content Maintenance Solution|Content Management System
    Owner viewable = no
    Owner settable = no
    Publicly viewable = no
    Type = A value chosen from a list
    Field options = widget=radio
    Required field = yes
    Show on the join form = yes
    */

    protected $field_name = 'What is Composr a kind of?';
    protected $expected_answer = 'Content Management System';

    protected $field_id = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $ANTISPAM_QUESTION_FIELD_ID;
        if (!isset($ANTISPAM_QUESTION_FIELD_ID)) {
            $ANTISPAM_QUESTION_FIELD_ID = mixed();
            $ANTISPAM_QUESTION_FIELD_ID = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_custom_fields', 'id', [$GLOBALS['FORUM_DB']->translate_field_ref('cf_name') => $this->field_name]);
            if ($ANTISPAM_QUESTION_FIELD_ID === null) {
                $ANTISPAM_QUESTION_FIELD_ID = false;
            }
        }

        $this->field_id = ($ANTISPAM_QUESTION_FIELD_ID === false) ? null : $ANTISPAM_QUESTION_FIELD_ID;
    }

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
        require_code('cns_members');
        $mappings = cns_get_custom_field_mappings($GLOBALS['FORUM_DRIVER']->get_guest_id());
        $f = 'field_' . strval($this->field_id);
        if (!isset($mappings[$f])) {
            // So it does not crash if the CPF does not exist
            return '1=0';
        }

        return '(SELECT field_' . strval($this->field_id) . ' FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields mcf WHERE mcf.mf_member_id=id) NOT IN (\'\', \'' . db_escape_string($this->expected_answer) . '\')';
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
        if ($this->field_id === null) {
            return [];
        }

        if (!addon_installed('antispam_question')) {
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
        if ($this->field_id === null) {
            return 0;
        }

        if (!addon_installed('antispam_question')) {
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
        if ($this->field_id === null) {
            return false;
        }

        if (!addon_installed('antispam_question')) {
            return false;
        }

        $is_exclusive = true;

        require_code('cns_members');
        if (!function_exists('cns_get_custom_field_mappings')) {
            return false; // Startup loop with keep_safe_mode=1
        }
        $mappings = cns_get_custom_field_mappings($member_id);
        $f = 'field_' . strval($this->field_id);
        if (!isset($mappings[$f])) {
            return false;
        }
        $val = $mappings[$f];
        return ($val !== null) && ($val != $this->expected_answer);
    }
}
