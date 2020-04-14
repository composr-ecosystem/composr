<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    weather
 */

/**
 * Hook class.
 */
class Hook_health_check_weather extends Hook_Health_Check
{
    protected $category_label = 'API connections';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     * @return array A pair: category label, list of results
     */
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null)
    {
        if (($check_context != CHECK_CONTEXT__INSTALL) && (addon_installed('weather'))) {
            $openweathermap_api_key = get_option('openweathermap_api_key');
            if ($openweathermap_api_key == '') {
                return [$this->category_label, $this->results];
            }

            $this->process_checks_section('testWeatherConnection', 'Weather', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass, $urls_or_page_links, $comcode_segments);
        }

        return [$this->category_label, $this->results];
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     */
    public function testWeatherConnection($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null, $urls_or_page_links = null, $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            return;
        }

        require_code('weather');

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
