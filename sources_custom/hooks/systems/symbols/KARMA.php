<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_symbol_KARMA
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('karma')) {
            return '';
        }

        // Param 0 is member ID. Defaults to current member.
        if ((empty($param[0]))) {
            $member_id = get_member();
        } else {
            $member_id = intval($param[0]);
        }

        if (is_guest($member_id)) {
            return '0'; // Guests have no karma
        }

        require_code('karma');
        $karma = get_karma($member_id);

        if (empty($param[1])) {
            $param[1] = '0';
        }

        // Param 1: the karma to return. 0 = total karma (good - bad karma), 1 = good karma, 2 = bad karma.
        switch ($param[1]) {
            case '0':
                return strval($karma[0] - $karma[1]);
            case '1':
                if (!has_privilege(get_member(), 'view_bad_karma')) {
                    return '';
                }
                return strval($karma[0]);
            case '2':
                if (!has_privilege(get_member(), 'view_bad_karma')) {
                    return '';
                }
                return strval($karma[1]);
        }

        return '';
    }
}
