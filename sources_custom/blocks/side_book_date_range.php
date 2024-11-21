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

// TODO (can optionally take a filter of what bookables to allow choosing from) - for date ranges

class Block_side_book_date_range
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
            return do_template('RED_ALERT', ['_GUID' => '9b2c5b83e3aa54bb8be197ed2e9fafd1', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('calendar'))]);
        }
        if (!addon_installed('ecommerce')) {
            return do_template('RED_ALERT', ['_GUID' => '4ae9405b41d15bf782f84bc2c8c1a77c', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
        }

        return make_string_tempcode('Not yet implemented');
    }
}
