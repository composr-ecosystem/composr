<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

function get_patreon_patrons_on_minimum_level($level)
{
    $patreon_patrons = [];

    require_code('files_spreadsheets_read');
    $sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/data_custom/patreon_patrons.csv');
    while (($row = $sheet_reader->read_row()) !== false) {
        if (intval($row['as_level']) < $level) {
            continue;
        }

        $patreon_patrons[] = [
            'name' => $row['name'],
            'username' => $row['username'],
            'monthly' => intval($row['as_level']),
        ];
    }
    $sheet_reader->close();

    sort_maps_by($patreon_patrons, 'name', false, true);

    return $patreon_patrons;
}
