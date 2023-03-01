<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_community_billboard extends Standard_crud_module
{
    protected $lang_type = 'COMMUNITY_BILLBOARD';
    protected $special_edit_frontend = true;
    protected $redirect_type = 'edit';
    protected $menu_label = 'COMMUNITY_BILLBOARD';
    protected $select_name = 'MESSAGE';
    protected $table = 'community_billboard';
    protected $orderer = 'the_message';

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['update_require_upgrade'] = true;
        $info['version'] = 4;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('community_billboard');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('community_billboard', [
                'id' => '*AUTO',
                'member_id' => 'MEMBER',
                'the_message' => 'SHORT_TRANS__COMCODE',
                'days' => 'INTEGER',
                'order_time' => 'TIME',
                'activation_time' => '?TIME',
                'active_now' => 'BINARY',
                'notes' => 'LONG_TEXT',
            ]);

            $GLOBALS['SITE_DB']->create_index('community_billboard', 'find_active_billboard_msg', ['active_now']);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 4)) { // LEGACY
            rename_config_option('system_flagrant', 'system_community_billboard');

            $GLOBALS['SITE_DB']->rename_table('text', 'community_billboard');

            $GLOBALS['SITE_DB']->alter_table_field('community_billboard', 'user_id', 'MEMBER', 'member_id');
        }
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
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('community_billboard', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('ecommerce')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
        }

        $type = get_param_string('type', 'browse');

        require_lang('community_billboard');

        set_helper_panel_tutorial('tut_points');

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
        require_code('community_billboard');

        $this->add_one_label = do_lang_tempcode('ADD_COMMUNITY_BILLBOARD');
        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_COMMUNITY_BILLBOARD');
        $this->edit_one_label = do_lang_tempcode('EDIT_COMMUNITY_BILLBOARD');

        if ($type == 'browse') {
            return $this->browse();
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
        require_code('templates_donext');
        return do_next_manager(
            get_screen_title('COMMUNITY_BILLBOARD'),
            comcode_lang_string('DOC_COMMUNITY_BILLBOARD'),
            [
                ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_COMMUNITY_BILLBOARD')],
                ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_COMMUNITY_BILLBOARD')],
            ],
            do_lang('COMMUNITY_BILLBOARD')
        );
    }

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
        if (!addon_installed('community_billboard')) {
            return null;
        }

        return [
            'browse' => ['COMMUNITY_BILLBOARD_MANAGE', 'menu/adminzone/audit/community_billboard'],
        ];
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen
     * @return array A pair: The choose table, Whether re-ordering is supported from this screen
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'the_message ASC', INPUT_FILTER_GET_COMPLEX);
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = [
            'the_message' => do_lang_tempcode('MESSAGE'),
            'days' => do_lang_tempcode('DAYS_ORDERED'),
            'order_time' => do_lang_tempcode('ORDER_DATE'),
            'member_id' => do_lang_tempcode('metadata:OWNER'),
        ];
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $header_row = results_header_row([
            do_lang_tempcode('MESSAGE'),
            do_lang_tempcode('DAYS_ORDERED'),
            do_lang_tempcode('ORDER_DATE'),
            do_lang_tempcode('_UP_FOR'),
            do_lang_tempcode('metadata:OWNER'),
            do_lang_tempcode('ACTIONS'),
        ], $sortables, 'sort', $sortable . ' ' . $sort_order);

        $result_entries = new Tempcode();

        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_url = build_url($url_map + ['id' => $row['id']], '_SELF');

            $username = protect_from_escaping($GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($row['member_id']));

            $activation_time = $row['activation_time'];
            $days = ($activation_time === null) ? '' : float_format(floatval(time() - $activation_time) / 60.0 / 60.0 / 24.0, 3);

            $result_entries->attach(results_entry([protect_from_escaping(get_translated_tempcode('community_billboard', $row, 'the_message')), integer_format($row['days']), get_timezoned_date_time($row['order_time']), ($row['active_now'] == 1) ? $days : do_lang_tempcode('NA_EM'), $username, protect_from_escaping(hyperlink($edit_url, do_lang_tempcode('EDIT'), false, true, do_lang('EDIT') . ' #' . strval($row['id'])))], true));
        }

        return [results_table(do_lang($this->menu_label), either_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order), false];
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
     * Get Tempcode for a community billboard message adding/editing form.
     *
     * @param  SHORT_TEXT $message The message
     * @param  integer $days The number of days to display for
     * @param  LONG_TEXT $notes Notes
     * @param  BINARY $validated Whether the message is for immediate use
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(string $message = '', int $days = 1, string $notes = '', int $validated = 1) : array
    {
        $fields = new Tempcode();
        $fields->attach(form_input_line_comcode(do_lang_tempcode('MESSAGE'), do_lang_tempcode('DESCRIPTION_MESSAGE'), 'message', $message, true));
        $fields->attach(form_input_integer(do_lang_tempcode('DAYS_ORDERED'), do_lang_tempcode('NUMBER_DAYS_DESCRIPTION'), 'days', $days, true));
        if (get_option('enable_staff_notes') == '1') {
            $fields->attach(form_input_text(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_NOTES'), 'notes', $notes, false));
        }
        $fields->attach(form_input_tick(do_lang_tempcode('IMMEDIATE_USE'), do_lang_tempcode(($message == '') ? 'DESCRIPTION_IMMEDIATE_USE_ADD' : 'DESCRIPTION_IMMEDIATE_USE'), 'validated', $validated == 1));

        return [$fields, new Tempcode()];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('community_billboard', ['*'], ['id' => intval($id)]);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];
        $date = get_timezoned_date_time($myrow['order_time']);
        $date_raw = $myrow['order_time'];
        list($fields, $hidden) = $this->get_form_fields(get_translated_text($myrow['the_message']), $myrow['days'], $myrow['notes'], $myrow['active_now']);

        $username = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($myrow['member_id']);

        $text = do_template('COMMUNITY_BILLBOARD_DETAILS', [
            '_GUID' => 'dcc7a8b027d450a3c17c79b23b39cd87',
            'USERNAME' => $username,
            '_DAYS_ORDERED' => strval($myrow['days']),
            'DAYS_ORDERED' => integer_format($myrow['days']),
            'DATE_RAW' => strval($date_raw),
            'DATE' => $date,
        ]);

        return [$fields, $hidden, new Tempcode(), $text];
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        $message = post_param_string('message');
        $notes = post_param_string('notes', '');
        $validated = post_param_integer('validated', 0);

        $id = add_community_billboard_message($message, post_param_integer('days'), $notes, $validated);

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $id) : ?object
    {
        $message = post_param_string('message');
        $notes = post_param_string('notes', '');
        $validated = post_param_integer('validated', 0);

        edit_community_billboard_message(intval($id), $message, $notes, $validated);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation(string $id)
    {
        delete_community_billboard_message(intval($id));
    }
}
