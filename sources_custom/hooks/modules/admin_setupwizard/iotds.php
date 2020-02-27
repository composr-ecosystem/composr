<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_sw_iotds
{
    /**
     * Run function for blocks in the setup wizard.
     *
     * @return array A map between block names and pairs (BLOCK_POSITION_* constants for what is supported, then a BLOCK_POSITION_* constant for what is the default)
     */
    public function get_blocks()
    {
        if (!addon_installed('iotds')) {
            return [];
        }

        return ['main_iotd' => [BLOCK_POSITION_CELL, null]];
    }
}
