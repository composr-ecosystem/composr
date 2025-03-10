<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    locations_catalogues
 */

function init__locations_catalogues_install()
{
    disable_php_memory_limit();
    cms_disable_time_limit();
}

function install_location_data()
{
    // TODO: #6162
    warn_exit('Currently, location catalogues are disabled as the new locations library broke compatibility with this addon. See tracker issue 6162.');

    require_code('files');
    require_code('locations');
    require_code('files_spreadsheets_read');

    // Open WorldGazetteer.csv
    $locations = [];
    $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/locations/WorldGazetteer.csv');
    while (($line = $sheet_reader->read_row()) !== false) {
        if ($line['Latitude'] != '') {
            $line['Latitude'] = float_to_raw_string(floatval($line['Latitude']) / 100.0, 10);
            $line['Longitude'] = float_to_raw_string(floatval($line['Longitude']) / 100.0, 10);
        }

        // Fix inconsistencies
        $line['Country'] = preg_replace('#^(Smaller|External) Territories of (the )?#', '', $line['Country']);
        if ($line['Country'] == 'UK') {
            $line['Country'] = 'United Kingdom';
        }
        if ($line['Country'] == 'Reunion') {
            $line['Country'] = 'France';
        }

        $locations[] = $line;
    }
    $sheet_reader->close();

    // Load US locations using CivicSpace-zipcodes.csv
    $us_locations = [];
    require_code('locations/us');
    $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/locations/CivicSpace-zipcodes.csv');
    while (($line = $sheet_reader->read_row()) !== false) {
        $state_name = state_code_to_state_name($line['state']);
        $us_locations[$state_name][$line['city']] = $line;
    }
    $sheet_reader->close();

    // Load World locations using World_Cities_Location_table.csv
    $world_locations = [];
    require_code('locations/us');
    $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/locations/World_Cities_Location_table.csv');
    while (($line = $sheet_reader->read_row()) !== false) {
        // Fix inconsistencies
        if ($line['Country'] == 'United States') {
            $line['Country'] = 'United States of America';
        }

        $world_locations[$line['Country']][$line['City']] = $line;
    }
    $sheet_reader->close();

    // Delete current data
    $GLOBALS['SITE_DB']->query_delete('locations');

    // Merge it all together, and put it into DB
    foreach ($locations as $location) {
        if ($location['Latitude'] == '') {
            if (($location['Country'] == 'United States of America') && ($location['Type'] == 'locality')) {
                // Get match for latitude / longitude in CivicSpace-zipcodes.csv
                if (isset($us_locations[$location['Parent1']][$location['Name']])) {
                    $us_location = $us_locations[$location['Parent1']][$location['Name']];
                    $location['Latitude'] = $us_location['latitude'];
                    $location['Longitude'] = $us_location['longitude'];
                }
            }
        }

        if ($location['Latitude'] == '') {
            // Get match for latitude / longitude in World_Cities_Location_table.csv
            if (isset($world_locations[$location['Country']][$location['Name']])) {
                $world_location = $world_locations[$location['Country']][$location['Name']];
                $location['Latitude'] = $world_location['Latitude'];
                $location['Longitude'] = $world_location['Longitude'];
            }
        }

        $GLOBALS['SITE_DB']->query_insert('locations', [
            'l_place' => $location['Name'],
            'l_type' => $location['Type'],
            'l_continent' => find_continent($location['Country']),
            'l_country' => $location['Country'],
            'l_parent_1' => $location['Parent1'],
            'l_parent_2' => $location['Parent2'],
            'l_parent_3' => $location['Parent3'],
            'l_population' => intval($location['Population']),
            'l_latitude' => ($location['Latitude'] == '') ? null : floatval($location['Latitude']),
            'l_longitude' => ($location['Longitude'] == '') ? null : floatval($location['Longitude']),
        ]);
    }
}

/**
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param  string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters
 */
function remove_accents(string $string) : string
{
    if (!preg_match('/[\x80-\xff]/', $string)) {
        return $string;
    }

    static $chars = null;
    if ($chars === null) {
        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
            // Euro Sign
            chr(226) . chr(130) . chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194) . chr(163) => '',
        ];
    }

    return strtr($string, $chars);
}

function _worldcities_remaining_locations()
{
    require_code('locations');

    // Load US locations using worldcitiespop.csv
    $many_locations = [];
    require_code('files_spreadsheets_read');
    $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/locations/worldcitiespop.csv');
    while (($line = $sheet_reader->read_row()) !== false) {
        $country_name = find_country_name_from_iso(cms_strtoupper_ascii($line['Country']));
        $many_locations[$country_name][$line['City']] = $line['Latitude'] . ',' . $line['Longitude'];
        $many_locations[$country_name][cms_mb_strtolower($line['AccentCity'])] = $line['Latitude'] . ',' . $line['Longitude'];
        $many_locations[$country_name][cms_mb_strtolower(remove_accents($line['AccentCity']))] = $line['Latitude'] . ',' . $line['Longitude'];
    }
    $sheet_reader->close();

    $from = 0;
    do {
        $unknown = $GLOBALS['SITE_DB']->query('SELECT id,l_country,l_place FROM ' . get_table_prefix() . 'locations WHERE l_latitude IS NULL AND id>' . strval($from) . ' ORDER BY id', 1000);

        foreach ($unknown as $location) {
            $place = cms_mb_strtolower(remove_accents($location['l_place']));
            if (isset($many_locations[$location['l_country']][$place])) {
                list($latitude, $longitude) = explode(',', $many_locations[$location['l_country']][$place]);
                $GLOBALS['SITE_DB']->query_update('locations', ['l_latitude' => floatval($latitude), 'l_longitude' => floatval($longitude)], ['id' => $location['id']], '', 1);
            }

            $from = $location['id'];
        }
    } while (!empty($unknown));
}

function transcode_remaining_locations()
{
    //if (file_exists(get_file_base().'/data_custom/locations/worldcitiespop.csv')) _worldcities_remaining_locations();

    $errored = 0;

    require_code('locations_geocoding');
    $service = null;
    choose_geocoding_service($service);

    $from = 0;
    do {
        $unknown = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'locations WHERE l_latitude IS NULL AND id>' . strval($from) . ' ORDER BY id', 100);

        if ($service == 'mapquest') {
            // For MapQuest we do bulk conversions...

            $url = 'http://www.mapquestapi.com/geocoding/v1/batch?key=' . urlencode(get_option('mapquest_geocoding_api_key')) . '&callback=renderBatch&outFormat=xml';
            foreach ($unknown as $i => $location) {
                $lstring = '{city="' . remove_accents($location['l_place']) . '", country="' . $location['l_country'] . '"}';
                $url .= '&location=' . urlencode($lstring);

                $unknown[$i]['l_string'] = $lstring;

                $from = $location['id'];
            }

            $_result = http_get_contents($url, ['convert_to_internal_encoding' => true]);

            $matches = [];
            if (strpos($_result, '<lat>') !== false) {
                foreach ($unknown as $i => $location) {
                    $matches = [];
                    if (preg_match('#<location>' . preg_quote($location['l_string'], '#')/*<<< xmlentities doesn't work around this for some reason*/ . '</location>.*<geocodeQualityCode>(.*)</geocodeQualityCode>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>#sU', $_result, $matches) != 0) {
                        if (($matches[1] == 'A5XAX') || ($matches[1] == 'A5XBX')) {
                            $GLOBALS['SITE_DB']->query_update('locations', ['l_latitude' => floatval($matches[2]), 'l_longitude' => floatval($matches[3])], ['id' => $location['id']], '', 1);
                        }
                    }
                }
                $errored = 0;
            } else {
                $errored++;
            }

            if ($errored == 10) {
                warn_exit($_result);
            }
        } else {
            foreach ($unknown as $location) {
                $lstring = $location['l_place'] . ', ' . $location['l_parent_3'] . ', ' . $location['l_parent_2'] . ', ' . $location['l_parent_1'] . ', ' . $location['l_country'];
                $error_msg = new Tempcode();
                $result = geocode($lstring, $error_msg);
                if ($result !== null) {
                    list($latitude, $longitude) = $result;
                    $GLOBALS['SITE_DB']->query_update('locations', ['l_latitude' => $latitude, 'l_longitude' => $longitude], ['id' => $location['id']], '', 1);
                    $errored = 0;
                } else {
                    $errored++;
                }

                if ($errored == 10) {
                    warn_exit($error_msg);
                }

                $from = $location['id'];
            }
        }
    } while (!empty($unknown));
}

function create_catalogue_category_tree($catalogue_name = 'places', $country = null, $fixup = false/*used to fix old bug where countries without regions were not imported*/)
{
    if ($country === null) { // We will do this by looping through each country, recursing back into this function to do just this country. This will take about 5 hours to run, so it's important to be able to do in a measured way.
        $countries = $GLOBALS['SITE_DB']->query_select('locations', ['DISTINCT l_country'], [], 'ORDER BY l_country');
        foreach ($countries as $country) {
            create_catalogue_category_tree($catalogue_name, $country['l_country'], $fixup);
            echo('Done ' . $country['l_country'] . "\n");
            cms_flush_safe();
        }

        // Recalculate continent bounds, as will be outdated
        recalculate_continent_bounds($catalogue_name);

        set_value('disable_cat_cat_perms', '1');

        return;
    }

    static $root_cat = null;
    if ($root_cat === null) {
        $root_cat = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'MIN(id)', ['c_name' => $catalogue_name]);
    }

    $locations = $GLOBALS['SITE_DB']->query_select('locations', ['*'], ['l_country' => $country] + ($fixup ? ['l_parent_1' => ''] : []));

    // Go through from deepest, ensuring tree structure for each
    $full_tree = [];
    require_code('catalogues2');
    require_code('fields');
    foreach ($locations as $location) {
        $location['l_depth'] = 0;
        if ($location['l_parent_3'] != '') {
            $location['l_depth'] = 3;
        } elseif ($location['l_parent_2'] != '') {
            $location['l_depth'] = 2;
        } elseif ($location['l_parent_1'] != '') {
            $location['l_depth'] = 1;
        }

        if (intval($location['l_population']) < 100) {
            continue; // Too small to consider
        }
        if ($location['l_longitude'] === null) {
            continue; // No info, probably an error or unconventional location
        }

        $tree_pos = [];
        $tree_pos[] = $location['l_continent'];
        $tree_pos[] = $location['l_country'];
        if ($location['l_parent_1'] != '') {
            $tree_pos[] = $location['l_parent_1'];
        }
        if ($location['l_parent_2'] != '') {
            $tree_pos[] = $location['l_parent_2'];
        }
        if ($location['l_parent_3'] != '') {
            $tree_pos[] = $location['l_parent_3'];
        }
        $tree_pos[] = $location['l_place'];

        // Save into full tree
        switch (count($tree_pos)) {
            case 3:
                $full_tree[$tree_pos[0]]['children'][$tree_pos[1]]['children'][$tree_pos[2]]['details'] = $location;
                break;
            case 4:
                $full_tree[$tree_pos[0]]['children'][$tree_pos[1]]['children'][$tree_pos[2]]['children'][$tree_pos[3]]['details'] = $location;
                break;
            case 5:
                $full_tree[$tree_pos[0]]['children'][$tree_pos[1]]['children'][$tree_pos[2]]['children'][$tree_pos[3]]['children'][$tree_pos[4]]['details'] = $location;
                break;
            case 6:
                $full_tree[$tree_pos[0]]['children'][$tree_pos[1]]['children'][$tree_pos[2]]['children'][$tree_pos[3]]['children'][$tree_pos[4]]['children'][$tree_pos[5]]['details'] = $location;
                break;
            case 7:
                $full_tree[$tree_pos[0]]['children'][$tree_pos[1]]['children'][$tree_pos[2]]['children'][$tree_pos[3]]['children'][$tree_pos[4]]['children'][$tree_pos[5]]['children'][$tree_pos[6]]['details'] = $location;
                break;
        }
    }

    // Create root nodes under catalogue root
    static $fields = null;
    if ($fields === null) {
        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['*'], ['c_name' => '_catalogue_category'], 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
    }
    static $first_cat = null;
    if ($first_cat === null) {
        $first_cat = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'MIN(id)', ['c_name' => '_catalogue_category']);
    }
    if ($first_cat === null) { // Repair needed, must have a category
        require_code('catalogues2');
        require_lang('catalogues');
        actual_add_catalogue_category('_catalogue_category', do_lang('CUSTOM_FIELDS_FOR', do_lang('CATALOGUE_CATEGORY')), '', '', null);
    }
    foreach ($full_tree as $name => $child) {
        if (!array_key_exists('details', $child)) { // Continent, which is not in DB
            $child['details'] = [
                'l_latitude' => null,
                'l_longitude' => null,
            ];
        }

        _create_catalogue_subtree($fields, $first_cat, $catalogue_name, $root_cat, $full_tree, $child, [$name]);
    }
}

function _create_catalogue_subtree($fields, $first_cat, $catalogue_name, $root_cat, $full_tree, $node, $tree_pos)
{
    if (!array_key_exists('children', $node)) {
        $node['children'] = []; // Bottom level
    }

    $id = _create_catalogue_position($catalogue_name, $tree_pos, $root_cat, $node['details'], $full_tree);

    $node['details']['l_min_latitude'] = $node['details']['l_latitude'];
    $node['details']['l_max_latitude'] = $node['details']['l_latitude'];
    $node['details']['l_min_longitude'] = $node['details']['l_longitude'];
    $node['details']['l_max_longitude'] = $node['details']['l_longitude'];

    foreach ($node['children'] as $name => $child) {
        if (!array_key_exists('details', $child)) { // Continent, which is not in DB
            $child['details'] = [
                'l_latitude' => null,
                'l_longitude' => null,
            ];
        }

        $child = _create_catalogue_subtree($fields, $first_cat, $catalogue_name, $root_cat, $full_tree, $child, array_merge($tree_pos, [$name]));
        $node['children'][$name] = $child; // If updated

        // Work out latitude/longitude bounding boxes
        if (($child['details']['l_min_latitude'] !== null) && (($node['details']['l_min_latitude'] === null) || ($child['details']['l_min_latitude'] < $node['details']['l_min_latitude']))) {
            $node['details']['l_min_latitude'] = $child['details']['l_min_latitude'];
        }
        if (($child['details']['l_max_latitude'] !== null) && (($node['details']['l_max_latitude'] === null) || ($child['details']['l_max_latitude'] > $node['details']['l_max_latitude']))) {
            $node['details']['l_max_latitude'] = $child['details']['l_max_latitude'];
        }
        if (($child['details']['l_min_longitude'] !== null) && (($node['details']['l_min_longitude'] === null) || ($child['details']['l_min_longitude'] < $node['details']['l_min_longitude']))) {
            $node['details']['l_min_longitude'] = $child['details']['l_min_longitude'];
        }
        if (($child['details']['l_max_longitude'] !== null) && (($node['details']['l_max_longitude'] === null) || ($child['details']['l_max_longitude'] > $node['details']['l_max_longitude']))) {
            $node['details']['l_max_longitude'] = $child['details']['l_max_longitude'];
        }
    }

    if ($node['details']['l_latitude'] === null) {
        $node['details']['l_latitude'] = ($node['details']['l_min_latitude'] + $node['details']['l_max_latitude']) / 2;
    }
    if ($node['details']['l_longitude'] === null) {
        $node['details']['l_longitude'] = ($node['details']['l_min_longitude'] + $node['details']['l_max_longitude']) / 2;
    }

    // Save into category: bounding box, and own latitude/longitude if specified
    $map = [
        $fields[0]['id'] => float_to_raw_string($node['details']['l_latitude'], 10),
        $fields[1]['id'] => float_to_raw_string($node['details']['l_longitude'], 10),
        $fields[2]['id'] => float_to_raw_string($node['details']['l_min_latitude'], 10),
        $fields[3]['id'] => float_to_raw_string($node['details']['l_max_latitude'], 10),
        $fields[4]['id'] => float_to_raw_string($node['details']['l_min_longitude'], 10),
        $fields[5]['id'] => float_to_raw_string($node['details']['l_max_longitude'], 10),
    ];

    if ($id !== null) {
        $existing = get_bound_content_entry('catalogue_category', strval($id));
        if ($existing !== null) {
            actual_edit_catalogue_entry($existing, $first_cat, 1, '', 0, 0, 0, $map);
        } else {
            $catalogue_entry_id = actual_add_catalogue_entry($first_cat, 1, '', 0, 0, 0, $map);

            $GLOBALS['SITE_DB']->query_insert('catalogue_entry_linkage', [
                'catalogue_entry_id' => $catalogue_entry_id,
                'content_type' => 'catalogue_category',
                'content_id' => strval($id),
            ]);
        }
    }

    return $node;
}

function _create_catalogue_position($catalogue_name, $tree_pos, $cat, $location, &$tree)
{
    $added = false;

    static $cache = null;
    if ($cache === null) {
        $cache = [];
    }

    foreach ($tree_pos as $name) {
        $tree = &$tree['children'][$name];

        if (!isset($tree['cc_id'])) {
            $tree['cc_id'] = isset($cache[$cat][$name]) ? $cache[$cat][$name] : $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'id', ['cc_parent_id' => $cat, $GLOBALS['SITE_DB']->translate_field_ref('cc_title') => $name]);

            if ($tree['cc_id'] === null) {
                $tree['cc_id'] = actual_add_catalogue_category($catalogue_name, $name, '', '', $cat);
                $added = true;
            } else {
                $added = false;
            }

            $cache[$cat][$name] = $tree['cc_id'];
        }

        $cat = $tree['cc_id'];
    }

    if (!$added) {
        return null;
    }
    return $cat;
}

function recalculate_continent_bounds($catalogue_name = 'places')
{
    require_code('locations');
    $continents = find_continents();
    $first_cat = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'MIN(id)', ['c_name' => $catalogue_name]);
    foreach ($continents as $continent) {
        $category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'id', ['cc_parent_id' => $first_cat, $GLOBALS['SITE_DB']->translate_field_ref('cc_title') => $continent]);
        recalculate_bounding_long_lat($category_id);
    }
}

function recalculate_bounding_long_lat($category)
{
    static $fields = null;
    if ($fields === null) {
        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['*'], ['c_name' => '_catalogue_category'], 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
    }

    $min_latitude = null;
    $max_latitude = null;
    $min_longitude = null;
    $max_longitude = null;

    $subcategories = $GLOBALS['SITE_DB']->query_select('catalogue_categories', ['id'], ['cc_parent_id' => $category]);
    require_code('fields');
    foreach ($subcategories as $subcat) {
        $assocated_catalogue_entry_id = get_bound_content_entry('catalogue_category', strval($subcat['id']));

        $_min_latitude = $GLOBALS['SITE_DB']->query_select_value('catalogue_efv_float', 'cv_value', ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[2]['id']]);
        $_max_latitude = $GLOBALS['SITE_DB']->query_select_value('catalogue_efv_float', 'cv_value', ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[3]['id']]);
        $_min_longitude = $GLOBALS['SITE_DB']->query_select_value('catalogue_efv_float', 'cv_value', ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[4]['id']]);
        $_max_longitude = $GLOBALS['SITE_DB']->query_select_value('catalogue_efv_float', 'cv_value', ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[5]['id']]);

        if (($min_latitude === null) || ($_min_latitude < $min_latitude)) {
            $min_latitude = $_min_latitude;
        }
        if (($max_latitude === null) || ($_max_latitude > $max_latitude)) {
            $max_latitude = $_max_latitude;
        }
        if (($min_longitude === null) || ($_min_longitude < $min_longitude)) {
            $min_longitude = $_min_longitude;
        }
        if (($max_longitude === null) || ($_max_longitude > $max_longitude)) {
            $max_longitude = $_max_longitude;
        }
    }

    $assocated_catalogue_entry_id = get_bound_content_entry('catalogue_category', strval($category));

    $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $min_latitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[5]['id']], '', 1);
    $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $max_latitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[5]['id']], '', 1);
    $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $min_longitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[5]['id']], '', 1);
    $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $max_longitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[5]['id']], '', 1);
}
