<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
    $type = 'google'; // Other values not supported

    $lstring = preg_replace('#, (Africa|Americas|Asia|Europe|Oceania)$#', '', $lstring); // Confuses Bing

    // Web service to get remaining latitude/longitude
    if ($type == 'bing') {
        $url = 'http://dev.virtualearth.net/REST/v1/Locations?query=' . urlencode($lstring) . '&o=xml&key=AvmgsVWtIoJeCnZXdDnu3dQ7izV9oOowHCNDwbN4R1RPA9OXjfsQX1Cr9HSrsY4j';
    } elseif ($type == 'yahoo') {
        $url = 'http://where.yahooapis.com/geocode?q=' . urlencode($lstring) . '&appid=dj0yJmk9N0x3TTdPaDNvdElCJmQ9WVdrOWFGWjVOa3hzTldFbWNHbzlNVFU0TXpBMU9EWTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mNg--';
    } elseif ($type == 'google') {
        $url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . urlencode($lstring);
        $key = get_option('google_geocode_api_key');
        /*if ($key == '') { Actually, does work
            $error_msg = do_lang_tempcode('GOOGLE_GEOCODE_API_NOT_CONFIGURED');
            return null;
        }*/
        $url .= '&language=' . urlencode(strtolower(get_site_default_lang()));
        if ($key != '') {
            $url .= '&key=' . urlencode($key);
        }
    } else {
        exit('unknown type');
    }
    $result = http_download_file($url);
    $matches = array();
    if ((($type == 'bing') && (preg_match('#<Latitude>([\-\d\.]+)</Latitude>\s*<Longitude>([\-\d\.]+)</Longitude>#', $result, $matches) != 0)) || (($type == 'google') && (preg_match('#<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>#', $result, $matches) != 0)) || (($type == 'yahoo') && (preg_match('#<latitude>([\-\d\.]+)</latitude>\s*<longitude>([\-\d\.]+)</longitude>#', $result, $matches) != 0))) {
        $latitude = floatval($matches[1]);
        $longitude = floatval($matches[2]);

        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', array('*'), array('c_name' => '_catalogue_category'), 'ORDER BY cf_order,' . $GLOBALS['FORUM_DB']->translate_field_ref('cf_name'));
        require_code('content');
        require_code('fields');
        $assocated_catalogue_entry_id = get_bound_content_entry('catalogue_category', strval($category_id));

        $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', array('cv_value' => $latitude), array('ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[0]['id']), '', 1);
        $GLOBALS['SITE_DB']->query_update('catalogue_efv_float', array('cv_value' => $longitude), array('ce_id' => $assocated_catalogue_entry_id, 'cf_id' => $fields[1]['id']), '', 1);

        return '1';
    }
    return '0';
}

function find_nearest_location($latitude, $longitude, $latitude_field_id = null, $longitude_field_id = null, $error_tolerance = null)
{
    if (is_null($error_tolerance)) { // Ah, pick a default
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

    if ((is_null($latitude_field_id)) || (is_null($longitude_field_id))) { // Just do a raw query on locations table
        $query = 'SELECT * FROM ' . get_table_prefix() . 'locations WHERE ' . $where;
        $locations = $GLOBALS['SITE_DB']->query($query);
    } else { // Catalogue query (works both for entries and categories that use custom fields)
        $where = str_replace(array('l_latitude', 'l_longitude'), array('a.cv_value', 'b.cv_value'), $where);
        $query = 'SELECT a.ce_id,c.id,cc_title,a.cv_value AS l_latitude,b.cv_value AS l_longitude FROM ' . get_table_prefix() . 'catalogue_efv_float a JOIN ' . get_table_prefix() . 'catalogue_efv_float b ON a.ce_id=b.ce_id AND a.cf_id=' . strval($latitude_field_id) . ' AND b.cf_id=' . strval($longitude_field_id) . ' LEFT JOIN ' . get_table_prefix() . 'catalogue_entry_linkage x ON a.ce_id=x.catalogue_entry_id AND ' . db_string_equal_to('x.content_type', 'catalogue_category') . ' LEFT JOIN ' . get_table_prefix() . 'catalogue_categories c ON ' . db_cast('c.id', 'CHAR') . '=x.content_id WHERE ' . $where;
        $locations = $GLOBALS['SITE_DB']->query($query, null, null, false, false, array('cc_title' => 'SHORT_TRANS'));
    }

    if (count($locations) == 0) {
        if ($error_tolerance >= 1.0) {
            return null; // Nothing, in whole world
        }

        return find_nearest_location($latitude, $longitude, $latitude_field_id, $longitude_field_id, $error_tolerance * GEO_SEARCH_EXPANSION_FACTOR);
    }

    $best = mixed();
    $best_at = mixed();
    foreach ($locations as $l) {
        $dist = latlong_distance_miles($l['l_latitude'], $l['l_longitude'], $latitude, $longitude);

        if ((is_null($best)) || ($dist < $best)) {
            $best = $dist;
            $best_at = $l;
        }
    }
    $locations = array($best_at);

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
