<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class stats_test_set extends cms_test_case
{
    protected $dummy_data_added = [];
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_set_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testPreprocessRawData()
    {
        push_query_limiting(false);

        require_code('stats');
        require_code('temporal');
        require_lang('stats');
        require_code('developer_tools');

        $dummy_data = [
            // 'table_name' => [array of forced field=>value maps; one row will be created for each array item],
            'actionlogs' => [
                [],
                ['the_type' => 'ACCESSED_ADMIN_ZONE']
            ],
            'f_moderator_logs' => [[]],
            'banner_clicks' => [[]],
            'f_topics' => [
                [],
                ['_ALLOW_NULL_' => false],
                ['_ALLOW_NULL_' => false, 't_forum_id' => null],
            ],
            'f_posts' => [
                [],
                ['_ALLOW_NULL_' => false, 'p_whisper_to_member' => null],
                ['p_whisper_to_member' => 2],
            ],
            'f_poll_votes' => [
                [],
                ['pv_revoked' => 0],
            ],
            'f_members' => [
                [],
                ['m_dob_year' => 2000, 'm_dob_month' => 1, 'm_dob_day' => 1],
                ['m_cache_num_posts' => 10],
            ],
            'download_logging' => [[]],
            'logged_mail_messages' => [[]],
            'stats_known_events' => [[]],
            'stats_known_tracking' => [[]],
            'stats_events' => [[]],
            'stats' => [
                [],
                ['member_id' => 2],
                ['browser' => 'Mozilla Firefox'],
                ['referer_url' => 'https://example.com'],
            ],
            'f_invites' => [[]],
            'stats_link_tracker' => [[]],
            'stats_known_links' => [[]],
            'newsletter_subscribers' => [
                [],
                ['code_confirm' => 0],
            ],
            'points_ledger' => [[]],
            'poll_votes' => [[]],
            'rating' => [[]],
            'searches_logged' => [[]],
            'failedlogins' => [[]],
            'hackattack' => [[]],
            'sitemap_cache' => [
                [],
                ['_ALLOW_NULL_' => false, 'page_link' => ':' . uniqid('', false)],
            ],
            'ecom_subscriptions' => [[]],
            'ecom_transactions' => [
                [],
                ['t_status' => 'Completed'],
            ],
            'usersonline_track' => [[]],
            'f_warnings' => [[]],

            'authors' => [[]],
            'banners' => [[]],
            'banner_types' => [[]],
            'calendar_types' => [[]],
            'catalogues' => [[]],
            'catalogue_categories' => [[]],
            'catalogue_entries' => [[]],
            'chat_rooms' => [[]],
            'comcode_pages' => [[]],
            'download_downloads' => [[]],
            'download_categories' => [[]],
            'calendar_events' => [[]],
            'f_forums' => [[]],
            'galleries' => [[]],
            'f_groups' => [[]],
            'images' => [[]],
            'news' => [[]],
            'news_categories' => [[]],
            'poll' => [[]],
            'quizzes' => [[]],
            'videos' => [[]],
            'wiki_pages' => [[]],
            'wiki_posts' => [[]],
            'unsubscribed_emails' => [[]],
        ];
        if (addon_installed('tickets')) {
            require_code('tickets');
            $dummy_data['f_topics'][] = ['_ALLOW_NULL_' => false, 't_forum_id' => get_ticket_forum_id()];
        }
        if (addon_installed('points')) {
            $dummy_data['points_ledger'][] = ['status' => 0, 'sending_member' => $GLOBALS['FORUM_DRIVER']->get_guest_id(), 'receiving_member' => 2];
            $dummy_data['points_ledger'][] = ['status' => 0, 'sending_member' => 2, 'receiving_member' => $GLOBALS['FORUM_DRIVER']->get_guest_id()];
            $dummy_data['points_ledger'][] = ['status' => 0, 'sending_member' => 3, 'receiving_member' => 2];
            $dummy_data['points_ledger'][] = ['status' => 0, 'sending_member' => 3, 'receiving_member' => 2, 'amount_gift_points' => 1];

            // FUDGE: so we can test top members by points (which uses CPF and not database)
            $GLOBALS['FORUM_DRIVER']->set_custom_field(2, 'points_rank', '1000');
        }

        if (addon_installed('cms_homesite')) {
            $dummy_data['relayed_errors'] = [[], ['resolved' => 0], ['resolved' => 1]];
        }

        // Remove old preprocessed stats so we can force pre-processing again
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed');
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed_flat');

        // Generate dummy data so we can process stats on them
        foreach ($dummy_data as $table => $rows) {
            foreach ($rows as $forced_map) {
                if (isset($forced_map['_ALLOW_NULL_'])) {
                    $allow_null = $forced_map['_ALLOW_NULL_'];
                } else {
                    $allow_null = true;
                }
                if (!isset($this->dummy_data_added[$table])) {
                    $this->dummy_data_added[$table] = [];
                }
                $this->dummy_data_added[$table][] = make_dummy_db_row($table, $allow_null, $forced_map);
            }
        }

        $p_month = get_stats_month_for_timestamp(time());
        $server_timezone = get_server_timezone();

        $today = cms_date('Y-m-d');
        list($year, $month, $day) = array_map('intval', explode('-', $today));
        $end_time = cms_mktime(0, 0, 0, $month, $day, $year) + (60 * 60 * 31);
        $end_time = tz_time($end_time, $server_timezone);
        $start_time = cms_mktime(0, 0, 0, $month, $day, $year) - (60 * 60 * 24 * 365);
        $start_time = tz_time($start_time, $server_timezone);

        $hook_obs = find_all_hook_obs('modules', 'admin_stats', 'Hook_admin_stats_');
        $buckets = [];
        $buckets_existing = [];
        foreach ($hook_obs as $hook_name => $ob) {
            $_buckets = $ob->info();
            if ($_buckets === null) {
                continue;
            }
            $buckets = array_merge($buckets, array_keys($_buckets));
            preprocess_raw_data_for($hook_name, $start_time, $end_time);
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_bucket']);
        foreach ($rows as $row) {
            $buckets_existing[] = $row['p_bucket'];
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed_flat', ['p_bucket']);
        foreach ($rows as $row) {
            $buckets_existing[] = $row['p_bucket'];
        }

        // Exceptions
        $buckets_existing[] = 'tracker_issue_activity'; // We cannot populate dummy data on MantisBT because we do not track its database meta
        $buckets_existing[] = 'tracker_issues'; // We cannot populate dummy data on MantisBT because we do not track its database meta

        $buckets = array_unique($buckets);
        $buckets_existing = array_unique($buckets_existing);
        $buckets_diff = array_diff($buckets, $buckets_existing);

        $this->assertTrue(empty($buckets_diff), 'Expected pre-processed statistical data on these buckets but got none (maybe you need to define in this test the db tables used so dummy data can be generated): ' . implode(', ', $buckets_diff));

        // Remove old preprocessed stats so we can force pre-processing again
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed');
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed_flat');

        // Delete dummy data
        if (count($this->dummy_data_added) > 0) {
            foreach ($this->dummy_data_added as $table => $rows) {
                $db = get_db_for($table);
                foreach ($rows as $primary_map) {
                    if ($this->debug) {
                        $rows = $db->query_select($table, ['*'], $primary_map);
                        $this->dump($primary_map, $table . ' primary map');
                        $this->dump($rows, $table . ' data');
                    }
                    $db->query_delete($table, $primary_map);
                }
            }

            // FUDGE: We had to fudge points lifetime so re-calculate this
            if (addon_installed('points')) {
                require_code('tasks');
                call_user_func_array__long_task(do_lang('points:POINTS_CACHE'), null, 'points_recalculate_cpf', [], true, true, false);
            }
        }

        // Re-populate with actual statistics so stats do not show our dummy ones
        $hook_obs = find_all_hook_obs('modules', 'admin_stats', 'Hook_admin_stats_');
        foreach ($hook_obs as $hook_name => $ob) {
            $_buckets = $ob->info();
            if ($_buckets === null) {
                continue;
            }
            preprocess_raw_data_for($hook_name, $start_time, $end_time);
        }

        pop_query_limiting();
    }

    public function testGenerateFinalData()
    {
        require_code('stats');
        require_code('temporal');
        require_lang('stats');

        $p_month = get_stats_month_for_timestamp(time());

        $bucket_hook = [];
        $bucket_filters = [];
        $hook_obs = find_all_hook_obs('modules', 'admin_stats', 'Hook_admin_stats_');
        foreach ($hook_obs as $hook_name => $ob) {
            $_buckets = $ob->info();
            if ($_buckets === null) {
                continue;
            }
            foreach (array_keys($_buckets) as $bucket) {
                $bucket_hook[$bucket] = $hook_name;
                $bucket_filters[$bucket] = (((isset($_buckets['filters'])) && (is_array($_buckets['filters']))) ? $_buckets['filters'] : []);
            }
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_bucket', 'p_pivot'], ['p_month' => $p_month]);
        foreach ($rows as $row) {
            $this->assertTrue(isset($bucket_hook[$row['p_bucket']]), 'Orphaned bucket in database: ' . $row['p_bucket']);
            if (isset($bucket_hook[$row['p_bucket']])) {
                $this->run_filter_tests($bucket_filters[$row['p_bucket']], $hook_obs[$bucket_hook[$row['p_bucket']]], $row['p_bucket'], $row['p_pivot'], $p_month);
            }
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed_flat', ['p_bucket'], []);
        foreach ($rows as $row) {
            $this->assertTrue(isset($bucket_hook[$row['p_bucket']]), 'Orphaned bucket in database: ' . $row['p_bucket']);
            if (isset($bucket_hook[$row['p_bucket']])) {
                $this->run_filter_tests($bucket_filters[$row['p_bucket']], $hook_obs[$bucket_hook[$row['p_bucket']]], $row['p_bucket'], '', $p_month);
            }
        }
    }

    protected function run_filter_tests(array $filters, $hook, string $bucket, string $pivot, int $p_month)
    {
        // Test that the filters do not cause crashes (TODO: does not yet actually test the filters filter as they should)
        foreach ($filters as $filter_name => $filter_class) {
            // Test month range filters
            if ($filter_class instanceof CMSStatsDateMonthRangeFilter) {
                // Test integer filter
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => $p_month
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for month range filter (integer) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);

                // Test integer filter in array format
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => [$p_month]
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for month range filter (single item array) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);

                // Test integer filter in array range format
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => [$p_month - 1, $p_month]
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for month range filter (array range) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);
            }

            // Test text filters
            if ($filter_class instanceof CMSStatsTextFilter) {
                // Test blank filter
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => ''
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for text filter (blank) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);

                // Test non-blank filter
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => 'qwertyuiop'
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for text filter (blank) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);
            }

            // Test tick filters
            if ($filter_class instanceof CMSStatsTickFilter) {
                // Test un-ticked
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => '0'
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for tick filter (off) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);

                // Test ticked
                $data = $hook->generate_final_data($bucket, $pivot, [
                    $filter_name => '1'
                ]);
                $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for tick filter (on) on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);
            }

            // Test list filters
            if ($filter_class instanceof CMSStatsListFilter) {
                foreach ($filter_class->get_list_values() as $key => $val) {
                    $data = $hook->generate_final_data($bucket, $pivot, [
                        $filter_name => $val
                    ]);
                    $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for list filter (' . $val . ') on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);
                }
            }

            // Test pivot filters
            if ($filter_class instanceof CMSStatsDatePivot) {
                foreach ($filter_class->get_pivot_values() as $key => $val) {
                    $data = $hook->generate_final_data($bucket, $pivot, [
                        $filter_name => $val
                    ]);
                    $this->assertTrue(is_array($data) && (count($data) == 4), 'Did not receive standardised map data for pivot filter (' . $val . ') on ' . $bucket . '=>' . $pivot . '=>' . $filter_name);
                }
            }
        }
    }
}
