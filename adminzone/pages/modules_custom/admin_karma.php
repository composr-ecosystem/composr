<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Module page class.
 */
class Module_admin_karma
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'PDStig, LLC';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'karma';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        // Custom fields
        $GLOBALS['FORUM_DRIVER']->install_delete_custom_field('good_karma');
        $GLOBALS['FORUM_DRIVER']->install_delete_custom_field('bad_karma');

        // Privileges
        delete_privilege(['view_others_karma', 'view_bad_karma', 'has_karmic_influence', 'has_additional_karmic_influence', 'moderate_karma']);

        // Database
        $GLOBALS['SITE_DB']->drop_table_if_exists('karma');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        require_lang('karma');

        if ($upgrade_from === null) {
            // Custom fields
            $GLOBALS['FORUM_DRIVER']->install_create_custom_field('good_karma', 11, /*locked=*/1, /*viewable=*/0, /*settable=*/0, /*required=*/1, '', 'integer', 0, '0');
            $GLOBALS['FORUM_DRIVER']->install_create_custom_field('bad_karma', 11, /*locked=*/1, /*viewable=*/0, /*settable=*/0, /*required=*/1, '', 'integer', 0, '0');

            // Privileges
            add_privilege('KARMA', 'view_others_karma', true); // Default: Everyone can view each other's karma
            add_privilege('KARMA', 'view_bad_karma'); // Default: only staff can see bad karma
            add_privilege('KARMA', 'has_karmic_influence', true, false, true); // Default: Everyone except probation members can influence each other's karma
            add_privilege('KARMA', 'has_additional_karmic_influence'); // Default: Only staff are given additional influence
            add_privilege('KARMA', 'moderate_karma');

            // Database
            $GLOBALS['SITE_DB']->create_table('karma', [
                'id' => '*AUTO',
                'k_type' => 'ID_TEXT', // good|bad
                'k_member_from' => 'MEMBER',
                'k_member_to' => 'MEMBER',
                'k_amount' => 'INTEGER',
                'k_reason' => 'SHORT_TRANS__COMCODE',
                'k_content_type' => 'ID_TEXT',
                'k_content_id' => 'ID_TEXT',
                'k_date_and_time' => 'TIME',
                'k_reversed' => 'BINARY'
            ]);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmamember', ['k_member_from', 'k_member_to']);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmasystem', ['k_member_to']);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmacontent', ['k_content_type', 'k_content_id']);
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
        if (!addon_installed('karma')) {
            return null;
        }

        require_lang('karma');

        return [
            'browse' => ['KARMA', 'spare/social'],
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('karma', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('karma');

        set_helper_panel_tutorial('sup_adding_a_member_reputation_system');

        switch ($type) {
            case 'browse':
                $this->title = get_screen_title('KARMA');
                break;

            case 'view':
                $id = get_param_integer('id');
                $this->title = get_screen_title('VIEW_KARMA', true, [strval($id)]);
                breadcrumb_set_self(do_lang_tempcode('VIEW_KARMA', strval($id)));
                breadcrumb_set_parents([['_SELF:_SELF:browse', do_lang_tempcode('KARMA')]]);
                break;

            case 'edit':
            case '_edit':
                $id = post_param_integer('id');
                breadcrumb_set_parents([['_SELF:_SELF:browse', do_lang_tempcode('KARMA')], ['_SELF:_SELF:view:' . strval($id), do_lang_tempcode('VIEW_KARMA', strval($id))]]);
                $this->title = get_screen_title('EDIT_KARMA', true, [strval($id)]);
                breadcrumb_set_self(do_lang_tempcode('EDIT_KARMA', strval($id)));
                break;

            case 'delete':
            case '_delete':
                $id = post_param_integer('id');
                breadcrumb_set_parents([['_SELF:_SELF:browse', do_lang_tempcode('KARMA')], ['_SELF:_SELF:view:' . strval($id), do_lang_tempcode('VIEW_KARMA', strval($id))]]);
                $this->title = get_screen_title('REVERSE_KARMA', true, [strval($id)]);
                breadcrumb_set_self(do_lang_tempcode('REVERSE_KARMA', strval($id)));
                break;
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('karma', $error_msg)) {
            return $error_msg;
        }

        require_code('karma');
        require_code('form_templates');

        $type = get_param_string('type', 'browse');

        switch ($type) {
            case 'browse':
                return $this->browse();
            case 'view':
                return $this->view();
            case 'edit':
                return $this->edit();
            case '_edit':
                return $this->_edit();
            case 'delete':
                return $this->delete();
            case '_delete':
                return $this->_delete();
        }

        return new Tempcode();
    }

    /**
     * Karma records interface.
     *
     * @return Tempcode The result of execution
     */
    public function browse() : object
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        require_lang('karma');
        require_code('karma');
        require_code('templates_results_table');
        require_code('form_templates');

        $start = get_param_integer('karma_start', 0);
        $max = get_param_integer('karma_max', 50);

        // Filter parameters
        $filter_username = get_param_string('filter_karma_username', null, INPUT_FILTER_NONE);
        $filter_type = get_param_string('filter_karma_type', 'all');
        $filter_from = post_param_date('filter_karma_from', true);
        $filter_to = post_param_date('filter_karma_to', true);

        // Filter validation
        $member_id = null;
        if ($filter_username !== null) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($filter_username);
            if ($member_id === null) {
                attach_message(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($filter_username)), 'warn');
            }
        }

        // Sortable validation
        $sortables = ['k_date_and_time' => do_lang_tempcode('DATE_TIME')];

        $test = explode(' ', get_param_string('karma_sort', 'k_date_and_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }

        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        list($max_rows, $rows) = karma_get_logs($filter_type, $member_id, null, $max, $start, $sortable, $sort_order, null, $filter_from, $filter_to);
        $result_entries = new Tempcode();

        $map = [
            do_lang_tempcode('IDENTIFIER'),
            do_lang_tempcode('DATE_TIME'),
            do_lang_tempcode('TYPE'),
            do_lang_tempcode('AMOUNT'),
            do_lang_tempcode('MEMBER'),
            do_lang_tempcode('KARMA_INFLUENCER'),
            do_lang_tempcode('REASON'),
            do_lang_tempcode('STATUS'),
            do_lang_tempcode('ACTIONS'),
        ];
        $header_row = results_header_row($map, $sortables, 'karma_sort', $sortable . ' ' . $sort_order);

        foreach ($rows as $myrow) {
            $date = get_timezoned_date_time($myrow['k_date_and_time'], false);
            $reason = get_translated_tempcode('karma', $myrow, 'k_reason');
            $_date = hyperlink(build_url(['page' => '_SELF', 'type' => 'view', 'id' => $myrow['id']], '_SELF'), $date, false, true);

            // Hyperlink member and influencer
            if (is_guest($myrow['k_member_to'])) {
                $to = do_lang_tempcode('USER_SYSTEM');
            } else {
                $to_name = $GLOBALS['FORUM_DRIVER']->get_username($myrow['k_member_to'], false, USERNAME_DEFAULT_NULL);

                if (get_forum_type() == 'cns') {
                    $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($myrow['k_member_to'], true, null);
                    if (!is_object($url)) {
                        $url = make_string_tempcode($url);
                    }
                } else {
                    $_map = ['page' => 'members', 'type' => 'member', 'id' => $myrow['k_member_to']];
                    $url = build_url($_map, get_module_zone('members'), [], false, false, false);
                }

                $to = ($to_name === null) ? do_lang_tempcode('UNKNOWN_EM') : hyperlink($url, $to_name, false, true);
            }
            if (is_guest($myrow['k_member_from'])) {
                $from = do_lang_tempcode('USER_SYSTEM');
            } else {
                $from_name = $GLOBALS['FORUM_DRIVER']->get_username($myrow['k_member_from'], false, USERNAME_DEFAULT_NULL);

                if (get_forum_type() == 'cns') {
                    $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($myrow['k_member_from'], true, null);
                    if (!is_object($url)) {
                        $url = make_string_tempcode($url);
                    }
                } else {
                    $_map = ['page' => 'members', 'type' => 'member', 'id' => $myrow['k_member_from']];
                    $url = build_url($_map, get_module_zone('members'), [], false, false, false);
                }

                $from = ($from_name === null) ? do_lang_tempcode('UNKNOWN_EM') : hyperlink($url, $from_name, false, true);
            }

            $actions = new Tempcode();

            // Undo button
            if ($myrow['k_reversed'] == 0) {
                $delete_url = build_url(['page' => '_SELF', 'type' => 'delete', 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
                $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                    '_GUID' => '3d4444df14b8298a55d3d11ef19763bc',
                    'NAME' => '#' . strval($myrow['id']),
                    'URL' => $delete_url,
                    'HIDDEN' => form_input_hidden('id', strval($myrow['id'])),
                    'ACTION_TITLE' => do_lang_tempcode('UNDO'),
                    'ICON' => 'buttons/undo',
                    'GET' => false,
                ]));
            }

            // Edit / amend button
            $edit_url = build_url(['page' => '_SELF', 'type' => 'edit', 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
            $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                '_GUID' => '25178125de647e0736c3370db77078dc',
                'URL' => $edit_url,
                'HIDDEN' => form_input_hidden('id', strval($myrow['id'])),
                'NAME' => '#' . strval($myrow['id']),
                'ACTION_TITLE' => do_lang_tempcode('EDIT'),
                'ICON' => 'admin/edit',
                'GET' => false,
            ]));

            $map = [
                $myrow['id'],
                $_date,
                $myrow['k_type'], // TODO: more intuitive via language strings? Maybe icons?
                integer_format($myrow['k_amount']),
                $to,
                $from,
                $reason,
                do_lang_tempcode(($myrow['k_reversed'] == 0) ? 'KARMA_STATUS_ACTIVE' : 'KARMA_STATUS_REVERSED'),
                $actions,
            ];

            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('KARMA'), $start, 'karma_start', $max, 'karma_max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'karma_sort', paragraph(do_lang_tempcode('KARMA_LOG_HEAD')));

        // Start building fields for the filter box
        push_field_encapsulation(FIELD_ENCAPSULATION_RAW);

        $log_types = new Tempcode();
        $log_types->attach(form_input_list_entry('all', ($filter_type == 'all'), do_lang_tempcode('KARMA_FILTER_ALL')));
        $log_types->attach(form_input_list_entry('sender', ($filter_type == 'sender'), do_lang_tempcode('KARMA_FILTER_SENDER')));
        $log_types->attach(form_input_list_entry('recipient', ($filter_type == 'recipient'), do_lang_tempcode('KARMA_FILTER_RECIPIENT')));
        $log_types->attach(form_input_list_entry('sender_recipient', ($filter_type == 'sender_recipient'), do_lang_tempcode('KARMA_FILTER_SENDER_RECIPIENT')));
        $log_types->attach(form_input_list_entry('credit', ($filter_type == 'credit'), do_lang_tempcode('KARMA_FILTER_CREDIT')));

        $filters_row_a = [
            [
                'PARAM' => 'filter_karma_username',
                'LABEL' => do_lang_tempcode('MEMBER'),
                'FIELD' => form_input_username(do_lang_tempcode('MEMBER'), new Tempcode(), 'filter_karma_username', $filter_username, false),
            ],
            [
                'PARAM' => 'filter_karma_type',
                'LABEL' => do_lang_tempcode('TYPE'),
                'FIELD' => form_input_list(do_lang_tempcode('TYPE'), new Tempcode(), 'filter_karma_type', $log_types, null, false, false),
            ],
        ];

        $filters_row_b = [
            [
                'PARAM' => 'filter_karma_from',
                'LABEL' => do_lang_tempcode('FROM'),
                'FIELD' => form_input_date(do_lang_tempcode('FROM'), new Tempcode(), 'filter_karma_from', false, false, true, $filter_from),
            ],
            [
                'PARAM' => 'filter_karma_to',
                'LABEL' => do_lang_tempcode('TO'),
                'FIELD' => form_input_date(do_lang_tempcode('TO'), new Tempcode(), 'filter_karma_to', false, false, true, $filter_to),
            ],
        ];

        $url = build_url(['page' => '_SELF', 'type' => 'browse'], '_SELF');

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => 'ee1b42664dfe09da882d2a2173c2d749',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => new Tempcode(),
            'FILTERS_ROW_A' => $filters_row_a,
            'FILTERS_ROW_B' => $filters_row_b,
            'URL' => $url,
            'FILTERS_HIDDEN' => new Tempcode(),
        ]);

        pop_field_encapsulation();

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }

    /**
     * View an individual karma record
     *
     * @return Tempcode The result of execution
     */
    public function view()
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        $id = get_param_integer('id');

        $_row = $GLOBALS['SITE_DB']->query_select('karma', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];
        $reason = get_translated_tempcode('karma', $row, 'k_reason');

        require_lang('karma');
        require_code('templates_map_table');

        $status = do_lang_tempcode(($row['k_reversed'] == 0) ? 'KARMA_STATUS_ACTIVE' : 'KARMA_STATUS_REVERSED');
        $date = get_timezoned_date_time($row['k_date_and_time'], false);

        // Hyperlink member and influencer
        if (is_guest($row['k_member_to'])) {
            $to = do_lang_tempcode('USER_SYSTEM');
        } else {
            $to_name = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_to'], false, USERNAME_DEFAULT_NULL);

            if (get_forum_type() == 'cns') {
                $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($row['k_member_to'], true, null);
                if (!is_object($url)) {
                    $url = make_string_tempcode($url);
                }
            } else {
                $map = ['page' => 'members', 'type' => 'member', 'id' => $row['k_member_to']];
                $url = build_url($map, get_module_zone('members'), [], false, false, false);
            }

            $to = ($to_name === null) ? do_lang_tempcode('UNKNOWN_EM') : hyperlink($url, $to_name, false, true);
        }
        if (is_guest($row['k_member_from'])) {
            $from = do_lang_tempcode('USER_SYSTEM');
        } else {
            $from_name = $GLOBALS['FORUM_DRIVER']->get_username($row['k_member_from'], false, USERNAME_DEFAULT_NULL);

            if (get_forum_type() == 'cns') {
                $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($row['k_member_from'], true, null);
                if (!is_object($url)) {
                    $url = make_string_tempcode($url);
                }
            } else {
                $map = ['page' => 'members', 'type' => 'member', 'id' => $row['k_member_from']];
                $url = build_url($map, get_module_zone('members'), [], false, false, false);
            }

            $from = ($from_name === null) ? do_lang_tempcode('UNKNOWN_EM') : hyperlink($url, $from_name, false, true);
        }

        $buttons = new Tempcode();
        if ($row['k_reversed'] == 0) {
            $delete_url = build_url(['page' => '_SELF', 'type' => 'delete', 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
            $buttons->attach(do_template('BUTTON_SCREEN', [
                '_GUID' => '9c76feeb72b59a9fe9ead5519f249c1c',
                'IMMEDIATE' => true,
                'HIDDEN' => form_input_hidden('id', strval($row['id'])),
                'URL' => $delete_url,
                'TITLE' => do_lang_tempcode('UNDO'),
                'IMG' => 'buttons/undo',
            ]));
        }

        $edit_url = build_url(['page' => '_SELF', 'type' => 'edit', 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
        $buttons->attach(do_template('BUTTON_SCREEN', [
            '_GUID' => '492feada39e89665cf5a91cc2a9cd720',
            'IMMEDIATE' => true,
            'HIDDEN' => form_input_hidden('id', strval($row['id'])),
            'URL' => $edit_url,
            'TITLE' => do_lang_tempcode('EDIT'),
            'IMG' => 'admin/edit',
        ]));

        $fields = [
            'IDENTIFIER' => strval($id),
            'DATE' => $date,
            'TYPE' => $row['k_type'], // TODO: language string? icon?
            'AMOUNT' => integer_format($row['k_amount']),
            'MEMBER' => $to,
            'KARMA_INFLUENCER' => $from,
            'REASON' => $reason,
            'STATUS' => $status,
        ];

        $title = get_screen_title('KARMA', true, [strval($id)]);

        return map_table_screen($title, $fields, true, null, $buttons, true);
    }

    /**
     * The UI to edit a karma record.
     *
     * @return Tempcode The UI
     */
    public function edit() : object
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        $id = post_param_integer('id');
        $redirect = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);

        $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], ['id' => $id], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];
        $myreason = get_translated_text($myrow['k_reason']);

        // Show the UI
        require_code('form_templates');

        $fields = build_keep_post_fields();
        $fields->attach(form_input_line_comcode(do_lang('REASON'), do_lang('DESCRIPTION_KARMA_REASON'), 'reason', $myreason, true));

        $map = ['page' => '_SELF', 'type' => '_edit', 'redirect' => protect_url_parameter($redirect)];
        $url = build_url($map, '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => 'd9fd21ef566129468ea4297716966258',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $url,
            'JS_FUNCTION_CALLS' => [],
        ]);
    }

    /**
     * The actualiser to edit a karma record.
     *
     * @return Tempcode The UI
     */
    public function _edit() : object
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        $id = post_param_integer('id');
        $reason = post_param_string('reason');
        $redirect = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);

        // Edit reason
        $_reason = lang_remap_comcode('k_reason', $id, $reason);
        $GLOBALS['SITE_DB']->query_update('karma', $_reason, ['id' => $id], '', 1);

        // Log it
        log_it('AMEND_KARMA', strval($id), $reason);

        // Show it worked / Refresh
        if ($redirect == '') {
            $_redirect = build_url(['page' => '_SELF', 'type' => 'view', 'id' => strval($id)], '_SELF');
            $redirect = $_redirect->evaluate();
        }
        return redirect_screen($this->title, $redirect, do_lang_tempcode('SUCCESS'));
    }

    /**
     * The UI to reverse a point transaction.
     *
     * @return Tempcode The UI
     */
    public function delete() : object
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        $id = post_param_integer('id');
        $redirect = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);

        $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], ['id' => $id], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $myrow = $rows[0];
        $amount = $myrow['k_amount'];
        $type = do_lang_tempcode(($myrow['k_type'] == 'bad') ? 'BAD_KARMA' : 'GOOD_KARMA');
        $member_id = $myrow['k_member_to'];
        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);

        $preview = do_lang_tempcode('ARE_YOU_SURE_REVERSE_KARMA', integer_format($amount), $type, escape_html($username));

        $map = ['page' => '_SELF', 'type' => '_delete', 'redirect' => protect_url_parameter($redirect)];
        $url = build_url($map, '_SELF');

        return do_template('CONFIRM_SCREEN', [
            '_GUID' => 'c2b52b64793eb7a12015d7915024e6ff',
            'TITLE' => $this->title,
            'PREVIEW' => $preview,
            'URL' => $url,
            'FIELDS' => build_keep_post_fields(),
        ]);
    }

    /**
     * The actualiser to reverse karma.
     *
     * @return Tempcode The UI
     */
    public function _delete() : object
    {
        if (!has_privilege(get_member(), 'moderate_karma')) {
            access_denied('PRIVILEGE', 'moderate_karma');
        }

        $id = post_param_integer('id');

        $rows = $GLOBALS['SITE_DB']->query_select('karma', ['*'], ['id' => $id], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        // Actually reverse karma
        require_code('karma2');
        reverse_karma($id);

        // Show it worked / Refresh
        $url = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);
        if ($url == '') {
            $_url = build_url(['page' => '_SELF', 'type' => 'view', 'id' => strval($id)], '_SELF');
            $url = $_url->evaluate();
        }
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
