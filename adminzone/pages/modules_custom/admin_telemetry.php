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
class Module_admin_telemetry
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham and Patrick Schmalstig';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
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
            'telemetry_stats',
            'telemetry_sites',
            'telemetry_errors',
            'telemetry_errors_ignore',
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
            $GLOBALS['SITE_DB']->create_table('telemetry_sites', [
                'id' => '*AUTO',
                'public_key' => 'SHORT_TEXT', // base64
                'sign_public_key' => 'SHORT_TEXT', // base64
                'website_url' => 'URLPATH',
                'website_name' => 'SHORT_TEXT',
                'software_version' => 'SHORT_TEXT',
                'may_feature' => 'BINARY',
                'add_date_and_time' => 'TIME',
                'last_checked' => '?TIME', // Last time we checked data/installed.php (null: never)
                'website_installed' => 'SHORT_TEXT',
                'addons_installed' => 'SERIAL',
            ]);

            $GLOBALS['SITE_DB']->create_table('telemetry_stats', [
                'id' => '*AUTO',
                's_site' => 'AUTO_LINK',
                'software_version' => 'SHORT_TEXT',
                'date_and_time' => 'TIME',
                'count_members' => 'INTEGER',
                'count_daily_hits' => 'INTEGER',
            ]);

            $GLOBALS['SITE_DB']->create_table('telemetry_errors', [
                'id' => '*AUTO',
                'e_guid' => 'MINIID_TEXT',
                'e_site' => 'AUTO_LINK',
                'e_first_date_and_time' => 'TIME',
                'e_last_date_and_time' => 'TIME',
                'e_version' => 'ID_TEXT',
                'e_error_message' => 'LONG_TEXT',
                'e_error_hash' => 'SHORT_TEXT',
                'e_error_count' => 'INTEGER',
                'e_refs_compiled' => 'BINARY', // Whether this error references _compiled code; 0 always means the error is in original Composr code, but 1 does *not* always mean the error is in custom / user code.
                'e_resolved' => 'BINARY',
                'e_note' => 'LONG_TRANS__COMCODE',
            ]);

            $GLOBALS['SITE_DB']->create_table('telemetry_errors_ignore', [
                'id' => '*AUTO',
                'ignore_string' => 'SHORT_TEXT',
                'resolve_message' => 'LONG_TRANS__COMCODE',
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
            return $this->sites();
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
    public function sites() : object
    {
        require_code('templates_results_table');
        require_code('form_templates');

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 50);

        // Sortable validation
        $sortables = [
            'date_and_time' => do_lang_tempcode('CMS_LAST_ADMIN_ACCESS'),
            'software_version' => do_lang_tempcode('CMS_VERSION'),
            'count_members' => do_lang_tempcode('CMS_COUNT_MEMBERS'),
            'count_daily_hits' => do_lang_tempcode('CMS_HITS_24_HRS'),
        ];
        $test = explode(' ', get_param_string('sort', 'date_and_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('b84fffc6982b588da34b4a6c6ffb7b9f')));
        }
        $order_by = ' ORDER BY ' . $sortable . ' ' . $sort_order;

        $select = 'r.id AS id,r.website_url AS website_url,r.website_name AS website_name,r.software_version AS website_version,r.may_feature AS website_may_feature,r.website_installed AS website_installed,MAX(s.date_and_time) AS date_and_time,MAX(s.count_members) AS count_members,MAX(s.count_daily_hits) AS count_daily_hits';
        $where = 'r.website_url NOT LIKE \'%.composr.info%\''; // LEGACY
        if (!$GLOBALS['DEV_MODE']) {
            // Ignore local installs if not in dev mode
            $where .= ' AND ' . db_string_not_equal_to('r.website_url', '%://localhost%') . ' AND ' . db_string_not_equal_to('r.website_url', '%://127.0.0.1%') . ' AND ' . db_string_not_equal_to('r.website_url', '%://192.168.%') . ' AND ' . db_string_not_equal_to('r.website_url', '%://10.0.%');
        }
        $group_by = ' GROUP BY r.id, r.website_url, r.website_name, r.software_version, r.may_feature, r.website_installed';

        $sql = 'SELECT ' . $select . ' FROM ' . get_table_prefix() . 'telemetry_sites r LEFT JOIN ' . get_table_prefix() . 'telemetry_stats s ON s.s_site=r.id WHERE ' . $where . $group_by . $order_by;
        $rows = $GLOBALS['SITE_DB']->query($sql, $max, $start);
        $max_rows = $GLOBALS['SITE_DB']->query_select_value('telemetry_sites r', 'COUNT(*)', [], ' AND ' . $where);

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
            if (($r['website_may_feature'] == 0) && (get_param_integer('may_feature_only', 0) == 1)) {
                continue;
            }

            $rt = [];

            $rt['HITTIME'] = intval(round((time() - $r['date_and_time']) / 60 / 60));
            $rt['HITTIME_2'] = intval(round((time() - $r['date_and_time']) / 60 / 60 / 24));

            // NB: The Cron hook 'cmsusers' does the actual checking on an interval
            $rt['CMS_ACTIVE'] = $r['website_installed'];

            $rt['NOTE'] = ($r['website_may_feature'] == 1) ? do_lang('CMS_MAY_FEATURE') : do_lang('CMS_KEEP_PRIVATE');

            $rt['NUM_MEMBERS'] = (($r['count_members'] !== null) ? integer_format($r['count_members']) : '0');

            $rt['NUM_HITS_PER_DAY'] = (($r['count_daily_hits'] !== null) ? integer_format($r['count_daily_hits']) : '0');

            $_current = $GLOBALS['SITE_DB']->query_select('telemetry_stats', ['software_version', 'count_members', 'count_daily_hits', 'date_and_time'], ['s_site' => $r['id']], ' ORDER BY date_and_time DESC', 1);

            if (array_key_exists(0, $_current)) {
                $current = $_current[0];
                $current_members = integer_format($current['count_members']);
                $current_hits = integer_format($current['count_daily_hits']);
            } else {
                $current = null;
                $current_members = do_lang('UNKNOWN');
                $current_hits = do_lang('UNKNOWN');
            }

            $map = [
                hyperlink($r['website_url'], $r['website_name'], true, true, $r['website_url']),
                do_lang_tempcode(
                    '_CMS_LAST_ADMIN_ACCESS',
                    escape_html(do_lang('_AGO', do_lang('DAYS', integer_format($rt['HITTIME_2'])))),
                    escape_html(do_lang('_AGO', do_lang('HOURS', integer_format($rt['HITTIME']))))
                ),
                $rt['CMS_ACTIVE'],
                escape_html($r['website_version']),
                $rt['NOTE'],
                do_lang_tempcode('CMS_VALUE_WITH_MAX', escape_html($current_members), escape_html($rt['NUM_MEMBERS'])),
                do_lang_tempcode('CMS_VALUE_WITH_MAX', escape_html($current_hits), escape_html($rt['NUM_HITS_PER_DAY'])),
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

        // Prepare Filtercode
        require_code('filtercode');
        $active_filters = get_params_filtercode();

        // By default, hide resolved errors
        if ($active_filters == '') {
            $active_filters = 'e_resolved=0';
        }

        // Build WHERE query from Filtercode
        $end = '';
        list($extra_join, $end) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($active_filters), null, 'telemetry_errors');

        // Query
        $_max_rows = $GLOBALS['SITE_DB']->query('SELECT COUNT(*) as count_sites FROM ' . get_table_prefix() . 'telemetry_errors r LEFT JOIN ' . get_table_prefix() . 'telemetry_sites s ON r.e_site=s.id WHERE 1=1' . $end);
        $max_rows = $_max_rows[0]['count_sites'];
        $sortables = [
            'website_name' => do_lang_tempcode('NAME'),
            'e_first_date_and_time' => do_lang_tempcode('FIRST_REPORTED'),
            'e_last_date_and_time' => do_lang_tempcode('LAST_REPORTED'),
            'e_version' => do_lang_tempcode('VERSION'),
            'e_error_count' => do_lang_tempcode('TIMES_REPORTED'),
        ];
        $test = explode(' ', get_param_string('sort', 'e_last_date_and_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('cb54eb251ad058d0935c226b42440407')));
        }
        $select = 'SELECT r.*,s.website_url AS website_url,s.website_name AS website_name';
        $rows = $GLOBALS['SITE_DB']->query($select . ' FROM ' . get_table_prefix() . 'telemetry_errors r LEFT JOIN ' . get_table_prefix() . 'telemetry_sites s ON r.e_site=s.id WHERE 1=1' . $end . ' ORDER BY ' . $sortable . ' ' . $sort_order, $max, $start);

        // Build results table
        $result_entries = new Tempcode();

        require_code('templates_results_table');
        require_code('templates_tooltip');
        require_code('temporal');

        $filtercode = [
            'website_name<website_name_op><website_name>',
            'website_url<website_url_op><website_url>',
            'e_error_message<e_error_message_op><e_error_message>',
            'e_refs_compiled=<e_refs_compiled>',
            'e_first_date_and_time<e_first_date_and_time_op><e_first_date_and_time>',
            'e_last_date_and_time<e_last_date_and_time_op><e_last_date_and_time>',
            'e_version=<e_version>',
            'e_error_count<e_error_count_op><e_error_count>',
            'e_resolved=<e_resolved>'
        ];
        $filtercode_labels = [
            'website_name=' . do_lang('NAME'),
            'website_url=' . do_lang('URL'),
            'e_error_message=' . do_lang('ERROR_SUMMARY'),
            'e_refs_compiled=' . do_lang('ERROR_REFS_COMPILED'),
            'e_first_date_and_time=' . do_lang('FIRST_REPORTED'),
            'e_last_date_and_time=' . do_lang('LAST_REPORTED'),
            'e_version=' . do_lang('VERSION'),
            'e_error_count=' . do_lang('TIMES_REPORTED'),
            'e_resolved=' . do_lang('RESOLVED'),
        ];
        $filtercode_types = [
            'e_version=list',
        ];

        $map = [
            do_lang_tempcode('IDENTIFIER'),
            do_lang_tempcode('NAME'),
            do_lang_tempcode('ERROR_SUMMARY'),
            do_lang_tempcode('ERROR_REFS_COMPILED'),
            do_lang_tempcode('FIRST_REPORTED'),
            do_lang_tempcode('LAST_REPORTED'),
            do_lang_tempcode('VERSION'),
            do_lang_tempcode('TIMES_REPORTED'),
            do_lang_tempcode('ACTIONS'),
        ];
        $header_row = results_header_row($map, $sortables, 'sort', $sortable . ' ' . $sort_order);

        foreach ($rows as $myrow) {
            $id = hyperlink(build_url(['page' => '_SELF', 'type' => 'error', 'id' => $myrow['id']], '_SELF'), '#' . integer_format($myrow['id']), false, true);
            $website_url = (($myrow['website_url'] !== null) ? hyperlink($myrow['website_url'], $myrow['website_name'], true, true) : do_lang_tempcode('UNKNOWN'));
            $summary = generate_tooltip_by_truncation($myrow['e_error_message'], 160);
            $first_date = get_timezoned_date_time($myrow['e_first_date_and_time'], false);
            $last_date = get_timezoned_date_time($myrow['e_last_date_and_time'], false);

            $actions = new Tempcode();

            if ($myrow['e_resolved'] == 0) {
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
                (($myrow['e_refs_compiled'] == 1) ? do_lang('YES') : do_lang('NO')),
                $first_date,
                $last_date,
                $myrow['e_version'],
                integer_format($myrow['e_error_count']),
                $actions
            ];

            $result_entries->attach(results_entry($map, true));
        }

        $results_table = results_table(do_lang_tempcode('CMS_SITE_ERRORS'), $start, 'start', $max, 'max', $max_rows, $header_row, $result_entries, $sortables, $sortable, $sort_order, 'sort', paragraph(do_lang_tempcode('DESCRIPTION_CMS_SITE_ERRORS')));

        $form = new Tempcode();
        $button_url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        $form->attach(do_template('BUTTON_SCREEN', ['_GUID' => '318957ab73112a21637cd04627e2408d', 'IMMEDIATE' => false, 'URL' => $button_url, 'TITLE' => do_lang_tempcode('TELEMETRY_AUTORESOLVE'), 'IMG' => 'admin/delete2', 'HIDDEN' => new Tempcode()]));

        $filtercode_box = do_block('main_content_filtering', [
            'param' => implode(',', $filtercode),
            'table' => 'telemetry_errors',
            'labels' => implode(',', $filtercode_labels),
            'types' => implode(',', $filtercode_types),
        ]);

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '358ae22e7f23a3f68eac4aa1e24df85b',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => $form,
            'FILTERCODE_BOX' => $filtercode_box,
        ]);

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
        $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        $_website_name = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_sites', 'website_name', ['id' => $row['e_site']]);
        $_website_url = $GLOBALS['SITE_DB']->query_select_value_if_there('telemetry_sites', 'website_url', ['id' => $row['e_site']]);
        if (($_website_name !== null) && ($_website_url !== null)) {
            $website_url = hyperlink($_website_name, $_website_url, true, true);
        } else {
            $website_url = do_lang('UNKNOWN');
        }

        require_code('templates_map_table');
        require_code('temporal');

        $formatted_id = '#' . integer_format($row['id']);
        $guid = $row['e_guid'];
        $first_date = get_timezoned_date_time($row['e_first_date_and_time'], false);
        $last_date = get_timezoned_date_time($row['e_last_date_and_time'], false);
        $resolved = ($row['e_resolved'] == 1);
        $refs_compiled = ($row['e_refs_compiled'] == 1);

        $buttons = new Tempcode();

        if ($row['e_resolved'] == 0) {
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
            'GUID' => $guid,
            'URL' => $website_url,
            'ERROR_REFS_COMPILED' => $refs_compiled ? do_lang('YES') : do_lang('NO'),
            'ERROR_MESSAGE' => $row['e_error_message'],
            'FIRST_REPORTED' => $first_date,
            'LAST_REPORTED' => $last_date,
            'VERSION' => $row['e_version'],
            'TIMES_REPORTED' => integer_format($row['e_error_count']),
            'RESOLVED' => $resolved ? do_lang('YES') : do_lang('NO'),
            'TELEMETRY_IGNORE_NOTE' => get_translated_text($row['e_note']),
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
        $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        $note = get_translated_text($row['e_note']);

        require_code('form_templates');

        $fields = new Tempcode();

        $fields->attach(form_input_tick(do_lang_tempcode('MARK_RESOLVED'), do_lang_tempcode('DESCRIPTION_MARK_RESOLVED'), 'resolved', false));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_RELAYED_ERROR_NOTES'), 'note', $note, false));

        $resolve_url = build_url(['page' => '_SELF', 'type' => '_resolve_error', 'id' => $id], '_SELF');

        require_code('form_templates');
        list($warning_details, $ping_url) = handle_conflict_resolution(strval($id));

        return do_template('FORM_SCREEN', [
            '_GUID' => '904b2916eea66a19f6906842c81da308',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $resolve_url,
            'WARNING_DETAILS' => $warning_details,
            'PING_URL' => $ping_url,
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
        $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        $resolved = post_param_integer('resolved', 0);
        $new_note = post_param_string('note');

        // Actualiser
        $map = ['e_resolved' => $resolved];
        $map += lang_remap_comcode('e_note', $row['e_note'], $new_note);
        $GLOBALS['SITE_DB']->query_update('telemetry_errors', $map, ['id' => $id]);
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

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('telemetry_errors_ignore', 'COUNT(*)');
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
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('5bf811d6f2bc5838948b270b574d5a4e')));
        }
        $rows = $GLOBALS['SITE_DB']->query_select('telemetry_errors_ignore', ['*'], [], ' ORDER BY ' . $sortable . ' ' . $sort_order, $max, $start);

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

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '2d1c505f5d7d49c53ca4dcb8febf14ab',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => $form,
        ]);

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
            $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors_ignore', ['*'], ['id' => $id], '', 1);
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

        require_code('form_templates');
        list($warning_details, $ping_url) = handle_conflict_resolution(false, false); // Very sensitive because it triggers auto-resolve on save

        return do_template('FORM_SCREEN', [
            '_GUID' => '31899d61b3ddf63ea0efd35829519146',
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'URL' => $resolve_url,
            'WARNING_DETAILS' => $warning_details,
            'PING_URL' => $ping_url,
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
            $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors_ignore', ['*'], ['id' => $id], '', 1);
            if (($_row === null) || (!array_key_exists(0, $_row))) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $row = $_row[0];

            $map = [
                'ignore_string' => $ignore_string,
            ];
            $map += lang_remap_comcode('resolve_message', $row['resolve_message'], $resolve_message);
            $GLOBALS['SITE_DB']->query_update('telemetry_errors_ignore', $map, ['id' => $id]);
        } else {
            $map = [
                'ignore_string' => $ignore_string,
            ];
            $map += insert_lang_comcode('resolve_message', $resolve_message, 4);
            $GLOBALS['SITE_DB']->query_insert('telemetry_errors_ignore', $map);
        }

        // Auto-resolve existing errors according to the specified criteria
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
        $start = 0;
        $max = 100;
        $count = 0;
        do {
            $rows = $GLOBALS['SITE_DB']->query_select('telemetry_errors', ['id', 'e_error_message', 'e_note'], ['e_resolved' => 0], '', $max, $start);
            foreach ($rows as $row) {
                if (strpos($row['e_error_message'], $ignore_string) !== false) {
                    $count++;
                    $map = ['e_resolved' => 1];
                    $map += lang_remap_comcode('e_note', $row['e_note'], $resolve_message);
                    $GLOBALS['SITE_DB']->query_update('telemetry_errors', $map, ['id' => $row['id']]);
                }
            }

            $start += $max;
        } while (!empty($rows));

        $url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('TELEMETRY_IGNORE_ERRORS_SUCCESS', escape_html(integer_format($count))));
    }

    /**
     * The UI or actualiser for deleting an ignore error.
     *
     * @param  AUTO_LINK $id The ID of the ignore error to delete
     * @return Tempcode The results
     */
    public function ignore_error_delete(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('telemetry_errors_ignore', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $row = $_row[0];

        // Prompt for confirmation
        if (get_param_integer('confirm', 0) == 0) {
            $preview = do_lang_tempcode('ARE_YOU_SURE_DELETE_IGNORE_ERROR', escape_html($row['ignore_string']));

            require_code('form_templates');
            list($warning_details, $ping_url) = handle_conflict_resolution(strval($id));

            return do_template('CONFIRM_SCREEN', [
                '_GUID' => '40c66abcd60ac85ac70d58d2d5da307e',
                'TITLE' => $this->title,
                'PREVIEW' => $preview,
                'URL' => get_self_url(false, false, ['confirm' => 1]),
                'FIELDS' => build_keep_post_fields(),
                'WARNING_DETAILS' => $warning_details,
                'PING_URL' => $ping_url,
            ]);
        }

        $GLOBALS['SITE_DB']->query_delete('telemetry_errors_ignore', ['id' => $id]);

        $url = build_url(['page' => '_SELF', 'type' => 'ignore_errors'], '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
