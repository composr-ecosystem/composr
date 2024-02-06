<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _api_geocoding_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        set_option('google_geocoding_api_enabled', '1');
        $this->load_key_options('mapquest');
        $this->load_key_options('bing');
        $this->load_key_options('google');
    }

    public function testIPGeocode()
    {
        if (get_db_type() == 'xml') {
            $this->assertTrue(false, 'Cannot run with XML database driver, too slow');
            return;
        }

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('ip_country', 'id'); // Debugging note: Should be about 253k rows in this table
        $has_geolocation_data = ($test !== null);
        if (!$has_geolocation_data) {
            require_code('tasks');
            require_lang('stats');
            call_user_func_array__long_task(do_lang('INSTALL_GEOLOCATION_DATA'), get_screen_title('INSTALL_GEOLOCATION_DATA'), 'install_geolocation_data', [], false, true);
        }

        require_code('locations');
        $country = geolocate_ip('217.160.72.6');
        $this->assertTrue(geolocate_ip('217.160.72.6') == 'DE', 'Expected DE got ' . (($country === null) ? '(unknown)' : $country));
    }

    public function testGeocode()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $this->run_health_check('API connections', 'Geocoding');
    }
}
