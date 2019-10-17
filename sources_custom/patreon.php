<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

function get_patreon_patrons_on_minimum_level($level)
{
    $patreon_patrons = array();

    $myfile = fopen(get_custom_file_base() . '/data_custom/patreon_patrons.csv', 'rb');
    // TODO: #3032 (must default charset to utf-8 if no BOM though)
    fgetcsv($myfile); // Skip header
    while (($row = fgetcsv($myfile)) !== false) {
        if (!isset($row[2])) {
            continue;
        }

        if (intval($row[2]) < $level) {
            continue;
        }

        $patreon_patrons[] = array(
            'name' => $row[0],
            'username' => $row[1],
            'monthly' => intval($row[2]),
        );
    }
    fclose($myfile);

    sort_maps_by($patreon_patrons, 'name', false, true);

    return $patreon_patrons;
}
