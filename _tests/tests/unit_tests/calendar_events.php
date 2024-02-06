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
class calendar_events_test_set extends cms_test_case
{
    protected $event_id;

    public function setUp()
    {
        parent::setUp();

        require_code('calendar');
        require_code('calendar2');
    }

    public function testAllDayInAheadTimeZone()
    {
        if (($this->only !== null) && ($this->only != 'testAllDayInAheadTimeZone')) {
            return;
        }

        // Amsterdam-timezone event, which is *ahead* of UTC, so potentially could cause problems if we make a mistake (as it starts the previous day from the UTC perspective)

        $period_start = mktime(0, 0, 0, 4, 6, 2015);
        $period_end = mktime(23, 59, 0, 4, 6, 2015);

        // Check in USA
        $_period_start = 2 * $period_start - tz_time($period_start, 'America/New_York');
        $_period_end = 2 * $period_end - tz_time($period_end, 'America/New_York');
        $periods = find_periods_recurrence('Europe/Amsterdam', 0, 2015/*year*/, 4/*month*/, 6/*day*/, 'day_of_month', null, null, null, null, null, 'day_of_month', null, null, 'none', null, $_period_start, $_period_end);
        $this->assertTrue(count($periods) == 1); // We expect to see it as it starts this day in USA point of view

        // Check in Amsterdam
        $_period_start = 2 * $period_start - tz_time($period_start, 'Europe/Amsterdam');
        $_period_end = 2 * $period_end - tz_time($period_end, 'Europe/Amsterdam');
        $periods = find_periods_recurrence('Europe/Amsterdam', 0, 2015/*year*/, 4/*month*/, 6/*day*/, 'day_of_month', null, null, null, null, null, 'day_of_month', null, null, 'none', null, $_period_start, $_period_end);
        $this->assertTrue(empty($periods)); // We expect to *not* see it, as the 6th counts as 11pm for Amsterdam (we would have said 5th for all-day-Amsterdam-6th); except DST is also in effect

        // ---

        $period_start = mktime(0, 0, 0, 4, 7, 2015);
        $period_end = mktime(23, 59, 0, 4, 7, 2015);

        // Check in USA
        $_period_start = 2 * $period_start - tz_time($period_start, 'America/New_York');
        $_period_end = 2 * $period_end - tz_time($period_end, 'America/New_York');
        $periods = find_periods_recurrence('Europe/Amsterdam', 0, 2015/*year*/, 4/*month*/, 6/*day*/, 'day_of_month', null, null, null, null, null, 'day_of_month', null, null, 'none', null, $_period_start, $_period_end);
        $this->assertTrue(count($periods) == 0, 'Got ' . strval(count($periods)) . ' periods');

        // Check in Amsterdam
        $_period_start = 2 * $period_start - tz_time($period_start, 'Europe/Amsterdam');
        $_period_end = 2 * $period_end - tz_time($period_end, 'Europe/Amsterdam');
        $periods = find_periods_recurrence('Europe/Amsterdam', 0, 2015/*year*/, 4/*month*/, 6/*day*/, 'day_of_month', null, null, null, null, null, 'day_of_month', null, null, 'none', null, $_period_start, $_period_end);
        $this->assertTrue(count($periods) == 1, 'Got ' . strval(count($periods)) . ' periods'); // We expect to see it
    }

    public function testApiShiftRecurrence()
    {
        if (($this->only !== null) && ($this->only != 'testApiShiftRecurrence')) {
            return;
        }

        // Given an event shifted to a different recurrence, ensure the dates do shift correctly...

        $event = [
            'e_start_day' => 8,
            'e_start_month' => 10,
            'e_start_year' => 2012,
            'e_start_monthly_spec_type' => 'day_of_month',
            'e_start_hour' => 10,
            'e_start_minute' => 30,
            'e_end_day' => 9,
            'e_end_month' => 10,
            'e_end_year' => 2012,
            'e_end_monthly_spec_type' => 'day_of_month',
            'e_end_hour' => 10,
            'e_end_minute' => 30,
            'e_timezone' => 'Europe/London',
            'e_do_timezone_conv' => 1,
            'e_recurrence' => 'monthly',
            'e_recurrences' => null,
        ];
        $event = adjust_event_dates_for_a_recurrence('2012-11-9', $event, 'Europe/London');
        $this->assertTrue($event['e_start_month'] == 11);
        $this->assertTrue($event['e_end_month'] == 11);

        // And now check timezones are respected for a negative timezone case viewed from the same timezone it was added in...

        $event = [
            'e_start_day' => 30, // 29th local time
            'e_start_month' => 7,
            'e_start_year' => 2015,
            'e_start_monthly_spec_type' => 'day_of_month',
            'e_start_hour' => 1, // 21 local time
            'e_start_minute' => 00,
            'e_end_day' => null,
            'e_end_month' => null,
            'e_end_year' => null,
            'e_end_monthly_spec_type' => null,
            'e_end_hour' => null,
            'e_end_minute' => null,
            'e_timezone' => 'America/Guyana',
            'e_do_timezone_conv' => 1,
            'e_recurrence' => '',
            'e_recurrences' => null,
        ];
        $event = adjust_event_dates_for_a_recurrence('2015-07-29', $event, 'America/Guyana');
        $this->assertTrue($event['e_start_day'] == 30);

        // More complex case...

        $event = [
            'e_start_day' => 1, // 1st Tuesday of November 2012 = 6th
            'e_start_month' => 11,
            'e_start_year' => 2012,
            'e_start_monthly_spec_type' => 'dow_of_month',
            'e_start_hour' => 10,
            'e_start_minute' => 30,
            'e_end_day' => 2, // 1st Wednesday of November 2012 = 7th
            'e_end_month' => 11,
            'e_end_year' => 2012,
            'e_end_monthly_spec_type' => 'dow_of_month',
            'e_end_hour' => 10,
            'e_end_minute' => 30,
            'e_timezone' => 'Europe/London',
            'e_do_timezone_conv' => 1,
            'e_recurrence' => 'monthly',
            'e_recurrences' => null,
        ];
        $event = adjust_event_dates_for_a_recurrence('2012-12-4', $event, 'Europe/London'); // 1st Tuesday of December 2012
        $this->assertTrue($event['e_start_day'] == 4); // 1st Tuesday of December = 4th
        $this->assertTrue($event['e_start_month'] == 12);
        $this->assertTrue($event['e_start_monthly_spec_type'] == 'day_of_month');
        $this->assertTrue($event['e_end_day'] == 5); // 5th, as relative according to first month
        $this->assertTrue($event['e_end_month'] == 12);
        $this->assertTrue($event['e_end_monthly_spec_type'] == 'day_of_month');
    }

    public function testDstBoundaryShift()
    {
        if (($this->only !== null) && ($this->only != 'testDstBoundaryShift')) {
            return;
        }

        list($hours, $minutes) = dst_boundary_difference_for_recurrence(2012, 3, 10, 2012, 8, 10, 'Europe/London');
        $this->assertTrue($hours == -1);
        $this->assertTrue($minutes == 0);
    }

    public function testApiWeekNumbersConsistent()
    {
        if (($this->only !== null) && ($this->only != 'testApiWeekNumbersConsistent')) {
            return;
        }

        foreach (['0', '1'] as $ssw) {
            set_option('ssw', $ssw);

            $year = 2000;
            for ($week = -5/*intentionally go before one year*/; $week < 55/*intentionally go past one year*/; $week++) {
                list($month, $day, $_year) = date_from_week_of_year($year, $week);
                if ($month === null) {
                    continue;
                }

                $week_num = get_week_number_for(mktime(0, 0, 0, $month, $day, $year));
                if (($week >= 1) && ($week < 52)) {
                    $expected_week_num = strval($_year) . '-' . str_pad(strval($week), 2, '0', STR_PAD_LEFT);
                    $this->assertTrue($week_num == $expected_week_num);
                } else {
                    // Just make sure it is self-consistent when $week is resolved into something proper
                    list($__year, $__week) = array_map('intval', explode('-', $week_num));
                    list($month, $day, $_year) = date_from_week_of_year($__year, $__week);
                    $expected_week_num = strval($_year) . '-' . str_pad(strval($__week), 2, '0', STR_PAD_LEFT);
                    $this->assertTrue($week_num == $expected_week_num);
                }
            }
        }
    }

    public function testApiDayOfWeek()
    {
        if (($this->only !== null) && ($this->only != 'testApiDayOfWeek')) {
            return;
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // The dates (2012/1/1, 2012/1/8, and 2012/1/9) found from looking at an actual calendar

        // Converting from calendar to dow_of_month
        $this->assertTrue(find_abstract_day(2011, 1, 1, 'dow_of_month') == array_search('Saturday', $days));
        $this->assertTrue(find_abstract_day(2011, 1, 8, 'dow_of_month') == array_search('Saturday', $days) + 7);
        $this->assertTrue(find_abstract_day(2011, 1, 9, 'dow_of_month') == array_search('Sunday', $days) + 7);

        // Converting from dow_of_month to calendar (i.e. the reverse)
        $year = 2011;
        $month = 1;
        $hour = 0;
        $minute = 0;
        $this->assertTrue(find_concrete_day_of_month($year, $month, array_search('Saturday', $days), 'dow_of_month', $hour, $minute, 'UTC', false) == 1);
        $year = 2011;
        $month = 1;
        $hour = 0;
        $minute = 0;
        $this->assertTrue(find_concrete_day_of_month($year, $month, array_search('Saturday', $days) + 7, 'dow_of_month', $hour, $minute, 'UTC', false) == 8);
        $year = 2011;
        $month = 1;
        $hour = 0;
        $minute = 0;
        $this->assertTrue(find_concrete_day_of_month($year, $month, array_search('Sunday', $days) + 7, 'dow_of_month', $hour, $minute, 'UTC', false) == 9);
    }

    public function testApiFindsCorrectTimezonedStart()
    {
        if (($this->only !== null) && ($this->only != 'testApiFindsCorrectTimezonedStart')) {
            return;
        }

        $timestamp = cal_get_start_utctime_for_event('Europe/London'/*i.e. BST/DTC*/, 2012, 8, 10, 'day_of_month', null, null, true);
        $this->assertTrue(mktime(23, 0, 0, 8, 10, 2012) == $timestamp);
    }

    public function testApiFindsCorrectTimezonedEnd()
    {
        if (($this->only !== null) && ($this->only != 'testApiFindsCorrectTimezonedEnd')) {
            return;
        }

        $timestamp = cal_get_end_utctime_for_event('Europe/London'/*i.e. BST/DTC*/, 2012, 8, 10, 'day_of_month', null, null, true);
        $this->assertTrue(mktime(22, 59, 0, 8, 10, 2012) == $timestamp);
    }

    public function testApiFindsCorrectTimezonedStart2()
    {
        if (($this->only !== null) && ($this->only != 'testApiFindsCorrectTimezonedStart2')) {
            return;
        }

        // DST
        $this->assertTrue(23 == find_timezone_start_hour_in_utc('Europe/London', 2012, 8, 10, 'day_of_month'));

        // No DST
        $this->assertTrue(0 == find_timezone_start_minute_in_utc('Europe/London', 2012, 8, 10, 'day_of_month'));
    }

    public function testApiFindsCorrectTimezonedEnd2()
    {
        if (($this->only !== null) && ($this->only != 'testApiFindsCorrectTimezonedEnd2')) {
            return;
        }

        // DST
        $this->assertTrue(22 == find_timezone_end_hour_in_utc('Europe/London', 2012, 8, 10, 'day_of_month'));
        $this->assertTrue(59 == find_timezone_end_minute_in_utc('Europe/London', 2012, 8, 10, 'day_of_month'));
    }

    public function testApiDaysBetween()
    {
        if (($this->only !== null) && ($this->only != 'testApiDaysBetween')) {
            return;
        }

        $days = get_days_between(1, 1, 2011, 1, 1, 2012);
        $this->assertTrue($days == 365);

        // And a leap year
        $days = get_days_between(1, 1, 2000, 1, 1, 2001);
        $this->assertTrue($days == 366);
    }

    public function testWindowBinding()
    {
        if (($this->only !== null) && ($this->only != 'testWindowBinding')) {
            return;
        }

        // The time window for a long running event is cut correctly
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2011;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'none';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2, 10, 2010);
        $period_end = mktime(23, 59, 0, 2, 10, 2010);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $cnt = count($_recurrences);
        $this->assertTrue($cnt == 1, 'Got ' . integer_format($cnt) . ' recurrences');
        foreach ($_recurrences as $_recurrence) {
            list($window_start, $window_end, $start, $end, $start_untimezoned, $end_untimezoned) = $_recurrence;
            $this->assertTrue($window_start == $period_start);
            $this->assertTrue($window_end == $period_end);
        }
    }

    public function testRecurrenceSimpleMonthly()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceSimpleMonthly')) {
            return;
        }

        // Recurrences work for simple monthly recurrences
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'monthly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2 + 3, 10, 2009);
        $period_end = mktime(23, 59, 0, 2 + 3, 10, 2009);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 1);
        foreach ($_recurrences as $_recurrence) {
            list($window_start, $window_end, $start, $end, $start_untimezoned, $end_untimezoned) = $_recurrence;
            $this->assertTrue($start_untimezoned == $period_start);
            $this->assertTrue($end_untimezoned == $period_start); // Intentional, as we set this event to have same end date as start date as period start
        }
    }

    public function testRecurrenceSimpleYearly()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceSimpleYearly')) {
            return;
        }

        // Recurrences work for simple yearly recurrences
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'yearly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2, 10, 2009 + 3);
        $period_end = mktime(23, 59, 0, 2, 10, 2009 + 3);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 1);
        foreach ($_recurrences as $_recurrence) {
            list($window_start, $window_end, $start, $end, $start_untimezoned, $end_untimezoned) = $_recurrence;
            $this->assertTrue($start_untimezoned == $period_start);
            $this->assertTrue($end_untimezoned == $period_start); // Intentional, as we set this event to have same end date as start date as period start
        }
    }

    public function testRecurrenceSimpleWeekly()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceSimpleWeekly')) {
            return;
        }

        // Recurrences work for simple weekly recurrences
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'weekly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2/*month*/, 10 + 7 * 3/*day*/, 2009/*year*/);
        $period_end = mktime(23, 59, 0, 2/*month*/, 10 + 7 * 3/*day*/, 2009/*year*/);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 1);
        foreach ($_recurrences as $_recurrence) {
            list($window_start, $window_end, $start, $end, $start_untimezoned, $end_untimezoned) = $_recurrence;
            $this->assertTrue($start_untimezoned == $period_start);
            $this->assertTrue($end_untimezoned == $period_start); // Intentional, as we set this event to have same end date as start date as period start
        }
    }

    public function testRecurrenceSimpleStopAtN()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceSimpleStopAtN')) {
            return;
        }

        // Only have exact number of recurrences specified
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'monthly';
        $recurrences = 2;
        $period_start = mktime(0, 0, 0, 2 - 5, 10, 2009);
        $period_end = mktime(23, 59, 0, 2 + 5, 10, 2009);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 2);
    }

    public function testRecurrenceNthDayOfWeek()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceNthDayOfWeek')) {
            return;
        }

        // Recurrence by reference to the nth day of week within a month works
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 1; // 1st Tuesday
        $start_monthly_spec_type = 'dow_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 1;
        $end_monthly_spec_type = 'dow_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'monthly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2, 1, 2009);
        $period_end = mktime(23, 59, 0, 2, 31, 2009);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(date('D', $_recurrences[0][2]) == 'Tue'); // Tue 3rd Feb 2009
        $this->assertTrue(date('D', $_recurrences[1][2]) == 'Tue'); // Tue 10th Feb 2009
        $this->assertTrue($_recurrences[1][2] > $_recurrences[0][2]);
    }

    public function testRecurrenceNthDayOfWeekTimezones()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceNthDayOfWeekTimezones')) {
            return;
        }

        /*
        This is a really complex case. The dates are stored relative to the timezone. The times are not - they are UTC.
        Times must be UTC for consistency with other code. Dates must be in the timezone due to non-regularity.

        We will have a test event in America/New_York from 7:45pm until 9:45pm, which will result in an end time SEEMINGLY before it starts, due to the above strange rule.
        */

        $timezone = 'America/New_York';
        $do_timezone_conv = 0;
        $start_year = 2013;
        $start_month = 6;
        $start_day = 0; // 1st Monday
        $start_monthly_spec_type = 'dow_of_month';
        $start_hour = 23;
        $start_minute = 45;
        $end_year = 2013;
        $end_month = 6;
        $end_day = 1;
        $end_monthly_spec_type = 'dow_of_month';
        $end_hour = 1;
        $end_minute = 45;
        $recurrence = 'monthly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 5, 1, 2013);
        $period_end = mktime(23, 59, 0, 7, 31, 2013);
        $recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(date('D', $recurrences[0][2]) == 'Mon'); // Mon 3rd June 2013
        $this->assertTrue(date('D', $recurrences[0][3]) == 'Mon'); // Mon 3rd June 2013
        $this->assertTrue(date('D', $recurrences[1][2]) == 'Mon'); // Mon 10th June 2013
        $this->assertTrue(date('D', $recurrences[1][3]) == 'Mon'); // Mon 10th June 2013
        $this->assertTrue($recurrences[0][3] > $recurrences[0][2]);
    }

    public function testDayGap()
    {
        if (($this->only !== null) && ($this->only != 'testDayGap')) {
            return;
        }

        // For some dow start and dow end time, that naively seems non-consecutive/negative, ensure it does actually compute with the consistent and expected day gap

        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2013;
        $start_month = 6;
        $start_day = 5; // 1st Saturday (=1st June 2013)
        $start_monthly_spec_type = 'dow_of_month';
        $start_hour = 2;
        $start_minute = 0;
        $end_year = 2013;
        $end_month = 6;
        $end_day = 0; // 1st Monday (=3rd June 2013)
        $end_monthly_spec_type = 'dow_of_month';
        $end_hour = 2;
        $end_minute = 0;
        $recurrence = 'monthly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 5, 1, 2013);
        $period_end = mktime(23, 59, 0, 7, 31, 2013);
        $recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(($recurrences[0][3] - $recurrences[0][2]) == 60 * 60 * 24 * 2); // 2 days in initial month, where both still dow-defined
        $this->assertTrue(($recurrences[1][3] - $recurrences[1][2]) == 60 * 60 * 24 * 2); // 2 days in second month, due to extrapolated consistent gap (starts on dow, ends after gap)
    }

    public function testRecurrenceFastForward()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceFastForward')) {
            return;
        }

        // Jumping forward 10 years for a monthly occurring event, it still displays with the expected date
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'monthly';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2, 10, 2009 + 10);
        $period_end = mktime(23, 59, 0, 2, 10, 2009 + 10);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 1);
        $this->assertTrue(intval(date('m', $_recurrences[0][2])) == $start_month);
        $this->assertTrue(intval(date('d', $_recurrences[0][2])) == $start_day);
        $this->assertTrue(intval(date('Y', $_recurrences[0][2])) == 2009 + 10);
    }

    public function testRecurrenceMasks()
    {
        if (($this->only !== null) && ($this->only != 'testRecurrenceMasks')) {
            return;
        }

        // Test recurrence masks work
        $timezone = 'UTC';
        $do_timezone_conv = 0;
        $start_year = 2009;
        $start_month = 2;
        $start_day = 10;
        $start_monthly_spec_type = 'day_of_month';
        $start_hour = 0;
        $start_minute = 0;
        $end_year = 2009;
        $end_month = 2;
        $end_day = 10;
        $end_monthly_spec_type = 'day_of_month';
        $end_hour = 0;
        $end_minute = 0;
        $recurrence = 'monthly 1001';
        $recurrences = null;
        $period_start = mktime(0, 0, 0, 2, 9, 2009);
        $period_end = mktime(23, 59, 0, 5, 11, 2009);
        $_recurrences = find_periods_recurrence($timezone, $do_timezone_conv, $start_year, $start_month, $start_day, $start_monthly_spec_type, $start_hour, $start_minute, $end_year, $end_month, $end_day, $end_monthly_spec_type, $end_hour, $end_minute, $recurrence, $recurrences, $period_start, $period_end);
        $this->assertTrue(count($_recurrences) == 2);
        $this->assertTrue(intval(date('m', $_recurrences[0][2])) == $start_month);
        $this->assertTrue(intval(date('m', $_recurrences[1][2])) == $start_month + 3);
    }

    public function testAddCalendarEvent()
    {
        if (($this->only !== null) && ($this->only != 'testAddCalendarEvent')) {
            return;
        }

        $this->event_id = add_calendar_event(8, 'none', null, 0, 'test_event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, null, 1, null, 1, 1, 1, 1, '', null, 0, null, null, null);
        $this->assertTrue('test_event' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'e_title', ['id' => $this->event_id])));
    }

    public function testEditCalendarEvent()
    {
        if (($this->only !== null) && ($this->only != 'testEditCalendarEvent')) {
            return;
        }

        edit_calendar_event($this->event_id, 8, 'none', null, 0, 'test_event1', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, get_users_timezone(), 1, null, '', '', 1, 1, 1, 1, '');
        $this->assertTrue('test_event1' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'e_title', ['id' => $this->event_id])));
    }

    public function testDeleteCalendarEvent()
    {
        if (($this->only !== null) && ($this->only != 'testDeleteCalendarEvent')) {
            return;
        }

        delete_calendar_event($this->event_id);
    }

    public function testNormaliseTimeArray()
    {
        if (($this->only !== null) && ($this->only != 'testNormaliseTimeArray')) {
            return;
        }

        $this->assertTrue(normalise_time_array([-10, 1, 1, 2, 2017], 'Europe/London') == [50, 0, 1, 2, 2017]); // Roll-backward via time

        $this->assertTrue(normalise_time_array([null, null, 13, 1, 2017], 'Europe/London') == [null, null, 1, 1, 2018]); // Roll-forward, no time
    }
}

/*
These complexities also exist, but we don't have automated tests for them:
 - In the week view, a multi-day event wraps around correctly
 - In the week view, events that finish at the end of a day show right up to the end, and not on the next day
 - In any view, events that started before the bound of the view, flow in correctly
 - When editing an event that is recurring with a day-of-week recurrence, it correctly detects the day of the week it was saved as
  - Initially
  - And dynamically as you change the start day
*/
