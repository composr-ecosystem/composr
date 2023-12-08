<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

require_code('locations_catalogues_geopositioning');

prepare_for_known_ajax_response();

header('Content-type: text/plain; charset=' . get_charset());

safe_ini_set('ocproducts.xss_detect', '0');

$lstring = get_param_string('lstring', null);
if (!is_null($lstring)) {

    $url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . urlencode($lstring);
    if (isset($_COOKIE['google_bias'])) {
        $url .= '&region=' . urlencode($_COOKIE['google_bias']);
    }
    $key = get_option('google_geocode_api_key');
    /*if ($key == '') { Actually, does work
        $error_msg = do_lang_tempcode('GOOGLE_GEOCODE_API_NOT_CONFIGURED');
        return null;
    }*/
    $url .= '&language=' . urlencode(strtolower(get_site_default_lang()));
    if ($key != '') {
        $url .= '&key=' . urlencode($key);
    }
    $result = http_download_file($url);
    $matches = array();
    if (preg_match('#<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>#s', $result, $matches) != 0) {
        echo '[';

        echo 'null';
        echo ',\'' . addslashes($lstring) . '\'';
        echo ',' . float_to_raw_string(floatval($matches[1]), 30);
        echo ',' . float_to_raw_string(floatval($matches[2]), 30);
        echo ',' . float_to_raw_string(floatval($matches[3]), 30);
        echo ',' . float_to_raw_string(floatval($matches[4]), 30);
        echo ',' . float_to_raw_string(floatval($matches[5]), 30);
        echo ',' . float_to_raw_string(floatval($matches[6]), 30);

        echo ']';
    }
} else {

    if (get_param_integer('use_google', 0) == 1) {
        $url = 'http://maps.googleapis.com/maps/api/geocode/xml?latlng=' . urlencode(get_param_string('latitude')) . ',' . urlencode(get_param_string('longitude'));
        if (isset($_COOKIE['google_bias'])) {
            $url .= '&region=' . urlencode($_COOKIE['google_bias']);
        }
        $key = get_option('google_geocode_api_key');
        /*if ($key == '') { Actually, does work
            $error_msg = do_lang_tempcode('GOOGLE_GEOCODE_API_NOT_CONFIGURED');
            return null;
        }*/
        $url .= '&language=' . urlencode(strtolower(get_site_default_lang()));
        if ($key != '') {
            $url .= '&key=' . urlencode($key);
        }
        $result = http_download_file($url);
        $matches = array();
        if (preg_match('#<formatted_address>([^<>]*)</formatted_address>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>.*<lat>([\-\d\.]+)</lat>\s*<lng>([\-\d\.]+)</lng>#Us', $result, $matches) != 0) {
            echo '[';

            echo 'null';
            echo ',\'' . addslashes($matches[1]) . '\'';
            echo ',' . float_to_raw_string(floatval($matches[2]), 30);
            echo ',' . float_to_raw_string(floatval($matches[3]), 30);
            echo ',' . float_to_raw_string(floatval($matches[4]), 30);
            echo ',' . float_to_raw_string(floatval($matches[5]), 30);
            echo ',' . float_to_raw_string(floatval($matches[6]), 30);
            echo ',' . float_to_raw_string(floatval($matches[7]), 30);

            echo ']';
        }
    } else {
        $bits = find_nearest_location(floatval(get_param_string('latitude')), floatval(get_param_string('longitude')), get_param_integer('latitude_search_field', null), get_param_integer('longitude_search_field', null));
        if (!is_null($bits)) {
            // Give out different IDs, depending on what the search fields were in.

            $with_contents = (get_param_integer('with_contents', 0) == 1);
            if (($with_contents) && (isset($bits['id'])) && (!is_null(get_param_integer('latitude_search_field', null))) && (!is_null(get_param_integer('longitude_search_field', null)))) {
                $backup = $bits['id'];
                do { // Ensure we have some entries under the idealised location
                    $num_entries = $GLOBALS['SITE_DB']->query_select_value('catalogue_childcountcache', 'c_num_rec_entries', array('cc_id' => $bits['id']));
                    if ($num_entries == 0) {
                        $bits['id'] = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'cc_parent_id', array('id' => $bits['id']));
                        if (is_null($bits['id'])) {
                            break;
                        }
                    }
                } while ($num_entries == 0);
                if (is_null($bits['id'])) {
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
                    if (!is_null($parent_id)) {
                        $row = $GLOBALS['SITE_DB']->query_select('catalogue_categories', array('cc_parent_id', 'cc_title'), array('id' => $parent_id), '', 1);
                        $parent_id = $row[0]['cc_parent_id'];
                        if (!is_null($parent_id)) { // Top level skipped also
                            if ($done_one) {
                                echo ', ';
                            }
                            echo addslashes(get_translated_text($row[0]['cc_title']));
                            $done_one = true;
                        }
                    }
                } while (!is_null($parent_id));
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
