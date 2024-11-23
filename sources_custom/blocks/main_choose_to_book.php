<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    booking
 */

// TODO (can optionally take a filter of what bookables to show) - for choosing what to book from a list of possibilities, with date ranges or recurrence-choice shown for input, depending on nature of each bookable
// Should show how many codes there are and how many taken

class Block_main_choose_to_book
{
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'booking';
        $info['parameters'] = [];
        return $info;
    }

    public function run()
    {
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('booking', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('calendar')) {
            return do_template('RED_ALERT', ['_GUID' => '2ae91f7b2de3538695b8ae8611cf0947', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('calendar'))]);
        }
        if (!addon_installed('ecommerce')) {
            return do_template('RED_ALERT', ['_GUID' => 'dedd91cf3aaa5e2a9f9c66307638f3e2', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
        }

        return make_string_tempcode('Not yet implemented');
    }
}
