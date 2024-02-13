<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    google_search_console
 */

/**
 * Hook class.
 */
class Hook_admin_stats_google_keywords extends CMSStatsProvider
{
    /**
     * Find metadata about stats graphs that are provided by this stats hook.
     *
     * @param  boolean $for_kpi Whether this is for setting up a KPI
     * @return ?array Map of metadata (null: hook is disabled)
     */
    public function info(bool $for_kpi = false) : ?array
    {
        if (!addon_installed('google_search_console')) {
            return null;
        }

        if (get_option('google_apis_api_key') == '') {
            return null;
        }

        require_code('oauth');
        $refresh_token = get_oauth_refresh_token('google_search_console');
        if ($refresh_token === null) {
            return null;
        }

        list($min_month, $max_month) = find_known_stats_date_month_bounds();

        return [
            'google_keywords_hits' => [
                'label' => do_lang_tempcode('GOOGLE_KEYWORDS_HITS'),
                'category' => 'search_traffic',
                'filters' => [
                    'google_keywords_hits__month_range' => new CMSStatsDateMonthRangeFilter('google_keywords_hits__month_range', do_lang_tempcode('DATE_RANGE'), [$max_month - 12, $max_month], $for_kpi),
                    'google_keywords_hits__keyword' => new CMSStatsTextFilter('google_keywords_hits__keyword', do_lang_tempcode('KEYWORD')),
                ],
                'pivot' => null,
            ],
            'google_keywords_impressions' => [
                'label' => do_lang_tempcode('GOOGLE_KEYWORDS_IMPRESSIONS'),
                'category' => 'search_traffic',
                'filters' => [
                    'google_keywords_impressions__month_range' => new CMSStatsDateMonthRangeFilter('google_keywords_impressions__month_range', do_lang_tempcode('DATE_RANGE'), [$max_month - 12, $max_month], $for_kpi),
                    'google_keywords_impressions__keyword' => new CMSStatsTextFilter('google_keywords_impressions__keyword', do_lang_tempcode('KEYWORD')),
                ],
                'pivot' => null,
            ],
            'google_keywords_ctr' => [
                'label' => do_lang_tempcode('GOOGLE_KEYWORDS_CTR'),
                'category' => 'search_traffic',
                'filters' => [
                    'google_keywords_ctr__month_range' => new CMSStatsDateMonthRangeFilter('google_keywords_ctr__month_range', do_lang_tempcode('DATE_RANGE'), [$max_month - 12, $max_month], $for_kpi),
                    'google_keywords_ctr__keyword' => new CMSStatsTextFilter('google_keywords_ctr__keyword', do_lang_tempcode('KEYWORD')),
                ],
                'pivot' => null,
            ],
            'google_keywords_positions' => [
                'label' => do_lang_tempcode('GOOGLE_KEYWORDS_POSITIONS'),
                'category' => 'search_traffic',
                'filters' => [
                    'google_keywords_positions__month_range' => new CMSStatsDateMonthRangeFilter('google_keywords_positions__month_range', do_lang_tempcode('DATE_RANGE'), [$max_month - 12, $max_month], $for_kpi),
                    'google_keywords_positions__keyword' => new CMSStatsTextFilter('google_keywords_positions__keyword', do_lang_tempcode('KEYWORD')),
                ],
                'pivot' => null,
            ],
        ];
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
        // Google does this for us
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
        // https://developers.google.com/webmaster-tools/search-console-api-original/v3/searchanalytics/query

        $range = $this->convert_month_range_filter_to_pair($filters[$bucket . '__month_range']);

        $_start_month = $range[0];
        $_end_month = $range[1];
        if (!empty($filters[$bucket . '__keyword'])) {
            $keyword = str_replace(['*', '?'], ['', ''], $filters[$bucket . '__keyword']);
        } else {
            $keyword = null;
        }

        try {
            $result = get_google_search_console_data($_start_month, $_end_month, $keyword, null);
        } catch (Exception $e) {
            attach_message('Google Search Console API: ' . $e->getMessage(), 'warn', false, true);
            return null;
        }

        $data = [];

        if (!isset($result['rows'])) {
            attach_message('No data from Google Search Console API', 'warn', false, true);
            return null;
        }

        foreach ($result['rows'] as $row) {
            $keyword = $row['keys'][0];

            switch ($bucket) {
                case 'google_keywords_hits':
                    $data[$keyword] = intval($row['clicks']);
                    break;
                case 'google_keywords_impressions':
                    $data[$keyword] = intval($row['impressions']);
                    break;
                case 'google_keywords_ctr':
                    $data[$keyword] = $row['ctr'] * 100.0;
                    break;
                case 'google_keywords_positions':
                    $data[$keyword] = intval($row['position']);
                    break;
            }
        }

        switch ($bucket) {
            case 'google_keywords_hits':
                return [
                    'type' => self::GRAPH_BAR_CHART,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('KEYWORD'),
                    'y_axis_label' => do_lang_tempcode('GOOGLE_KEYWORDS_HITS'),
                    'limit_bars' => true,
                ];
            case 'google_keywords_impressions':
                return [
                    'type' => self::GRAPH_BAR_CHART,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('KEYWORD'),
                    'y_axis_label' => do_lang_tempcode('GOOGLE_KEYWORDS_IMPRESSIONS'),
                    'limit_bars' => true,
                ];
            case 'google_keywords_ctr':
                return [
                    'type' => self::GRAPH_BAR_CHART,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('KEYWORD'),
                    'y_axis_label' => do_lang_tempcode('GOOGLE_KEYWORDS_CTR'),
                    'limit_bars' => true,
                ];
            case 'google_keywords_positions':
                return [
                    'type' => self::GRAPH_BAR_CHART,
                    'data' => $data,
                    'x_axis_label' => do_lang_tempcode('KEYWORD'),
                    'y_axis_label' => do_lang_tempcode('GOOGLE_KEYWORDS_POSITIONS'),
                    'limit_bars' => true,
                    'low_first' => true,
                    'skip_other_bar' => true,
                ];
        }

        fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }
}
