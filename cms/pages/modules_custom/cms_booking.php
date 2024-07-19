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

require_code('crud_module');

/**
 * Module page class.
 */
class Module_cms_booking extends Standard_crud_module
{
    protected $lang_type = 'BOOKABLE';
    protected $select_name = 'TITLE';
    protected $code_require = 'booking';
    protected $permissions_require = 'cat_high';
    protected $user_facing = false;
    protected $menu_label = 'BOOKINGS';
    protected $orderer = 'sort_order';
    protected $table = 'bookable';
    protected $bookings_crud_module;

    protected $donext_type = null;

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('booking')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        $ret = [
            'browse' => ['BOOKINGS', 'booking/booking'],
        ];

        if (has_privilege($member_id, 'submit_highrange_content', 'cms_booking')) {
            $ret += [
                'add_booking' => ['ADD_BOOKING', 'booking/booking'],
            ];
        }

        if (has_privilege($member_id, 'edit_highrange_content', 'cms_booking')) {
            $ret += [
                'edit_booking' => ['EDIT_BOOKING', 'booking/booking'],
            ];
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'add' => ['ADD_BOOKABLE', 'booking/bookable'],
            ];
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'edit' => ['EDIT_BOOKABLE', 'booking/bookable'],
            ];
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'add_other' => ['ADD_BOOKABLE_SUPPLEMENT', 'booking/supplement'],
            ];
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'edit_other' => ['EDIT_BOOKABLE_SUPPLEMENT', 'booking/supplement'],
            ];
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'add_category' => ['ADD_BOOKABLE_BLACKED', 'booking/blacked'],
            ];
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += [
                'edit_category' => ['EDIT_BOOKABLE_BLACKED', 'booking/blacked'],
            ];
        }

        $ret += parent::get_entry_points();

        return $ret;
    }

    /**
     * Find privileges defined as overridable by this module.
     *
     * @return array A map of privileges that are overridable; privilege to 0 or 1. 0 means "not category overridable". 1 means "category overridable".
     */
    public function get_privilege_overrides() : array
    {
        require_lang('booking');
        return ['submit_cat_highrange_content' => [0, 'ADD_BOOKABLE'], 'edit_cat_highrange_content' => [0, 'EDIT_BOOKABLE'], 'delete_cat_highrange_content' => [0, 'DELETE_BOOKABLE']];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment)
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run(bool $top_level = true, ?string $type = null) : ?object
    {
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('booking', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('calendar')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('calendar')));
        }
        if (!addon_installed('ecommerce')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
        }

        if ($top_level) {
            $this->cat_crud_module = class_exists('Mx_cms_booking_blacks') ? new Mx_cms_booking_blacks() : new Module_cms_booking_blacks(); // Blacks
            $this->alt_crud_module = class_exists('Mx_cms_booking_supplements') ? new Mx_cms_booking_supplements() : new Module_cms_booking_supplements(); // Supplements
            $this->bookings_crud_module = class_exists('Mx_cms_booking_bookings') ? new Mx_cms_booking_bookings() : new Module_cms_booking_bookings(); // Bookings
        }

        require_lang('booking');

        if ($type === null) {
            $type = get_param_string('type', 'browse');

            // Type equivalencies, for metadata purposes (i.e. activate correct title-generation code)
            if ($type == 'add_booking') {
                $type = 'add';
            }
            if ($type == '_add_booking') {
                $type = '_add';
            }
            if ($type == 'edit_booking') {
                $type = 'edit';
            }
            if ($type == '_edit_booking') {
                $type = '_edit';
            }
            if ($type == '__edit_booking') {
                $type = '__edit';
            }
            $this->bookings_crud_module->pre_run(false, $type);
        }

        return parent::pre_run($top_level);
    }

    /**
     * Standard crud_module run_start.
     *
     * @param  ID_TEXT $type The type of module execution
     * @return Tempcode The output of the run
     */
    public function run_start(string $type) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_code('booking2');

        if ($type == 'browse') {
            return $this->browse();
        }
        if ($type == 'add_booking') {
            return $this->bookings_crud_module->add();
        }
        if ($type == '_add_booking') {
            return $this->bookings_crud_module->_add();
        }
        if ($type == 'edit_booking') {
            return $this->bookings_crud_module->edit();
        }
        if ($type == '_edit_booking') {
            return $this->bookings_crud_module->_edit();
        }
        if ($type == '__edit_booking') {
            return $this->bookings_crud_module->__edit();
        }

        return new Tempcode();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        return booking_do_next();
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen
     * @return array A quartet: The choose table, Whether re-ordering is supported from this screen, Search URL, Archive URL
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'sort_order ASC', INPUT_FILTER_GET_COMPLEX);
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = [
            'title' => do_lang_tempcode('TITLE'),
            'categorisation' => do_lang_tempcode('BOOKABLE_CATEGORISATION'),
            'price' => do_lang_tempcode('PRICE'),
            'sort_order' => do_lang_tempcode('SORT_ORDER'),
            'enabled' => do_lang_tempcode('ENABLED'),
        ];
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = [];
        $fh[] = do_lang_tempcode('TITLE');
        $fh[] = do_lang_tempcode('BOOKABLE_CATEGORISATION');
        $fh[] = do_lang_tempcode('PRICE');
        $fh[] = do_lang_tempcode('BOOKABLE_ACTIVE_FROM');
        $fh[] = do_lang_tempcode('BOOKABLE_ACTIVE_TO');
        $fh[] = do_lang_tempcode('ENABLED');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_header_row($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $result_entries = new Tempcode();

        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_url = build_url($url_map + ['id' => $row['id']], '_SELF');

            $_row = db_map_restrict($row, ['id', 'title', 'categorisation']);

            $fr = [];
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable', $_row, 'title'));
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable', $_row, 'categorisation'));
            $fr[] = float_format($row['price']);
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['active_from_month'], $row['active_from_day'], $row['active_from_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['active_to_month'], $row['active_to_day'], $row['active_to_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            $fr[] = ($row['enabled'] == 1) ? do_lang_tempcode('YES') : do_lang_tempcode('NO');
            $fr[] = protect_from_escaping(hyperlink($edit_url, do_lang_tempcode('EDIT'), false, true));

            $result_entries->attach(results_entry($fr, true));
        }

        return [results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order), false];
    }

    /**
     * Get Tempcode for an adding form.
     *
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    /**
     * Get a form for entering a bookable.
     *
     * @param  ?array $details Details of the bookable (null: new)
     * @param  array $supplements List of supplements
     * @param  array $blacks List of blacks
     * @param  array $codes List of codes
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(?array $details = null, array $supplements = [], array $blacks = [], array $codes = []) : array
    {
        if ($details === null) {
            $max_sort_order = $GLOBALS['SITE_DB']->query_select_value('bookable', 'MAX(sort_order)');
            if ($max_sort_order === null) {
                $max_sort_order = 0;
            }

            $details = [
                'title' => null,
                'the_description' => null,
                'price' => 0.00,
                'categorisation' => null,
                'cycle_type' => '',
                'cycle_pattern' => '',
                'user_may_choose_code' => 0,
                'supports_notes' => 0,
                'dates_are_ranges' => 1,
                'sort_order' => $max_sort_order + 1,

                'enabled' => 1,

                'active_from_day' => intval(date('d')),
                'active_from_month' => intval(date('m')),
                'active_from_year' => intval(date('Y')),
                'active_to_day' => null,
                'active_to_month' => null,
                'active_to_year' => null,
            ];
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('cycle_type', $details['cycle_type']));
        $hidden->attach(form_input_hidden('cycle_pattern', $details['cycle_pattern']));
        $hidden->attach(form_input_hidden('user_may_choose_code', strval($details['user_may_choose_code'])));
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_line_comcode(do_lang_tempcode('TITLE'), do_lang_tempcode('DESCRIPTION_TITLE'), 'title', ($details['title'] === null) ? '' : get_translated_text($details['title']), true));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('DESCRIPTION'), do_lang_tempcode('DESCRIPTION_DESCRIPTION'), 'description', ($details['the_description'] === null) ? '' : get_translated_text($details['the_description']), false));
        $fields->attach(form_input_line(do_lang_tempcode('PRICE'), do_lang_tempcode('DESCRIPTION_BOOKABLE_PRICE'), 'price', float_to_raw_string($details['price'], 2), true));
        $categorisation = ($details['categorisation'] === null) ? '' : get_translated_text($details['categorisation']);
        if ($categorisation == '') {
            $_categorisation = $GLOBALS['SITE_DB']->query_select('bookable', ['categorisation', 'COUNT(*) AS cnt'], [], 'GROUP BY categorisation ORDER BY cnt DESC', 1);
            if (!empty($_categorisation)) {
                $categorisation = get_translated_text($_categorisation[0]['categorisation']);
            } else {
                $categorisation = do_lang('GENERAL');
            }
        }
        $fields->attach(form_input_line(do_lang_tempcode('BOOKABLE_CATEGORISATION'), do_lang_tempcode('DESCRIPTION_BOOKABLE_CATEGORISATION'), 'categorisation', $categorisation, true));
        //$fields->attach(form_input_select(do_lang_tempcode('CYCLE_TYPE'), do_lang_tempcode('DESCRIPTION_CYCLE_TYPE'), 'cycle_type', $details['cycle_type'], false));
        //$fields->attach(form_input_line(do_lang_tempcode('CYCLE_PATTERN'), do_lang_tempcode('DESCRIPTION_CYCLE_PATTERN'), 'cycle_pattern', $details['cycle_pattern'], false));
        //$fields->attach(form_input_tick(do_lang_tempcode('USER_MAY_CHOOSE_CODE'), do_lang_tempcode('DESCRIPTION_USER_MAY_CHOOSE_CODE'), 'user_may_choose_code', $details['user_may_choose_code']==1));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_NOTES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_NOTES'), 'supports_notes', $details['supports_notes'] == 1));
        $fields->attach(form_input_tick(do_lang_tempcode('BOOKABLE_DATES_ARE_RANGES'), do_lang_tempcode('DESCRIPTION_BOOKABLE_DATES_ARE_RANGES'), 'dates_are_ranges', $details['dates_are_ranges'] == 1));

        $fields->attach(form_input_text(do_lang_tempcode('BOOKABLE_CODES'), do_lang_tempcode('DESCRIPTION_BOOKABLE_CODES'), 'codes', implode("\n", $codes), true, false));

        $_supplements = new Tempcode();
        $all_supplements = $GLOBALS['SITE_DB']->query_select('bookable_supplement', ['id', 'title', 'sort_order'], [], 'ORDER BY sort_order');
        foreach ($all_supplements as $s) {
            $_supplements->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $supplements), get_translated_text($s['title'])));
        }
        if (!$_supplements->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('SUPPLEMENTS'), do_lang_tempcode('DESCRIPTION_BOOKABLE_SUPPLEMENTS'), 'supplements', $_supplements));
        }

        $_blacks = new Tempcode();
        $all_blacks = $GLOBALS['SITE_DB']->query_select('bookable_blacked', ['id', 'blacked_explanation', 'blacked_from_year', 'blacked_from_month', 'blacked_from_day'], [], 'ORDER BY blacked_from_year,blacked_from_month,blacked_from_day');
        foreach ($all_blacks as $s) {
            $_blacks->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $blacks), get_translated_text($s['blacked_explanation'])));
        }
        if (!$_blacks->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('BLACKOUTS'), do_lang_tempcode('DESCRIPTION_BOOKABLE_BLACKS'), 'blacks', $_blacks));
        }

        $fields->attach(form_input_date(do_lang_tempcode('BOOKABLE_ACTIVE_FROM'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ACTIVE_FROM'), 'active_from', true, false, false, [0, 0, $details['active_from_month'], $details['active_from_day'], $details['active_from_year']], 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_date(do_lang_tempcode('BOOKABLE_ACTIVE_TO'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ACTIVE_TO'), 'active_to', false, ($details['active_to_month'] === null), false, ($details['active_to_month'] === null) ? null : [0, 0, $details['active_to_month'], $details['active_to_day'], $details['active_to_year']], 10, null, null, true, get_server_timezone()));

        $fields->attach(form_input_integer(do_lang_tempcode('SORT_ORDER'), do_lang_tempcode('DESCRIPTION_SORT_ORDER'), 'sort_order', $details['sort_order'], true));

        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ENABLED'), 'enabled', $details['enabled'] == 1));

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], ['id' => intval($id)], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $supplements = collapse_1d_complexity('supplement_id', $GLOBALS['SITE_DB']->query_select('bookable_supplement_for', ['supplement_id'], ['bookable_id' => $id]));
        $blacks = collapse_1d_complexity('blacked_id', $GLOBALS['SITE_DB']->query_select('bookable_blacked_for', ['blacked_id'], ['bookable_id' => $id]));
        $codes = collapse_1d_complexity('code', $GLOBALS['SITE_DB']->query_select('bookable_codes', ['code'], ['bookable_id' => $id]));

        return $this->get_form_fields($myrow, $supplements, $blacks, $codes);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        list($bookable_details, $codes, $blacked, $supplements) = get_bookable_details_from_form();

        $id = add_bookable($bookable_details, $codes, $blacked, $supplements);

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        $id = intval($_id);

        list($bookable_details, $codes, $blacked, $supplements) = get_bookable_details_from_form();

        edit_bookable($id, $bookable_details, $codes, $blacked, $supplements);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        $id = intval($_id);

        delete_bookable($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  ?Tempcode $title The title (output of get_screen_title) (null: don't use full page)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?ID_TEXT $id The ID of whatever we are working with (null: deleted)
     * @return Tempcode The UI
     */
    public function do_next_manager(?object $title, object $description, ?string $id = null) : object
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_supplements extends Standard_crud_module
{
    protected $lang_type = 'BOOKABLE_SUPPLEMENT';
    protected $select_name = 'EXPLANATION';
    protected $code_require = 'booking';
    protected $permissions_require = 'cat_high';
    protected $user_facing = false;
    protected $menu_label = 'BOOKINGS';
    protected $orderer = 'sort_order';
    protected $table = 'bookable_supplement';

    protected $donext_type = null;

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen
     * @return array A quartet: The choose table, Whether re-ordering is supported from this screen, Search URL, Archive URL
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'sort_order ASC', INPUT_FILTER_GET_COMPLEX);
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = [
            'title' => do_lang_tempcode('TITLE'),
            'price' => do_lang_tempcode('PRICE'),
            'sort_order' => do_lang_tempcode('SORT_ORDER'),
        ];
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = [];
        $fh[] = do_lang_tempcode('TITLE');
        $fh[] = do_lang_tempcode('PRICE');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_header_row($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_url = build_url($url_map + ['id' => $row['id']], '_SELF');

            $_row = db_map_restrict($row, ['id', 'title']);

            $fr = [];
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable_supplement', $_row, 'title'));
            $fr[] = float_format($row['price']);
            $fr[] = protect_from_escaping(hyperlink($edit_url, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return [results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false];
    }

    /**
     * Get Tempcode for an adding form.
     *
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    /**
     * Get a form for entering a bookable supplement.
     *
     * @param  ?array $details Details of the supplement (null: new)
     * @param  array $bookables List of bookables this is for
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(?array $details = null, array $bookables = []) : array
    {
        if ($details === null) {
            $details = [
                'price' => 0.00,
                'price_is_per_period' => 0,
                'supports_quantities' => 0,
                'title' => null,
                'promo_code' => '',
                'supports_notes' => 0,
                'sort_order' => 1,
            ];

            $bookables = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('bookable', ['id']));
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('promo_code', $details['promo_code']));
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('TITLE'), do_lang_tempcode('DESCRIPTION_TITLE'), 'title', ($details['title'] === null) ? '' : get_translated_text($details['title']), true));
        $fields->attach(form_input_line(do_lang_tempcode('PRICE'), do_lang_tempcode('DESCRIPTION_SUPPLEMENT_PRICE'), 'price', float_to_raw_string($details['price'], 2), true));
        $fields->attach(form_input_tick(do_lang_tempcode('PRICE_IS_PER_PERIOD'), do_lang_tempcode('DESCRIPTION_PRICE_IS_PER_PERIOD'), 'price_is_per_period', $details['price_is_per_period'] == 1));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_QUANTITIES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_QUANTITIES'), 'supports_quantities', $details['supports_quantities'] == 1));
        //$fields->attach(form_input_line(do_lang_tempcode('PROMO_CODE'), do_lang_tempcode('DESCRIPTION_PROMO_CODE'), 'promo_code', $details['promo_code'], true));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_NOTES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_NOTES'), 'supports_notes', $details['supports_notes'] == 1));
        $fields->attach(form_input_integer(do_lang_tempcode('SORT_ORDER'), do_lang_tempcode('DESCRIPTION_SORT_ORDER'), 'sort_order', $details['sort_order'], true));

        $_bookables = new Tempcode();
        $all_bookables = $GLOBALS['SITE_DB']->query_select('bookable', ['id', 'title', 'sort_order'], [], 'ORDER BY sort_order');
        foreach ($all_bookables as $s) {
            $_bookables->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $bookables), get_translated_text($s['title'])));
        }
        if (!$_bookables->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('BOOKABLES'), do_lang_tempcode('DESCRIPTION_SUPPLEMENT_BOOKABLES'), 'bookables', $_bookables));
        }

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable_supplement', ['*'], ['id' => intval($id)], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $bookables = collapse_1d_complexity('bookable_id', $GLOBALS['SITE_DB']->query_select('bookable_supplement_for', ['bookable_id'], ['supplement_id' => $id]));

        return $this->get_form_fields($myrow, $bookables);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        list($details, $bookables) = get_bookable_supplement_details_from_form();

        $id = add_bookable_supplement($details, $bookables);

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        $id = intval($_id);

        list($details, $bookables) = get_bookable_supplement_details_from_form();

        edit_bookable_supplement($id, $details, $bookables);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        $id = intval($_id);

        delete_bookable_supplement($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  ?Tempcode $title The title (output of get_screen_title) (null: don't use full page)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?ID_TEXT $id The ID of whatever we are working with (null: deleted)
     * @return Tempcode The UI
     */
    public function do_next_manager(?object $title, object $description, ?string $id = null) : object
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_blacks extends Standard_crud_module
{
    protected $lang_type = 'BOOKABLE_BLACKED';
    protected $select_name = 'EXPLANATION';
    protected $code_require = 'booking';
    protected $permissions_require = 'cat_high';
    protected $user_facing = false;
    protected $menu_label = 'BOOKINGS';
    protected $orderer = 'id';
    protected $table = 'bookable_blacked';

    protected $donext_type = null;

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen
     * @return array A quartet: The choose table, Whether re-ordering is supported from this screen, Search URL, Archive URL
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'blacked_from_year,blacked_from_month,blacked_from_day ASC', INPUT_FILTER_GET_COMPLEX);
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = [
            'blacked_from_year,blacked_from_month,blacked_from_day' => do_lang_tempcode('DATE'),
        ];
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = [];
        $fh[] = do_lang_tempcode('FROM');
        $fh[] = do_lang_tempcode('TO');
        $fh[] = do_lang_tempcode('BLACKED_EXPLANATION');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_header_row($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_url = build_url($url_map + ['id' => $row['id']], '_SELF');

            $_row = db_map_restrict($row, ['id', 'blacked_explanation']);

            $fr = [];
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['blacked_from_month'], $row['blacked_from_day'], $row['blacked_from_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['blacked_to_month'], $row['blacked_to_day'], $row['blacked_to_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable_blacked', $_row, 'blacked_explanation'));
            $fr[] = protect_from_escaping(hyperlink($edit_url, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return [results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false];
    }

    /**
     * Get Tempcode for an adding form.
     *
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    /**
     * Get a form for entering a bookable black.
     *
     * @param  ?array $details Details of the black (null: new)
     * @param  array $bookables List of bookables this is for
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(?array $details = null, array $bookables = []) : array
    {
        if ($details === null) {
            $details = [
                'blacked_from_day' => intval(date('d')),
                'blacked_from_month' => intval(date('m')),
                'blacked_from_year' => intval(date('Y')),
                'blacked_to_day' => intval(date('d')),
                'blacked_to_month' => intval(date('m')),
                'blacked_to_year' => intval(date('Y')),
                'blacked_explanation' => null,
            ];

            $bookables = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('bookable', ['id']));
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_date(do_lang_tempcode('BLACKED_FROM'), do_lang_tempcode('DESCRIPTION_BLACKED_FROM'), 'blacked_from', true, false, false, [0, 0, $details['blacked_from_month'], $details['blacked_from_day'], $details['blacked_from_year']], 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_date(do_lang_tempcode('BLACKED_TO'), do_lang_tempcode('DESCRIPTION_BLACKED_TO'), 'blacked_to', true, false, false, [0, 0, $details['blacked_to_month'], $details['blacked_to_day'], $details['blacked_to_year']], 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_text(do_lang_tempcode('BLACKED_EXPLANATION'), do_lang_tempcode('DESCRIPTION_BLACKED_EXPLANATION'), 'blacked_explanation', ($details['blacked_explanation'] === null) ? '' : get_translated_text($details['blacked_explanation']), true, false));

        $_bookables = new Tempcode();
        $all_bookables = $GLOBALS['SITE_DB']->query_select('bookable', ['id', 'title', 'sort_order'], [], 'ORDER BY sort_order');
        foreach ($all_bookables as $s) {
            $_bookables->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $bookables), get_translated_text($s['title'])));
        }
        $fields->attach(form_input_multi_list(do_lang_tempcode('BOOKABLES'), do_lang_tempcode('DESCRIPTION_BLACKED_BOOKABLES'), 'bookables', $_bookables));

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable_blacked', ['*'], ['id' => intval($id)], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $bookables = collapse_1d_complexity('bookable_id', $GLOBALS['SITE_DB']->query_select('bookable_blacked_for', ['bookable_id'], ['blacked_id' => $id]));

        return $this->get_form_fields($myrow, $bookables);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        list($details, $bookables) = get_bookable_blacked_details_from_form();

        $id = add_bookable_blacked($details, $bookables);

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        $id = intval($_id);

        list($details, $bookables) = get_bookable_blacked_details_from_form();

        edit_bookable_blacked($id, $details, $bookables);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        $id = intval($_id);

        delete_bookable_blacked($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  ?Tempcode $title The title (output of get_screen_title) (null: don't use full page)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?ID_TEXT $id The ID of whatever we are working with (null: deleted)
     * @return Tempcode The UI
     */
    public function do_next_manager(?object $title, object $description, ?string $id = null) : object
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_bookings extends Standard_crud_module
{
    protected $lang_type = 'BOOKING';
    protected $select_name = 'MEMBER_ID';
    protected $code_require = 'booking';
    protected $permissions_require = 'high';
    protected $user_facing = false;
    protected $menu_label = 'BOOKINGS';
    protected $orderer = 'id';
    protected $table = 'booking';
    protected $type_code = 'booking';
    protected $non_integer_id = true;

    protected $donext_type = null;

    /**
     * Standard CRUD-module entry function to get rows for selection from.
     *
     * @param  boolean $recache Whether to force a re-cache
     * @param  ?ID_TEXT $orderer Order to use (null: automatic)
     * @param  ?array $where Extra where clauses
     * @param  boolean $force_site_db Whether to always access using the site database
     * @param  string $join Extra join clause for our query (blank: none)
     * @param  ?integer $max Maximum to show (null: standard)
     * @return array A pair: Rows for selection from, Total results
     */
    public function get_entry_rows(bool $recache = false, ?string $orderer = null, ?array $where = [], bool $force_site_db = false, string $join = '', ?int $max = null) : array
    {
        if ((!$recache) && ($orderer !== null) && (!empty($where))) {
            if (isset($this->cached_entry_rows)) {
                return [$this->cached_entry_rows, $this->cached_max_rows];
            }
        }

        if ($orderer === null) {
            $orderer = 'id';
        }
        $request = [];
        if (get_param_integer('id', null) !== null) {
            $where = ['member_id' => get_param_integer('id')];
        }
        if (get_option('member_booking_only') == '1') {
            $_rows = $GLOBALS['SITE_DB']->query_select('booking r ' . $join, ['DISTINCT member_id'], $where, 'ORDER BY ' . $orderer);
        } else {
            $_rows = $GLOBALS['SITE_DB']->query_select('booking r ' . $join, ['id'], $where, 'ORDER BY ' . $orderer);
        }
        foreach ($_rows as $row) {
            if (get_option('member_booking_only') == '1') {
                $member_request = get_member_booking_request($row['member_id']);

                foreach ($member_request as $i => $r) {
                    $r['_id'] = strval($row['member_id']) . '_' . strval($i);
                    $request[] = $r;
                }
            } else {
                $member_request = get_booking_request_from_db([$row['id']]);

                $r = $member_request[0];
                $r['_id'] = strval($row['id']);
                $request[] = $r;
            }
        }

        if ($max === null) {
            $max = either_param_integer('max', 20);
        }
        $start = get_param_integer('start', 0);

        $_entries = [];
        foreach ($request as $i => $row) {
            if ($i < $start) {
                continue;
            }
            if (count($_entries) > $max) {
                break;
            }

            $_entries[] = $row;
        }

        if (($orderer !== null) && (!empty($where))) {
            $this->cached_entry_rows = $_entries;
            $this->cached_max_rows = count($request);
        }

        return [$_entries, count($request)];
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen
     * @return array A quartet: The choose table, Whether re-ordering is supported from this screen, Search URL, Archive URL
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        attach_message(do_lang_tempcode('EASIER_TO_EDIT_BOOKING_VIA_MEMBER', escape_html(static_evaluate_tempcode(build_url(['page' => 'members'], get_module_zone('members'))))), 'inform', true);

        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'b_year DESC,b_month DESC,b_day DESC', INPUT_FILTER_GET_COMPLEX);
        list(, $sortable, $sort_order) = preg_split('#(.*) (ASC|DESC)#', $current_ordering, 2, PREG_SPLIT_DELIM_CAPTURE);
        $sortables = [
            'b_year DESC,b_month DESC,b_day' => do_lang_tempcode('DATE'),
            'bookable_id' => do_lang_tempcode('BOOKABLE'),
            'booked_at' => do_lang_tempcode('BOOKING_DATE'),
        ];
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = [];
        $fh[] = do_lang_tempcode('BOOKABLE');
        $fh[] = do_lang_tempcode('FROM');
        $fh[] = do_lang_tempcode('TO');
        $fh[] = do_lang_tempcode('NAME');
        $fh[] = do_lang_tempcode('QUANTITY');
        $fh[] = do_lang_tempcode('BOOKING_DATE');
        $fh[] = do_lang_tempcode('ACTIONS');
        // FUTURE: Show paid at, transaction IDs, and codes, and allow sorting of those
        $header_row = results_header_row($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_url = build_url($url_map + ['id' => $row['_id']], '_SELF');

            $fr = [];
            $fr[] = get_translated_text($GLOBALS['SITE_DB']->query_select_value('bookable', 'title', ['id' => $row['bookable_id']]));
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['start_month'], $row['start_day'], $row['start_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['end_month'], $row['end_day'], $row['end_year']), false, false, $GLOBALS['FORUM_DRIVER']->get_guest_id());
            if (get_option('member_booking_only') == '1') {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['_rows'][0]['member_id'], true);
                if ($username === null) {
                    $fr[] = $row['_rows'][0]['customer_name'];
                } else {
                    $fr[] = $username;
                }
            } else {
                $fr[] = $row['_rows'][0]['customer_name'];
            }
            $fr[] = number_format($row['quantity']);
            $fr[] = get_timezoned_date_time($row['_rows'][0]['booked_at']);
            $fr[] = protect_from_escaping(hyperlink($edit_url, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return [results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false];
    }

    /**
     * Get Tempcode for an adding form.
     *
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    /**
     * Get a form for entering a booking.
     *
     * @param  ?array $details Details of the booking (null: new)
     * @param  ?MEMBER $member_id Who the booking is for (null: current member)
     * @return mixed Either Tempcode; or a tuple: the input fields, hidden fields
     */
    public function get_form_fields(?array $details = null, ?int $member_id = null)
    {
        $hidden = new Tempcode();

        $fields = new Tempcode();

        if ($details === null) {
            $bookable_id = get_param_integer('bookable_id', null);
            if ($bookable_id === null) {
                $bookables_list = new Tempcode();
                $max = 50;
                $start = 0;

                do {
                    $bookables = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], [], 'ORDER BY sort_order', $max, $start);
                    if (empty($bookables)) {
                        inform_exit(do_lang_tempcode('NO_CATEGORIES'));
                    }

                    foreach ($bookables as $bookable) {
                        $bookables_list->attach(form_input_list_entry(strval($bookable['id']), false, get_translated_text($bookable['title'])));
                    }
                    $start += $max;
                } while (!empty($bookables));

                $fields = form_input_huge_list(do_lang_tempcode('BOOKABLE'), '', 'bookable_id', $bookables_list, null, true);
                $post_url = get_self_url(false, false, [], false, true);
                $submit_name = do_lang_tempcode('PROCEED');
                $hidden = build_keep_post_fields();

                return do_template('FORM_SCREEN', [
                    '_GUID' => '05c227f908ce664269b2bb6ba0fff75e',
                    'TARGET' => '_self',
                    'GET' => true,
                    'SKIP_WEBSTANDARDS' => true,
                    'HIDDEN' => $hidden,
                    'TITLE' => $this->title,
                    'TEXT' => '',
                    'URL' => $post_url,
                    'FIELDS' => $fields,
                    'SUBMIT_ICON' => 'buttons/proceed',
                    'SUBMIT_NAME' => $submit_name,
                ]);
            }

            $details = [
                'bookable_id' => $bookable_id,
                'start_day' => get_param_integer('day', intval(date('d'))),
                'start_month' => get_param_integer('month', intval(date('m'))),
                'start_year' => get_param_integer('year', intval(date('Y'))),
                'end_day' => get_param_integer('day', intval(date('d'))),
                'end_month' => get_param_integer('month', intval(date('m'))),
                'end_year' => get_param_integer('year', intval(date('Y'))),
                'quantity' => 1,
                'notes' => '',
                'supplements' => [],
                'customer_name' => '',
                'customer_email' => '',
                'customer_mobile' => '',
                'customer_phone' => '',
            ];
        }
        if ($member_id === null) {
            $member_id = get_member();
        }

        $_bookable = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], ['id' => $details['bookable_id']], '', 1);
        if (!array_key_exists(0, $_bookable)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $bookable = $_bookable[0];

        $fields->attach(form_input_date(do_lang_tempcode('FROM'), '', 'bookable_' . strval($details['bookable_id']) . '_date_from', true, false, false, [0, 0, $details['start_month'], $details['start_day'], $details['start_year']], 10, null, null, true, get_server_timezone()));
        if ($bookable['dates_are_ranges'] == 1) {
            $fields->attach(form_input_date(do_lang_tempcode('TO'), '', 'bookable_' . strval($details['bookable_id']) . '_date_to', true, false, false, [0, 0, $details['end_month'], $details['end_day'], $details['end_year']], 10, null, null, true, get_server_timezone()));
        }
        $fields->attach(form_input_integer(do_lang_tempcode('QUANTITY'), '', 'bookable_' . strval($details['bookable_id']) . '_quantity', $details['quantity'], true));
        $fields->attach(form_input_text(do_lang_tempcode('NOTES'), '', 'bookable_' . strval($details['bookable_id']) . '_notes', $details['notes'], false, false));

        $member_directory_url = build_url(['page' => 'members'], get_module_zone('members'));
        if (get_option('member_booking_only') == '1') {
            $fields->attach(form_input_username(do_lang_tempcode('BOOKING_FOR'), do_lang_tempcode('DESCRIPTION_BOOKING_FOR', escape_html($member_directory_url->evaluate())), 'username', $GLOBALS['FORUM_DRIVER']->get_username($member_id), true, false));
        } else {
            $fields->attach(form_input_line(do_lang_tempcode('NAME'), '', 'customer_name', $details['customer_name'], true));
            $fields->attach(form_input_email(do_lang_tempcode('EMAIL_ADDRESS'), '', 'customer_email', $details['customer_email'], true));
            $fields->attach(form_input_line(do_lang_tempcode('MOBILE_NUMBER'), '', 'customer_mobile', $details['customer_mobile'], false));
            $fields->attach(form_input_line(do_lang_tempcode('PHONE_NUMBER'), '', 'customer_phone', $details['customer_phone'], true));
        }

        $supplement_rows = $GLOBALS['SITE_DB']->query_select('bookable_supplement a JOIN ' . get_table_prefix() . 'bookable_supplement_for b ON a.id=b.supplement_id', ['a.*'], ['bookable_id' => $details['bookable_id']], 'ORDER BY sort_order');
        foreach ($supplement_rows as $supplement_row) {
            $quantity = 0;
            $notes = '';
            if (array_key_exists($supplement_row['id'], $details['supplements'])) {
                $quantity = $details['supplements'][$supplement_row['id']]['quantity'];
                $notes = $details['supplements'][$supplement_row['id']]['notes'];
            }

            $_supplement_row = db_map_restrict($supplement_row, ['id', 'title']);

            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '384b1451a2e83190ec50555e30ceeedc', 'TITLE' => do_lang_tempcode('SUPPLEMENT', get_translated_tempcode('bookable_supplement', $_supplement_row, 'title'))]));

            if ($supplement_row['supports_quantities'] == 1) {
                $fields->attach(form_input_integer(do_lang_tempcode('QUANTITY'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_quantity', $quantity, true));
            } else {
                $fields->attach(form_input_tick(get_translated_tempcode('bookable_supplement', $_supplement_row, 'title'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_quantity', $quantity == 1));
            }
            $fields->attach(form_input_text(do_lang_tempcode('NOTES'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_notes', $notes, false, false));
        }

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $_id)
    {
        if (get_option('member_booking_only') == '0') {
            $request = get_booking_request_from_db([intval($_id)]);
            return $this->get_form_fields($request[0]);
        }

        list($member_id, $i) = array_map('intval', explode('_', $_id, 2));
        $request = get_member_booking_request($member_id);
        return $this->get_form_fields($request[$i], $member_id);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        if (get_option('member_booking_only') == '1') {
            $username = post_param_string('username', false, INPUT_FILTER_POST_IDENTIFIER);
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if ($member_id === null) {
                require_code('cns_members_action');
                require_code('crypt');
                $member_id = cns_make_member(
                    $username, // username
                    get_secure_random_password(), // password
                    '', // email_address
                    null, // primary_group
                    null, // secondary_groups
                    null, // dob_day
                    null, // dob_month
                    null, // dob_year
                    [], // custom_fields
                    null, // timezone
                    null, // language
                    '', // theme
                    '', // title
                    '', // photo_url
                    null, // avatar_url
                    '', // signature
                    null, // preview_posts
                    1, // reveal_age
                    1, // views_signatures
                    null, // auto_monitor_contrib_content
                    null, // smart_topic_notification
                    null, // mailing_list_style
                    1, // auto_mark_read
                    null, // sound_enabled
                    1, // allow_emails
                    1, // allow_emails_from_staff
                    0, // highlighted_name
                    '*', // pt_allow
                    '', // pt_rules_text
                    1, // validated
                    '', // validated_email_confirm_code
                    null, // probation_expiration_time
                    '0', // is_perm_banned
                    false // check_correctness
                );
            }
        } else {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        }

        $request = get_booking_request_from_form();
        $request = save_booking_form_to_db($request, [], $member_id);

        if ($request === null) {
            warn_exit(do_lang_tempcode('ERROR_OCCURRED'));
        }

        // Find $i by loading all member requests and finding which one this is contained in
        $request = get_member_booking_request($member_id);
        $i = null;
        foreach ($request as $i => $r) {
            foreach ($r['_rows'] as $row) {
                if ($row['id'] == $request[0]['_rows'][0]['id']) {
                    break 2;
                }
            }
        }

        if ($i === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (get_option('member_booking_only') == '0') {
            return [strval($request[0]['_rows'][0]['id'])];
        }

        return [strval($member_id) . '_' . strval($i), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        if (get_option('member_booking_only') == '0') {
            $old_request = get_booking_request_from_db([intval($_id)]);
            $i = 0;
        } else {
            list($member_id, $i) = array_map('intval', explode('_', $_id, 2));
            $old_request = get_member_booking_request($member_id);
        }
        $ignore_bookings = [];
        foreach ($old_request[$i]['_rows'] as $row) {
            $ignore_bookings[] = $row['id'];
        }

        $request = get_booking_request_from_form();
        $test = check_booking_dates_available($request, $ignore_bookings);
        if ($test !== null) {
            warn_exit($test);
        }

        // Delete then re-add
        $this->delete_actualisation($_id);
        list($this->new_id) = $this->add_actualisation();

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        if (get_option('member_booking_only') == '0') {
            $request = get_booking_request_from_db([intval($_id)]);
            $i = 0;
        } else {
            list($member_id, $i) = array_map('intval', explode('_', $_id, 2));
            $request = get_member_booking_request($member_id);
        }

        foreach ($request[$i]['_rows'] as $row) {
            delete_booking($row['id']);
        }
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  ?Tempcode $title The title (output of get_screen_title) (null: don't use full page)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?ID_TEXT $id The ID of whatever we are working with (null: deleted)
     * @return Tempcode The UI
     */
    public function do_next_manager(?object $title, object $description, ?string $id = null) : object
    {
        return booking_do_next();
    }
}
