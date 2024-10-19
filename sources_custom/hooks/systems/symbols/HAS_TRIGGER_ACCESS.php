<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    early_access
 */

/**
 * Hook class.
 */
class Hook_symbol_HAS_TRIGGER_ACCESS
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run($param)
    {
        if (!addon_installed('early_access')) {
            return '0';
        }

        require_code('early_access');
        return check_has_special_page_access_for_triggers($param) ? '1' : '0';
    }
}
