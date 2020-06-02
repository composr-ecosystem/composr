<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

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
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('booking', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('calendar')) {
            return do_template('RED_ALERT', ['_GUID' => '1ayp8sbbibrvi0946jt8xqs8bm2p6rdy', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('calendar'))]);
        }
        if (!addon_installed('ecommerce')) {
            return do_template('RED_ALERT', ['_GUID' => 's6qmg4mfv0x5d7ozrvoskkdape0opr7j', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
        }

        return new Tempcode();
    }
}
