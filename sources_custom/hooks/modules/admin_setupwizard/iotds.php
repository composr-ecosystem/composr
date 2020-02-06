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
     * @return array A pair: Main blocks and Side blocks (each is a map of block names to display types)
     */
    public function get_blocks()
    {
        if (!addon_installed('iotds')) {
            return [];
        }

        return [['main_iotd' => ['YES_CELL', 'YES_CELL']], []];
    }
}
