<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

/*
Much of this API works via booking details structure, which is an array consisting of...

bookable_id
start_day/month/year
end day/month/year
quantity
supplements
 quantity
 notes

The definition of booking here is user-focused, the user is making a booking for a period, ordering a number of bookables.

These are saved into the database under a different definition of 'booking', where bookings are actually very specific.

One user booking becomes a lot of separate bookings for separate things when it comes to the database.
*/

/**
 * Standard code module init function.
 */
function init__booking()
{
    define('SHOW_WARNINGS_UNTIL', time() + 60 * 60 * 24 * 31 * intval(get_option('bookings_show_warnings_for_months')));
    define('MAX_AHEAD_BOOKING_DATE', time() + 60 * 60 * 24 * 31 * intval(get_option('bookings_max_ahead_months')));
}

/**
 * AJAX script to work out the price of a booking.
 */
function booking_price_ajax_script()
{
    header('Content-type: text/plain; charset=' . get_charset());
    $request = get_booking_request_from_form();
    echo float_format(find_booking_price($request), 2);
}

/**
 * See if a booking is possible.
 *
 * @param  array $request Booking details structure to check, passed by reference as statuses get added.
 * @param  array $ignore_bookings Existing bookings to ignore (presumably the booking we're trying to make - if this is an edit).
 * @return ?Tempcode Error message (null: no issue).
 */
function check_booking_dates_available(&$request, $ignore_bookings)
{
    $success = mixed();
    foreach ($request as $i => $part) {
        foreach (days_in_range($part['start_day'], $part['start_month'], $part['start_year'], $part['end_day'], $part['end_month'], $part['end_year']) as $_date) {
            list($day, $month, $year) = $_date;
            $status_error = booking_date_available($part['bookable_id'], $day, $month, $year, $part['quantity'], $ignore_bookings);
            $part['status_error'] = $status_error;
            if ((!is_null($status_error)) && (is_null($success))) {
                $success = $status_error; // Set status to first error
            }
        }
        $request[$i] = $part;
    }
    return $success;
}

/**
 * Read in notes from POST environment, using special naming conventions.
 *
 * @param  string $prefix The prefix for defining what to read in.
 * @return string The notes.
 */
function read_booking_notes_from_form($prefix)
{
    // Read in notes. We have a special post parameter syntax for defining structured notes (custom fields on the form), so that we can allow webmasters to take some rich input
    $notes = post_param_string($prefix, '');

    $prefix .= '_';
    foreach (array_keys($_POST) as $post) {
        if (substr($post, 0, strlen($prefix)) == $prefix) {
            if ($notes != '') {
                $notes .= "\n\n";
            }
            $notes .= post_param_string('descript_' . $post, $post) . ':' . "\n" . post_param_string($post, '');
        }
    }
    return $notes;
}

/**
 * Read booking request from POST environment.
 *
 * @return array Booking details structure.
 */
function get_booking_request_from_form()
{
    $request = array();

    $bookables = list_to_map('id', $GLOBALS['SITE_DB']->query_select('bookable', array('*')));
    foreach ($bookables as $bookable_id => $bookable) {
        $all_supplements = $GLOBALS['SITE_DB']->query_select('bookable_supplement', array('*'));

        $quantity = post_param_integer('bookable_' . strval($bookable_id) . '_quantity', 0);
        if ($quantity > 0) {
            $start = post_param_date('bookable_' . strval($bookable_id) . '_date_from');
            if (is_null($start)) {
                $start = post_param_date('bookable_date_from');
            }
            $start_day = intval(date('d', $start));
            $start_month = intval(date('m', $start));
            $start_year = intval(date('Y', $start));
            if ($bookable['dates_are_ranges'] == 1) {
                $end = post_param_date('bookable_' . strval($bookable_id) . '_date_to');
                if (is_null($end)) {
                    $end = post_param_date('bookable_date_to');
                }
                $end_day = intval(date('d', $end));
                $end_month = intval(date('m', $end));
                $end_year = intval(date('Y', $end));
            } else {
                $end_day = $start_day;
                $end_month = $start_month;
                $end_year = $start_year;
            }

            $notes = read_booking_notes_from_form('bookable_' . strval($bookable_id) . '_notes');

            $supplements = array();
            foreach ($all_supplements as $supplement) {
                $s_quantity = post_param_integer('bookable_' . strval($bookable_id) . '_supplement_' . strval($supplement['id']) . '_quantity', 0);
                if ($s_quantity > 0) {
                    $s_notes = read_booking_notes_from_form('bookable_' . strval($bookable_id) . '_supplement_' . strval($supplement['id']) . '_notes');

                    $supplements[$supplement['id']] = array(
                        'quantity' => $s_quantity,
                        'notes' => $s_notes,
                    );
                }
            }

            $customer_email = post_param_string('customer_email', '');
            $customer_email_confirm = post_param_string('customer_email_confirm', null);
            if ((!is_null($customer_email_confirm)) && ($customer_email != $customer_email_confirm)) {
                warn_exit(do_lang_tempcode('EMAIL_ADDRESS_MISMATCH'));
            }

            $request[] = array(
                'bookable_id' => $bookable_id,
                'start_day' => $start_day,
                'start_month' => $start_month,
                'start_year' => $start_year,
                'end_day' => $end_day,
                'end_month' => $end_month,
                'end_year' => $end_year,
                'quantity' => $quantity,
                'notes' => $notes,
                'supplements' => $supplements,
                'customer_name' => post_param_string('customer_name', ''),
                'customer_email' => $customer_email,
                'customer_mobile' => post_param_string('customer_mobile', ''),
                'customer_phone' => post_param_string('customer_phone', ''),
            );
        }
    }

    return $request;
}

/**
 * Take details posted about a booking, and save to the database.
 *
 * @param  array $request Booking details structure.
 * @param  array $ignore_bookings Existing bookings to ignore (presumably the booking we're trying to make - if this is an edit).
 * @param  ?MEMBER $member_id The member ID we are saving as (null: current user).
 * @return ?array Booking details structure (null: error -- reshow form).
 */
function save_booking_form_to_db($request, $ignore_bookings, $member_id = null)
{
    if (is_null($member_id)) {
        $member_id = get_member();
    }

    if (get_option('member_booking_only') == '1') {
        if (is_guest($member_id)) {
            fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
    }

    $test = check_booking_dates_available($request, $ignore_bookings);
    if (!is_null($test)) {
        attach_message($test, 'warn');
        return null;
    }

    $request = add_booking($request, $member_id);

    return $request;
}

/**
 * Add a new booking to the database.
 *
 * @param  array $request Booking details structure.
 * @param  MEMBER $member_id Member ID being added against.
 * @return array Booking details structure.
 */
function add_booking($request, $member_id)
{
    foreach ($request as $rid => $req) {
        $days = days_in_range($req['start_day'], $req['start_month'], $req['start_year'], $req['end_day'], $req['end_month'], $req['end_year']);

        // Now insert into DB. Technically each day gets its own booking
        for ($i = 0; $i < $req['quantity']; $i++) {
            $code = mixed();

            foreach ($days as $j => $_date) {
                list($day, $month, $year) = $_date;

                $code = find_free_bookable_code($req['bookable_id'], $day, $month, $year, $code); // Hopefully $code will stay the same, but it might not
                if (is_null($code)) {
                    fatal_exit(do_lang_tempcode('INTERNAL_ERROR')); // Should not be possible, as we already checked availability
                }

                $row = array(
                    'bookable_id' => $req['bookable_id'],
                    'member_id' => $member_id,
                    'b_day' => $day,
                    'b_month' => $month,
                    'b_year' => $year,
                    'code_allocation' => $code,
                    'notes' => $req['notes'],
                    'booked_at' => time(),
                    'paid_at' => null,
                    'paid_trans_id' => null,
                    'customer_name' => $req['customer_name'],
                    'customer_email' => $req['customer_email'],
                    'customer_mobile' => $req['customer_mobile'],
                    'customer_phone' => $req['customer_phone'],
                );
                $booking_id = $GLOBALS['SITE_DB']->query_insert('booking', $row, true);

                if (!array_key_exists('_rows', $request[$rid])) {
                    $request[$rid]['_rows'] = array();
                }
                $request[$rid]['_rows'][] = $row;

                // Supplements
                foreach ($req['supplements'] as $supplement_id => $supplement) {
                    $GLOBALS['SITE_DB']->query_insert('booking_supplement', array(
                        'booking_id' => $booking_id,
                        'supplement_id' => $supplement_id,
                        'quantity' => $supplement['quantity'],
                        'notes' => $supplement['notes'],
                    ));
                }
            }
        }
    }

    return $request;
}

/**
 * Find an available code for a booking for the bookable on a given date.
 *
 * @param  AUTO_LINK $bookable_id Bookable ID.
 * @param  integer $day Day.
 * @param  integer $month Month.
 * @param  integer $year Year.
 * @param  ID_TEXT $preferred_code Preferred code (often passed in as the last code provided, in order to provide continuity to guests).
 * @return ?ID_TEXT The code (null: could not find a code).
 */
function find_free_bookable_code($bookable_id, $day, $month, $year, $preferred_code)
{
    $_available = $GLOBALS['SITE_DB']->query_select('bookable_codes a LEFT JOIN ' . get_table_prefix() . 'booking b ON a.code=b.code_allocation AND a.bookable_id=b.bookable_id AND b.b_day=' . strval($day) . ' AND b.b_month=' . strval($month) . ' AND b.b_year=' . strval($year), array('code'), array('b.id' => null, 'a.bookable_id' => $bookable_id));
    $available = collapse_1d_complexity('code', $_available);
    if (in_array($preferred_code, $available)) {
        return $preferred_code;
    }
    if (!array_key_exists(0, $available)) {
        return null;
    }
    return $available[0];
}

/**
 * Find the re-constituted booking ID a specific booking row ID is in.
 *
 * @param  MEMBER $member_id Member ID is for.
 * @param  AUTO_LINK $id Booking row ID.
 * @return ID_TEXT Re-constituted booking ID.
 */
function find_booking_under($member_id, $id)
{
    if (get_option('member_booking_only') == '0') {
        return strval($id);
    }

    $all = get_member_booking_request($member_id);
    foreach ($all as $i => $r) {
        foreach ($r['_rows'] as $row) {
            if ($row['id'] == $id) {
                break;
            }
        }
    }

    return strval($member_id) . '_' . strval($i);
}

/**
 * Find the price for a booking. This may involve multiple bookables, as at this point we don't care about that or not (once in the DB, it will actually be considered many separate bookings)
 *
 * @param  array $request Booking details structure to check.
 * @return REAL The price.
 */
function find_booking_price($request)
{
    $price = 0.0;

    foreach ($request as $i => $part) {
        $days = days_in_range($part['start_day'], $part['start_month'], $part['start_year'], $part['end_day'], $part['end_month'], $part['end_year']);
        foreach ($days as $_date) {
            $price += find_bookable_price($part['bookable_id']) * $part['quantity'];
        }

        foreach ($part['supplements'] as $supplement_id => $supplement_part) {
            $supplement_quantity = $supplement_part['quantity'];

            $_supplement = $GLOBALS['SITE_DB']->query_select('bookable_supplement', array('*'), array('id' => $supplement_id), '', 1);
            if (array_key_exists(0, $_supplement)) {
                $price += $_supplement[0]['price'] * $supplement_quantity * (($_supplement[0]['price_is_per_period'] == 1) ? count($days) : 1);

                if (($supplement_quantity != 0) && ($_supplement[0]['supports_quantities'] == 0)) {
                    fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
                }
            }
        }
    }

    return $price;
}

/**
 * Find the price of a bookable.
 *
 * @param  AUTO_LINK $bookable_id Bookable ID.
 * @return REAL Price.
 */
function find_bookable_price($bookable_id)
{
    return $GLOBALS['SITE_DB']->query_select_value('bookable', 'price', array('id' => $bookable_id));
}

/**
 * Find a list of days within a date range (inclusive).
 *
 * @param  integer $start_day Day (start).
 * @param  integer $start_month Month (start).
 * @param  integer $start_year Year (start).
 * @param  integer $end_day Day (end).
 * @param  integer $end_month Month (end).
 * @param  integer $end_year Year (end).
 * @return array List of days.
 */
function days_in_range($start_day, $start_month, $start_year, $end_day, $end_month, $end_year)
{
    $start_date = mktime(0, 0, 0, $start_month, $start_day, $start_year);
    $end_date = mktime(0, 0, 0, $end_month, $end_day, $end_year);

    $days = array();
    $days[] = array(intval(date('d', $start_date)), intval(date('m', $start_date)), intval(date('Y', $start_date)));

    $current_date = $start_date;
    while ($current_date < $end_date) {
        $current_date = strtotime('+1 day', $current_date);
        $days[] = array(intval(date('d', $current_date)), intval(date('m', $current_date)), intval(date('Y', $current_date)));
    }

    return $days;
}

/**
 * Finds whether a particular booking date is available for a particular bookable.
 *
 * @param  AUTO_LINK $bookable_id Bookable.
 * @param  integer $day Day.
 * @param  integer $month Month.
 * @param  integer $year Year.
 * @param  integer $quantity Quantity needed.
 * @param  array $ignore_bookings Existing bookings to ignore (presumably the booking we're trying to make - if this is an edit).
 * @return ?Tempcode Error message (null: no issue).
 */
function booking_date_available($bookable_id, $day, $month, $year, $quantity, $ignore_bookings)
{
    $asked = mktime(0, 0, 0, $month, $day, $year);

    // Check bookable exists
    $_bookable_row = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), array('id' => $bookable_id), '', 1);
    if (!array_key_exists(0, $_bookable_row)) {
        return do_lang_tempcode('INTERNAL_ERROR');
    }
    $bookable_row = $_bookable_row[0];
    $codes_in_total = $GLOBALS['SITE_DB']->query_select_value('bookable_codes', 'COUNT(*)', array('bookable_id' => $bookable_id));

    // Check bookable enabled
    if ($bookable_row['enabled'] == 0) {
        return do_lang_tempcode('INTERNAL_ERROR');
    }

    // Check bookable time is in active period
    $from = mktime(0, 0, 0, $bookable_row['active_from_month'], $bookable_row['active_from_day'], $bookable_row['active_from_year']);
    if ($asked < $from) {
        return do_lang_tempcode('BOOKING_IMPOSSIBLE_NOT_STARTED', escape_html(get_timezoned_date($from, false, true, false, true)));
    }
    if (!is_null($bookable_row['active_to_month'])) {
        $to = mktime(0, 0, 0, $bookable_row['active_to_month'], $bookable_row['active_to_day'], $bookable_row['active_to_year']);
        if ($asked >= $to) {
            return do_lang_tempcode('BOOKING_IMPOSSIBLE_ENDED', escape_html(get_timezoned_date($to, false, true, false, true)));
        }
    }

    // Check bookable is not blacked for time
    $blacks = $GLOBALS['SITE_DB']->query_select('bookable_blacked b JOIN ' . get_table_prefix() . 'bookable_blacked_for f ON f.blacked_id=b.id', array('*'), array('f.bookable_id' => $bookable_id));
    foreach ($blacks as $black) {
        $from = mktime(0, 0, 0, $black['blacked_from_month'], $black['blacked_from_day'], $black['blacked_from_year']);
        $to = mktime(0, 0, 0, $black['blacked_to_month'], $black['blacked_to_day'], $black['blacked_to_year']);
        if (($asked >= $from) && ($asked < $to)) {
            if ($from == $to) {
                return do_lang_tempcode('BOOKING_IMPOSSIBLE_BLACKED_ONEOFF', escape_html(get_timezoned_date($from, false, true, false, true)), escape_html(get_translated_text($black['blacked_explanation'])));
            } else {
                return do_lang_tempcode('BOOKING_IMPOSSIBLE_BLACKED_PERIOD', escape_html(get_timezoned_date($from, false, true, false, true)), escape_html(get_timezoned_date($to, false, true, false, true)), escape_html(get_translated_text($black['blacked_explanation'])));
            }
        }
    }

    // Check no overlap
    $query = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'booking WHERE b_day=' . strval($day) . ' AND b_month=' . strval($month) . ' AND b_year=' . strval($year);
    foreach ($ignore_bookings as $b) {
        $query .= ' AND id<>' . strval($b);
    }
    $codes_taken_already = $GLOBALS['SITE_DB']->query_value_if_there($query, false, true);
    if ($codes_taken_already + $quantity > $codes_in_total) {
        return do_lang_tempcode('BOOKING_IMPOSSIBLE_FULL', get_timezoned_date($asked, false, true, false, true));
    }

    // Good!
    return null;
}

/**
 * Send out booking mails.
 *
 * @param  array $request Booking details structure.
 */
function send_booking_emails($request)
{
    require_code('notifications');

    if (get_option('member_booking_only') == '1') {
        $customer_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member());
        $customer_name = $GLOBALS['FORUM_DRIVER']->get_username(get_member());
    } else {
        $customer_email = $request[0]['customer_email'];
        $customer_name = $request[0]['customer_name'];
    }

    $tpl_map = array(
        'EMAIL_ADDRESS' => $customer_email,
        'MEMBER_ID' => strval(get_member()),
        'USERNAME' => $customer_name,
        'PRICE' => float_format(find_booking_price($request)),
        'DETAILS' => make_booking_request_printable($request),
        'MOBILE_NUMBER' => $request[0]['customer_mobile'],
        'PHONE_NUMBER' => $request[0]['customer_phone'],
    );

    // Send receipt to customer
    $receipt = do_notification_template('BOOKING_CONFIRM_FCOMCODE', $tpl_map);
    $subject = do_lang('SUBJECT_BOOKING_CONFIRM', get_site_name());
    $body = static_evaluate_tempcode($receipt);
    if (get_option('member_booking_only') == '1') {
        dispatch_notification('booking_customer', null, $subject, $body, array(get_member()), A_FROM_SYSTEM_PRIVILEGED);
    } else {
        require_code('mail');
        mail_wrap($subject, $body, array($customer_email), $customer_name);
    }

    // Send notice to staff
    $subject = do_lang('SUBJECT_BOOKING_NOTICE', $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true), get_site_name(), $GLOBALS['FORUM_DRIVER']->get_username(get_member()));
    $notice = do_notification_template(
        'BOOKING_NOTICE_FCOMCODE',
        array(
            '_GUID' => 'd223b42f024f853f63cd9908155667a8',
            'EMAIL_ADDRESS' => $customer_email,
            'MEMBER_ID' => strval(get_member()),
            'USERNAME' => $customer_name,
            'PRICE' => float_format(find_booking_price($request)),
            'DETAILS' => make_booking_request_printable($request)
        ),
        get_site_default_lang()
    );
    dispatch_notification('booking_inform_staff', null, $subject, static_evaluate_tempcode($notice), null, null, 2);
}

/**
 * Convert an internal booking details structure to a 'printable' version of the same.
 *
 * @param  array $request Booking details structure.
 * @return array Printable booking details structure.
 */
function make_booking_request_printable($request)
{
    $out = array();

    foreach ($request as $_part) {
        $start = mktime(0, 0, 0, $_part['start_month'], $_part['start_day'], $_part['start_year']);
        $end = mktime(0, 0, 0, $_part['end_month'], $_part['end_day'], $_part['end_year']);

        $bookable_row = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), array('id' => $_part['bookable_id']), '', 1);

        $part = array(
            'BOOKABLE_TITLE' => get_translated_tempcode('bookable', $bookable_row[0], 'title'),
            'PRICE' => float_format($bookable_row[0]['price']),
            'CATEGORISATION' => get_translated_text($bookable_row[0]['categorisation']),
            'DESCRIPTION' => get_translated_tempcode('bookable', $bookable_row[0], 'description'),
            'QUANTITY' => integer_format($_part['quantity']),
            '_QUANTITY' => strval($_part['quantity']),
            'START' => get_timezoned_date($start, false, true, false, true),
            'END' => ($start == $end) ? '' : get_timezoned_date($end, false, true, false, true),
            '_START' => strval($start),
            '_END' => strval($end),
            'NOTES' => $_part['notes'],
            'SUPPLEMENTS' => array(),
        );
        foreach ($_part['supplements'] as $supplement_id => $supplement) {
            $supplement_row = $GLOBALS['SITE_DB']->query_select('bookable_supplement', array('*'), array('id' => $supplement_id), '', 1);

            $part['SUPPLEMENTS'][] = array(
                'SUPPLEMENT_TITLE' => get_translated_tempcode('bookable_supplement', $supplement_row[0], 'title'),
                'SUPPLEMENT_PRICE' => float_format($supplement_row[0]['price']),
                'SUPPLEMENT_PRICE_IS_PER_PERIOD' => $supplement_row[0]['price_is_per_period'] == 1,
                'SUPPLEMENT_QUANTITY' => integer_format($supplement['quantity']),
                '_SUPPLEMENT_QUANTITY' => strval($supplement['quantity']),
                'SUPPLEMENT_NOTES' => $supplement['notes'],
            );
        }

        $out[] = $part;
    }
    return $out;
}
