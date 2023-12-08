<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

/**
 * Module page class.
 */
class Module_booking
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable');
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable_blacked');
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable_blacked_for');
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable_codes');
        $GLOBALS['SITE_DB']->drop_table_if_exists('booking');
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable_supplement');
        $GLOBALS['SITE_DB']->drop_table_if_exists('bookable_supplement_for');
        $GLOBALS['SITE_DB']->drop_table_if_exists('booking_supplement');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('bookable', array(
                'id' => '*AUTO',
                //'num_available' => 'INTEGER',      Implied by number of bookable_codes attached to bookable_id
                'title' => 'SHORT_TRANS__COMCODE',
                'description' => 'LONG_TRANS__COMCODE',
                'price' => 'REAL',
                'categorisation' => 'SHORT_TRANS__COMCODE', // (will work as a heading, for the booking form)
                'cycle_type' => 'ID_TEXT', // (same as event recurrences in the calendar addon; can be none [which would remove date chooser]) - a room cycles daily for example
                'cycle_pattern' => 'SHORT_TEXT',
                'user_may_choose_code' => 'BINARY',
                'supports_notes' => 'BINARY',
                'dates_are_ranges' => 'BINARY', // (if not, will only ask for a single date)
                'calendar_type' => '?AUTO_LINK', // (this is auto-added and synched on edits; type has no perms by default)
                'add_date' => 'TIME',
                'edit_date' => '?TIME',
                'submitter' => 'MEMBER',
                'sort_order' => 'INTEGER',

                'enabled' => 'BINARY',

                // (useful for defining seasonable bookables- e.g. summer bookable costing more, or with more rooms)
                'active_from_day' => 'SHORT_INTEGER',
                'active_from_month' => 'SHORT_INTEGER',
                'active_from_year' => 'INTEGER',
                'active_to_day' => '?SHORT_INTEGER',
                'active_to_month' => '?SHORT_INTEGER',
                'active_to_year' => '?INTEGER',
            ));

            $GLOBALS['SITE_DB']->create_table('bookable_blacked', array(
                'id' => '*AUTO',
                'blacked_from_day' => 'SHORT_INTEGER',
                'blacked_from_month' => 'SHORT_INTEGER',
                'blacked_from_year' => 'INTEGER',
                'blacked_to_day' => 'SHORT_INTEGER',
                'blacked_to_month' => 'SHORT_INTEGER',
                'blacked_to_year' => 'INTEGER',
                'blacked_explanation' => 'LONG_TRANS__COMCODE',
            ));

            $GLOBALS['SITE_DB']->create_table('bookable_blacked_for', array(
                'bookable_id' => '*AUTO_LINK',
                'blacked_id' => '*AUTO_LINK',
            ));

            $GLOBALS['SITE_DB']->create_table('bookable_codes', array(
                'bookable_id' => '*AUTO_LINK',
                'code' => '*ID_TEXT', // (room numbers, seats, etc) ; can be auto-generated if requested
            ));

            $GLOBALS['SITE_DB']->create_table('bookable_supplement', array(
                'id' => '*AUTO',
                'price' => 'REAL',
                'price_is_per_period' => 'BINARY', // If this is the case, the supplement will actually be repeated out in separate records, each tied to a booking for that date
                'supports_quantities' => 'BINARY',
                'title' => 'SHORT_TRANS__COMCODE',
                'promo_code' => 'ID_TEXT', // If non-blank, the user must enter this promo-code to purchase this
                'supports_notes' => 'BINARY',
                'sort_order' => 'INTEGER',
            ));

            $GLOBALS['SITE_DB']->create_table('bookable_supplement_for', array(
                'supplement_id' => '*AUTO_LINK',
                'bookable_id' => '*AUTO_LINK',
            ));

            $GLOBALS['SITE_DB']->create_table('booking', array(
                'id' => '*AUTO',
                'bookable_id' => 'AUTO_LINK',
                'member_id' => 'MEMBER',
                'b_day' => 'SHORT_INTEGER',
                'b_month' => 'SHORT_INTEGER',
                'b_year' => 'INTEGER',
                'code_allocation' => 'ID_TEXT', // These code allocations will be given out arbitrarily, which means later on if things get busy, things could be suboptimal (e.g. people's 'stay' split across different codes on different dates, while reorganising could solve that). So a human would probably reorganise this manually in some cases, and it should not be considered a real-world guarantee, or a necessary thing to make sure people get a full run-length on a single code
                'notes' => 'LONG_TEXT',
                'booked_at' => 'TIME', // time booking was made
                'paid_at' => '?TIME',
                'paid_trans_id' => '?AUTO_LINK',
                'customer_name' => 'SHORT_TEXT',
                'customer_email' => 'SHORT_TEXT',
                'customer_mobile' => 'SHORT_TEXT',
                'customer_phone' => 'SHORT_TEXT',
            ));

            $GLOBALS['SITE_DB']->create_table('booking_supplement', array(
                'booking_id' => '*AUTO_LINK',
                'supplement_id' => '*AUTO_LINK',
                'quantity' => 'INTEGER',
                'notes' => 'LONG_TEXT',
            ));
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 2)) {
            $GLOBALS['SITE_DB']->add_table_field('booking', 'customer_name', 'SHORT_TEXT');
            $GLOBALS['SITE_DB']->add_table_field('booking', 'customer_email', 'SHORT_TEXT');
            $GLOBALS['SITE_DB']->add_table_field('booking', 'customer_mobile', 'SHORT_TEXT');
            $GLOBALS['SITE_DB']->add_table_field('booking', 'customer_phone', 'SHORT_TEXT');
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('CREATE_BOOKING', 'menu/book'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('booking');

        if ($type == 'browse') {
            $this->title = get_screen_title('CREATE_BOOKING');
        }

        if ($type == 'flesh_out') {
            $this->title = get_screen_title('CREATE_BOOKING');
        }

        if ($type == 'join_or_login') {
            $this->title = get_screen_title('CREATE_BOOKING');
        }

        if ($type == 'thanks') {
            $this->title = get_screen_title('CREATE_BOOKING');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        require_code('booking');
        require_code('cns_join');
        require_javascript('booking');

        $type = get_param_string('type', 'browse');

        if ((is_guest()) && (get_forum_type() != 'cns')) {
            access_denied('NOT_AS_GUEST');
        } elseif (is_guest()) {
            check_joining_allowed();
        }

        // Decide what to do
        if ($type == 'browse') {
            return $this->choose_bookables_and_dates(); // NB: This may be skipped, if blocks were used to access
        }
        if ($type == 'flesh_out') {
            return $this->flesh_out(); // Finish full details for the booking
        }
        if ($type == 'account') {
            return $this->join_or_login(); // NB: This may be skipped if user already logged in
        }
        if ($type == 'done') {
            return $this->thanks();
        }

        return new Tempcode();
    }

    /**
     * Allow the user to choose what to book, on a high level - what bookables, what dates.
     *
     * @return Tempcode The result of execution.
     */
    public function choose_bookables_and_dates()
    {
        $query = 'SELECT * FROM ' . get_table_prefix() . 'bookable WHERE enabled=1';
        $filter = get_param_string('filter', '*');
        require_code('selectcode');
        $query .= ' AND ' . selectcode_to_sqlfragment($filter, 'id');
        $bookables = $GLOBALS['SITE_DB']->query($query . ' ORDER BY sort_order', null, null, false, true);

        $has_date_ranges = false;
        $has_single_dates = false;
        $has_details = false;

        $min_min_date = time();
        $max_max_date = mixed();

        $date_from = time();
        $date_to = time();

        $categories = array();
        foreach ($bookables as $bookable) {
            $active_from = mktime(0, 0, 0, $bookable['active_from_month'], $bookable['active_from_day'], $bookable['active_from_year']);
            $active_to = mixed();
            $active_to = is_null($bookable['active_to_year']) ? null : mktime(0, 0, 0, $bookable['active_to_month'], $bookable['active_to_day'], $bookable['active_to_year']);
            $min_date = $active_from;
            $max_date = $active_to;

            if ($min_date < $min_min_date) {
                $min_min_date = $min_date;
            }
            if ((is_null($max_max_date)) || (!is_null($active_to))) {
                if ($max_date > $max_max_date) {
                    $max_max_date = $max_date;
                }
            }

            if ($bookable['dates_are_ranges'] == 1) {
                $has_date_ranges = true;
            } elseif ($bookable['dates_are_ranges'] == 0) {
                $has_single_ranges = true;
            }

            $messages = array();

            // Message if not currently active
            if ($active_from > time()) {
                $messages[] = do_lang_tempcode('NOTE_BOOKING_IMPOSSIBLE_NOT_STARTED', get_timezoned_date($active_from, false, true, false, true));
            }

            // Message if becomes inactive within next 6 months
            if ((!is_null($active_to)) && ($active_to < SHOW_WARNINGS_UNTIL)) {
                $messages[] = do_lang_tempcode('NOTE_BOOKING_IMPOSSIBLE_ENDED', get_timezoned_date($active_to, false, true, false, true));
            }

            // Message about any black-outs within next 6 months
            $blacked = $GLOBALS['SITE_DB']->query_select(
                'bookable_blacked b JOIN ' . get_table_prefix() . 'bookable_blacked_for f ON f.blacked_id=b.id',
                array('*'),
                array(
                    'bookable_id' => $bookable['id'],
                ),
                'ORDER BY id'
            );
            foreach ($blacked as $black) {
                $black_from = mktime(0, 0, 0, $black['blacked_from_month'], $black['blacked_from_day'], $black['blacked_from_year']);
                $black_to = mixed();
                $black_to = is_null($black['blacked_to_year']) ? null : mktime(0, 0, 0, $black['blacked_to_month'], $black['blacked_to_day'], $black['blacked_to_year']);
                if (($black_from > time()) && ($black_to < SHOW_WARNINGS_UNTIL)) {
                    $messages[] = do_lang_tempcode(
                        ($black_from == $black_to) ? 'NOTE_BOOKING_IMPOSSIBLE_BLACKED_ONEOFF' : 'NOTE_BOOKING_IMPOSSIBLE_BLACKED_PERIOD',
                        get_timezoned_date($black_from, false, true, false, true),
                        get_timezoned_date($black_to, false, true, false, true),
                        get_translated_tempcode('bookable_blacked', $black, 'blacked_explanation')
                    );
                }
            }

            $category = get_translated_text($bookable['categorisation']);

            if (!array_key_exists($category, $categories)) {
                $categories[$category] = array('CATEGORY_TITLE' => $category, 'BOOKABLES' => array());
            }

            $quantity_available = $GLOBALS['SITE_DB']->query_select_value('bookable_codes', 'COUNT(*)', array('bookable_id' => $bookable['id']));

            list($quantity, $date_from, $date_to) = $this->_read_chosen_bookable_settings($bookable);

            if (is_null($max_max_date)) {
                $max_max_date = MAX_AHEAD_BOOKING_DATE;
            }

            $description = get_translated_tempcode('bookable', $bookable, 'description');

            if ((!$description->is_empty()) || (count($messages) > 0)) {
                $has_details = true;
            }

            $categories[$category]['BOOKABLES'][] = array(
                'BOOKABLE_ID' => strval($bookable['id']),
                'BOOKABLE_QUANTITY_AVAILABLE' => strval($quantity_available),
                'BOOKABLE_MESSAGES' => $messages,
                'BOOKABLE_TITLE' => get_translated_tempcode('bookable', $bookable, 'title'),
                'BOOKABLE_DESCRIPTION' => $description,
                'BOOKABLE_PRICE' => float_format($bookable['price']),
                'BOOKABLE_PRICE_RAW' => float_to_raw_string($bookable['price']),

                'BOOKABLE_SELECT_DATE_RANGE' => $bookable['dates_are_ranges'] == 1,
                'BOOKABLE_MIN_DATE_DAY' => date('d', $min_date),
                'BOOKABLE_MIN_DATE_MONTH' => date('m', $min_date),
                'BOOKABLE_MIN_DATE_YEAR' => date('Y', $min_date),
                'BOOKABLE_MAX_DATE_DAY' => date('d', $max_date),
                'BOOKABLE_MAX_DATE_MONTH' => date('m', $max_date),
                'BOOKABLE_MAX_DATE_YEAR' => date('Y', $max_date),

                // For re-entrancy
                'BOOKABLE_QUANTITY' => strval($quantity),
                'BOOKABLE_DATE_FROM_DAY' => date('d', $date_from),
                'BOOKABLE_DATE_FROM_MONTH' => date('m', $date_from),
                'BOOKABLE_DATE_FROM_YEAR' => date('Y', $date_from),
                'BOOKABLE_DATE_TO_DAY' => date('d', $date_to),
                'BOOKABLE_DATE_TO_MONTH' => date('m', $date_to),
                'BOOKABLE_DATE_TO_YEAR' => date('Y', $date_to),
            );
            //Wrong - we're sorting by sort_order  sort_maps_by($categories[$category]['BOOKABLES'], 'BOOKABLE_TITLE');
        }

        ksort($categories);

        // Messages shared by all bookables will be transferred so as to avoid repetition
        $shared_messages = array();
        $done_one = false;
        foreach ($categories as $category_title => $bookables) {
            foreach ($bookables['BOOKABLES'] as $i => $bookable) {
                foreach ($bookable['BOOKABLE_MESSAGES'] as $j => $message) {
                    if (!$done_one) { // Ah, may be in all
                        $in_all = true;
                        foreach ($categories as $_category_title => $_bookables) {
                            foreach ($bookables['BOOKABLES'] as $_i => $_bookable) {
                                if (!in_array($message, $_bookable['BOOKABLE_MESSAGES'])) {
                                    $in_all = false;
                                    break 2;
                                }
                            }
                        }
                        if ($in_all) {
                            $shared_messages[] = $message;
                        }
                        $done_one = true;
                    }
                    if (in_array($message, $shared_messages)) { // Known to be in all
                        unset($categories[$category_title]['BOOKABLES'][$i]['BOOKABLE_MESSAGES'][$j]);
                    }
                }
            }
        }

        return do_template('BOOKING_START_SCREEN', array(
            '_GUID' => '12787a01e3408b56f61f4b41cefa1325',
            'TITLE' => $this->title,
            'CATEGORIES' => $categories,
            'POST_URL' => build_url(array('page' => '_SELF', 'type' => 'flesh_out', 'usergroup' => get_param_integer('usergroup', null)), '_SELF'),
            'SHARED_MESSAGES' => $shared_messages,
            'HAS_DATE_RANGES' => $has_date_ranges,
            'HAS_SINGLE_DATES' => $has_single_dates,
            'HAS_DETAILS' => $has_details,
            'HAS_MIXED_DATE_TYPES' => $has_single_dates && $has_date_ranges,
            'MIN_DATE_DAY' => date('d', $min_min_date),
            'MIN_DATE_MONTH' => date('m', $min_min_date),
            'MIN_DATE_YEAR' => date('Y', $min_min_date),
            'MAX_DATE_DAY' => date('d', $max_max_date),
            'MAX_DATE_MONTH' => date('m', $max_max_date),
            'MAX_DATE_YEAR' => date('Y', $max_max_date),
            'DATE_FROM_DAY' => date('d', $date_from),
            'DATE_FROM_MONTH' => date('m', $date_from),
            'DATE_FROM_YEAR' => date('Y', $date_from),
            'DATE_TO_DAY' => date('d', $date_to),
            'DATE_TO_MONTH' => date('m', $date_to),
            'DATE_TO_YEAR' => date('Y', $date_to),
            'HIDDEN' => build_keep_post_fields(),
        ));
    }

    /**
     * Read settings the user has chosen, from the POST environment.
     *
     * @param  array $bookable Details of the particular bookable.
     * @return array Tuple of details: number wanted, date from, date to).
     */
    public function _read_chosen_bookable_settings($bookable)
    {
        $quantity = post_param_integer('bookable_' . strval($bookable['id']) . '_quantity', 0);
        $date_from = post_param_date('bookable_' . strval($bookable['id']) . '_date_from');
        if (is_null($date_from)) {
            $date_from = post_param_date('bookable_date_from'); // allow to be specified for whole form (the norm actually)
        }
        if (is_null($date_from)) {
            $date_from = time();
        }
        $date_to = post_param_date('bookable_' . strval($bookable['id']) . '_date_to');
        if (is_null($date_to)) {
            $date_to = post_param_date('bookable_date_to'); // allow to be specified for whole form (the norm actually); may still be null, if ranges not being used
        }
        if (is_null($date_to)) {
            $date_to = $date_from;
        }

        return array($quantity, $date_from, $date_to);
    }

    /**
     * Flesh out the details of a booking.
     *
     * @return Tempcode The result of execution.
     */
    public function flesh_out()
    {
        // Check booking: redirect to last step as re-entrant if not valid
        $request = get_booking_request_from_form();
        $test = check_booking_dates_available($request, array());
        if (!is_null($test)) {
            attach_message($test, 'warn');
            return $this->choose_bookables_and_dates();
        }

        $bookables = array();

        $found = false;

        $bookable_rows = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), null, 'ORDER BY sort_order');
        foreach ($bookable_rows as $bookable_row) {
            if (post_param_integer('bookable_' . strval($bookable_row['id']) . '_quantity', 0) > 0) {
                $found = true;

                $supplements = array();
                $supplement_rows = $GLOBALS['SITE_DB']->query_select('bookable_supplement a JOIN ' . get_table_prefix() . 'bookable_supplement_for b ON a.id=b.supplement_id', array('a.*'), array('bookable_id' => $bookable_row['id']), 'ORDER BY sort_order');
                foreach ($supplement_rows as $supplement_row) {
                    $supplements[] = array(
                        'SUPPLEMENT_ID' => strval($supplement_row['id']),
                        'SUPPLEMENT_TITLE' => get_translated_tempcode('bookable_supplement', $supplement_row, 'title'),
                        'SUPPLEMENT_SUPPORTS_QUANTITY' => $supplement_row['supports_quantities'] == 1,
                        'SUPPLEMENT_QUANTITY' => strval(post_param_integer('bookable_' . strval($bookable_row['id']) . '_supplement_' . strval($supplement_row['id']) . '_quantity', 0)),
                        'SUPPLEMENT_SUPPORTS_NOTES' => $supplement_row['supports_notes'] == 1,
                        'SUPPLEMENT_NOTES' => post_param_string('bookable_' . strval($bookable_row['id']) . '_supplement_' . strval($supplement_row['id']) . '_notes', ''),
                    );
                }

                $bookables[] = array(
                    'BOOKABLE_ID' => strval($bookable_row['id']),
                    'BOOKABLE_TITLE' => get_translated_tempcode('bookable', $bookable_row, 'title'),
                    'BOOKABLE_SUPPORTS_NOTES' => $bookable_row['supports_notes'] == 1,
                    'BOOKABLE_NOTES' => post_param_string('bookable_' . strval($bookable_row['id']) . '_notes', ''),
                    'BOOKABLE_SUPPLEMENTS' => $supplements,

                    'BOOKABLE_QUANTITY' => strval(post_param_integer('bookable_' . strval($bookable_row['id']) . '_quantity')), // Can select up to this many supplements
                );
            }
        }

        if (!$found) {
            warn_exit(do_lang_tempcode('BOOK_QUANTITY_NOTHING_CHOSEN'));
        }

        require_javascript('ajax');
        require_javascript('checking');

        return do_template('BOOKING_FLESH_OUT_SCREEN', array(
            '_GUID' => '255280fa4f9bb37e3dae76f5bca46ace',
            'TITLE' => $this->title,
            'BOOKABLES' => $bookables,
            'PRICE' => float_format(find_booking_price($request)),
            'POST_URL' => build_url(array('page' => '_SELF', 'type' => 'account', 'usergroup' => get_param_integer('usergroup', null)), '_SELF'),
            'BACK_URL' => build_url(array('page' => '_SELF', 'type' => 'browse', 'usergroup' => get_param_integer('usergroup', null)), '_SELF'),
            'HIDDEN' => build_keep_post_fields(),
        ));
    }

    /**
     * Let the user login / do an inline join.
     *
     * @return Tempcode The result of execution.
     */
    public function join_or_login()
    {
        // Check login: skip to thanks if logged in
        if (get_option('member_booking_only') == '1') {
            if (!is_guest()) {
                return $this->thanks();
            }
        }

        $url = build_url(array('page' => '_SELF', 'type' => 'done'), '_SELF');

        $hidden = build_keep_post_fields();

        // Booking not related to members
        if (get_option('member_booking_only') == '0') {
            require_code('form_templates');
            $fields = new Tempcode();
            $fields->attach(form_input_line(do_lang_tempcode('YOUR_NAME'), '', 'customer_name', $GLOBALS['FORUM_DRIVER']->get_username(get_member()), true));
            $fields->attach(form_input_email(do_lang_tempcode('YOUR_EMAIL_ADDRESS'), '', 'customer_email', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()), true));
            $fields->attach(form_input_email(do_lang_tempcode('ALT_FIELD', do_lang_tempcode('CONFIRM_EMAIL_ADDRESS')), '', 'customer_email_confirm', $GLOBALS['FORUM_DRIVER']->get_member_email_address(get_member()), true));
            $fields->attach(form_input_line(do_lang_tempcode('YOUR_MOBILE_NUMBER'), '', 'customer_mobile', '', false));
            $fields->attach(form_input_line(do_lang_tempcode('YOUR_PHONE_NUMBER'), '', 'customer_phone', '', true));
            $submit_name = do_lang_tempcode('BOOK');
            $form = do_template('FORM', array('_GUID' => '18e831a00ac918b06c7f761c7d7d5fb0', 'TEXT' => do_lang_tempcode('A_FEW_DETAILS'), 'HIDDEN' => $hidden, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => $submit_name, 'URL' => $url));
            $javascript = '';
        } else {
            // Integrated signup
            list($javascript, $form) = cns_join_form($url, true, false, false, false);
        }

        return do_template('BOOKING_JOIN_OR_LOGIN_SCREEN', array('_GUID' => 'b6e499588de8e2136122949478bac2e7', 'TITLE' => $this->title, 'JAVASCRIPT' => $javascript, 'FORM' => $form, 'HIDDEN' => $hidden));
    }

    /**
     * E-mails staff and receipt notice to user, and saves everything.
     *
     * @return Tempcode The result of execution.
     */
    public function thanks()
    {
        // Finish join operation, if applicable
        if ((is_guest()) && (get_option('member_booking_only') == '1')) {
            list($messages) = cns_join_actual(true, false, false, true, false, false, false, true);
            if (!$messages->is_empty()) {
                return inform_screen($this->title, $messages);
            }
        }

        // Read request
        $request = get_booking_request_from_form();
        if (!array_key_exists(0, $request)) {
            warn_exit(do_lang_tempcode('NOTHING_IN_BOOKING'));
        }

        // Save
        $test = save_booking_form_to_db($request, array());
        if (is_null($test)) {
            warn_exit(do_lang_tempcode('BOOKING_ERROR'));
        }

        // Send emails
        send_booking_emails($request);

        // Show success
        $customer_name = post_param_string('customer_name', is_guest() ? '' : $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true));
        return inform_screen($this->title, do_lang_tempcode('BOOKING_SUCCESS', escape_html($customer_name)));
    }
}

/*

NOTE (for future expansion, not critical base functionality)...

Implement online payment support; will need nice way to remove unpaid bookings in future too

Promo codes

Implement permissions for bookables

Manual choice of codes (seats or whatever)

Implement support for the defined cycle patterns (currently just daily or none)

What if we want to run on a reduced capacity for a period?

Implement blocks

*/
