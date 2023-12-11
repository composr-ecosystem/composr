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

require_code('crud_module');

/**
 * Module page class.
 */
class Module_cms_booking extends Standard_crud_module
{
    public $lang_type = 'BOOKABLE';
    public $select_name = 'TITLE';
    public $code_require = 'booking';
    public $permissions_require = 'cat_high';
    public $user_facing = false;
    public $menu_label = 'BOOKINGS';
    public $orderer = 'sort_order';
    public $title_is_multi_lang = true;
    public $table = 'bookable';
    public $bookings_crud_module;

    public $donext_type = null;

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
        if ($member_id === null) {
            $member_id = get_member();
        }

        $ret = array(
           'browse' => array('BOOKINGS', 'menu/booking'),
        );

        if (has_privilege($member_id, 'submit_highrange_content', 'cms_booking')) {
            $ret += array(
               'add_booking' => array('ADD_BOOKING', 'menu/booking'),
            );
        }

        if (has_privilege($member_id, 'edit_highrange_content', 'cms_booking')) {
            $ret += array(
               'edit_booking' => array('EDIT_BOOKING', 'menu/booking'),
            );
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'add' => array('ADD_BOOKABLE', 'menu/bookable'),
            );
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'edit' => array('EDIT_BOOKABLE', 'menu/bookable'),
            );
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'add_category' => array('ADD_BOOKABLE_SUPPLEMENT', 'menu/supplement'),
            );
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'edit_category' => array('EDIT_BOOKABLE_SUPPLEMENT', 'menu/supplement'),
            );
        }

        if (has_privilege($member_id, 'submit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'add_other' => array('ADD_BOOKABLE_BLACKED', 'menu/blacked'),
            );
        }

        if (has_privilege($member_id, 'edit_cat_highrange_content', 'cms_booking')) {
            $ret += array(
               'edit_other' => array('EDIT_BOOKABLE_BLACKED', 'menu/blacked'),
            );
        }

        $ret += parent::get_entry_points();

        return $ret;
    }

    /**
     * Find privileges defined as overridable by this module.
     *
     * @return array A map of privileges that are overridable; privilege to 0 or 1. 0 means "not category overridable". 1 means "category overridable".
     */
    public function get_privilege_overrides()
    {
        require_lang('booking');
        return array('submit_cat_highrange_content' => array(0, 'ADD_BOOKABLE'), 'edit_cat_highrange_content' => array(0, 'EDIT_BOOKABLE'), 'delete_cat_highrange_content' => array(0, 'DELETE_BOOKABLE'));
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called.
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment).
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run($top_level = true, $type = null)
    {
        if ($top_level) {
            $this->cat_crud_module = class_exists('Mx_cms_booking_blacks') ? new Mx_cms_booking_blacks() : new Module_cms_booking_blacks(); // Blacks
            $this->alt_crud_module = class_exists('Mx_cms_booking_supplements') ? new Mx_cms_booking_supplements() : new Module_cms_booking_supplements(); // Supplements
            $this->bookings_crud_module = class_exists('Mx_cms_booking_bookings') ? new Mx_cms_booking_bookings() : new Module_cms_booking_bookings(); // Bookings
        }

        require_lang('booking');

        if (is_null($type)) {
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
    public function run_start($type)
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
    public function browse()
    {
        return booking_do_next();
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'sort_order ASC');
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            'title' => do_lang_tempcode('TITLE'),
            'categorisation' => do_lang_tempcode('BOOKABLE_CATEGORISATION'),
            'price' => do_lang_tempcode('PRICE'),
            'sort_order' => do_lang_tempcode('SORT_ORDER'),
            'enabled' => do_lang_tempcode('ENABLED'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = array();
        $fh[] = do_lang_tempcode('TITLE');
        $fh[] = do_lang_tempcode('BOOKABLE_CATEGORISATION');
        $fh[] = do_lang_tempcode('PRICE');
        $fh[] = do_lang_tempcode('BOOKABLE_ACTIVE_FROM');
        $fh[] = do_lang_tempcode('BOOKABLE_ACTIVE_TO');
        $fh[] = do_lang_tempcode('ENABLED');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_field_title($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $_row = db_map_restrict($row, array('id', 'title', 'categorisation'));

            $fr = array();
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable', $_row, 'title'));
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable', $_row, 'categorisation'));
            $fr[] = float_format($row['price']);
            $fr[] = get_timezoned_date(mktime($row['active_from_month'], $row['active_from_day'], $row['active_from_year']), false, true, false, true);
            $fr[] = get_timezoned_date(mktime($row['active_to_month'], $row['active_to_day'], $row['active_to_year']), false, true, false, true);
            $fr[] = ($row['enabled'] == 1) ? do_lang_tempcode('YES') : do_lang_tempcode('NO');
            $fr[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return array(results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Get a form for entering a bookable.
     *
     * @param  ?array $details Details of the bookable (null: new).
     * @param  ?array $supplements List of supplements (null: new).
     * @param  ?array $blacks List of blacks (null: new).
     * @param  ?array $codes List of codes (null: new).
     * @return array Tuple: form fields, hidden fields.
     */
    public function get_form_fields($details = null, $supplements = null, $blacks = null, $codes = null)
    {
        if (is_null($supplements)) {
            $supplements = array();
        }
        if (is_null($blacks)) {
            $blacks = array();
        }
        if (is_null($codes)) {
            $codes = array();
        }

        if (is_null($details)) {
            $max_sort_order = $GLOBALS['SITE_DB']->query_select_value('bookable', 'MAX(sort_order)');
            if (is_null($max_sort_order)) {
                $max_sort_order = 0;
            }

            $details = array(
                'title' => null,
                'description' => null,
                'price' => 0.0,
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
            );
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('cycle_type', $details['cycle_type']));
        $hidden->attach(form_input_hidden('cycle_pattern', $details['cycle_pattern']));
        $hidden->attach(form_input_hidden('user_may_choose_code', strval($details['user_may_choose_code'])));
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_line_comcode(do_lang_tempcode('TITLE'), do_lang_tempcode('DESCRIPTION_TITLE'), 'title', is_null($details['title']) ? '' : get_translated_text($details['title']), true));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('DESCRIPTION'), do_lang_tempcode('DESCRIPTION_DESCRIPTION'), 'description', is_null($details['description']) ? '' : get_translated_text($details['description']), false));
        $fields->attach(form_input_line(do_lang_tempcode('PRICE'), do_lang_tempcode('DESCRIPTION_BOOKABLE_PRICE'), 'price', float_to_raw_string($details['price'], 2), true));
        $categorisation = is_null($details['categorisation']) ? '' : get_translated_text($details['categorisation']);
        if ($categorisation == '') {
            $_categorisation = $GLOBALS['SITE_DB']->query_select_value_if_there('bookable', 'categorisation', null, 'GROUP BY categorisation ORDER BY COUNT(*) DESC');
            if (is_null($_categorisation)) {
                $categorisation = do_lang('GENERAL');
            } else {
                $categorisation = get_translated_text($_categorisation);
            }
        }
        $fields->attach(form_input_line(do_lang_tempcode('BOOKABLE_CATEGORISATION'), do_lang_tempcode('DESCRIPTION_BOOKABLE_CATEGORISATION'), 'categorisation', $categorisation, true));
        //$fields->attach(form_input_select(do_lang_tempcode('CYCLE_TYPE'), do_lang_tempcode('DESCRIPTION_CYCLE_TYPE'), 'cycle_type', $details['cycle_type'], false));
        //$fields->attach(form_input_line(do_lang_tempcode('CYCLE_PATTERN'), do_lang_tempcode('DESCRIPTION_CYCLE_PATTERN'), 'cycle_pattern', $details['cycle_pattern'], false));
        //$fields->attach(form_input_tick(do_lang_tempcode('USER_MAY_CHOOSE_CODE'), do_lang_tempcode('DESCRIPTION_USER_MAY_CHOOSE_CODE'), 'user_may_choose_code', $details['user_may_choose_code']==1));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_NOTES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_NOTES'), 'supports_notes', $details['supports_notes'] == 1));
        $fields->attach(form_input_tick(do_lang_tempcode('BOOKABLE_DATES_ARE_RANGES'), do_lang_tempcode('DESCRIPTION_BOOKABLE_DATES_ARE_RANGES'), 'dates_are_ranges', $details['dates_are_ranges'] == 1));

        $fields->attach(form_input_text(do_lang_tempcode('BOOKABLE_CODES'), do_lang_tempcode('DESCRIPTION_BOOKABLE_CODES'), 'codes', implode("\n", $codes), true));

        $_supplements = new Tempcode();
        $all_supplements = $GLOBALS['SITE_DB']->query_select('bookable_supplement', array('id', 'title'), null, 'ORDER BY sort_order');
        foreach ($all_supplements as $s) {
            $_supplements->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $supplements), get_translated_text($s['title'])));
        }
        if (!$_supplements->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('SUPPLEMENTS'), do_lang_tempcode('DESCRIPTION_BOOKABLE_SUPPLEMENTS'), 'supplements', $_supplements));
        }

        $_blacks = new Tempcode();
        $all_blacks = $GLOBALS['SITE_DB']->query_select('bookable_blacked', array('id', 'blacked_explanation'), null, 'ORDER BY blacked_from_year,blacked_from_month,blacked_from_day');
        foreach ($all_blacks as $s) {
            $_blacks->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $blacks), get_translated_text($s['blacked_explanation'])));
        }
        if (!$_blacks->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('BLACKOUTS'), do_lang_tempcode('DESCRIPTION_BOOKABLE_BLACKS'), 'blacks', $_blacks));
        }

        $fields->attach(form_input_date(do_lang_tempcode('BOOKABLE_ACTIVE_FROM'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ACTIVE_FROM'), 'active_from', true, false, false, array(0, 0, $details['active_from_month'], $details['active_from_day'], $details['active_from_year']), 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_date(do_lang_tempcode('BOOKABLE_ACTIVE_TO'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ACTIVE_TO'), 'active_to', false, true, false, is_null($details['active_to_month']) ? null : array(0, 0, $details['active_to_month'], $details['active_to_day'], $details['active_to_year']), 10, null, null, true, get_server_timezone()));

        $fields->attach(form_input_integer(do_lang_tempcode('SORT_ORDER'), do_lang_tempcode('DESCRIPTION_SORT_ORDER'), 'sort_order', $details['sort_order'], true));

        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_BOOKABLE_ENABLED'), 'enabled', $details['enabled'] == 1));

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return array A tuple of lots of info
     */
    public function fill_in_edit_form($_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), array('id' => intval($id)), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $supplements = collapse_1d_complexity('supplement_id', $GLOBALS['SITE_DB']->query_select('bookable_supplement_for', array('supplement_id'), array('bookable_id' => $id)));
        $blacks = collapse_1d_complexity('blacked_id', $GLOBALS['SITE_DB']->query_select('bookable_blacked_for', array('blacked_id'), array('bookable_id' => $id)));
        $codes = collapse_1d_complexity('code', $GLOBALS['SITE_DB']->query_select('bookable_codes', array('code'), array('bookable_id' => $id)));

        return $this->get_form_fields($myrow, $supplements, $blacks, $codes);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The ID of the entry added
     */
    public function add_actualisation()
    {
        list($bookable_details, $codes, $blacked, $supplements) = get_bookable_details_from_form();

        $id = add_bookable($bookable_details, $codes, $blacked, $supplements);

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     */
    public function edit_actualisation($_id)
    {
        $id = intval($_id);

        list($bookable_details, $codes, $blacked, $supplements) = get_bookable_details_from_form();

        edit_bookable($id, $bookable_details, $codes, $blacked, $supplements);
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        $id = intval($_id);

        delete_bookable($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  Tempcode $title The title (output of get_screen_title)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?AUTO_LINK $id The ID of whatever was just handled (null: N/A)
     * @return Tempcode The UI
     */
    public function do_next_manager($title, $description, $id)
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_supplements extends Standard_crud_module
{
    public $lang_type = 'BOOKABLE_SUPPLEMENT';
    public $select_name = 'EXPLANATION';
    public $code_require = 'booking';
    public $permissions_require = 'cat_high';
    public $user_facing = false;
    public $menu_label = 'BOOKINGS';
    public $orderer = 'sort_order';
    public $title_is_multi_lang = true;
    public $table = 'bookable_supplement';

    public $donext_type = null;

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'sort_order ASC');
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            'title' => do_lang_tempcode('TITLE'),
            'price' => do_lang_tempcode('PRICE'),
            'sort_order' => do_lang_tempcode('SORT_ORDER'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = array();
        $fh[] = do_lang_tempcode('TITLE');
        $fh[] = do_lang_tempcode('PRICE');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_field_title($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $_row = db_map_restrict($row, array('id', 'title'));

            $fr = array();
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable_supplement', $_row, 'title'));
            $fr[] = float_format($row['price']);
            $fr[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return array(results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Get a form for entering a bookable supplement.
     *
     * @param  ?array $details Details of the supplement (null: new).
     * @param  ?array $bookables List of bookables this is for (null: new).
     * @return array Tuple: form fields, hidden fields.
     */
    public function get_form_fields($details = null, $bookables = null)
    {
        if (is_null($bookables)) {
            $bookables = array();
        }

        if (is_null($details)) {
            $max_sort_order = $GLOBALS['SITE_DB']->query_select_value('bookable_supplement', 'MAX(sort_order)');
            if (is_null($max_sort_order)) {
                $max_sort_order = 0;
            }

            $details = array(
                'price' => 0.0,
                'price_is_per_period' => 0,
                'supports_quantities' => 0,
                'title' => null,
                'promo_code' => '',
                'supports_notes' => 0,
                'sort_order' => 1,
            );

            $bookables = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('bookable', array('id')));
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('promo_code', $details['promo_code']));
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('TITLE'), do_lang_tempcode('DESCRIPTION_TITLE'), 'title', is_null($details['title']) ? '' : get_translated_text($details['title']), true));
        $fields->attach(form_input_line(do_lang_tempcode('PRICE'), do_lang_tempcode('DESCRIPTION_SUPPLEMENT_PRICE'), 'price', float_to_raw_string($details['price'], 2), true));
        $fields->attach(form_input_tick(do_lang_tempcode('PRICE_IS_PER_PERIOD'), do_lang_tempcode('DESCRIPTION_PRICE_IS_PER_PERIOD'), 'price_is_per_period', $details['price_is_per_period'] == 1));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_QUANTITIES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_QUANTITIES'), 'supports_quantities', $details['supports_quantities'] == 1));
        //$fields->attach(form_input_line(do_lang_tempcode('PROMO_CODE'), do_lang_tempcode('DESCRIPTION_PROMO_CODE'), 'promo_code', $details['promo_code'], true));
        $fields->attach(form_input_tick(do_lang_tempcode('SUPPORTS_NOTES'), do_lang_tempcode('DESCRIPTION_SUPPORTS_NOTES'), 'supports_notes', $details['supports_notes'] == 1));
        $fields->attach(form_input_integer(do_lang_tempcode('SORT_ORDER'), do_lang_tempcode('DESCRIPTION_SORT_ORDER'), 'sort_order', $details['sort_order'], true));

        $_bookables = new Tempcode();
        $all_bookables = $GLOBALS['SITE_DB']->query_select('bookable', array('id', 'title'), null, 'ORDER BY sort_order');
        foreach ($all_bookables as $s) {
            $_bookables->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $bookables), get_translated_text($s['title'])));
        }
        if (!$_bookables->is_empty()) {
            $fields->attach(form_input_multi_list(do_lang_tempcode('BOOKABLES'), do_lang_tempcode('DESCRIPTION_SUPPLEMENT_BOOKABLES'), 'bookables', $_bookables));
        }

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return array A tuple of lots of info
     */
    public function fill_in_edit_form($_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable_supplement', array('*'), array('id' => intval($id)), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $bookables = collapse_1d_complexity('bookable_id', $GLOBALS['SITE_DB']->query_select('bookable_supplement_for', array('bookable_id'), array('supplement_id' => $id)));

        return $this->get_form_fields($myrow, $bookables);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The ID of the entry added
     */
    public function add_actualisation()
    {
        list($details, $bookables) = get_bookable_supplement_details_from_form();

        $id = add_bookable_supplement($details, $bookables);

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     */
    public function edit_actualisation($_id)
    {
        $id = intval($_id);

        list($details, $bookables) = get_bookable_supplement_details_from_form();

        edit_bookable_supplement($id, $details, $bookables);
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        $id = intval($_id);

        delete_bookable_supplement($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  Tempcode $title The title (output of get_screen_title)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?AUTO_LINK $id The ID of whatever was just handled (null: N/A)
     * @return Tempcode The UI
     */
    public function do_next_manager($title, $description, $id)
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_blacks extends Standard_crud_module
{
    public $lang_type = 'BOOKABLE_BLACKED';
    public $select_name = 'EXPLANATION';
    public $code_require = 'booking';
    public $permissions_require = 'cat_high';
    public $user_facing = false;
    public $menu_label = 'BOOKINGS';
    public $orderer = 'id';
    public $title_is_multi_lang = true;
    public $table = 'bookable_blacked';

    public $donext_type = null;

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'blacked_from_year,blacked_from_month,blacked_from_day ASC');
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            'blacked_from_year,blacked_from_month,blacked_from_day' => do_lang_tempcode('DATE'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = array();
        $fh[] = do_lang_tempcode('FROM');
        $fh[] = do_lang_tempcode('TO');
        $fh[] = do_lang_tempcode('BLACKED_EXPLANATION');
        $fh[] = do_lang_tempcode('ACTIONS');
        $header_row = results_field_title($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $_row = db_map_restrict($row, array('id', 'blacked_explanation'));

            $fr = array();
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['blacked_from_month'], $row['blacked_from_day'], $row['blacked_from_year']), false);
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['blacked_to_month'], $row['blacked_to_day'], $row['blacked_to_year']), false);
            $fr[] = protect_from_escaping(get_translated_tempcode('bookable_blacked', $_row, 'blacked_explanation'));
            $fr[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return array(results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Get a form for entering a bookable black.
     *
     * @param  ?array $details Details of the black (null: new).
     * @param  ?array $bookables List of bookables this is for (null: new).
     * @return array Tuple: form fields, hidden fields.
     */
    public function get_form_fields($details = null, $bookables = null)
    {
        if (is_null($bookables)) {
            $bookables = array();
        }

        if (is_null($details)) {
            $details = array(
                'blacked_from_day' => intval(date('d')),
                'blacked_from_month' => intval(date('m')),
                'blacked_from_year' => intval(date('Y')),
                'blacked_to_day' => intval(date('d')),
                'blacked_to_month' => intval(date('m')),
                'blacked_to_year' => intval(date('Y')),
                'blacked_explanation' => null,
            );

            $bookables = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('bookable', array('id')));
        }

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('timezone', get_server_timezone()));

        $fields = new Tempcode();
        $fields->attach(form_input_date(do_lang_tempcode('BLACKED_FROM'), do_lang_tempcode('DESCRIPTION_BLACKED_FROM'), 'blacked_from', true, false, false, array(0, 0, $details['blacked_from_month'], $details['blacked_from_day'], $details['blacked_from_year']), 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_date(do_lang_tempcode('BLACKED_TO'), do_lang_tempcode('DESCRIPTION_BLACKED_TO'), 'blacked_to', true, false, false, array(0, 0, $details['blacked_to_month'], $details['blacked_to_day'], $details['blacked_to_year']), 10, null, null, true, get_server_timezone()));
        $fields->attach(form_input_text(do_lang_tempcode('BLACKED_EXPLANATION'), do_lang_tempcode('DESCRIPTION_BLACKED_EXPLANATION'), 'blacked_explanation', is_null($details['blacked_explanation']) ? '' : get_translated_text($details['blacked_explanation']), true));

        $_bookables = new Tempcode();
        $all_bookables = $GLOBALS['SITE_DB']->query_select('bookable', array('id', 'title'), null, 'ORDER BY sort_order');
        foreach ($all_bookables as $s) {
            $_bookables->attach(form_input_list_entry(strval($s['id']), in_array($s['id'], $bookables), get_translated_text($s['title'])));
        }
        $fields->attach(form_input_multi_list(do_lang_tempcode('BOOKABLES'), do_lang_tempcode('DESCRIPTION_BLACKED_BOOKABLES'), 'bookables', $_bookables));

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return array A tuple of lots of info
     */
    public function fill_in_edit_form($_id)
    {
        $id = intval($_id);

        $rows = $GLOBALS['SITE_DB']->query_select('bookable_blacked', array('*'), array('id' => intval($id)), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $bookables = collapse_1d_complexity('bookable_id', $GLOBALS['SITE_DB']->query_select('bookable_blacked_for', array('bookable_id'), array('blacked_id' => $id)));

        return $this->get_form_fields($myrow, $bookables);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The ID of the entry added
     */
    public function add_actualisation()
    {
        list($details, $bookables) = get_bookable_blacked_details_from_form();

        $id = add_bookable_blacked($details, $bookables);

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     */
    public function edit_actualisation($_id)
    {
        $id = intval($_id);

        list($details, $bookables) = get_bookable_blacked_details_from_form();

        edit_bookable_blacked($id, $details, $bookables);
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        $id = intval($_id);

        delete_bookable_blacked($id);
    }

    /**
     * The do-next manager for after download content management (event types only).
     *
     * @param  Tempcode $title The title (output of get_screen_title)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?AUTO_LINK $id The ID of whatever was just handled (null: N/A)
     * @return Tempcode The UI
     */
    public function do_next_manager($title, $description, $id)
    {
        return booking_do_next();
    }
}

/**
 * Module page class.
 */
class Module_cms_booking_bookings extends Standard_crud_module
{
    public $lang_type = 'BOOKING';
    public $select_name = 'MEMBER_ID';
    public $code_require = 'booking';
    public $permissions_require = 'high';
    public $user_facing = false;
    public $menu_label = 'BOOKINGS';
    public $orderer = 'id';
    public $title_is_multi_lang = true;
    public $table = 'booking';
    public $type_code = 'booking';
    public $non_integer_id = true;

    public $donext_type = null;

    /**
     * Standard CRUD-module entry function to get rows for selection from.
     *
     * @param  boolean $recache Whether to force a recache
     * @param  ?ID_TEXT $orderer Order to use (null: automatic)
     * @param  ?array $where Extra where clauses (null: none)
     * @param  boolean $force_site_db Whether to always access using the site database
     * @param  string $join Extra join clause for our query (blank: none)
     * @param  ?integer $max Maximum to show (null: standard)
     * @return array A pair: Rows for selection from, Total results
     */
    public function get_entry_rows($recache = false, $orderer = null, $where = null, $force_site_db = false, $join = '', $max = null)
    {
        if ((!$recache) && (!is_null($orderer)) && (!is_null($where))) {
            if (isset($this->cached_entry_rows)) {
                return array($this->cached_entry_rows, $this->cached_max_rows);
            }
        }

        $select_field = !is_null($this->orderer) ? $this->orderer : ($this->table_prefix . strtolower($this->select_name));

        if (is_null($orderer)) {
            $orderer = $select_field;
        }
        $table = is_null($this->table) ? $this->module_type : $this->table;
        $db = ((substr($table, 0, 2) == 'f_') && (!$force_site_db) && (get_forum_type() == 'cns')) ? $GLOBALS['FORUM_DB'] : $GLOBALS['SITE_DB'];
        if ($force_site_db) {
            $dbs_bak = $GLOBALS['NO_DB_SCOPE_CHECK'];
            $GLOBALS['NO_DB_SCOPE_CHECK'] = true;
        }
        $request = array();
        if (get_param_integer('id', null) !== null) {
            $where = array('member_id' => get_param_integer('id'));
        }
        if (get_option('member_booking_only') == '1') {
            $_rows = $db->query_select($table . ' r ' . $join, array('DISTINCT member_id', $orderer), $where, 'ORDER BY ' . $orderer);
        } else {
            $_rows = $db->query_select($table . ' r ' . $join, array('id'), $where, 'ORDER BY ' . $orderer);
        }
        foreach ($_rows as $row) {
            if (get_option('member_booking_only') == '1') {
                $member_request = get_member_booking_request($row['member_id']);

                foreach ($member_request as $i => $r) {
                    $r['_id'] = strval($row['member_id']) . '_' . strval($i);
                    $request[] = $r;
                }
            } else {
                $member_request = get_booking_request_from_db(array($row['id']));

                $r = $member_request[0];
                $r['_id'] = strval($row['id']);
                $request[] = $r;
            }
        }

        if ($max === null) {
            $max = either_param_integer('max', 20);
        }
        $start = get_param_integer('start', 0);

        if ($force_site_db) {
            $GLOBALS['NO_DB_SCOPE_CHECK'] = $dbs_bak;
        }
        $_entries = array();
        foreach ($request as $i => $row) {
            if ($i < $start) {
                continue;
            }
            if (count($_entries) > $max) {
                break;
            }

            $_entries[] = $row;
        }

        if ((!is_null($orderer)) && (!is_null($where))) {
            $this->cached_entry_rows = $_entries;
            $this->cached_max_rows = count($request);
        }

        return array($_entries, count($request));
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table($url_map)
    {
        attach_message(do_lang_tempcode('EASIER_TO_EDIT_BOOKING_VIA_MEMBER', escape_html(static_evaluate_tempcode(build_url(array('page' => 'members'), get_module_zone('members'))))), 'inform', true);

        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'b_year DESC,b_month DESC,b_day DESC');
        list(, $sortable, $sort_order) = preg_split('#(.*) (ASC|DESC)#', $current_ordering, 2, PREG_SPLIT_DELIM_CAPTURE);
        $sortables = array(
            'b_year DESC,b_month DESC,b_day' => do_lang_tempcode('DATE'),
            'bookable_id' => do_lang_tempcode('BOOKABLE'),
            'booked_at' => do_lang_tempcode('BOOKING_DATE'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = array();
        $fh[] = do_lang_tempcode('BOOKABLE');
        $fh[] = do_lang_tempcode('FROM');
        $fh[] = do_lang_tempcode('TO');
        $fh[] = do_lang_tempcode('NAME');
        $fh[] = do_lang_tempcode('QUANTITY');
        $fh[] = do_lang_tempcode('BOOKING_DATE');
        $fh[] = do_lang_tempcode('ACTIONS');
        // FUTURE: Show paid at, transaction IDs, and codes, and allow sorting of those
        $header_row = results_field_title($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['_id']), '_SELF');

            $fr = array();
            $fr[] = get_translated_text($GLOBALS['SITE_DB']->query_select_value('bookable', 'title', array('id' => $row['bookable_id'])));
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['start_month'], $row['start_day'], $row['start_year']), false, true, true);
            $fr[] = get_timezoned_date(mktime(0, 0, 0, $row['end_month'], $row['end_day'], $row['end_year']), false, true, false, true);
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
            $fr[] = get_timezoned_date($row['_rows'][0]['booked_at']);
            $fr[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true));

            $fields->attach(results_entry($fr, true));
        }

        return array(results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Get a form for entering a booking.
     *
     * @param  ?array $details Details of the booking (null: new).
     * @param  ?MEMBER $member_id Who the booking is for (null: current member).
     * @return mixed Either Tempcode; or a tuple: form fields, hidden fields.
     */
    public function get_form_fields($details = null, $member_id = null)
    {
        $hidden = new Tempcode();

        $fields = new Tempcode();

        if (is_null($details)) {
            $bookable_id = get_param_integer('bookable_id', null);
            if (is_null($bookable_id)) {
                $bookables = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), null, 'ORDER BY sort_order');
                if (count($bookables) == 0) {
                    inform_exit(do_lang_tempcode('NO_CATEGORIES'));
                }

                $bookables_list = new Tempcode();
                foreach ($bookables as $bookable) {
                    $bookables_list->attach(form_input_list_entry(strval($bookable['id']), false, get_translated_text($bookable['title'])));
                }

                $fields = form_input_list(do_lang_tempcode('BOOKABLE'), '', 'bookable_id', $bookables_list, null, true);
                $post_url = get_self_url(false, false, null, false, true);
                $submit_name = do_lang_tempcode('PROCEED');
                $hidden = build_keep_post_fields();

                return do_template('FORM_SCREEN', array(
                    '_GUID' => '05c227f908ce664269b2bb6ba0fff75e',
                    'TARGET' => '_self',
                    'GET' => true,
                    'SKIP_WEBSTANDARDS' => true,
                    'HIDDEN' => $hidden,
                    'TITLE' => $this->title,
                    'TEXT' => '',
                    'URL' => $post_url,
                    'FIELDS' => $fields,
                    'SUBMIT_ICON' => 'buttons__proceed',
                    'SUBMIT_NAME' => $submit_name,
                ));
            }

            $details = array(
                'bookable_id' => $bookable_id,
                'start_day' => get_param_integer('day', intval(date('d'))),
                'start_month' => get_param_integer('month', intval(date('m'))),
                'start_year' => get_param_integer('year', intval(date('Y'))),
                'end_day' => get_param_integer('day', intval(date('d'))),
                'end_month' => get_param_integer('month', intval(date('m'))),
                'end_year' => get_param_integer('year', intval(date('Y'))),
                'quantity' => 1,
                'notes' => '',
                'supplements' => array(),
                'customer_name' => '',
                'customer_email' => '',
                'customer_mobile' => '',
                'customer_phone' => '',
            );
        }
        if (is_null($member_id)) {
            $member_id = get_member();
        }

        $_bookable = $GLOBALS['SITE_DB']->query_select('bookable', array('*'), array('id' => $details['bookable_id']), '', 1);
        if (!array_key_exists(0, $_bookable)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $bookable = $_bookable[0];

        $fields->attach(form_input_date(do_lang_tempcode('FROM'), '', 'bookable_' . strval($details['bookable_id']) . '_date_from', true, false, false, array(0, 0, $details['start_month'], $details['start_day'], $details['start_year']), 10, null, null, true, get_server_timezone()));
        if ($bookable['dates_are_ranges'] == 1) {
            $fields->attach(form_input_date(do_lang_tempcode('TO'), '', 'bookable_' . strval($details['bookable_id']) . '_date_to', true, false, false, array(0, 0, $details['end_month'], $details['end_day'], $details['end_year']), 10, null, null, true, get_server_timezone()));
        }
        $fields->attach(form_input_integer(do_lang_tempcode('QUANTITY'), '', 'bookable_' . strval($details['bookable_id']) . '_quantity', $details['quantity'], true));
        $fields->attach(form_input_text(do_lang_tempcode('NOTES'), '', 'bookable_' . strval($details['bookable_id']) . '_notes', $details['notes'], false));

        $member_directory_url = build_url(array('page' => 'members'), get_module_zone('members'));
        if (get_option('member_booking_only') == '1') {
            $fields->attach(form_input_username(do_lang_tempcode('BOOKING_FOR'), do_lang_tempcode('DESCRIPTION_BOOKING_FOR', escape_html($member_directory_url->evaluate())), 'username', $GLOBALS['FORUM_DRIVER']->get_username($member_id), true, false));
        } else {
            $fields->attach(form_input_line(do_lang_tempcode('NAME'), '', 'customer_name', $details['customer_name'], true));
            $fields->attach(form_input_email(do_lang_tempcode('EMAIL_ADDRESS'), '', 'customer_email', $details['customer_email'], true));
            $fields->attach(form_input_line(do_lang_tempcode('MOBILE_NUMBER'), '', 'customer_mobile', $details['customer_mobile'], false));
            $fields->attach(form_input_line(do_lang_tempcode('PHONE_NUMBER'), '', 'customer_phone', $details['customer_phone'], true));
        }

        $supplement_rows = $GLOBALS['SITE_DB']->query_select('bookable_supplement a JOIN ' . get_table_prefix() . 'bookable_supplement_for b ON a.id=b.supplement_id', array('a.*'), array('bookable_id' => $details['bookable_id']), 'ORDER BY sort_order');
        foreach ($supplement_rows as $supplement_row) {
            $quantity = 0;
            $notes = '';
            if (array_key_exists($supplement_row['id'], $details['supplements'])) {
                $quantity = $details['supplements'][$supplement_row['id']]['quantity'];
                $notes = $details['supplements'][$supplement_row['id']]['notes'];
            }

            $_supplement_row = db_map_restrict($supplement_row, array('id', 'title'));

            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '384b1451a2e83190ec50555e30ceeedc', 'TITLE' => do_lang_tempcode('SUPPLEMENT', get_translated_tempcode('bookable_supplement', $_supplement_row, 'title')))));

            if ($supplement_row['supports_quantities'] == 1) {
                $fields->attach(form_input_integer(do_lang_tempcode('QUANTITY'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_quantity', $quantity, true));
            } else {
                $fields->attach(form_input_tick(get_translated_tempcode('bookable_supplement', $_supplement_row, 'title'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_quantity', $quantity == 1));
            }
            $fields->attach(form_input_text(do_lang_tempcode('NOTES'), '', 'bookable_' . strval($details['bookable_id']) . '_supplement_' . strval($supplement_row['id']) . '_notes', $notes, false));
        }

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return array A tuple of lots of info
     */
    public function fill_in_edit_form($_id)
    {
        if (get_option('member_booking_only') == '0') {
            $request = get_booking_request_from_db(array(intval($_id)));
            return $this->get_form_fields($request[0]);
        }

        list($member_id, $i) = array_map('intval', explode('_', $_id, 2));
        $request = get_member_booking_request($member_id);
        return $this->get_form_fields($request[$i], $member_id);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The ID of the entry added
     */
    public function add_actualisation()
    {
        if (get_option('member_booking_only') == '1') {
            $username = post_param_string('username');
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
            if (is_null($member_id)) {
                require_code('cns_members_action');
                $member_id = cns_make_member($username, uniqid('', true), '', array(), null, null, null, array(), null, null, 1, null, null, '', null, '', 0, 0, 1, '', '', '', 1, 1, null, 1, 1, null, '', false);
            }
        } else {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_guest_id();
        }

        $request = get_booking_request_from_form();
        $request = save_booking_form_to_db($request, array(), $member_id);

        if (is_null($request)) {
            warn_exit(do_lang_tempcode('ERROR_OCCURRED'));
        }

        // Find $i by loading all member requests and finding which one this is contained in
        $request = get_member_booking_request($member_id);
        foreach ($request as $i => $r) {
            foreach ($r['_rows'] as $row) {
                if ($row['id'] == $request[0]['_rows'][0]['id']) {
                    break 2;
                }
            }
        }

        if (get_option('member_booking_only') == '0') {
            return strval($request[0]['_rows'][0]['id']);
        }

        return strval($member_id) . '_' . strval($i);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     */
    public function edit_actualisation($_id)
    {
        if (get_option('member_booking_only') == '0') {
            $old_request = get_booking_request_from_db(array(intval($_id)));
            $i = 0;
        } else {
            list($member_id, $i) = array_map('intval', explode('_', $_id, 2));
            $old_request = get_member_booking_request($member_id);
        }
        $ignore_bookings = array();
        foreach ($old_request[$i]['_rows'] as $row) {
            $ignore_bookings[] = $row['id'];
        }

        $request = get_booking_request_from_form();
        $test = check_booking_dates_available($request, $ignore_bookings);
        if (!is_null($test)) {
            warn_exit($test);
        }

        // Delete then re-add
        $this->delete_actualisation($_id);
        $this->new_id = $this->add_actualisation();
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        if (get_option('member_booking_only') == '0') {
            $request = get_booking_request_from_db(array(intval($_id)));
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
     * @param  Tempcode $title The title (output of get_screen_title)
     * @param  Tempcode $description Some description to show, saying what happened
     * @param  ?AUTO_LINK $id The ID of whatever was just handled (null: N/A)
     * @return Tempcode The UI
     */
    public function do_next_manager($title, $description, $id)
    {
        return booking_do_next();
    }
}
