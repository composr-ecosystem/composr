<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
    public function run($param)
    {
        require_code('points');
        $member = isset($param[0]) ? intval($param[0]) : get_member();
        $value = strval(total_points($member) - non_overridden__total_points($member));
        return $value;
    }
}
