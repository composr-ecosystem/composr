<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
     * @param  boolean $show_unusable_categories Whether to include categories that might not be accessible for some reason
     * @return array A pair: category label, list of results
     */
    public function run(?array $sections_to_run, int $check_context, bool $manual_checks = false, bool $automatic_repair = false, ?bool $use_test_data_for_pass = null, ?array $urls_or_page_links = null, ?array $comcode_segments = null, bool $show_unusable_categories = false) : array
    {
        if (($show_unusable_categories) || (($check_context != CHECK_CONTEXT__INSTALL) && (addon_installed('weather')))) {
            if (($show_unusable_categories) || (get_option('openweathermap_api_key') != '')) {
                $this->process_checks_section('testWeatherConnection', 'Weather', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass, $urls_or_page_links, $comcode_segments);
            }
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
    public function testWeatherConnection(int $check_context, bool $manual_checks = false, bool $automatic_repair = false, ?bool $use_test_data_for_pass = null, ?array $urls_or_page_links = null, ?array $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            $this->log('Skipped; we are running from installer.');
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            $this->log('Skipped; running on specific page links.');
            return;
        }

        require_code('weather');

        try {
            $result = weather_lookup(null, 24.466667, 39.6, 'metric', null, 'openweathermap');
            $this->assertTrue(($result !== null) && ($result[0]['city_name'] == 'Medina'), 'Unexpected result looking up weather current conditions by GPS');
            $this->assertTrue(($result !== null) && (array_key_exists(0, $result[1])) && ($result[1][0]['city_name'] == 'Medina'), 'Unexpected result looking up weather forecast by GPS');
        } catch (Exception $e) {
            $this->assertTrue(false, 'Failed to lookup weather by GPS: ' . $e->getMessage());
        }

        try {
            $result = weather_lookup('Medina', null, null, 'metric', null, 'openweathermap');
            $this->assertTrue(($result !== null) && (preg_match('#Medina|Munawwarah#', $result[0]['city_name']) != 0), 'Unexpected result looking up weather current conditions by location string');
            $this->assertTrue(($result !== null) && (array_key_exists(0, $result[1])) && (preg_match('#Medina|Munawwarah#', $result[1][0]['city_name']) != 0), 'Unexpected result looking up weather forecast by location string');
        } catch (Exception $e) {
            $this->assertTrue(false, 'Failed to lookup weather by location string: ' . $e->getMessage());
        }
    }
}
