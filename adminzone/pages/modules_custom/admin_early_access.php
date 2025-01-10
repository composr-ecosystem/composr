<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    early_access
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_early_access extends Standard_crud_module
{
    protected $lang_type = 'EARLY_ACCESS_CODE';
    protected $select_name = 'CODE';
    protected $menu_label = 'EARLY_ACCESS_CODES';
    protected $do_preview = null;
    protected $view_entry_point = '_SEARCH:admin_early_access:_edit:_ID';

    protected $array_key = 'c_access_code';
    protected $table = 'early_access_codes';

    protected $orderer = 'c_creation_time';

    protected $non_integer_id = true;

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Christopher Graham; Patrick Schmalstig';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'early_access';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('early_access_codes');
        $GLOBALS['SITE_DB']->drop_table_if_exists('early_access_code_content');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        require_lang('early_access');

        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('early_access_codes', [
                'c_access_code' => '*ID_TEXT',
                'c_label' => 'SHORT_TEXT',
                'c_trigger_access' => 'ID_TEXT', // NB: Formerly c_change_tag
                'c_date_from' => '?TIME',
                'c_date_to' => '?TIME',
                'c_num_views' => 'INTEGER', // NB: Formerly c_num_views_used
                'c_num_views_allowed' => '?INTEGER', // NB: Formerly c_num_views_assigned
                'c_created_by' => 'MEMBER',
                'c_creation_time' => 'TIME',
                'c_edit_time' => 'TIME',
            ]);
            $GLOBALS['SITE_DB']->create_table('early_access_code_content', [
                'a_access_code' => '*ID_TEXT',
                'a_content_type' => '*ID_TEXT',
                'a_content_id' => '*ID_TEXT',
            ]);
        }
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
        if (!addon_installed('early_access')) {
            return null;
        }
        if (!addon_installed('validation')) {
            return null;
        }

        require_lang('early_access');

        return [
            'browse' => ['EARLY_ACCESS_CODES', 'menu/adminzone/security/permissions/privileges'],
            'view' => ['EARLY_ACCESS_CODES', 'menu/adminzone/security/permissions/privileges'],
        ] + parent::get_entry_points();
    }

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run(bool $top_level = true, ?string $type = null) : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('early_access', $error_msg)) {
            return $error_msg;
        }
        if (!addon_installed__messaged('validation', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('early_access');

        if ($type != 'view') { // The view table is too large for the panel
            set_helper_panel_tutorial('sup_early_access');
            set_helper_panel_text(comcode_lang_string('DOC_EARLY_ACCESS_CODES'));
        }

        switch ($type) {
            case 'browse':
            case 'view':
                $this->title = get_screen_title('EARLY_ACCESS_CODES');
                break;
        }

        return parent::pre_run($top_level);
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run_start() : object
    {
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('early_access', $error_msg)) {
            return $error_msg;
        }
        if (!addon_installed__messaged('validation', $error_msg)) {
            return $error_msg;
        }

        require_code('early_access');
        require_code('form_templates');

        $type = get_param_string('type', 'browse');

        switch ($type) {
            case 'browse':
                return $this->browse();
            case 'view':
                return $this->view();
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
            get_screen_title('EARLY_ACCESS_CODES'),
            comcode_lang_string('DOC_EARLY_ACCESS_CODES'),
            [
                ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_EARLY_ACCESS_CODE')],
                ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_EARLY_ACCESS_CODE')],
                ['admin/view_archive', ['_SELF', ['type' => 'view'], '_SELF'], do_lang('VIEW_EARLY_ACCESS_CODES')],
            ],
            do_lang('EARLY_ACCESS_CODES')
        );
    }

    /**
     * Get form fields for adding a new record.
     *
     * @return array Array of fields, hidden
     */
    public function get_form_fields_for_add() : array
    {
        return $this->get_form_fields();
    }

    /**
     * Get form fields for adding / editing.
     *
     * @param  ?ID_TEXT $access_code The access code (null: we are adding a new record)
     * @param  SHORT_TEXT $label The label (blank: we are adding a new record)
     * @param  ID_TEXT $trigger_access The trigger access for this code used in templates (blank: none, or we are adding a new record)
     * @param  array $content Duples of content type and content ID associated with this code (empty: none, or new record)
     * @param  ?TIME $date_from The time at which the access code starts (null: immediately, or we are adding a new record)
     * @param  ?TIME $date_to The time at which the access code expires (null: no expiration, or we are adding a new record)
     * @param  ?integer $views_allowed The maximum number of views permitted for this access code (null: no limit, or we are adding a new record)
     * @return array Array of fields, hidden
     */
    public function get_form_fields(string $access_code = null, string $label = '', string $trigger_access = '', array $content = [], ?int $date_from = null, ?int $date_to = null, ?int $views_allowed = null) : array
    {
        require_code('form_templates');
        require_code('content');
        require_code('validation');

        $fields = new Tempcode();
        $hidden = new Tempcode();

        if ($access_code === null) {
            $fields->attach(form_input_codename(do_lang_tempcode('EARLY_ACCESS_CODE'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_CODE'), 'id', $access_code, false));
        } else { // Do not allow editing the code once set
            $fields->attach(form_input_text(do_lang_tempcode('EARLY_ACCESS_CODE'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_CODE'), 'id', $access_code, false, true));
            $hidden->attach(form_input_hidden('id', $access_code));
        }

        $fields->attach(form_input_line(do_lang_tempcode('LABEL'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_CODE_LABEL'), 'label', $label, true));
        $fields->attach(form_input_codename(do_lang_tempcode('EARLY_ACCESS_TRIGGER_ACCESS'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_TRIGGER_ACCESS'), 'trigger_access', $trigger_access, false));

        // Populate content which is not validated; we can use this on the early access code
        $content_selection = new Tempcode();
        $needing_validation = get_content_needing_validation();
        foreach ($needing_validation as $_content) {
            list($content_type, $content_id) = $_content;
            list($title) = content_get_details($content_type, $content_id);
            if ($title === null) {
                continue;
            }

            $value = $content_type . '::' . $content_id;
            $content_selection->attach(form_input_list_entry($value, (in_array($value, $content)), $content_type . ': ' . $title));
        }
        $fields->attach(form_input_multi_list(do_lang_tempcode('CONTENT'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_CONTENT'), 'content', $content_selection));

        $fields->attach(form_input_date(do_lang_tempcode('FROM'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_FROM'), 'date_from', false, false, true, $date_from));
        $fields->attach(form_input_date(do_lang_tempcode('TO'), do_lang_tempcode('DESCRIPTION_EARLY_ACCESS_TO'), 'date_to', false, false, true, $date_to));
        $fields->attach(form_input_integer(do_lang_tempcode('NUM_VIEWS_ALLOWED'), do_lang_tempcode('DESCRIPTION_NUM_VIEWS_ALLOWED'), 'views_allowed', $views_allowed, false));

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries() : object
    {
        $fields = new Tempcode();

        $rows = $GLOBALS['SITE_DB']->query_select('early_access_codes', ['*'], [], ' ORDER BY c_creation_time DESC');

        foreach ($rows as $row) {
            $label = $row['c_label'] . ' (' . $row['c_access_code'] . ')';
            $fields->attach(form_input_list_entry($row['c_access_code'], false, $label));
        }

        return $fields;
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('early_access_codes', ['*'], ['c_access_code' => $id]);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $access_code = $myrow['c_access_code'];
        $label = $myrow['c_label'];
        $trigger_access = $myrow['c_trigger_access'];
        $date_from = $myrow['c_date_from'];
        $date_to = $myrow['c_date_to'];
        $views_allowed = $myrow['c_num_views_allowed'];

        $content = [];
        $content_rows = $GLOBALS['SITE_DB']->query_select('early_access_code_content', ['*'], ['a_access_code' => $id]);
        foreach ($content_rows as $row) {
            $content[] = $row['a_content_type'] . '::' . $row['a_content_id'];
        }

        $ret = $this->get_form_fields($access_code, $label, $trigger_access, $content, $date_from, $date_to, $views_allowed);

        return $ret;
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        $access_code = post_param_string('access_code', '', INPUT_FILTER_VERY_STRICT);
        if ($access_code == '') { // Blank means we want to generate one randomly
            require_code('crypt');
            $access_code = get_secure_random_string();
        }
        $access_code = filter_naughty_harsh($access_code);

        $label = post_param_string('label');
        $trigger_access = post_param_string('trigger_access', '', INPUT_FILTER_VERY_STRICT);
        $trigger_access = filter_naughty_harsh($trigger_access);

        $content = [];
        $post_content = $_POST['content'];
        foreach ($post_content as $i => $_content) {
            $__content = explode('::', $_content, 2);
            if (count($__content) != 2) { // Invalid
                continue;
            }

            $content[] = $__content;
        }

        $date_from = post_param_date('date_from');
        $date_to = post_param_date('date_to');
        $views_allowed = post_param_integer('views_allowed', null);

        require_code('early_access2');
        add_early_access_code($access_code, $label, $trigger_access, $date_from, $date_to, $views_allowed, $content);

        $hyperlink = hyperlink(page_link_to_tempcode_url(':home:keep_access_code=' . urlencode($access_code)), do_lang_tempcode('EARLY_ACCESS_CODE_USE_HYPERLINK'), true, false);
        $usage = do_lang_tempcode('EARLY_ACCESS_CODE_USE', protect_from_escaping($hyperlink), escape_html($access_code));

        return [$access_code, $usage];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $id) : ?object
    {
        $label = post_param_string('label');
        $trigger_access = post_param_string('trigger_access', '', INPUT_FILTER_VERY_STRICT);
        $trigger_access = filter_naughty_harsh($trigger_access);

        $content = [];
        $post_content = $_POST['content'];
        foreach ($post_content as $i => $_content) {
            $__content = explode('::', $_content, 2);
            if (count($__content) != 2) { // Invalid
                continue;
            }

            $content[] = $__content;
        }

        $date_from = post_param_date('date_from');
        $date_to = post_param_date('date_to');
        $views_allowed = post_param_integer('views_allowed', null);

        require_code('early_access2');
        edit_early_access_code($id, $label, $trigger_access, $date_from, $date_to, $views_allowed, $content);

        $hyperlink = hyperlink(page_link_to_tempcode_url(':home:keep_access_code=' . urlencode($id)), do_lang_tempcode('EARLY_ACCESS_CODE_USE_HYPERLINK'), true, false);
        $usage = do_lang_tempcode('EARLY_ACCESS_CODE_USE', protect_from_escaping($hyperlink), escape_html($id));

        return $usage;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation(string $id)
    {
        $name = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_access_code', ['c_access_code' => $id]);
        if ($name === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        require_code('early_access2');
        delete_early_access_code($id);
    }

    /**
     * Table UI of codes.
     *
     * @return Tempcode The result of execution
     */
    public function view() : object
    {
        require_lang('early_access');
        require_code('early_access');

        require_code('templates_results_table');
        require_code('form_templates');
        require_code('templates');
        require_code('templates_tooltip');

        require_code('content');
        require_code('urls');

        $start = get_param_integer('early_access_start', 0);
        $max = get_param_integer('early_access_max', 50);

        // Sortable validation
        $sortables = [
            'c_date_from' => do_lang_tempcode('FROM'),
            'c_date_to' => do_lang_tempcode('TO'),
            'c_num_views' => do_lang_tempcode('VIEWS'),
            'c_num_views_allowed' => do_lang_tempcode('NUM_VIEWS_ALLOWED'),
            'c_creation_time' => do_lang_tempcode('DATE_TIME')
        ];
        $test = explode(' ', get_param_string('early_access_sort', 'c_creation_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('9333df6115ba5c0fbf3e25244f158398')));
        }

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('early_access_codes', 'COUNT(*)', []);
        $rows = $GLOBALS['SITE_DB']->query_select('early_access_codes', ['*'], [], '', $max, $start);

        $result_entries = new Tempcode();

        // Table header
        $map = [
            do_lang_tempcode('CODE'),
            do_lang_tempcode('TRIGGER_ACCESS'),
            do_lang_tempcode('LABEL'),
            do_lang_tempcode('BY'),
            do_lang_tempcode('DATE_TIME'),
            do_lang_tempcode('FROM'),
            do_lang_tempcode('TO'),
            do_lang_tempcode('VIEWS'),
            do_lang_tempcode('NUM_VIEWS_ALLOWED'),
            do_lang_tempcode('ACTIONS'),
        ];
        $header_row = results_header_row($map, $sortables, 'early_access_sort', $sortable . ' ' . $sort_order);

        // Table data
        foreach ($rows as $row) {
            // Build up tooltip of content
            $tooltip_contents = new Tempcode();
            $content_rows = $GLOBALS['SITE_DB']->query_select('early_access_code_content', ['*'], ['a_access_code' => $row['c_access_code']]);
            foreach ($content_rows as $content_row) {
                list($content_title, , $info, , $content_url) = content_get_details($content_row['a_content_type'], $content_row['a_content_id']);
                if ($content_title === null) {
                    continue;
                }

                $caption = do_lang($info['content_type_label']) . ': ' . $content_title;
                $url = hyperlink($content_url, $caption, false, true);
                $tooltip_contents->attach(paragraph($url));
            }
            $access_code = tooltip($row['c_access_code'], $tooltip_contents, true);
            $trigger_access = $row['c_trigger_access'];
            $label = generate_tooltip_by_truncation(escape_html($row['c_label']));

            if (get_forum_type() == 'cns') {
                $_by_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($row['c_created_by'], true, null);
                if (!is_object($_by_url)) {
                    $by_url = make_string_tempcode($_by_url);
                } else {
                    $by_url = $_by_url;
                }
            } else {
                $_map = ['page' => 'members', 'type' => 'member', 'id' => $row['c_created_by']];
                $by_url = build_url($_map, get_module_zone('members'), [], false, false, false);
            }

            $by_hyperlink = hyperlink($by_url, $GLOBALS['FORUM_DRIVER']->get_username($row['c_created_by']), false, true);

            $date = get_timezoned_date_time($row['c_creation_time'], false);
            $date_from = do_lang('NA');
            $date_to = do_lang('NA');
            if ($row['c_date_from'] !== null) {
                $date_from = get_timezoned_date_time($row['c_date_from'], false);
            }
            if ($row['c_date_to'] !== null) {
                $date_to = get_timezoned_date_time($row['c_date_to'], false);
            }

            $views = integer_format($row['c_num_views']);
            $views_allowed = do_lang('NA');
            if ($row['c_num_views_allowed'] !== null) {
                $views_allowed = integer_format($row['c_num_views_allowed']);
            }

            $actions = new Tempcode();

            // Edit button
            $edit_url = build_url(['page' => '_SELF', 'type' => '_edit', 'id' => $row['c_access_code']], '_SELF');
            $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                '_GUID' => '805a968539d4cc73520a35b176d0d3fd',
                'URL' => $edit_url,
                'HIDDEN' => form_input_hidden('id', strval($row['c_access_code'])),
                'NAME' => '#' . strval($row['c_access_code']),
                'ACTION_TITLE' => do_lang_tempcode('EDIT'),
                'ICON' => 'admin/edit',
                'GET' => false,
            ]));

            // Delete button
            $delete_url = build_url(['page' => '_SELF', 'type' => 'delete', 'id' => $row['c_access_code']], '_SELF');
            $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                '_GUID' => '337837e2be8d4210e336b27bb44fc561',
                'NAME' => '#' . strval($row['c_access_code']),
                'URL' => $delete_url,
                'HIDDEN' => form_input_hidden('id', strval($row['c_access_code'])),
                'ACTION_TITLE' => do_lang_tempcode('DELETE'),
                'ICON' => 'buttons/no',
                'GET' => false,
            ]));

            $map = [
                $access_code,
                $trigger_access,
                $label,
                $by_hyperlink,
                $date,
                $date_from,
                $date_to,
                $views,
                $views_allowed,
                $actions,
            ];
            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('EARLY_ACCESS_CODES'), $start, 'early_access_start', $max, 'early_access_max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'early_access_sort');

        $url = build_url(['page' => '_SELF', 'type' => 'view'], '_SELF');

        $form = new Tempcode();
        $button_url = build_url(['page' => '_SELF', 'type' => 'add'], '_SELF');
        $form->attach(do_template('BUTTON_SCREEN', ['_GUID' => '3d7762361715db377a6b4149e3d25ee2', 'IMMEDIATE' => false, 'URL' => $button_url, 'TITLE' => do_lang_tempcode('ADD'), 'IMG' => 'admin/add', 'HIDDEN' => new Tempcode()]));

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '92aa93641b57c5a4be0508c571bfef17',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => $form,
            'URL' => $url,
        ]);

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }
}
