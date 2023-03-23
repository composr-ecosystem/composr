<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    locations_catalogues
 */

function init__locations_catalogues_geopositioning()
{
    define('MIN_LAT', deg2rad(-90.0));
    define('MAX_LAT', deg2rad(90.0));
    define('MIN_LON', deg2rad(-180.0));
    define('MAX_LON', deg2rad(180.0));

    define('EARTH_RADIUS', 6371010.0); // In m

    define('GEO_SEARCH_EXPANSION_FACTOR', 1.3);
}

function fix_geoposition($lstring, $category_id)
{
    $lstring = preg_replace('#, (Africa|Americas|Asia|Europe|Oceania)$#', '', $lstring); // Confuses Bing

    require_code('locations_geocoding');
    $result = geocode($lstring);
    if ($result !== null) {
        list($latitude, $longitude) = $result;

        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['*'], ['c_name' => '_catalogue_category'], 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
        require_code('content');
        require_code('fields');
        $assocated_catalogue_entry_id = get_bound_content_entry('catalogue_category', strval($category_id));

        $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $latitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[0]['id']], '', 1);
        $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', ['cv_value' => $longitude], ['ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[1]['id']], '', 1);

        return '1';
    }
    return '0';
}

function find_nearest_location($latitude, $longitude, $latitude_field_id = null, $longitude_field_id = null, $error_tolerance = null)
{
    if ($error_tolerance === null) { // Ah, pick a default
        $error_tolerance = 50.0 / EARTH_RADIUS; // 50 metres radius
    }

    // ====
    // Much help from http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates

    $rad_lat = deg2rad($latitude);
    $rad_lon = deg2rad($longitude);

    $min_lat = $rad_lat - $error_tolerance;
    $max_lat = $rad_lat + $error_tolerance;

    if ($min_lat > MIN_LAT && $max_lat < MAX_LAT) {
        $delta_lon = asin(sin($error_tolerance) / cos($rad_lat));
        $min_lon = $rad_lon - $delta_lon;
        if ($min_lon < MIN_LON) {
            $min_lon += 2.0 * M_PI;
        }
        $max_lon = $rad_lon + $delta_lon;
        if ($max_lon > MAX_LON) {
            $max_lon -= 2.0 * M_PI;
        }
    } else {
        // a pole is within the distance
        $min_lat = max($min_lat, MIN_LAT);
        $max_lat = min($max_lat, MAX_LAT);
        $min_lon = MIN_LON;
        $max_lon = MAX_LON;
    }

    $meridian_180_within_distance = ($min_lon > $max_lon);

    $min_lat = rad2deg($min_lat);
    $max_lat = rad2deg($max_lat);
    $min_lon = rad2deg($min_lon);
    $max_lon = rad2deg($max_lon);

    $where = '(l_latitude>=' . float_to_raw_string($min_lat, 10) . ' AND l_latitude<=' . float_to_raw_string($max_lat, 10) . ') AND (l_longitude>=' . float_to_raw_string($min_lon, 10) . ' ' .
             ($meridian_180_within_distance ? 'OR' : 'AND') . ' l_longitude<=' . float_to_raw_string($max_lon, 10) . ') AND ' .
             'acos(sin(' . float_to_raw_string($rad_lat, 10) . ')*sin(radians(l_latitude))+cos(' . float_to_raw_string($rad_lat, 10) . ')*cos(radians(l_latitude))*cos(radians(l_longitude)-' . float_to_raw_string($rad_lon, 10) . '))<=' . float_to_raw_string($error_tolerance, 10);

    // ==== ^^^

    if (($latitude_field_id === null) || ($longitude_field_id === null)) { // Just do a raw query on locations table
        $query = 'SELECT * FROM ' . get_table_prefix() . 'locations WHERE ' . $where;
        $locations = $GLOBALS['SITE_DB']->query($query);
    } else { // Catalogue query (works both for entries and categories that use custom fields)
        $where = str_replace(['l_latitude', 'l_longitude'], ['a.cv_value', 'b.cv_value'], $where);
        $query = 'SELECT a.ce_id,c.id,cc_title,a.cv_value AS l_latitude,b.cv_value AS l_longitude FROM ' . get_table_prefix() . 'catalogue_efv_float a JOIN ' . get_table_prefix() . 'catalogue_efv_float b ON a.ce_id=b.ce_id AND a.cf_id=' . strval($latitude_field_id) . ' AND b.cf_id=' . strval($longitude_field_id) . ' LEFT JOIN ' . get_table_prefix() . 'catalogue_entry_linkage x ON a.ce_id=x.catalogue_entry_id AND ' . db_string_equal_to('x.content_type', 'catalogue_category') . ' LEFT JOIN ' . get_table_prefix() . 'catalogue_categories c ON ' . db_cast('c.id', 'CHAR') . '=x.content_id WHERE ' . $where;
        $locations = $GLOBALS['SITE_DB']->query($query, null, 0, false, false, ['cc_title' => 'SHORT_TRANS']);
    }

    if (empty($locations)) {
        if ($error_tolerance >= 1.0) {
            return null; // Nothing, in whole world
        }

        return find_nearest_location($latitude, $longitude, $latitude_field_id, $longitude_field_id, $error_tolerance * GEO_SEARCH_EXPANSION_FACTOR);
    }

    $best = null;
    $best_at = null;
    foreach ($locations as $l) {
        $dist = latlong_distance_miles($l['l_latitude'], $l['l_longitude'], $latitude, $longitude);

        if (($best === null) || ($dist < $best)) {
            $best = $dist;
            $best_at = $l;
        }
    }
    $locations = [$best_at];

    return $locations[0];
}

function latlong_distance_miles($lat1, $lng1, $lat2, $lng2, $miles = true)
{
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lng1 *= $pi80;
    $lat2 *= $pi80;
    $lng2 *= $pi80;

    $r = 6372.797; // mean radius of Earth in km
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1.0 - $a));
    $km = $r * $c;

    return ($miles ? ($km * 0.621371192) : $km);
}
