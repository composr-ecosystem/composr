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
class _api_weather_test_set extends cms_test_case
{
    public function testWeatherAPI()
    {
        require_code('weather');

        $this->load_key_options('openweathermap');

        $errormsg = '';
        $result = weather_lookup(null, 24.466667, 39.6, 'metric', null, $errormsg, 'openweathermap');
        $this->assertTrue(($result !== null) && ($result[0]['city_name'] == 'Medina'), 'Failed to lookup weather current conditions by GPS; ' . $errormsg);
        $this->assertTrue(($result !== null) && (array_key_exists(0, $result[1])) && ($result[1][0]['city_name'] == 'Medina'), 'Failed to lookup weather forecast by GPS; ' . $errormsg);

        $errormsg = '';
        $result = weather_lookup('Medina', null, null, 'metric', null, $errormsg, 'openweathermap');
        $this->assertTrue(($result !== null) && (preg_match('#Medina|Munawwarah#', $result[0]['city_name']) != 0), 'Failed to lookup weather current conditions by location string; ' . $errormsg);
        $this->assertTrue(($result !== null) && (array_key_exists(0, $result[1])) && (preg_match('#Medina|Munawwarah#', $result[1][0]['city_name']) != 0), 'Failed to lookup weather forecast by location string; ' . $errormsg);
    }
}
