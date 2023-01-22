<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class ___timezones_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testTimezoneList()
    {
        require_code('temporal2');
        $selection_list = _get_timezone_list(false);
        unset($selection_list['UTC']); // This is special, should not be checked/considered against others

        $timezones = timezone_identifiers_list();

        $quick = (get_param_integer('quick', 0) == 1);

        // Cluster tzinfo zones
        $all_cities = [];
        $equivalencies = [];
        foreach ($timezones as $timezone) {
            if ($timezone == 'UTC') {
                continue;
            }

            $ob = new DateTimeZone($timezone);
            $offsets = [];
            $current_year = intval(date('Y'));
            foreach ([$current_year, $current_year + 1] as $year) {
                for ($hour = 0; $hour < 24; $hour += $quick ? 3 : 1) {
                    $timestamp = mktime($hour, 0, 0, 1, 1, $year);
                    for ($day_of_year = 1; $day_of_year <= 355; $day_of_year += $quick ? 3 : 1) {
                        $offsets[] = timezone_offset_get($ob, date_create('@' . strval($timestamp), $ob));
                        $timestamp += 60 * 60 * 24;
                    }
                }
            }
            $_offsets = implode(',', $offsets);
            if (!isset($equivalencies[$_offsets])) {
                $equivalencies[$_offsets] = [];
            }
            $equivalencies[$_offsets][] = $timezone;

            $all_cities[] = $this->tz_to_city($timezone);
        }

        // Check tzinfo zones against Composr zones
        $matched = [];
        foreach ($equivalencies as $_offsets => $equivalent_timezones) {
            $found = [];
            foreach ($equivalent_timezones as $equivalent_timezone) {
                if (isset($selection_list[$equivalent_timezone])) {
                    $found[$equivalent_timezone] = $selection_list[$equivalent_timezone];
                    $matched[] = $equivalent_timezone;
                }
            }
            $this->assertTrue(!empty($found), 'Could not find anything covering tzinfo zone: ' . implode(',', $equivalent_timezones));
            if (!$quick) {
                $this->assertTrue(count($found) <= 1, 'Found multiple covering tzinfo zone: ' . implode(',', $equivalent_timezones) . '... ' . implode(',', $found));
            }

            // Check one of the identifies our Composr zones references our zone in its title
            foreach ($equivalent_timezones as $equivalent_timezone) {
                $okay = false;
                $city = $this->tz_to_city($equivalent_timezone);
                foreach ($found as $title) {
                    if ($this->found_city_in_composr_title($city, $title)) {
                        $okay = true;
                    }
                }
                $this->assertTrue($okay, 'City ' . $city . ' not found in any of ' . implode(', ', $found));
            }

            // Check a different Composr zone isn't grabbing our zone
            foreach ($selection_list as $timezone => $title) {
                if (!in_array($timezone, $equivalent_timezones)) {
                    foreach ($equivalent_timezones as $equivalent_timezone) {
                        $city = $this->tz_to_city($equivalent_timezone);
                        $this->assertTrue(!$this->found_city_in_composr_title($city, $title) || strpos($title, $city . ' (') !== false/*different place with same name*/, $city . ' should not be in ' . $title . ', should be in one of ' . implode(', ', $found));
                    }
                }
            }
        }

        // Check Composr zones
        $year_start = mktime(0, 0, 0, 1, 1, $current_year);
        $last_offset = null;
        foreach ($selection_list as $timezone => $title) {
            $this->assertTrue(in_array($timezone, $timezones), 'No matching tzinfo zone for Composr zone: ' . $timezone);
            if (in_array($timezone, $timezones)) {
                $offset = (tz_time($year_start, $timezone) - $year_start) / 3600.0;

                $this->assertTrue((($last_offset === null) || ($last_offset <= $offset)), $title . ' is out of order');
                $last_offset = $offset;

                $parts = explode(', ', preg_replace('#^\(UTC[+-]\d\d:\d\d\) #', '', $title));
                $parts_sorted = $parts;
                sort($parts);
                $this->assertTrue($parts == $parts_sorted, $title . ' does not have components in alphabetical order');
                foreach ($parts_sorted as $part) {
                    $part_stripped = str_replace('St. ', 'St ', preg_replace('#( \(.*\)| Islands| Island)#', '', $part));

                    // Exceptions - these have good reasons to be on our list even though they are not in tzinfo (capital cities, important known timezones for large areas, islands)
                    $exceptions = [
                        'Samoa',
                        'Hawaii-Aleutian',
                        'Hawaii',
                        'Pacific Time',
                        'Mountain Time',
                        'Guadalajara',
                        'Central America',
                        'Saskatchewan',
                        'Central Time',
                        'Quito',
                        'Eastern Time',
                        'Montreal',
                        'Georgetown',
                        'Port of Spain',
                        'Atlantic Time',
                        'Newfoundland',
                        'Greenland',
                        'St Pierre',
                        'Brasilia',
                        'Mid-Atlantic',
                        'Belfast',
                        'Edinburgh',
                        'Bern',
                        'Dar es Salaam',
                        'Abu Dhabi',
                        'Port Louis',
                        'Islamabad',
                        'Chennai',
                        'Mumbai',
                        'New Delhi',
                        'Sri Jayawardenepura Kotte',
                        'Astana',
                        'Hanoi',
                        'Beijing',
                        'Canberra',
                        'New Caledonia',
                        'Solomon',
                        'Marshall',
                        'Wellington',
                        'Johnston',
                    ];

                    if (!in_array($part_stripped, $exceptions)) {
                        $this->assertTrue(in_array($part_stripped, $all_cities), 'Could not find city in tzinfo: ' . $part_stripped);
                    }
                }

                $new = '(UTC';
                $new .= ($offset < 0.0) ? '-' : '+';
                $offset_abs = abs($offset);
                $hours = intval(floor($offset_abs));
                $new .= str_pad(strval($hours), 2, '0', STR_PAD_LEFT);
                $new .= ':';
                $new .= str_pad(strval(abs($hours - $offset_abs) * 100), 2, '0', STR_PAD_LEFT);
                $new .= ') ';
                $new_title = preg_replace('#^\(UTC[+-]\d\d:\d\d\) #', $new, $title);

                $this->assertTrue($new_title == $title, $title . ' should be ' . $new_title);
            }
        }
    }

    protected function tz_to_city($timezone)
    {
        if ($timezone == 'Antarctica/DumontDUrville') {
            return 'Dumont d\'Urville';
        }

        return ucwords(str_replace('_', ' ', preg_replace('#^.*/#', '', $timezone)));
    }

    protected function found_city_in_composr_title($city, $title)
    {
        return (preg_match('#(\) |, )' . preg_quote($city, '#') . '( \(| Islands| Island|,|$)#i', str_replace('St. ', 'St ', $title)) != 0);
    }
}
