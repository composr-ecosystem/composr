<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    world_regions
 */

/**
 * Hook class.
 */
class Hook_symbol_STATE_CODE_TO_NAME
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('world_regions')) {
            return '';
        }

        $value = '';

        if (!empty($param[0])) {
            require_code('locations/us');
            $value = find_state_name_from_code_US($param[0]);
        }

        return $value;
    }
}
