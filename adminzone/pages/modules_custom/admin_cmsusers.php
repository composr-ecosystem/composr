<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
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
        $info['version'] = 3;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_homesite';
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
                'num_members' => 'INTEGER',
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
                'error_count' => 'INTEGER',
                'resolved' => 'BINARY'
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
        if (!addon_installed('composr_homesite')) {
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
        if (!addon_installed__messaged('composr_homesite', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        if (($type == 'error') || ($type == 'resolve_error')) {
            breadcrumb_set_parents([['_SELF:_SELF:errors', do_lang_tempcode('CMS_SITE_ERRORS')]]);
        }

        require_lang('composr_homesite');

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
        require_code('composr_homesite');
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
        return new Tempcode();
    }

    /**
     * List of sites that have installed Composr.
     *
     * @return Tempcode The result of execution
     */
    public function users() : object
    {
        $_sort = get_param_string('sort', 'num_hits_per_day DESC');
        list($sort, $dir) = explode(' ', $_sort);
        $anti_dir = (($dir == 'ASC') ? 'DESC' : 'ASC');

        $order_by = '';
        switch ($sort) {
            case 'website_name':
                $order_by = ' ORDER BY website_name';
                break;

            case 'hittime':
                $order_by = ' ORDER BY hittime';
                break;

            case 'l_version':
                $order_by = ' ORDER BY l_version';
                break;

            case 'num_members':
                $order_by = ' ORDER BY num_members';
                break;

            case 'num_hits_per_day':
                $order_by = ' ORDER BY num_hits_per_day';
                break;

            default:
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $order_by .= ' ' . $dir;

        $max = 500;

        $select = 'website_url,website_name,MAX(l_version) AS l_version,MAX(hittime) AS hittime,MAX(num_members) AS num_members,MAX(num_hits_per_day) AS num_hits_per_day';
        $where = 'website_url NOT LIKE \'%.composr.info%\'';
        if (!$GLOBALS['DEV_MODE']) {
            $where .= ' AND ' . db_string_not_equal_to('website_name', '') . ' AND ' . db_string_not_equal_to('website_name', '(unnamed)');
        }
        $sql = 'SELECT ' . $select . ' FROM ' . get_table_prefix() . 'logged WHERE ' . $where . ' GROUP BY website_url,website_name ' . $order_by;
        $rows = $GLOBALS['SITE_DB']->query($sql, $max);

        $seen_before = [];

        $_rows = [];
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

            $rt['L_VERSION'] = $r['l_version'];

            $rt['WEBSITE_URL'] = $r['website_url'];

            $rt['WEBSITE_NAME'] = $r['website_name'];

            $rt['HITTIME'] = integer_format(intval(round((time() - $r['hittime']) / 60 / 60)));
            $rt['HITTIME_2'] = integer_format(intval(round((time() - $r['hittime']) / 60 / 60 / 24)));

            if ($i < 100) {
                $active = get_value_newer_than('testing__' . $r['website_url'] . '/_config.php', time() - 60 * 60 * 10, true);
                if ($active === null) {
                    $test = cms_http_request($r['website_url'] . '/_config.php', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'byte_limit' => 0, 'ua' => 'Simple install stats', 'timeout' => 2.0]);
                    if ($test->data !== null) {
                        $active = do_lang('YES');
                    } else {
                        $active = @strval($test->message);
                        if ($active == '') {
                            $active = do_lang('NO');
                        } else {
                            $active .= do_lang('CMS_WHEN_CHECKING');
                        }
                    }
                    set_value('testing__' . $r['website_url'] . '/_config.php', $active, true);
                }
                $rt['CMS_ACTIVE'] = $active;
            } else {
                $rt['CMS_ACTIVE'] = do_lang('CMS_CHECK_LIMIT');
            }

            $rt['NOTE'] = $perm ? do_lang('CMS_MAY_FEATURE') : do_lang('CMS_KEEP_PRIVATE');

            $rt['NUM_MEMBERS'] = integer_format($r['num_members']);

            $rt['NUM_HITS_PER_DAY'] = integer_format($r['num_hits_per_day']);

            $_rows[] = $rt;
        }

        return do_template('CMS_SITES_SCREEN', [
            '_GUID' => '7f4b56c730f2b613994a3fe6f00ed525',
            'TITLE' => $this->title,
            'ROWS' => $_rows,

            'WEBSITE_NAME_DIR' => ($sort == 'website_name') ? $anti_dir : 'ASC',
            'HITTIME_DIR' => ($sort == 'hittime') ? $anti_dir : 'DESC',
            'L_VERSION_DIR' => ($sort == 'l_version') ? $anti_dir : 'ASC',
            'NUM_MEMBERS_DIR' => ($sort == 'num_members') ? $anti_dir : 'DESC',
            'NUM_HITS_PER_DAY_DIR' => ($sort == 'num_hits_per_day') ? $anti_dir : 'DESC',
        ]);
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
            $end .= ' AND website_url LIKE ' . db_encode_like('%' . $filter_website . '%');
        }
        if ($filter_error_message != '') {
            $end .= ' AND website_message LIKE ' . db_encode_like('%' . $filter_error_message . '%');
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
                $resolve_url = build_url(['page' => '_SELF', 'type' => 'resolve_error', 'id' => $myrow['id'], 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
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

        $url = build_url(['page' => '_SELF', 'type' => 'errors'], '_SELF');

        $tpl = do_template('RESULTS_TABLE_SCREEN', [
            '_GUID' => '358ae22e7f23a3f68eac4aa1e24df85b',
            'TITLE' => $this->title,
            'RESULTS_TABLE' => $results_table,
            'FORM' => new Tempcode(),
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
            $resolve_url = build_url(['page' => '_SELF', 'type' => 'resolve_error', 'id' => $id, 'redirect' => protect_url_parameter(SELF_REDIRECT)], '_SELF');
            $buttons->attach(do_template('BUTTON_SCREEN', [
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
     * The UI / actualiser for resolving an error message.
     *
     * @param  integer $id The ID of the error to resolve
     * @return Tempcode the UI / results
     */
    public function resolve_error(int $id) : object
    {
        $_row = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], ['id' => $id], '', 1);
        if (($_row === null) || (!array_key_exists(0, $_row))) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        // Confirm?
        if (get_param_integer('confirm', 0) == 0) {
            $preview = do_lang_tempcode('CONFIRM_CMS_SITE_RESOLVE_ERROR', integer_format($id));
            return do_template('CONFIRM_SCREEN', [
                '_GUID' => 'd3d654c7dcffb353638d08b53697488b',
                'TITLE' => $this->title,
                'PREVIEW' => $preview,
                'URL' => get_self_url(false, false, ['confirm' => 1]),
                'FIELDS' => build_keep_post_fields(),
            ]);
        }

        // Actualiser
        $GLOBALS['SITE_DB']->query_update('relayed_errors', ['resolved' => 1], ['id' => $id]);
        $url = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);
        if ($url == '') {
            $_url = build_url(['page' => '_SELF', 'type' => 'errors'], '_SELF');
            $url = $_url->evaluate();
        }
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
