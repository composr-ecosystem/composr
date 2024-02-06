<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    group_points
 */

/**
 * Hook class.
 */
class Hook_symbol_POINTS_FROM_USERGROUPS
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('group_points')) {
            return '';
        }

        if (!addon_installed('points')) {
            return '';
        }

        require_code('points');
        $member_id = ((isset($param[0])) && (is_numeric($param[0]))) ? intval($param[0]) : get_member();
        $value = strval(points_lifetime($member_id) - non_overridden__points_lifetime($member_id));
        return $value;
    }
}
