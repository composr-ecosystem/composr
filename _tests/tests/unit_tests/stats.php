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
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_set_time_limit(TIME_LIMIT_EXTEND__MODEST);

        push_query_limiting(false);
    }

    public function testPreprocessRawData()
    {
        require_code('stats');
        require_code('temporal');
        require_lang('stats');

        // Remove old preprocessed stats so we can force pre-processing again
        $p_month = get_stats_month_for_timestamp(time());
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed');
        $GLOBALS['SITE_DB']->query_delete('stats_preprocessed_flat');

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

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed', ['p_bucket'], ['p_month' => $p_month]);
        foreach ($rows as $row) {
            $buckets_existing[] = $row['p_bucket'];
        }

        $rows = $GLOBALS['SITE_DB']->query_select('stats_preprocessed_flat', ['p_bucket']);
        foreach ($rows as $row) {
            $buckets_existing[] = $row['p_bucket'];
        }

        $buckets = array_unique($buckets);
        $buckets_existing = array_unique($buckets_existing);
        $buckets_diff = array_diff($buckets, $buckets_existing);

        $this->assertTrue(empty($buckets_diff), 'No statistics were found for these buckets (you may need to generate activity for these buckets and/or install test content and/or run the stress test loader, and then re-run the test): ' . implode(', ', $buckets_diff));
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
                $bucket_filters[$bucket] = isset($_buckets['filters']) && is_array($_buckets['filters']) ? $_buckets['filters'] : [];
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

    public function tearDown()
    {
        pop_query_limiting();

        parent::tearDown();
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
