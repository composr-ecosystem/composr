<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class geocoding_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        // Please don't use these on a live site, we just need these to test against
        set_option('mapquest_geocoding_api_key', 'O6xkX0ZeucJRLDDCzyqahkCzAJpVmmfB');
        set_option('bing_geocoding_api_key', 'AvmgsVWtIoJeCnZXdDnu3dQ7izV9oOowHCNDwbN4R1RPA9OXjfsQX1Cr9HSrsY4j');
        set_option('google_geocoding_api_enabled', '1');
        set_option('google_apis_api_key', 'AIzaSyD-jqeO_HlD1bLmA68JhAJOBajZw96-UHE');
    }

    public function testIPGeocode()
    {
        if (get_db_type() == 'xml') {
            $this->assertTrue(false, 'Cannot run with XML database driver, too slow');
            return;
        }

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('ip_country', 'id');
        $has_geolocation_data = ($test !== null);
        if (!$has_geolocation_data) {
            require_code('tasks');
            require_lang('stats');
            call_user_func_array__long_task(do_lang('INSTALL_GEOLOCATION_DATA'), get_screen_title('INSTALL_GEOLOCATION_DATA'), 'install_geolocation_data', [], false, true);
        }

        require_code('locations');
        $this->assertTrue(geolocate_ip('217.160.72.6') == 'DE');
    }

    public function testGeocode()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        require_code('locations_geocoding');

        foreach (['google', 'bing', 'mapquest'] as $service) {
            $error_msg = new Tempcode();
            $result = geocode('Berlin, DE', $error_msg, $service);
            $this->assertTrue(($result !== null) && ($result[0] > 52.0) && ($result[0] < 53.0) && ($result[1] > 13.0) && ($result[1] < 14.0), 'Wrong coordinate on ' . $service);
        }

        // Note if this breaks there's also similar code in locations_catalogues_geoposition and locations_catalogues_geopositioning (non-bundled addons)
    }

    public function testReverseGeocode()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        require_code('locations_geocoding');

        foreach (['google', 'bing', 'mapquest'] as $service) {
            $error_msg = new Tempcode();
            $address = reverse_geocode(52.516667, 13.388889, $error_msg, $service);
            if (($this->debug) || ($address === null)) {
                var_dump($error_msg->evaluate());
                var_dump($address);
            }
            $this->assertTrue($address !== null, 'Failure on ' . $service);
            if ($address !== null) {
                $this->assertTrue($address[2] == 'Berlin', 'Wrong city on ' . $service . ', got ' . $address[2] . ', expected Berlin');
                $this->assertTrue($address[6] == 'DE', 'Wrong country on ' . $service . ', got ' . $address[6] . ', expected DE');
            }

            $error_msg = new Tempcode();
            $address = reverse_geocode(64.133333, -21.933333, $error_msg, $service);
            if (($this->debug) || ($address === null)) {
                var_dump($error_msg->evaluate());
                var_dump($address);
            }
            $this->assertTrue($address !== null, 'Failure on ' . $service);
            if ($address !== null) {
                $this->assertTrue(substr($address[2], 0, 3) == 'Rey', 'Wrong city on ' . $service . ', got ' . $address[2] . ', expected ~Raycevick'); // Only check first chars due to charset issues
            }
        }
    }
}
