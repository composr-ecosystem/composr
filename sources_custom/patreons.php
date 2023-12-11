<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

function get_patreons_on_minimum_level($level)
{
    $patreons = array();

    $myfile = fopen(get_custom_file_base() . '/data_custom/patreons.csv', 'rb');
    fgetcsv($myfile); // Skip header
    while (($row = fgetcsv($myfile)) !== false) {
        if (!isset($row[2])) {
            continue;
        }

        if (intval($row[2]) < $level) {
            continue;
        }

        $patreons[] = array(
            'name' => $row[0],
            'username' => $row[1],
            'monthly' => intval($row[2]),
        );
    }
    fclose($myfile);

    sort_maps_by($patreons, 'name');

    return $patreons;
}
