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
 * Hook class.
 */
class Hook_admin_stats_cms_homesite extends CMSStatsProvider
{
    /**
     * Find metadata about stats categories that are defined by this stats hook.
     *
     * @return ?array Map of metadata (null: hook is disabled)
     */
    public function category_info() : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        require_lang('cms_homesite');

        return [
            'cms_homesite' => [
                'label_lang_string' => 'STATS_CATEGORY_cms_homesite',
                'icon' => 'spare/development',
            ],
        ];
    }

    /**
     * Find metadata about stats graphs that are provided by this stats hook.
     *
     * @param  boolean $for_kpi Whether this is for setting up a KPI
     * @return ?array Map of metadata (null: hook is disabled)
     */
    public function info(bool $for_kpi = false) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        $ret = [];
        $ret['relayed_errors'] = [
            'label' => do_lang_tempcode('CMS_SITE_ERRORS'),
            'category' => 'cms_homesite',
            'filters' => [
                'relayed_errors__month_range' => new CMSStatsDateMonthRangeFilter('relayed_errors__month_range', do_lang_tempcode('DATE_RANGE'), null, $for_kpi),
                'relayed_errors__resolved' => new CMSStatsTickFilter('relayed_errors__resolved', do_lang_tempcode('RESOLVED'), true),
            ],
            'pivot' => new CMSStatsDatePivot('relayed_errors__pivot', $this->get_date_pivots(!$for_kpi)),
            'support_kpis' => null,
        ];

        if (addon_installed('cms_homesite_tracker')) {
            $tracker_issue_types = [
                's_all' => do_lang('TRACKER_ISSUE_TYPE_all'),
                's_10' => do_lang('TRACKER_ISSUE_TYPE_not_assigned'),
                's_50' => do_lang('TRACKER_ISSUE_TYPE_assigned'),
                's_80' => do_lang('TRACKER_ISSUE_TYPE_resolved'),
                's_90' => do_lang('TRACKER_ISSUE_TYPE_closed'),
            ];
            $ret['tracker_issue_activity'] = [
                'label' => do_lang_tempcode('TRACKER_ISSUE_ACTIVITY'),
                'category' => 'cms_homesite',
                'filters' => [
                    'tracker_issue_activity__month_range' => new CMSStatsDateMonthRangeFilter('tracker_issue_activity__month_range', do_lang_tempcode('DATE_RANGE'), null, $for_kpi),
                    'tracker_issue_activity__type' => new CMSStatsListFilter('tracker_issue_activity__type', do_lang_tempcode('TRACKER_ISSUE_STATUS'), $tracker_issue_types),
                ],
                'pivot' => new CMSStatsDatePivot('tracker_issue_activity__pivot', $this->get_date_pivots(!$for_kpi)),
                'support_kpis' => self::KPI_HIGH_IS_GOOD,
            ];

            // Get tracker categories
            $_categories = collapse_2d_complexity('id', 'name', $GLOBALS['SITE_DB']->query('SELECT id,name FROM mantis_category_table WHERE status=0 ORDER BY name'));
            $categories = [];
            $categories['c_all'] = do_lang('ALL');
            foreach ($_categories as $id => $_category) {
                $categories['c_' . strval($id)] = $_category;
            }
            $categories = array_unique($categories);

            $ret['tracker_issues'] = [
                'label' => do_lang_tempcode('TRACKER_ISSUES'),
                'category' => 'cms_homesite',
                'filters' => [
                    'tracker_issues__month_range' => new CMSStatsDateMonthRangeFilter('tracker_issues__month_range', do_lang_tempcode('DATE_RANGE'), null, $for_kpi),
                    'tracker_issues__type' => new CMSStatsListFilter('tracker_issues__type', do_lang_tempcode('TRACKER_ISSUE_CATEGORY'), $categories),
                ],
                'pivot' => new CMSStatsDatePivot('tracker_issues__pivot', $this->get_date_pivots(!$for_kpi)),
                'support_kpis' => self::KPI_HIGH_IS_GOOD,
            ];
        }

        return $ret;
    }

    /**
     * Preprocess raw data in the database into something we can efficiently draw graphs/conclusions from.
     *
     * @param  TIME $start_time Start timestamp
     * @param  TIME $end_time End timestamp
     * @param  array $data_buckets Map of data buckets; a map of bucket name to nested maps with the following maps in sequence: 'month', 'pivot', 'value' (then further map data) ; extended and returned by reference
     */
    public function preprocess_raw_data(int $start_time, int $end_time, array &$data_buckets)
    {
        require_code('temporal');

        $server_timezone = get_server_timezone();

        $date_pivots = $this->get_date_pivots();

        /* relayed errors */

        $max = 1000;
        $start = 0;

        $query = 'SELECT first_date_and_time,resolved FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'relayed_errors WHERE ';
        $query .= 'first_date_and_time>=' . strval($start_time) . ' AND ';
        $query .= 'first_date_and_time<=' . strval($end_time);
        $query .= ' ORDER BY first_date_and_time';
        do {
            $rows = $GLOBALS['SITE_DB']->query($query, $max, $start);
            foreach ($rows as $row) {
                $timestamp = $row['first_date_and_time'];
                $timestamp = tz_time($timestamp, $server_timezone);

                $resolved = strval($row['resolved']);

                $month = to_epoch_interval_index($timestamp, 'months');

                foreach (array_keys($date_pivots) as $pivot) {
                    $pivot_value = $this->calculate_date_pivot_value($pivot, $timestamp);

                    if (!isset($data_buckets['relayed_errors'][$month][$pivot][$pivot_value][$resolved])) {
                        $data_buckets['relayed_errors'][$month][$pivot][$pivot_value][$resolved] = 0;
                    }
                    $data_buckets['relayed_errors'][$month][$pivot][$pivot_value][$resolved]++;

                    // For all
                    if (!isset($data_buckets['relayed_errors'][$month][$pivot][$pivot_value][''])) {
                        $data_buckets['relayed_errors'][$month][$pivot][$pivot_value][''] = 0;
                    }
                    $data_buckets['relayed_errors'][$month][$pivot][$pivot_value]['']++;
                }
            }

            $start += $max;
        } while (!empty($rows));

        /* tracker issue activity */

        if (addon_installed('cms_homesite_tracker')) {
            $max = 1000;
            $start = 0;

            $query = 'SELECT `status`,`last_updated` FROM mantis_bug_table WHERE ';
            $query .= '`last_updated`>=' . strval($start_time) . ' AND ';
            $query .= '`last_updated`<=' . strval($end_time);
            $query .= ' ORDER BY `last_updated`';
            do {
                $rows = $GLOBALS['SITE_DB']->query($query, $max, $start);
                foreach ($rows as $row) {
                    $timestamp = $row['last_updated'];
                    $timestamp = tz_time($timestamp, $server_timezone);

                    $status = strval($row['status']);

                    $month = to_epoch_interval_index($timestamp, 'months');

                    foreach (array_keys($date_pivots) as $pivot) {
                        $pivot_value = $this->calculate_date_pivot_value($pivot, $timestamp);

                        if (!isset($data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_' . strval($status)])) {
                            $data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_' . strval($status)] = 0;
                        }
                        $data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_' . strval($status)]++;

                        // For all
                        if (!isset($data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_all'])) {
                            $data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_all'] = 0;
                        }
                        $data_buckets['tracker_issue_activity'][$month][$pivot][$pivot_value]['s_all']++;
                    }
                }

                $start += $max;
            } while (!empty($rows));
        }

        /* tracker issues */

        if (addon_installed('cms_homesite_tracker')) {
            $max = 1000;
            $start = 0;

            $query = 'SELECT `status`,`date_submitted`,`category_id` FROM mantis_bug_table WHERE ';
            $query .= '`date_submitted`>=' . strval($start_time) . ' AND ';
            $query .= '`date_submitted`<=' . strval($end_time);
            $query .= ' ORDER BY `date_submitted`';
            do {
                $rows = $GLOBALS['SITE_DB']->query($query, $max, $start);
                foreach ($rows as $row) {
                    $timestamp = $row['date_submitted'];
                    $timestamp = tz_time($timestamp, $server_timezone);

                    $category = strval($row['category_id']);

                    $month = to_epoch_interval_index($timestamp, 'months');

                    foreach (array_keys($date_pivots) as $pivot) {
                        $pivot_value = $this->calculate_date_pivot_value($pivot, $timestamp);

                        if (!isset($data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_' . strval($category)])) {
                            $data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_' . strval($category)] = 0;
                        }
                        $data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_' . strval($category)]++;

                        // For all
                        if (!isset($data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_all'])) {
                            $data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_all'] = 0;
                        }
                        $data_buckets['tracker_issues'][$month][$pivot][$pivot_value]['c_all']++;
                    }
                }

                $start += $max;
            } while (!empty($rows));
        }
    }

    /**
     * Generate final data from preprocessed data.
     *
     * @param  string $bucket Data bucket we want data for
     * @param  string $pivot Pivot value
     * @param  array $filters Map of filters (including pivot if applicable)
     * @return ?array Final data in standardised map format (null: could not generate)
     */
    public function generate_final_data(string $bucket, string $pivot, array $filters) : ?array
    {
        switch ($bucket) {
            case 'relayed_errors':
                $range = $this->convert_month_range_filter_to_pair($filters[$bucket . '__month_range']);

                $data = $this->fill_data_by_date_pivots($pivot, $range[0], $range[1]);

                $where = [
                    'p_bucket' => $bucket,
                    'p_pivot' => $pivot,
                ];
                $extra = '';
                $extra .= ' AND p_month>=' . strval($range[0]);
                $extra .= ' AND p_month<=' . strval($range[1]);
                $data_rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_data'], $where, $extra);
                foreach ($data_rows as $data_row) {
                    $_data = @unserialize($data_row['p_data']);

                    foreach ($_data as $pivot_value => $__) {
                        $pivot_value = $this->make_date_pivot_value_nice($pivot, $pivot_value);

                        foreach ($__ as $resolved => $value) {
                            if ((empty($filters[$bucket . '__resolved'])) && ($resolved == '1')) {
                                continue;
                            }

                            if (!isset($data[$pivot_value])) {
                                $data[$pivot_value] = 0;
                            }

                            $data[$pivot_value] += $value;
                        }
                    }
                }

                return [
                    'type' => null,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('TIME_IN_TIMEZONE', escape_html(make_nice_timezone_name(get_site_timezone()))),
                    'y_axis_label' => do_lang_tempcode('COUNT_NEW'),
                ];

            case 'tracker_issue_activity':
                $range = $this->convert_month_range_filter_to_pair($filters[$bucket . '__month_range']);

                $data = $this->fill_data_by_date_pivots($pivot, $range[0], $range[1]);

                $where = [
                    'p_bucket' => $bucket,
                    'p_pivot' => $pivot,
                ];
                $extra = '';
                $extra .= ' AND p_month>=' . strval($range[0]);
                $extra .= ' AND p_month<=' . strval($range[1]);
                $data_rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_data'], $where, $extra);
                foreach ($data_rows as $data_row) {
                    $_data = @unserialize($data_row['p_data']);

                    foreach ($_data as $pivot_value => $__) {
                        $pivot_value = $this->make_date_pivot_value_nice($pivot, $pivot_value);

                        foreach ($__ as $type => $value) {
                            if (($type != 's_all') && (!empty($filters[$bucket . '__type'])) && ($filters[$bucket . '__type'] != $type)) {
                                continue;
                            }

                            if (!isset($data[$pivot_value])) {
                                $data[$pivot_value] = 0;
                            }

                            $data[$pivot_value] += $value;
                        }
                    }
                }

                return [
                    'type' => null,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('TIME_IN_TIMEZONE', escape_html(make_nice_timezone_name(get_site_timezone()))),
                    'y_axis_label' => do_lang_tempcode('COUNT_TOTAL'),
                ];

            case 'tracker_issues':
                $range = $this->convert_month_range_filter_to_pair($filters[$bucket . '__month_range']);

                $data = $this->fill_data_by_date_pivots($pivot, $range[0], $range[1]);

                $where = [
                    'p_bucket' => $bucket,
                    'p_pivot' => $pivot,
                ];
                $extra = '';
                $extra .= ' AND p_month>=' . strval($range[0]);
                $extra .= ' AND p_month<=' . strval($range[1]);
                $data_rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_data'], $where, $extra);
                foreach ($data_rows as $data_row) {
                    $_data = @unserialize($data_row['p_data']);

                    foreach ($_data as $pivot_value => $__) {
                        $pivot_value = $this->make_date_pivot_value_nice($pivot, $pivot_value);

                        foreach ($__ as $category => $value) {
                            if (($category != 'c_all') && (!empty($filters[$bucket . '__type'])) && ($filters[$bucket . '__type'] != $category)) {
                                continue;
                            }

                            if (!isset($data[$pivot_value])) {
                                $data[$pivot_value] = 0;
                            }

                            $data[$pivot_value] += $value;
                        }
                    }
                }

                return [
                    'type' => null,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('TIME_IN_TIMEZONE', escape_html(make_nice_timezone_name(get_site_timezone()))),
                    'y_axis_label' => do_lang_tempcode('COUNT_NEW'),
                ];
        }

        fatal_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('45c7df6e0f45531285abf135c3143815')));
    }
}
