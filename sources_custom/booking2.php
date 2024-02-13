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

/*
This file deals specifically with maintaining the bookables, not specific bookings for them.
*/

/**
 * Get a do-next manager for bookings.
 *
 * @return Tempcode Booking do-next manager
 */
function booking_do_next() : object
{
    require_lang('calendar');
    require_code('templates_donext');
    require_code('fields');
    return do_next_manager(
        get_screen_title('BOOKINGS'),
        comcode_lang_string('DOC_BOOKING'),
        [
            has_privilege(get_member(), 'submit_cat_highrange_content', 'cms_booking') ? ['booking/bookable', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_BOOKABLE')] : null,
            has_privilege(get_member(), 'edit_cat_highrange_content', 'cms_booking') ? ['booking/bookable', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_BOOKABLE')] : null,
            has_privilege(get_member(), 'submit_cat_highrange_content', 'cms_booking') ? ['booking/supplement', ['_SELF', ['type' => 'add_other'], '_SELF'], do_lang('ADD_BOOKABLE_SUPPLEMENT')] : null,
            has_privilege(get_member(), 'edit_cat_highrange_content', 'cms_booking') ? ['booking/supplement', ['_SELF', ['type' => 'edit_other'], '_SELF'], do_lang('EDIT_BOOKABLE_SUPPLEMENT')] : null,
            has_privilege(get_member(), 'submit_cat_highrange_content', 'cms_booking') ? ['booking/blacked', ['_SELF', ['type' => 'add_category'], '_SELF'], do_lang('ADD_BOOKABLE_BLACKED')] : null,
            has_privilege(get_member(), 'edit_cat_highrange_content', 'cms_booking') ? ['booking/blacked', ['_SELF', ['type' => 'edit_category'], '_SELF'], do_lang('EDIT_BOOKABLE_BLACKED')] : null,
            has_privilege(get_member(), 'submit_highrange_content', 'cms_booking') ? ['admin/add', ['_SELF', ['type' => 'add_booking'], '_SELF'], do_lang('ADD_BOOKING')] : null,
            has_privilege(get_member(), 'edit_highrange_content', 'cms_booking') ? ['admin/edit', ['_SELF', ['type' => 'edit_booking'], '_SELF'], do_lang('EDIT_BOOKING')] : null,
            has_actual_page_access(get_member(), 'calendar') ? ['menu/rich_content/calendar', ['calendar', ['type' => 'browse', 'view' => 'month'], '_SEARCH'], do_lang('CALENDAR')] : null,
        ],
        do_lang('BOOKINGS')
    );
}

/**
 * For a member, find reconstituted booking request details for all bookings.
 *
 * @param  MEMBER $member_id Member to find for
 * @return array Reconstituted booking details structure to check
 */
function get_member_booking_request(int $member_id) : array
{
    $booking_ids = $GLOBALS['SITE_DB']->query_select('booking', ['id', 'booked_at'], ['member_id' => $member_id], 'ORDER BY booked_at DESC');
    return get_booking_request_from_db(collapse_1d_complexity('id', $booking_ids));
}

/**
 * For a list of booking IDs (assumed to be from same member), reconstitute/simplify as much as possible, and return the booking details structure.
 *
 * @param  array $booking_ids List of booking IDs
 * @return array Reconstituted booking details structure to check
 */
function get_booking_request_from_db(array $booking_ids) : array
{
    $request = [];

    foreach ($booking_ids as $booking_id) {
        $booking = $GLOBALS['SITE_DB']->query_select('booking', ['*'], ['id' => $booking_id], '', 1);
        if (!array_key_exists(0, $booking)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $supplements = $GLOBALS['SITE_DB']->query_select('booking_supplement', ['supplement_id', 'quantity', 'notes'], ['booking_id' => $booking_id]);

        $request[] = [
            'bookable_id' => $booking[0]['bookable_id'],
            'start_day' => $booking[0]['b_day'],
            'start_month' => $booking[0]['b_month'],
            'start_year' => $booking[0]['b_year'],
            'end_day' => $booking[0]['b_day'],
            'end_month' => $booking[0]['b_month'],
            'end_year' => $booking[0]['b_year'],
            'notes' => $booking[0]['notes'],
            'supplements' => list_to_map('supplement_id', $supplements),
            'quantity' => 1,
            '_rows' => [$booking[0]], // Used by code that wants exact details, not standard part of booking details structure
            'customer_name' => $booking[0]['customer_name'],
            'customer_email' => $booking[0]['customer_email'],
            'customer_mobile' => $booking[0]['customer_mobile'],
            'customer_phone' => $booking[0]['customer_phone'],
        ];
    }

    reconstitute_booking_requests($request);

    return $request;
}

/**
 * From single booking details, convert it into a reconstituted structure.
 *
 * @param  array $request Booking details structure to check
 * @return boolean Whether any changes happened
 */
function reconstitute_booking_requests(array &$request) : bool
{
    $changes = false;

    // Sort, so that we know we only merge when things are consecutive
    sort_maps_by($request, 'bookable_id,start_year,start_month,start_day');

    $all_supplements = collapse_2d_complexity('id', 'price_is_per_period', $GLOBALS['SITE_DB']->query_select('bookable_supplement', ['id', 'price_is_per_period']));

    // Scan through, merging same things together and updating quantity
    for ($i = 0; $i < count($request); $i++) {
        cms_mb_asort($request[$i]['supplements'], SORT_NATURAL | SORT_FLAG_CASE);

        if ($i != 0) {
            $a = &$request[$i - 1];
            $b = &$request[$i];

            if (($a['bookable_id'] == $b['bookable_id']) && ($a['start_day'] == $b['start_day']) && ($a['start_month'] == $b['start_month']) && ($a['start_year'] == $b['start_year']) && ($a['end_day'] == $b['end_day']) && ($a['end_month'] == $b['end_month']) && ($a['end_year'] == $b['end_year']) && ($a['supplements'] == $b['supplements'])) {
                $a['quantity'] += $b['quantity'];
                $a['_rows'] = array_merge($a['_rows'], $b['_rows']);
                unset($request[$i]);
                $request = array_values($request);
                $i--;

                $changes = true;
            }
        }
    }

    // Scan through, merging consequtive days on same bookable into sequences
    for ($i = 0; $i < count($request); $i++) {
        if ($i != 0) {
            $a = &$request[$i - 1];
            $b = &$request[$i];

            // List just those supplements which are per-period, so we can assure that the same ones are taken for both $a and $b (hence mergeable). If so we merge all, knowing the one-offs should be merged also.
            $a_filtered_supplements = $a['supplements'];
            foreach (array_keys($a_filtered_supplements) as $supplement_id) {
                if ($all_supplements[$supplement_id] == 0) {
                    unset($a_filtered_supplements[$supplement_id]);
                }
            }
            $b_filtered_supplements = $b['supplements'];
            foreach (array_keys($b_filtered_supplements) as $supplement_id) {
                if ($all_supplements[$supplement_id] == 0) {
                    unset($b_filtered_supplements[$supplement_id]);
                }
            }

            $a_end_timestamp = mktime(0, 0, 0, $a['end_month'], $a['end_day'], $a['end_year']);
            $b_start_timestamp = mktime(0, 0, 0, $b['start_month'], $b['start_day'], $b['start_year']);

            if (($a['bookable_id'] == $b['bookable_id']) && ($a['quantity'] == $b['quantity']) && (strtotime('+1 day', $a_end_timestamp) == $b_start_timestamp) && ($a_filtered_supplements == $b_filtered_supplements)) {
                $a['end_day'] = $b['end_day'];
                $a['end_month'] = $b['end_month'];
                $a['end_year'] = $b['end_year'];
                $a['supplements'] += $b['supplements'];
                $a['_rows'] = array_merge($a['_rows'], $b['_rows']);
                if (($b['notes'] != '') && (strpos($a['notes'], $b['notes']) === false)) {
                    $a['notes'] .= "\n\n" . $b['notes'];
                }
                unset($request[$i]);
                $request = array_values($request);
                $i--;

                $changes = true;
            }
        }
    }

    return $changes;
}

/**
 * Find the future booking(s) IDs owned by a member.
 *
 * @param  ?MEMBER $member_id Member ID (null: current user)
 * @return array Booking IDs
 */
function get_future_member_booking_ids(?int $member_id = null) : array
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    $day = intval(date('d'));
    $month = intval(date('m'));
    $year = intval(date('Y'));

    $booking_ids = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'booking WHERE member_id=' . strval($member_id) . ' AND (year>' . strval($year) . ' OR year=' . strval($year) . ' AND month>' . strval($month) . ' OR year=' . strval($year) . ' AND month=' . strval($month) . ' AND day>=' . strval($day) . ')');
    return $booking_ids;
}

/**
 * Delete a specific booking. To edit a booking you need to delete then re-add.
 *
 * @param  AUTO_LINK $id Booking ID
 */
function delete_booking(int $id)
{
    $GLOBALS['SITE_DB']->query_delete('booking', ['id' => $id], '', 1);
    $GLOBALS['SITE_DB']->query_delete('booking_supplement', ['booking_id' => $id]);
}

/**
 * Read bookable details from POST environment.
 *
 * @return array Tuple: bookable_details, blacked, codes, supplements
 */
function get_bookable_details_from_form() : array
{
    $active_from = post_param_date('active_from');
    $active_to = post_param_date('active_to');
    if ($active_to !== null) {
        if ($active_to < $active_from) {
            warn_exit(do_lang_tempcode('DATE_AROUND'));
        }
    }

    require_code('ecommerce');

    $bookable_details = [
        'title' => post_param_string('title'),
        'the_description' => post_param_string('description'),
        'price' => float_unformat(str_replace(ecommerce_get_currency_symbol(), '', post_param_string('price'))),
        'categorisation' => post_param_string('categorisation'),
        'cycle_type' => post_param_string('cycle_type'),
        'cycle_pattern' => post_param_string('cycle_pattern'),
        'user_may_choose_code' => post_param_integer('user_may_choose_code', 0),
        'supports_notes' => post_param_integer('supports_notes', 0),
        'dates_are_ranges' => post_param_integer('dates_are_ranges', 0),

        'enabled' => post_param_integer('enabled', 0),
        'sort_order' => post_param_integer('sort_order'),

        'active_from_day' => intval(date('d', $active_from)),
        'active_from_month' => intval(date('m', $active_from)),
        'active_from_year' => intval(date('Y', $active_from)),
        'active_to_day' => ($active_to === null) ? null : intval(date('d', $active_to)),
        'active_to_month' => ($active_to === null) ? null : intval(date('m', $active_to)),
        'active_to_year' => ($active_to === null) ? null : intval(date('Y', $active_to)),
    ];

    /*
    $blacked = [];
    $supplements = [];
    foreach (array_keys($_POST) as $key) {
        if (substr($key, 0, 8) == 'blacked_') {
            if (post_param_integer($key, 0) == 1) {
                $blacked[] = intval(substr($key, 8));
            }
        }

        if (substr($key, 0, 11) == 'supplement_') {
            if (post_param_integer($key, 0) == 1) {
                $supplements[] = intval(substr($key, 11));
            }
        }
    }
    */
    if (!isset($_POST['blacked'])) {
        $_POST['blacked'] = [];
    }
    $blacked = array_map('intval', $_POST['blacked']);
    if (!isset($_POST['supplements'])) {
        $_POST['supplements'] = [];
    }
    $supplements = array_map('intval', $_POST['supplements']);

    $codes = explode("\n", post_param_string('codes', ''));
    if ((count($codes) == 1) && (is_numeric($codes[0]))) {
        $codes = generate_random_booking_codes(intval($codes[0]));
    }

    return [$bookable_details, $codes, $blacked, $supplements];
}

/**
 * Generate a new set of booking codes.
 *
 * @param  integer $num How many codes to generate
 * @return array The generated codes
 */
function generate_random_booking_codes(int $num) : array
{
    $codes = [];
    while (count($codes) < $num) {
        $codes[substr(md5(uniqid('cms_booking_', true)), 0, 5)] = 1;
    }
    return array_keys($codes);
}

/**
 * Read supplement details from POST environment.
 *
 * @return array Tuple: Supplement details, list of bookables
 */
function get_bookable_supplement_details_from_form() : array
{
    if (!isset($_POST['bookables'])) {
        $_POST['bookables'] = [];
    }
    $bookables = array_map('intval', $_POST['bookables']);

    return [[
                     'price' => float_unformat(post_param_string('price')),
                     'price_is_per_period' => post_param_integer('price_is_per_period', 0),
                     'supports_quantities' => post_param_integer('supports_quantities', 0),
                     'title' => post_param_string('title'),
                     'promo_code' => post_param_string('promo_code'),
                     'supports_notes' => post_param_integer('supports_notes', 0),
                     'sort_order' => post_param_integer('sort_order'),
                 ], $bookables];
}

/**
 * Read blacked details from POST environment.
 *
 * @return array Tuple: Blacked details, list of bookables
 */
function get_bookable_blacked_details_from_form() : array
{
    if (!isset($_POST['bookables'])) {
        $_POST['bookables'] = [];
    }
    $bookables = array_map('intval', $_POST['bookables']);

    $blacked_from = post_param_date('blacked_from');
    $blacked_to = post_param_date('blacked_to');

    if ($blacked_to < $blacked_from) {
        warn_exit(do_lang_tempcode('DATE_AROUND'));
    }

    return [[
                     'blacked_from_day' => intval(date('d', $blacked_from)),
                     'blacked_from_month' => intval(date('m', $blacked_from)),
                     'blacked_from_year' => intval(date('Y', $blacked_from)),
                     'blacked_to_day' => intval(date('d', $blacked_to)),
                     'blacked_to_month' => intval(date('m', $blacked_to)),
                     'blacked_to_year' => intval(date('Y', $blacked_to)),
                     'blacked_explanation' => post_param_string('blacked_explanation'),
                 ], $bookables];
}

/**
 * Add a bookable.
 *
 * @param  array $bookable_details Bookable details
 * @param  array $_codes List of codes
 * @param  array $blacked List of black-outs
 * @param  array $supplements List of supplements
 * @param  ?TIME $add_date Add date (null: now)
 * @param  ?MEMBER $submitter Submitting user (null: current user)
 * @return AUTO_LINK Bookable ID
 */
function add_bookable(array $bookable_details, array $_codes, array $blacked = [], array $supplements = [], ?int $add_date = null, ?int $submitter = null) : int
{
    if ($add_date === null) {
        $add_date = time();
    }
    if ($submitter === null) {
        $submitter = get_member();
    }

    $title = $bookable_details['title'];

    require_code('global4');
    prevent_double_submit('ADD_BOOKABLE', null, $title);

    $bookable_details = insert_lang_comcode('title', $bookable_details['title'], 1) + $bookable_details;
    $bookable_details = insert_lang_comcode('the_description', $bookable_details['the_description'], 1) + $bookable_details;
    $bookable_details = insert_lang_comcode('categorisation', $bookable_details['categorisation'], 1) + $bookable_details;

    $bookable_details['calendar_type'] = null;
    $bookable_details['add_date'] = $add_date;
    $bookable_details['edit_date'] = null;
    $bookable_details['submitter'] = $submitter;

    $bookable_id = $GLOBALS['SITE_DB']->query_insert('bookable', $bookable_details, true);

    require_code('calendar2');
    require_code('crypt');
    $external_feed = find_script('bookings_ical') . '?id=' . strval($bookable_id) . '&pass=' . ratchet_hash($GLOBALS['SITE_INFO']['master_password'], get_site_salt());
    $bookable_details['calendar_type'] = add_event_type($title, 'icons/calendar/booking', $external_feed);

    $GLOBALS['SITE_DB']->query_update('bookable', ['calendar_type' => $bookable_details['calendar_type']], ['id' => $bookable_id], '', 1);

    foreach ($blacked as $blacked_id) {
        $GLOBALS['SITE_DB']->query_insert('bookable_blacked_for', [
            'blacked_id' => $blacked_id,
            'bookable_id' => $bookable_id,
        ]);
    }

    $codes = [];
    foreach ($_codes as $i => $code) {
        $codes[$i] = substr(strval($code), 0, 80);
    }
    $codes = array_unique($codes);
    foreach ($codes as $code) {
        $GLOBALS['SITE_DB']->query_insert('bookable_codes', [
            'bookable_id' => $bookable_id,
            'code' => $code,
        ]);
    }

    foreach ($supplements as $supplement_id) {
        $GLOBALS['SITE_DB']->query_insert('bookable_supplement_for', [
            'supplement_id' => $supplement_id,
            'bookable_id' => $bookable_id,
        ]);
    }

    log_it('ADD_BOOKABLE', strval($bookable_id), $title);

    return $bookable_id;
}

/**
 * Edit a bookable.
 *
 * @param  AUTO_LINK $bookable_id Bookable ID
 * @param  array $bookable_details Bookable details
 * @param  array $codes List of codes
 * @param  ?array $blacked List of black-outs (null: no change)
 * @param  ?array $supplements List of supplements (null: no change)
 */
function edit_bookable(int $bookable_id, array $bookable_details, array $codes, ?array $blacked = null, ?array $supplements = null)
{
    $_old_bookable = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], ['id' => $bookable_id], '', 1);
    if (!array_key_exists(0, $_old_bookable)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $title = $bookable_details['title'];

    $bookable_details += lang_remap_comcode('title', $_old_bookable[0]['title'], $bookable_details['title']);
    $bookable_details += lang_remap_comcode('the_description', $_old_bookable[0]['the_description'], $bookable_details['the_description']);
    $bookable_details += lang_remap_comcode('categorisation', $_old_bookable[0]['categorisation'], $bookable_details['categorisation']);

    $bookable_details['edit_date'] = time();

    $bookable_details['calendar_type'] = $_old_bookable[0]['calendar_type'];
    require_code('calendar2');
    $external_feed = find_script('bookings_ical') . '?id=' . strval($bookable_id) . '&pass=' . md5('booking_salt_' . $GLOBALS['SITE_INFO']['master_password']);
    if (($bookable_details['calendar_type'] === null) && ($GLOBALS['SITE_DB']->query_select_value_if_there('calendar_types', 'id', ['id' => $bookable_details['calendar_type']]) === null)) {
        $bookable_details['calendar_type'] = add_event_type($title, 'icons/calendar/booking', $external_feed);
    } else {
        edit_event_type($bookable_details['calendar_type'], $title, 'icons/calendar/booking', $external_feed);
    }

    $GLOBALS['SITE_DB']->query_update('bookable', $bookable_details, ['id' => $bookable_id], '', 1);

    $GLOBALS['SITE_DB']->query_delete('bookable_codes', ['bookable_id' => $bookable_id]);
    foreach ($codes as $i => $code) {
        $codes[$i] = substr($code, 0, 80);
    }
    $codes = array_unique($codes);
    foreach ($codes as $code) {
        $GLOBALS['SITE_DB']->query_insert('bookable_codes', [
            'bookable_id' => $bookable_id,
            'code' => $code,
        ]);
    }

    if ($blacked !== null) {
        $GLOBALS['SITE_DB']->query_delete('bookable_blacked_for', ['bookable_id' => $bookable_id]);
        foreach ($blacked as $blacked_id) {
            $GLOBALS['SITE_DB']->query_insert('bookable_blacked_for', [
                'blacked_id' => $blacked_id,
                'bookable_id' => $bookable_id,
            ]);
        }
    }

    if ($supplements !== null) {
        $GLOBALS['SITE_DB']->query_delete('bookable_supplement_for', ['bookable_id' => $bookable_id]);
        foreach ($supplements as $supplement_id) {
            $GLOBALS['SITE_DB']->query_insert('bookable_supplement_for', [
                'supplement_id' => $supplement_id,
                'bookable_id' => $bookable_id,
            ]);
        }
    }

    log_it('EDIT_BOOKABLE', strval($bookable_id), $title);
}

/**
 * Delete a bookable.
 *
 * @param  AUTO_LINK $bookable_id Bookable ID
 */
function delete_bookable(int $bookable_id)
{
    if ($GLOBALS['SITE_DB']->query_select_value_if_there('booking', 'id', ['bookable_id' => $bookable_id]) !== null) {
        warn_exit(do_lang_tempcode('CANNOT_DELETE_BOOKINGS_EXIST'));
    }

    $_old_bookable = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], ['id' => $bookable_id], '', 1);
    if (!array_key_exists(0, $_old_bookable)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $title = get_translated_text($_old_bookable[0]['title']);

    delete_lang($_old_bookable[0]['title']);
    delete_lang($_old_bookable[0]['the_description']);
    delete_lang($_old_bookable[0]['categorisation']);

    $calendar_type = $_old_bookable[0]['calendar_type'];
    if (($calendar_type === null) && ($GLOBALS['SITE_DB']->query_select_value_if_there('calendar_types', 'id', ['id' => $calendar_type]) === null) && ($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)', ['e_type' => $calendar_type]) == 0)) {
        require_code('calendar2');
        delete_event_type($calendar_type);
    }

    $GLOBALS['SITE_DB']->query_delete('bookable', ['id' => $bookable_id], '', 1);

    $GLOBALS['SITE_DB']->query_delete('bookable_blacked_for', ['bookable_id' => $bookable_id]);

    $GLOBALS['SITE_DB']->query_delete('bookable_codes', ['bookable_id' => $bookable_id]);

    $GLOBALS['SITE_DB']->query_delete('bookable_supplement_for', ['bookable_id' => $bookable_id]);

    log_it('DELETE_BOOKABLE', strval($bookable_id), $title);
}

/**
 * Add a bookable supplement.
 *
 * @param  array $details Supplement details
 * @param  array $bookables List of bookables to associate to
 * @return AUTO_LINK Supplement ID
 */
function add_bookable_supplement(array $details, array $bookables = []) : int
{
    /*require_code('global4');   $title is not actually unique enough to do this
    prevent_double_submit('ADD_BOOKABLE_SUPPLEMENT', null, $title);*/

    $title = $details['title'];

    $details = insert_lang_comcode('title', $details['title'], 1) + $details;

    $supplement_id = $GLOBALS['SITE_DB']->query_insert('bookable_supplement', $details, true);

    foreach ($bookables as $bookable_id) {
        $GLOBALS['SITE_DB']->query_insert('bookable_supplement_for', [
            'supplement_id' => $supplement_id,
            'bookable_id' => $bookable_id,
        ]);
    }

    log_it('ADD_BOOKABLE_SUPPLEMENT', strval($supplement_id), $title);

    return $supplement_id;
}

/**
 * Edit a bookable supplement.
 *
 * @param  AUTO_LINK $supplement_id Supplement ID
 * @param  array $details Supplement details
 * @param  array $bookables List of bookables to associate to
 */
function edit_bookable_supplement(int $supplement_id, array $details, array $bookables = [])
{
    $title = $details['title'];

    $_old_supplement = $GLOBALS['SITE_DB']->query_select('bookable_supplement', ['*'], ['id' => $supplement_id], '', 1);
    if (!array_key_exists(0, $_old_supplement)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $_old_supplement[0] += lang_remap('title', $_old_supplement[0]['title'], $details['title']);
    unset($details['title']);

    $GLOBALS['SITE_DB']->query_update('bookable_supplement', $details, ['id' => $supplement_id], '', 1);

    if ($bookables !== null) {
        $GLOBALS['SITE_DB']->query_delete('bookable_supplement_for', ['supplement_id' => $supplement_id]);
        foreach ($bookables as $bookable_id) {
            $GLOBALS['SITE_DB']->query_insert('bookable_supplement_for', [
                'supplement_id' => $supplement_id,
                'bookable_id' => $bookable_id,
            ]);
        }
    }

    log_it('EDIT_BOOKABLE_SUPPLEMENT', strval($supplement_id), $title);
}

/**
 * Delete a bookable supplement.
 *
 * @param  AUTO_LINK $supplement_id Supplement ID
 */
function delete_bookable_supplement(int $supplement_id)
{
    $_old_supplement = $GLOBALS['SITE_DB']->query_select('bookable_supplement', ['*'], ['id' => $supplement_id], '', 1);
    if (!array_key_exists(0, $_old_supplement)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $title = get_translated_text($_old_supplement[0]['title']);

    delete_lang($_old_supplement[0]['title']);

    $GLOBALS['SITE_DB']->query_delete('bookable_supplement', ['id' => $supplement_id], '', 1);

    $GLOBALS['SITE_DB']->query_delete('bookable_supplement_for', ['supplement_id' => $supplement_id]);

    log_it('DELETE_BOOKABLE_SUPPLEMENT', strval($supplement_id), $title);
}

/**
 * Add a bookable blacked.
 *
 * @param  array $details Blacked details
 * @param  array $bookables List of bookables to associate to
 * @return AUTO_LINK Blacked ID
 */
function add_bookable_blacked(array $details, array $bookables = []) : int
{
    $blacked_explanation = $details['blacked_explanation'];

    $details = insert_lang_comcode('blacked_explanation', $details['blacked_explanation'], 1) + $details;

    $blacked_id = $GLOBALS['SITE_DB']->query_insert('bookable_blacked', $details, true);

    foreach ($bookables as $bookable_id) {
        $GLOBALS['SITE_DB']->query_insert('bookable_blacked_for', [
            'blacked_id' => $blacked_id,
            'bookable_id' => $bookable_id,
        ]);
    }

    log_it('ADD_BOOKABLE_BLACKED', strval($blacked_id), $blacked_explanation);

    return $blacked_id;
}

/**
 * Edit a bookable blacked.
 *
 * @param  AUTO_LINK $blacked_id Blacked ID
 * @param  array $details Blacked details
 * @param  ?array $bookables List of bookables to associate to (null: no change)
 */
function edit_bookable_blacked(int $blacked_id, array $details, ?array $bookables = null)
{
    $blacked_explanation = $details['blacked_explanation'];

    $_old_blacked = $GLOBALS['SITE_DB']->query_select('bookable_blacked', ['*'], ['id' => $blacked_id], '', 1);
    if (!array_key_exists(0, $_old_blacked)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $_old_blacked[0] += lang_remap_comcode('blacked_explanation', $_old_blacked[0]['blacked_explanation'], $details['blacked_explanation']);
    unset($details['blacked_explanation']);

    $GLOBALS['SITE_DB']->query_update('bookable_blacked', $details, ['id' => $blacked_id], '', 1);

    if ($bookables !== null) {
        $GLOBALS['SITE_DB']->query_delete('bookable_blacked_for', ['blacked_id' => $blacked_id]);
        foreach ($bookables as $bookable_id) {
            $GLOBALS['SITE_DB']->query_insert('bookable_blacked_for', [
                'blacked_id' => $blacked_id,
                'bookable_id' => $bookable_id,
            ]);
        }
    }

    log_it('EDIT_BOOKABLE_BLACKED', strval($blacked_id), $blacked_explanation);
}

/**
 * Delete a bookable blacked.
 *
 * @param  AUTO_LINK $blacked_id Blacked ID
 */
function delete_bookable_blacked(int $blacked_id)
{
    $_old_blacked = $GLOBALS['SITE_DB']->query_select('bookable_blacked', ['*'], ['id' => $blacked_id], '', 1);
    if (!array_key_exists(0, $_old_blacked)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }

    $blacked_explanation = get_translated_text($_old_blacked[0]['blacked_explanation']);

    delete_lang($_old_blacked[0]['blacked_explanation']);

    $GLOBALS['SITE_DB']->query_delete('bookable_blacked', ['id' => $blacked_id], '', 1);

    $GLOBALS['SITE_DB']->query_delete('bookable_blacked_for', ['blacked_id' => $blacked_id]);

    log_it('DELETE_BOOKABLE_BLACKED', strval($blacked_id), $blacked_explanation);
}
