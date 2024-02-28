<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    booking
 */

/**
 * Show bookables ical feed.
 */
function bookables_ical_script()
{
    if (!addon_installed('booking')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('booking')));
    }

    if (!addon_installed('calendar')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('calendar')));
    }
    if (!addon_installed('ecommerce')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
    }

    require_code('calendar_ical');

    require_lang('booking');

    cms_ini_set('ocproducts.xss_detect', '0');

    //header('Content-Type: text/calendar; charset=' . get_charset());
    //header('Content-Disposition: inline; filename="bookables_export.ics"');

    cms_disable_time_limit();

    if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
        return '';
    }

    $query = 'SELECT * FROM ' . get_table_prefix() . 'bookable WHERE enabled=1';
    $filter = get_param_string('filter', '*', INPUT_FILTER_GET_COMPLEX);
    require_code('selectcode');
    $query .= ' AND ' . selectcode_to_sqlfragment($filter, 'id');
    $events = $GLOBALS['SITE_DB']->query($query, null, 0, false, true);
    echo "BEGIN:VCALENDAR\n";
    echo "VERSION:2.0\n";
    echo "PRODID:-//Christopher Graham/Composr//NONSGML v1.0//EN\n";
    echo "CALSCALE:GREGORIAN\n";
    echo "X-WR-CALNAME:" . ical_escape(get_site_name() . ': ' . do_lang('BOOKABLE_EVENTS')) . "\n";

    foreach ($events as $event) {
        echo "BEGIN:VEVENT\n";

        echo "DTSTAMP:" . date('Ymd', $event['add_date']) . "T" . date('His', $event['add_date']) . "\n";
        echo "CREATED:" . date('Ymd', $event['add_date']) . "T" . date('His', $event['add_date']) . "\n";
        if ($event['edit_date'] !== null) {
            echo "LAST-MODIFIED:" . date('Ymd', time()) . "T" . date('His', $event['edit_date']) . "\n";
        }

        echo "SUMMARY:" . ical_escape(get_translated_text($event['title'])) . "\n";
        $description = get_translated_text($event['the_description']);
        echo "DESCRIPTION:" . ical_escape($description) . "\n";

        if (!is_guest($event['submitter'])) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($event['submitter'], true, USERNAME_DEFAULT_NULL);
            if ($username !== null) {
                echo "ORGANIZER;CN=" . ical_escape($username) . ";DIR=" . ical_escape($GLOBALS['FORUM_DRIVER']->member_profile_url($event['submitter'], false)) . ":MAILTO:" . ical_escape($GLOBALS['FORUM_DRIVER']->get_member_email_address($event['submitter'])) . "\n";
            }
        }
        echo "CATEGORIES:" . ical_escape(get_translated_text($event['categorisation'])) . "\n";
        echo "CLASS:" . (($event['price'] == 0.0) ? 'PUBLIC' : 'PRIVATE') . "\n";
        echo "STATUS:" . (($event['enabled'] == 1) ? 'CONFIRMED' : 'TENTATIVE') . "\n";
        echo "UID:" . ical_escape(strval($event['id']) . '-bookable@' . get_base_url()) . "\n";
        $_url = build_url(['page' => 'booking', 'type' => 'browse', 'filter' => $event['id']], get_module_zone('booking'), [], false, false, true);
        $url = $_url->evaluate();
        echo "URL:" . ical_escape($url) . "\n";

        $time = mktime(0, 0, 0, $event['active_from_month'], $event['active_from_day'], $event['active_from_year']);
        $time2 = null;
        if ($event['cycle_type'] != 'none') {
            $parts = explode(' ', $event['cycle_type']);
            if (count($parts) == 1) {
                echo "DTSTART;TZ=" . get_site_timezone() . ":" . date('Ymd', $time) . "\n";
                $recurrence_code = 'FREQ=' . cms_strtoupper_ascii($parts[0]);
                echo "RRULE:" . $recurrence_code . "\n";
            } else {
                for ($i = 0; $i < strlen($parts[1]); $i++) {
                    switch ($parts[0]) {
                        case 'daily':
                            $time += 60 * 60 * 24;
                            if ($time2 !== null) {
                                $time2 += 60 * 60 * 24;
                            }
                            break;
                        case 'weekly':
                            $time += 60 * 60 * 24 * 7;
                            if ($time2 !== null) {
                                $time2 += 60 * 60 * 24 * 7;
                            }
                            break;
                        case 'monthly':
                            $days_in_month = intval(date('D', mktime(0, 0, 0, intval(date('m', $time)) + 1, 0, intval(date('Y', $time)))));
                            $time += 60 * 60 * $days_in_month;
                            if ($time2 !== null) {
                                $time2 += 60 * 60 * $days_in_month;
                            }
                            break;
                        case 'yearly':
                            $days_in_year = intval(date('Y', mktime(0, 0, 0, 0, 0, intval(date('Y', $time)) + 1)));
                            $time += 60 * 60 * 24 * $days_in_year;
                            if ($time2 !== null) {
                                $time2 += 60 * 60 * 24 * $days_in_year;
                            }
                            break;
                    }
                    if ($parts[1][$i] != '0') {
                        echo "DTSTART:" . date('Ymd', $time) . "\n";
                        $recurrence_code = 'FREQ=' . cms_strtoupper_ascii($parts[0]);
                        echo "RRULE:" . $recurrence_code . ";INTERVAL=" . strval(strlen($parts[1])) . ";COUNT=1\n";
                    }
                }
            }
        } else {
            echo "DTSTART:" . date('Ymd', $time) . "\n";

            if ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                $attendees = $GLOBALS['SITE_DB']->query_select('booking', ['*'], ['bookable_id' => $event['id']], '', 5000/*reasonable limit*/);
                if (count($attendees) == 5000) {
                    $attendees = [];
                }
                foreach ($attendees as $attendee) {
                    if (!is_guest($event['member_id'])) {
                        if (!is_guest($attendee['member_id'])) {
                            $customer_name = $GLOBALS['FORUM_DRIVER']->get_username($attendee['member_id'], true, USERNAME_DEFAULT_NULL);
                            if ($customer_name !== null) {
                                $customer_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($attendee['member_id']);
                                echo "ATTENDEE;CN=" . ical_escape($customer_name) . ";DIR=" . ical_escape($GLOBALS['FORUM_DRIVER']->member_profile_url($attendee['member_id'], false)) . ":MAILTO:" . ical_escape($customer_email) . "\n";
                            }
                        } else {
                            $customer_name = $attendee['customer_name'];
                            $customer_email = $attendee['customer_email'];
                            echo "ATTENDEE;CN=" . ical_escape($customer_name) . ";MAILTO:" . ical_escape($customer_email) . "\n";
                        }
                    }
                }
            }
        }

        echo "END:VEVENT\n";
    }
    echo "END:VCALENDAR\n";
    exit();
}

/**
 * Show bookings ical feed (NB: the event type for this is admins only, unless edited).
 */
function bookings_ical_script()
{
    if (!addon_installed('booking')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('booking')));
    }

    if (!addon_installed('calendar')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('calendar')));
    }
    if (!addon_installed('ecommerce')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
    }

    require_code('crypt');
    $pass_ok = ratchet_hash_verify($GLOBALS['SITE_INFO']['maintenance_password'], get_site_salt(), get_param_string('pass', '', INPUT_FILTER_GET_COMPLEX));
    if ((!$pass_ok) && (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member()))) {
        access_denied('I_ERROR');
    }

    require_code('calendar_ical');
    require_code('booking');

    cms_ini_set('ocproducts.xss_detect', '0');

    if ($pass_ok) {
        header('Content-Type: text/calendar; charset=' . get_charset());
        header('Content-Disposition: inline; filename="bookings_export.ics"');
    }
    // If not, it's an admin testing, so just display contents

    if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
        return '';
    }

    cms_disable_time_limit();

    $time = get_param_integer('from', time());
    if ($time < 0) {
        $time = time() + 60 * 60 * 24 * $time;
    }

    require_lang('booking');

    $id = get_param_integer('id');
    $where = 'bookable_id=' . strval($id) . ' AND (b_year>' . date('Y', $time) . ' OR (b_year=' . date('Y', $time) . ' AND b_month>' . date('m', $time) . ') OR (b_year=' . date('Y', $time) . ' AND b_month=' . date('m', $time) . ' AND b_day>=' . date('d', $time) . '))';
    $members_with_bookings = $GLOBALS['SITE_DB']->query('SELECT DISTINCT member_id,booked_at FROM ' . get_table_prefix() . 'booking WHERE ' . $where . ' ORDER BY booked_at DESC', 10000/*reasonable limit*/);
    echo "BEGIN:VCALENDAR\n";
    echo "VERSION:2.0\n";
    echo "PRODID:-//Christopher Graham/Composr//NONSGML v1.0//EN\n";
    echo "CALSCALE:GREGORIAN\n";
    $bookable_category = get_translated_text($GLOBALS['SITE_DB']->query_select_value('bookable', 'title', ['id' => $id]));
    echo "X-WR-CALNAME:" . ical_escape(get_site_name() . ': ' . do_lang('BOOKINGS') . ': ' . $bookable_category) . "\n";

    require_code('booking2');
    require_lang('booking');

    $max_time = time();

    foreach ($members_with_bookings as $member_with_booking) {
        $bookings = $GLOBALS['SITE_DB']->query('SELECT id,booked_at FROM ' . get_table_prefix() . 'booking WHERE (' . $where . ') AND member_id=' . strval($member_with_booking['member_id']) . ' ORDER BY booked_at DESC', 10000/*reasonable limit*/);

        $request = get_booking_request_from_db(collapse_1d_complexity('id', $bookings));

        foreach ($request as $i => $r) {
            $booking = $r['_rows'][0];
            $codes = '';
            foreach ($r['_rows'] as $row) {
                if ($codes != '') {
                    $codes .= ', ';
                }
                $codes .= $row['code_allocation'];
            }

            $supplements = $GLOBALS['SITE_DB']->query_select('booking_supplement a JOIN ' . get_table_prefix() . 'bookable_supplement b ON a.supplement_id=b.id', ['quantity', 'notes', 'title'], [
                'booking_id' => $booking['id'],
            ]);

            $_url = build_url(['page' => 'cms_booking', 'type' => '_edit_booking', 'id' => find_booking_under($booking['member_id'], $booking['id'])], get_module_zone('cms_booking'), [], false, false, true);
            $url = $_url->evaluate();

            $time_start = mktime(0, 0, 0, $r['start_month'], $r['start_day'], $r['start_year']);
            $time_end = mktime(0, 0, 0, $r['end_month'], $r['end_day'] + 1, $r['end_year']);
            if ($time_end > $max_time) {
                $max_time = $time_end;
            }

            $description = $booking['notes'];
            foreach ($supplements as $supplement) {
                $description .= "\n\n+ " . get_translated_text($supplement['title']) . 'x' . integer_format($supplement['quantity']);
                if ($supplement['notes'] != '') {
                    $description .= ' (' . $supplement['notes'] . ')';
                }
            }

            for ($j = 0; $j < $r['quantity']; $j++) {
                echo "BEGIN:VEVENT\n";

                echo "DTSTAMP:" . date('Ymd', $booking['booked_at']) . "T" . date('His', $booking['booked_at']) . "\n";
                echo "CREATED:" . date('Ymd', $booking['booked_at']) . "T" . date('His', $booking['booked_at']) . "\n";

                echo "SUMMARY:" . ical_escape($bookable_category/*do_lang('TAKEN', $codes)*/) . "\n";
                echo "DESCRIPTION:" . ical_escape($description) . "\n";

                if (!is_guest($booking['member_id'])) {
                    if (!is_guest($booking['member_id'])) {
                        $customer_name = $GLOBALS['FORUM_DRIVER']->get_username($booking['member_id'], true, USERNAME_DEFAULT_NULL);
                        if ($customer_name !== null) {
                            $customer_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($booking['member_id']);
                            echo "ORGANIZER;CN=" . ical_escape($customer_name) . ";DIR=" . ical_escape($GLOBALS['FORUM_DRIVER']->member_profile_url($booking['member_id'], false)) . ":MAILTO:" . ical_escape($customer_email) . "\n";
                        }
                    } else {
                        $customer_name = $booking['customer_name'];
                        $customer_email = $booking['customer_email'];
                        echo "ORGANIZER;CN=" . ical_escape($customer_name) . ";MAILTO:" . ical_escape($customer_email) . "\n";
                    }
                }
                echo "CATEGORIES:" . ical_escape($bookable_category) . "\n";
                echo "CLASS:PRIVATE\n";
                echo "STATUS:" . (($booking['paid_at'] !== null) ? 'CONFIRMED' : 'TENTATIVE') . "\n";
                echo "UID:" . ical_escape(strval($booking['id']) . '-booking@' . get_base_url()) . "\n";
                echo "URL:" . ical_escape($url) . "\n";

                echo "DTSTART:" . str_pad(strval($r['start_year']), 4, '0', STR_PAD_LEFT) . str_pad(strval($r['start_month']), 2, '0', STR_PAD_LEFT) . str_pad(strval($r['start_day']), 2, '0', STR_PAD_LEFT) . "\n";
                if ($r['end_day'] !== null) {
                    echo "DTEND:" . str_pad(strval($r['end_year']), 4, '0', STR_PAD_LEFT) . str_pad(strval($r['end_month']), 2, '0', STR_PAD_LEFT) . str_pad(strval($r['end_day']), 2, '0', STR_PAD_LEFT) . "\n";
                }
                echo 'TZID:' . get_site_timezone() . "\n";

                echo "END:VEVENT\n";
            }
        }
    }

    // Show free slots
    if (get_param_integer('free', 0) == 1) {
        $codes_in_total = collapse_1d_complexity('code', $GLOBALS['SITE_DB']->query_select('bookable_codes', ['code'], ['bookable_id' => $id]));
        $i = $time;
        $up_to = strtotime('+2 days', $max_time);
        do {
            $day = intval(date('d', $i));
            $month = intval(date('m', $i));
            $year = intval(date('Y', $i));
            $codes_taken_already = collapse_1d_complexity('code_allocation', $GLOBALS['SITE_DB']->query_select('booking', ['code_allocation'], ['b_day' => $day, 'b_month' => $month, 'b_year' => $year]));

            foreach (array_diff($codes_in_total, $codes_taken_already) as $code) {
                echo "BEGIN:VEVENT\n";

                echo "DTSTAMP:" . date('Ymd', time()) . "T" . date('His', time()) . "\n";
                echo "CREATED:" . date('Ymd', time()) . "T" . date('His', time()) . "\n";

                echo "SUMMARY:" . ical_escape(do_lang('NOT_TAKEN', $code)) . "\n";

                echo "CATEGORIES:" . ical_escape($bookable_category) . "\n";
                echo "CLASS:PUBLIC\n";
                echo "STATUS:TENTATIVE\n";
                echo "UID:" . ical_escape(strval($day) . '/' . strval($month) . '/' . strval($year) . '-' . $code . '-booking@' . get_base_url()) . "\n";
                $_url = build_url(['page' => 'cms_booking', 'type' => 'add_booking', 'bookable_id' => $id, 'day' => $day, 'month' => $month, 'year' => $year, 'code' => $code], get_module_zone('cms_booking'), [], false, false, true);
                $url = $_url->evaluate();
                echo "URL:" . ical_escape($url) . "\n";

                $time = mktime(0, 0, 0, $month, $day, $year);
                if ($time > $max_time) {
                    $max_time = $time;
                }
                echo "DTSTART:" . date('Ymd', $time) . "\n";

                echo "END:VEVENT\n";
            }

            $i = strtotime('+1 day', $i);
        } while ($i <= $up_to);
    }

    echo "END:VCALENDAR\n";
    exit();
}
