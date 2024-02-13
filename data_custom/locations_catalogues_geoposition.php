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

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $FILE_BASE = $_SERVER['SCRIPT_FILENAME']; // this is with symlinks-unresolved (__FILE__ has them resolved); we need as we may want to allow zones to be symlinked into the base directory without getting path-resolved
    $FILE_BASE = dirname($FILE_BASE);
    if (!is_file($FILE_BASE . '/sources/global.php')) {
        $RELATIVE_PATH = basename($FILE_BASE);
        $FILE_BASE = dirname($FILE_BASE);
    } else {
        $RELATIVE_PATH = '';
    }
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
global $KNOWN_UTF8;
$KNOWN_UTF8 = true;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('locations_catalogues')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('locations_catalogues')));
}

require_code('locations_catalogues_geopositioning');

prepare_backend_response('text/plain');

cms_ini_set('ocproducts.xss_detect', '0');

$lstring = get_param_string('lstring', null, INPUT_FILTER_GET_COMPLEX);
if ($lstring !== null) {
    require_code('locations_geocoding');
    $result = geocode($lstring);
    if ($result !== null) {
        list($latitude, $longitude, $ne_latitude, $ne_longitude, $sw_latitude, $sw_longitude) = $result;

        echo '[';

        echo 'null';
        echo ',\'' . addslashes($lstring) . '\'';
        echo ',' . float_to_raw_string($latitude, 30);
        echo ',' . float_to_raw_string($longitude, 30);
        echo ',' . float_to_raw_string($ne_latitude, 30);
        echo ',' . float_to_raw_string($ne_longitude, 30);
        echo ',' . float_to_raw_string($sw_latitude, 30);
        echo ',' . float_to_raw_string($sw_longitude, 30);

        echo ']';
    }
} else {
    if (get_param_integer('use_google', 0) == 1) {
        $latitude = floatval(get_param_string('latitude'));
        $longitude = floatval(get_param_string('longitude'));
        $result = reverse_geocode($latitude, $longitude);

        if ($result !== null) {
            list($formatted_address, $street_address, $city, $county, $state, $post_code, $country, $ne_latitude, $ne_longitude, $sw_latitude, $sw_longitude) = $result;

            echo '[';

            echo 'null';
            echo ',\'' . addslashes($formatted_address) . '\'';
            echo ',' . float_to_raw_string($longitude, 30);
            echo ',' . float_to_raw_string($longitude, 30);
            echo ',' . float_to_raw_string($ne_latitude, 30);
            echo ',' . float_to_raw_string($ne_longitude, 30);
            echo ',' . float_to_raw_string($sw_latitude, 30);
            echo ',' . float_to_raw_string($sw_longitude, 30);

            echo ']';
        }
    } else {
        $bits = find_nearest_location(floatval(get_param_string('latitude')), floatval(get_param_string('longitude')), get_param_integer('latitude_search_field', null), get_param_integer('longitude_search_field', null));
        if ($bits !== null) {
            // Give out different IDs, depending on what the search fields were in.

            $with_contents = (get_param_integer('with_contents', 0) == 1);
            if (($with_contents) && (isset($bits['id'])) && (get_param_integer('latitude_search_field', null) !== null) && (get_param_integer('longitude_search_field', null) !== null)) {
                $backup = $bits['id'];
                do { // Ensure we have some entries under the idealised location
                    $num_entries = $GLOBALS['SITE_DB']->query_select_value('catalogue_childcountcache', 'c_num_rec_entries', ['cc_id' => $bits['id']]);
                    if ($num_entries == 0) {
                        $bits['id'] = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'cc_parent_id', ['id' => $bits['id']]);
                        if ($bits['id'] === null) {
                            break;
                        }
                    }
                } while ($num_entries == 0);
                if ($bits['id'] === null) {
                    $bits['id'] = $backup; // Could find nothing, so revert to being specific
                }
            }

            echo '[';

            if (isset($bits['id'])) {
                echo strval($bits['id']); // Category or Location
            } elseif (isset($bits['ce_id'])) {
                echo strval($bits['ce_id']); // Entry
            } else {
                echo strval($bits['id']); // Location
            }

            echo ',';

            if (isset($bits['cc_title'])) {
                $done_one = false;
                echo '\'';
                $parent_id = $bits['id'];
                do {
                    if ($parent_id !== null) {
                        $row = $GLOBALS['SITE_DB']->query_select('catalogue_categories', ['cc_parent_id', 'cc_title'], ['id' => $parent_id], '', 1);
                        $parent_id = $row[0]['cc_parent_id'];
                        if ($parent_id !== null) { // Top level skipped also
                            if ($done_one) {
                                echo ', ';
                            }
                            echo addslashes(get_translated_text($row[0]['cc_title']));
                            $done_one = true;
                        }
                    }
                } while ($parent_id !== null);
                echo '\'';
            } elseif (isset($bits['l_place'])) {
                echo '\'';
                echo addslashes($bits['l_place']);
                if ($bits['l_parent_1'] != '') {
                    echo ', ' . addslashes($bits['l_parent_1']);
                }
                if ($bits['l_parent_2'] != '') {
                    echo ', ' . addslashes($bits['l_parent_2']);
                }
                if ($bits['l_parent_3'] != '') {
                    echo ', ' . addslashes($bits['l_parent_3']);
                }
                if ($bits['l_country'] != '') {
                    echo ', ' . addslashes($bits['l_country']);
                }
                echo '\'';
            } else {
                echo '\'\'';
            }

            echo ',' . float_to_raw_string($bits['l_latitude'], 30);
            echo ',' . float_to_raw_string($bits['l_longitude'], 30);

            echo ']';
        }
    }
}

cms_safe_exit_flow();
