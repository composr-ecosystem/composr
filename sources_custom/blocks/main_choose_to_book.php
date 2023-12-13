<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

// TODO (can optionally take a filter of what bookables to show) - for choosing what to book from a list of possibilities, with date ranges or recurrence-choice shown for input, depending on nature of each bookable
// Should show how many codes there are and how many taken

class Block_main_choose_to_book
{
    public function run()
    {
        return new Tempcode();
    }
}
