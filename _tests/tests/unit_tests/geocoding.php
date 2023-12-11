<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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

        // Please don't use this on a live site, we just need these to test against
        set_option('google_geocode_api_key', 'AIzaSyABpmhwlC5gw4W3tEUEQb8JoSczd1K7CJ4');
    }

    public function testIPGeocode()
    {
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('ip_country', 'id');
        $has_geolocation_data = ($test !== null);
        if (!$has_geolocation_data) {
            require_code('tasks');
            require_lang('stats');
            call_user_func_array__long_task(do_lang('INSTALL_GEOLOCATION_DATA'), get_screen_title('INSTALL_GEOLOCATION_DATA'), 'install_geolocation_data', null, false, true);
        }
        require_code('locations');

        $this->assertTrue(geolocate_ip('217.160.72.6') == 'DE');
    }

    public function testGeocodeGoogle()
    {
        require_code('locations_geocoding');

        $result = geocode('Berlin, DE');
        if ($result === null) {
            $this->assertTrue(false, 'Expected to receive geocode for Berlin, DE but did not.');
        } else {
            $this->assertTrue(($result[0] > 52.0) && ($result[0] < 53.0) && ($result[1] > 13.0) && ($result[1] < 14.0), 'Expected geocode for Berlin, DE to return 52.x,13.x but instead it returned ' . float_to_raw_string($result[0]) . ',' . float_to_raw_string($result[1]));
        }

        // Note if this breaks there's also similar code in locations_catalogues_geoposition and locations_catalogues_geopositioning (non-bundled addons)
    }

    public function testReverseGeocodeGoogle()
    {
        require_code('locations_geocoding');

        $errormsg = new Tempcode();
        $address = reverse_geocode(52.516667, 13.388889, $errormsg);
        if ((get_param_integer('debug', 0) == 1) || ($address === null))  {
            var_dump($errormsg->evaluate());
            var_dump($address);
        }
        $this->assertTrue($address[2] == 'Berlin');
        $this->assertTrue($address[6] == 'DE');

        $errormsg = new Tempcode();
        $address = reverse_geocode(64.133333, -21.933333, $errormsg);
        if ((get_param_integer('debug', 0) == 1) || ($address === null))  {
            var_dump($errormsg);
            var_dump($address);
        }
        $this->assertTrue($address[2] == 'Reykjavík');
        $this->assertTrue($address[6] == 'IS');
    }
}
