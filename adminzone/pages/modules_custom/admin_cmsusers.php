<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Module page class.
 */
class Module_admin_cmsusers
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 7;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'cms_homesite';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'may_feature',
            'logged',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
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
            $GLOBALS['SITE_DB']->create_table('may_feature', [
                'id' => '*AUTO',
                'url' => 'URLPATH',
            ]);

            $GLOBALS['SITE_DB']->create_table('logged', [
                'id' => '*AUTO',
                'website_url' => 'URLPATH',
                'website_name' => 'SHORT_TEXT',
                'l_version' => 'ID_TEXT',
                'hittime' => 'TIME',
                'count_members' => 'INTEGER',
                'num_hits_per_day' => 'INTEGER',
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 2)) { // LEGACY
            $GLOBALS['SITE_DB']->rename_table('mayfeature', 'may_feature');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('logged', 'num_members', 'INTEGER');
            $GLOBALS['SITE_DB']->add_table_field('logged', 'num_hits_per_day', 'INTEGER');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'is_registered');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'log_key');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'expire');
        }

        if (($upgrade_from === null) || ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->create_table('relayed_errors', [
                'id' => '*AUTO',
                'first_date_and_time' => 'TIME',
                'last_date_and_time' => 'TIME',
                'website_url' => 'URLPATH',
                'e_version' => 'ID_TEXT',
                'error_message' => 'LONG_TEXT',
                'error_hash' => 'SHORT_TEXT',
                'error_count' => 'INTEGER',
                'resolved' => 'BINARY',
                'note' => 'LONG_TRANS__COMCODE',
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 4)) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('relayed_errors', 'error_hash', 'SHORT_TEXT');
        }
        if (($upgrade_from !== null) && ($upgrade_from < 5)) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('relayed_errors', 'note', 'LONG_TRANS__COMCODE');
        }

        if (($upgrade_from === null) || ($upgrade_from < 6)) {
            $GLOBALS['SITE_DB']->create_table('relayed_errors_ignore', [
                'id' => '*AUTO',
                'ignore_string' => 'SHORT_TEXT',
                'resolve_message' => 'LONG_TRANS__COMCODE',
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 7)) { // LEGACY: 11.beta1
            // Database consistency fixes
            $GLOBALS['SITE_DB']->alter_table_field('logged', 'num_members', 'INTEGER', 'count_members');
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
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        return [
            'browse' => ['CMS_SITES_INSTALLED', 'admin/tool'],
            'errors' => ['CMS_SITE_ERRORS', 'admin/tool'],
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
        if (!addon_installed__messaged('cms_homesite', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        if (($type == 'error') || ($type == 'resolve_error')) {
            breadcrumb_set_parents([['_SELF:_SELF:errors', do_lang_tempcode('CMS_SITE_ERRORS')]]);
        }

        if (($type == 'ignore_errors') || ($type == 'ignore_error') || ($type == 'ignore_error_delete')) {
            breadcrumb_set_parents([['_SELF:_SELF:ignore_errors', do_lang_tempcode('TELEMETRY_IGNORE_TITLE')]]);
        }

        require_lang('cms_homesite');

        $this->title = get_screen_title('CMS_SITES_INSTALLED');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        require_code('cms_homesite');
        require_code('form_templates');

        $type = get_param_string('type', 'browse');
        if ($type == 'browse') {
            return $this->users();
        }
        if ($type == 'errors') {
            $this->title = get_screen_title('CMS_SITE_ERRORS');
            return $this->errors();
        }
        if ($type == 'error') {
            $id = get_param_integer('id');
            $this->title = get_screen_title('CMS_SITE_ERROR', true, [integer_format($id)]);
            return $this->error($id);
        }
        if ($type == 'resolve_error') {
            $id = get_param_integer('id');
            $this->title = get_screen_title('CMS_SITE_RESOLVE_ERROR', true, [integer_format($id)]);
            return $this->resolve_error($id);
        }
        if ($type == '_resolve_error') {
            $id = get_param_integer('id');
            $this->title = get_screen_title('CMS_SITE_RESOLVE_ERROR', true, [integer_format($id)]);
            return $this->_resolve_error($id);
        }
        if ($type == 'ignore_errors') {
            $this->title = get_screen_title('TELEMETRY_IGNORE_TITLE');
            return $this->ignore_errors();
        }
        if ($type == 'ignore_error') {
            $id = get_param_integer('id', null);
            $this->title = get_screen_title('TELEMETRY_IGNORE_ITEM_TITLE');
            return $this->ignore_error($id);
        }
        if ($type == '_ignore_error') {
            $id = get_param_integer('id', null);
            $this->title = get_screen_title('TELEMETRY_IGNORE_ITEM_TITLE');
            return $this->_ignore_error($id);
        }
        if ($type == 'ignore_error_delete') {
            $id = get_param_integer('id');
            $this->title = get_screen_title('TELEMETRY_IGNORE_ITEM_DELETE_TITLE');
            return $this->ignore_error_delete($id);
        }
        return new Tempcode();
    }

    /**
     * List of sites that have installed Composr.
     *
     * @return Tempcode The result of execution
     */
    public function users() : object
    {
        require_code('templates_results_table');
        require_code('form_templates');

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 50);

        // Sortable validation
        $sortables = [
            'hittime' => do_lang_tempcode('CMS_LAST_ADMIN_ACCESS'),
            'l_version' => do_lang_tempcode('CMS_VERSION'),
            'count_members' => do_lang_tempcode('CMS_COUNT_MEMBERS'),
            'num_hits_per_day' => do_lang_tempcode('CMS_HITS_24_HRS'),
        ];
        $test = explode(' ', get_param_string('sort', 'hittime DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }
        $order_by = 'ORDER BY ' . $sortable . ' ' . $sort_order;

        $select = 'website_url,MAX(l_version) AS l_version,MAX(hittime) AS hittime,MAX(count_members) AS count_members,MAX(num_hits_per_day) AS num_hits_per_day';
        $where = 'website_url NOT LIKE \'%.composr.info%\''; // LEGACY
        if (!$GLOBALS['DEV_MODE']) {
            // Ignore local installs
            $where .= ' AND ' . db_string_not_equal_to('website_url', '%://localhost%') . ' AND ' . db_string_not_equal_to('website_url', '%://127.0.0.1%') . ' AND ' . db_string_not_equal_to('website_url', '%://192.168.%') . ' AND ' . db_string_not_equal_to('website_url', '%://10.0.%');
        }
        $sql = 'SELECT ' . $select . ' FROM ' . get_table_prefix() . 'logged WHERE ' . $where . ' GROUP BY website_url ' . $order_by;
        $rows = $GLOBALS['SITE_DB']->query($sql, $max, $start);
        $_max_rows = $GLOBALS['SITE_DB']->query('SELECT COUNT(*) AS num_sites FROM (SELECT DISTINCT website_url FROM ' . get_table_prefix() . 'logged WHERE ' . $where . ') AS logged_sites');
        $max_rows = $_max_rows[0]['num_sites'];

        $map = [
            do_lang_tempcode('CMS_WEBSITE_NAME'),
            do_lang_tempcode('CMS_LAST_ADMIN_ACCESS'),
            do_lang_tempcode('CMS_STILL_INSTALLED'),
            do_lang_tempcode('CMS_VERSION'),
            do_lang_tempcode('CMS_PRIVACY'),
            do_lang_tempcode('CMS_COUNT_MEMBERS'),
            do_lang_tempcode('CMS_HITS_24_HRS'),
        ];
        $header_row = results_header_row($map, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $result_entries = new Tempcode();

        foreach ($rows as $i => $r) {
            // Test that they give feature permission
            $url_parts = cms_parse_url_safe($r['website_url']);
            if (!array_key_exists('host', $url_parts)) {
                continue;
            }
            $perm = $GLOBALS['SITE_DB']->query_select_value_if_there('may_feature', 'id', ['url' => $url_parts['scheme'] . '://' . $url_parts['host']]);
            if (($perm === null) && (get_param_integer('no_feature', 0) == 1)) {
                continue;
            }

            $rt = [];

            $rt['HITTIME'] = intval(round((time() - $r['hittime']) / 60 / 60));
            $rt['HITTIME_2'] = intval(round((time() - $r['hittime']) / 60 / 60 / 24));

            if ($rt['HITTIME_2'] < 365) { // Do not check install status of sites inactive for more than a year; no use
                $active = get_value_newer_than('testing__' . $r['website_url'] . '/data/installed.php', time() - 60 * 60 * 24, true);
                if ($active === null) {
                    $test = cms_http_request($r['website_url'] . '/data/installed.php', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'byte_limit' => (1024 * 4), 'ua' => get_brand_base_url() . ' install stats', 'timeout' => 3.0]);
                    if ($test->data === 'Yes') {
                        $active = do_lang('YES');
                    } else {
                        $active = @strval($test->message);
                        if ($active == '') {
                            $active = do_lang('NO');
                        } else {
                            $active .= do_lang('CMS_WHEN_CHECKING');
                        }
                    }
                    set_value('testing__' . $r['website_url'] . '/data/installed.php', $active, true);
                }
                $rt['CMS_ACTIVE'] = $active;
            } else {
                $rt['CMS_ACTIVE'] = do_lang('CMS_CHECK_LIMIT');
            }

            $rt['NOTE'] = $perm ? do_lang('CMS_MAY_FEATURE') : do_lang('CMS_KEEP_PRIVATE');

            $rt['NUM_MEMBERS'] = integer_format($r['count_members']);

            $rt['NUM_HITS_PER_DAY'] = integer_format($r['num_hits_per_day']);

            $current = $GLOBALS['SITE_DB']->query_select('logged', ['website_name', 'l_version', 'count_members', 'num_hits_per_day', 'hittime'], ['website_url' => $r['website_url']], ' ORDER BY hittime DESC', 1);

            $map = [
                hyperlink($r['website_url'], $current[0]['website_name'], true, true, $r['website_url']),
                do_lang_tempcode(
                    '_CMS_LAST_ADMIN_ACCESS',
                    escape_html(do_lang('_AGO', do_lang('DAYS', integer_format($rt['HITTIME_2'])))),
                    escape_html(do_lang('_AGO', do_lang('HOURS', integer_format($rt['HITTIME']))))
                ),
                $rt['CMS_ACTIVE'],
                do_lang_tempcode('CMS_VALUE_WITH_MAX', escape_html($current[0]['l_version']), escape_html($r['l_version'])),
                $rt['NOTE'],
                do_lang_tempcode('CMS_VALUE_WITH_MAX', escape_html(integer_format($current[0]['count_members'])), escape_html($rt['NUM_MEMBERS'])),
                do_lang_tempcode('CMS_VALUE_WITH_MAX', escape_html(integer_format($current[0]['num_hits_per_day'])), escape_html($rt['NUM_HITS_PER_DAY'])),
            ];

            $td_class = '';
            if ($rt['CMS_ACTIVE'] == do_lang('CMS_CHECK_LIMIT')) {
                $td_class = 'critical'; // Very likely no longer exists / is a dead site
            } elseif (($rt['HITTIME_2'] >= 30) && ($rt['CMS_ACTIVE'] != do_lang('YES'))) {
                $td_class = 'disabled'; // Probably uninstalled
            } elseif ($rt['NOTE'] == do_lang('CMS_MAY_FEATURE')) {
                $td_class = 'debug'; // Can be featured
            }

            $result_entries->attach(results_entry($map, true, null, '', $td_class));
        }

        $results_table = results_table(do_lang_tempcode('CMS_SITES_INSTALLED'), $start, 'start', $max, 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'sort');

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '869126427270bea53365b807dfbb6878',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,

            // TODO: get rid of the need for these in the Tempcode
            'FILTERS_ROW_A' => new Tempcode(),
            'FILTERS_ROW_B' => new Tempcode(),
            'FILTERS_HIDDEN' => new Tempcode(),
        ]);

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }

    /**
     * List of errors reported by telemetry.
     *
     * @return Tempcode The result of execution
     */
    public function errors()
    {
        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 50);

        // Filter parameters
        $filter_website = get_param_string('filter_website', '');
        $filter_error_message = get_param_string('filter_error_message', '');
        $filter_show_resolved = get_param_string('filter_show_resolved', 0);
        //$filter_from = post_param_date('filter_from', true);
        //$filter_to = post_param_date('filter_to', true);

        // Build WHERE query with filters
        $where = [];
        $end = '';
        if ($filter_website != '') {
            $end .= ' AND website_url LIKE \'' . db_encode_like('%' . $filter_website . '%') . '\'';
        }
        if ($filter_error_message != '') {
            $end .= ' AND error_message LIKE \'' . db_encode_like('%' . $filter_error_message . '%') . '\'';
        }
        if ($filter_show_resolved == 0) {
            $where['resolved'] = 0;
        }

        // Query
        $max_rows = $GLOBALS['SITE_DB']->query_select_value('relayed_errors', 'COUNT(*)', $where, $end);
        $sortables = [
            'website_url' => do_lang_tempcode('URL'),
            'first_date_and_time' => do_lang_tempcode('FIRST_REPORTED'),
            'last_date_and_time' => do_lang_tempcode('LAST_REPORTED'),
            'e_version' => do_lang_tempcode('VERSION'),
            'error_count' => do_lang_tempcode('TIMES_REPORTED'),
        ];
        $test = explode(' ', get_param_string('sort', 'last_date_and_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }
        $rows = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], $where, $end . ' ORDER BY ' . $sortable . ' ' . $sort_order, $max, $start);

        // Build results table
        $result_entries = new Tempcode();

        require_code('templates_results_table');
        require_code('templates_tooltip');
        require_code('temporal');

        $map = [
            do_lang_tempcode('IDENTIFIER'),
            do_lang_tempcode('URL'),
            do_lang_tempcode('ERROR_SUMMARY'),
            do_lang_tempcode('FIRST_REPORTED'),
            do_lang_tempcode('LAST_REPORTED'),
            do_lang_tempcode('VERSION'),
            do_lang_tempcode('TIMES_REPORTED'),
            do_lang_tempcode('ACTIONS'),
        ];
        $header_row = results_header_row($map, $sortables, 'sort', $sortable . ' ' . $sort_order);

        foreach ($rows as $myrow) {
            $id = hyperlink(build_url(['page' => '_SELF', 'type' => 'error', 'id' => $myrow['id']], '_SELF'), '#' . integer_format($myrow['id']), false, true);
            $website_url = hyperlink($myrow['website_url'], $myrow['website_url'], true, true);
            $summary = generate_tooltip_by_truncation($myrow['error_message'], 160);
            $first_date = get_timezoned_date_time($myrow['first_date_and_time'], false);
            $last_date = get_timezoned_date_time($myrow['last_date_and_time'], false);

            $actions = new Tempcode();

            if ($myrow['resolved'] == 0) {
                $resolve_url = build_url(['page' => '_SELF', 'type' => 'resolve_error', 'id' => $myrow['id']], '_SELF');
                $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                    '_GUID' => '1e30e4f5fcc295e0320eaced5d18e03c',
                    'NAME' => '#' . strval($myrow['id']),
                    'URL' => $resolve_url,
                    'HIDDEN' => new Tempcode(),
                    'ACTION_TITLE' => do_lang_tempcode('MARK_RESOLVED'),
                    'ICON' => 'buttons/close',
                    'GET' => true,
                ]));
            }

            $map = [
                $id,
                $website_url,
                $summary,
                $first_date,
                $last_date,
                $myrow['e_version'],
                integer_format($myrow['error_count']),
                $actions
            ];

            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('CMS_SITE_ERRORS'), $start, 'start', $max, 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'sort', paragraph(do_lang_tempcode('DESCRIPTION_CMS_SITE_ERRORS')));

        // Start building fields for the filter box
        push_field_encapsulation(FIELD_ENCAPSULATION_RAW);

        $filters_row_a = [
            [
                'PARAM' => 'filter_website',
                'LABEL' => do_lang_tempcode('URL'),
                'FIELD' => form_input_line(do_lang_tempcode('URL'), new Tempcode(), 'filter_website', $filter_website, false),
            ],
            [
                'PARAM' => 'filter_error_message',
                'LABEL' => do_lang_tempcode('ERROR_SUMMARY'),
                'FIELD' => form_input_line(do_lang_tempcode('ERROR_SUMMARY'), new Tempcode(), 'filter_error_message', $filter_error_message, false),
            ],
        ];

        $form = new Tempcode();
        $button_url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        $form->attach(do_template('BUTTON_SCREEN', ['_GUID' => '318957ab73112a21637cd04627e2408d', 'IMMEDIATE' => false, 'URL' => $button_url, 'TITLE' => do_lang_tempcode('TELEMETRY_AUTORESOLVE'), 'IMG' => 'admin/delete2', 'HIDDEN' => new Tempcode()]));

        $url = build_url(['page' => '_SELF', 'type' => 'errors'], '_SELF');

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '358ae22e7f23a3f68eac4aa1e24df85b',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => $form,
            'FILTERS_ROW_A' => $filters_row_a,
            'FILTERS_ROW_B' => new Tempcode(),
            'URL' => $url,
            'FILTERS_HIDDEN' => new Tempcode(),
        ]);

        pop_field_encapsulation();

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }

    /**
     * Display a single relayed error message.
     *
     * @param  int $id The error message to display
     * @return Tempcode The UI
     */
    public function error(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        require_code('templates_map_table');
        require_code('temporal');

        $formatted_id = '#' . integer_format($row['id']);
        $website_url = hyperlink($row['website_url'], $row['website_url'], true, true);
        $first_date = get_timezoned_date_time($row['first_date_and_time'], false);
        $last_date = get_timezoned_date_time($row['last_date_and_time'], false);
        $resolved = ($row['resolved'] == 1);

        $buttons = new Tempcode();

        if ($row['resolved'] == 0) {
            $resolve_url = build_url(['page' => '_SELF', 'type' => 'resolve_error', 'id' => $id], '_SELF');
            $buttons->attach(do_template('BUTTON_SCREEN', [
                '_GUID' => '5721066370f5f9fbd8f43621a54628ed',
                'IMMEDIATE' => true,
                'HIDDEN' => new Tempcode(),
                'URL' => $resolve_url,
                'TITLE' => do_lang_tempcode('MARK_RESOLVED'),
                'IMG' => 'buttons/close',
            ]));
        }

        $fields = [
            'IDENTIFIER' => $formatted_id,
            'URL' => $website_url,
            'ERROR_MESSAGE' => $row['error_message'],
            'FIRST_REPORTED' => $first_date,
            'LAST_REPORTED' => $last_date,
            'VERSION' => $row['e_version'],
            'TIMES_REPORTED' => integer_format($row['error_count']),
            'RESOLVED' => $resolved ? do_lang('YES') : do_lang('NO')
        ];

        $title = get_screen_title('CMS_SITE_ERROR', true, [integer_format($id)]);

        return map_table_screen($title, $fields, true, null, $buttons, true);
    }

    /**
     * The UI for resolving an error message.
     *
     * @param  integer $id The ID of the error to resolve
     * @return Tempcode The UI
     */
    public function resolve_error(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        $note = get_translated_text($row['note']);

        require_code('form_templates');

        $fields = new Tempcode();

        $fields->attach(form_input_tick(do_lang_tempcode('MARK_RESOLVED'), do_lang_tempcode('DESCRIPTION_MARK_RESOLVED'), 'resolved', false));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_RELAYED_ERROR_NOTES'), 'note', $note, false));

        $resolve_url = build_url(['page' => '_SELF', 'type' => '_resolve_error', 'id' => $id], '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => '904b2916eea66a19f6906842c81da308',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $resolve_url,
        ]);
    }

    /**
     * The actualiser for resolving an error message.
     *
     * @param  integer $id The ID of the error to resolve
     * @return Tempcode The results
     */
    public function _resolve_error(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        $resolved = post_param_integer('resolved', 0);
        $new_note = post_param_string('note');

        // Actualiser
        $map = ['resolved' => $resolved];
        $map += lang_remap_comcode('note', $row['note'], $new_note);
        $GLOBALS['SITE_DB']->query_update('relayed_errors', $map, ['id' => $id]);
        $url = build_url(['page' => '_SELF', 'type' => 'errors'], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }

    /**
     * List of strings to ignore in relayed errors.
     *
     * @return Tempcode The result of execution
     */
    public function ignore_errors()
    {
        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 50);

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('relayed_errors_ignore', 'COUNT(*)');
        $sortables = [
            'id' => do_lang_tempcode('IDENTIFIER'),
            'ignore_string' => do_lang_tempcode('TELEMETRY_IGNORE_STRING'),
        ];
        $test = explode(' ', get_param_string('sort', 'id DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }
        $rows = $GLOBALS['SITE_DB']->query_select('relayed_errors_ignore', ['*'], [], ' ORDER BY ' . $sortable . ' ' . $sort_order, $max, $start);

        // Build results table
        $result_entries = new Tempcode();

        require_code('templates_results_table');

        $map = [
            do_lang_tempcode('IDENTIFIER'),
            do_lang_tempcode('TELEMETRY_IGNORE_STRING'),
            do_lang_tempcode('ACTIONS'),
        ];
        $header_row = results_header_row($map, $sortables, 'sort', $sortable . ' ' . $sort_order);

        foreach ($rows as $myrow) {
            $actions = new Tempcode();

            $edit_url = build_url(['page' => '_SELF', 'type' => 'ignore_error', 'id' => $myrow['id']], '_SELF');
            $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                '_GUID' => '8e3bd4338b06b1d2d4d6a363cdbb77a2',
                'NAME' => '#' . strval($myrow['id']),
                'URL' => $edit_url,
                'HIDDEN' => new Tempcode(),
                'ACTION_TITLE' => do_lang_tempcode('EDIT'),
                'ICON' => 'admin/edit',
                'GET' => true,
            ]));

            $delete_url = build_url(['page' => '_SELF', 'type' => 'ignore_error_delete', 'id' => $myrow['id']], '_SELF');
            $actions->attach(do_template('COLUMNED_TABLE_ACTION', [
                '_GUID' => '7d190886d5eac455a3e99a9ba329e9df',
                'NAME' => '#' . strval($myrow['id']),
                'URL' => $delete_url,
                'HIDDEN' => new Tempcode(),
                'ACTION_TITLE' => do_lang_tempcode('DELETE'),
                'ICON' => 'admin/delete',
                'GET' => true,
            ]));

            $map = [
                integer_format($myrow['id']),
                $myrow['ignore_string'],
                $actions
            ];

            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('TELEMETRY_IGNORE_TITLE'), $start, 'start', $max, 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'sort', paragraph(do_lang_tempcode('TELEMETRY_IGNORE_TEXT')));

        $form = new Tempcode();
        $button_url = build_url(['page' => '_SELF', 'type' => 'ignore_error'], '_SELF');
        $form->attach(do_template('BUTTON_SCREEN', ['_GUID' => 'b503c11f8637a0e7ad7ed5c67c5c8c62', 'IMMEDIATE' => false, 'URL' => $button_url, 'TITLE' => do_lang_tempcode('ADD'), 'IMG' => 'admin/add', 'HIDDEN' => new Tempcode()]));

        $url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '2d1c505f5d7d49c53ca4dcb8febf14ab',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => $form,
            'URL' => $url,
            'FILTERS_ROW_B' => new Tempcode(),
            'FILTERS_HIDDEN' => new Tempcode(),
        ]);

        pop_field_encapsulation();

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }

    /**
     * The UI for adding or editing an ignore into the database
     *
     * @param  ?AUTO_LINK $id The ID of the ignore error (null: adding a new one)
     * @return Tempcode The UI
     */
    public function ignore_error(?int $id = null) : object
    {
        $line = '';
        $resolve_message = '';
        if ($id !== null) {
            $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors_ignore', ['*'], ['id' => $id], '', 1);
            if (($_row === null) || (!array_key_exists(0, $_row))) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $row = $_row[0];
            $line = $row['ignore_string'];
            $resolve_message = get_translated_text($row['resolve_message']);
        }

        require_code('form_templates');

        $fields = new Tempcode();

        $fields->attach(form_input_line(do_lang_tempcode('TELEMETRY_IGNORE_STRING'), do_lang_tempcode('DESCRIPTION_TELEMETRY_IGNORE_STRING'), 'ignore_string', $line, true));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_RELAYED_ERROR_NOTES'), 'resolve_message', $resolve_message, false));

        $resolve_url = build_url(['page' => '_SELF', 'type' => '_ignore_error', 'id' => $id], '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => '31899d61b3ddf63ea0efd35829519146',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $resolve_url,
        ]);
    }

    /**
     * The actualizer for adding or editing an ignore error.
     *
     * @param  ?AUTO_LINK $id The ID of the ignore error to edit (null: we are adding one))
     * @return Tempcode The results
     */
    public function _ignore_error(?int $id = null) : object
    {
        $ignore_string = post_param_string('ignore_string');
        $resolve_message = post_param_string('resolve_message', '');

        // Actualise the record
        if ($id !== null) {
            $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors_ignore', ['*'], ['id' => $id], '', 1);
            if (($_row === null) || (!array_key_exists(0, $_row))) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $row = $_row[0];

            $map = [
                'ignore_string' => $ignore_string,
            ];
            $map += lang_remap_comcode('resolve_message', $row['resolve_message'], $resolve_message);
            $GLOBALS['SITE_DB']->query_update('relayed_errors_ignore', $map, ['id' => $id]);
        } else {
            $map = [
                'ignore_string' => $ignore_string,
            ];
            $map += insert_lang_comcode('resolve_message', $resolve_message, 4);
            $GLOBALS['SITE_DB']->query_insert('relayed_errors_ignore', $map);
        }

        // Auto-resolve existing errors according to the specified criteria
        $start = 0;
        $max = 100;
        $count = 0;
        do {
            $rows = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['id', 'error_message', 'note'], ['resolved' => 0], '', $max, $start);
            foreach ($rows as $row) {
                if (strpos($row['error_message'], $ignore_string) !== false) {
                    $count++;
                    $map = ['resolved' => 1];
                    $map += lang_remap_comcode('note', $row['note'], $resolve_message);
                    $GLOBALS['SITE_DB']->query_update('relayed_errors', $map, ['id' => $row['id']]);
                }
            }

            $start += $max;
        } while (!empty($rows));

        $url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('TELEMETRY_IGNORE_ERRORS_SUCCESS', escape_html(integer_format($count))));
    }

    /**
     * The UI or actualizer for deleting an ignore error.
     *
     * @param  AUTO_LINK $id The ID of the ignore error to delete
     * @return Tempcode The results
     */
    public function ignore_error_delete(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors_ignore', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        // Prompt for confirmation
        if (get_param_integer('confirm', 0) == 0) {
            $preview = do_lang_tempcode('ARE_YOU_SURE_DELETE_IGNORE_ERROR', escape_html($row['ignore_string']));
            return do_template('CONFIRM_SCREEN', [
                '_GUID' => '40c66abcd60ac85ac70d58d2d5da307e',
                'TITLE' => $this->title,
                'PREVIEW' => $preview,
                'URL' => get_self_url(false, false, ['confirm' => 1]),
                'FIELDS' => build_keep_post_fields(),
            ]);
        }

        $GLOBALS['SITE_DB']->query_delete('relayed_errors_ignore', ['id' => $id]);

        $url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
