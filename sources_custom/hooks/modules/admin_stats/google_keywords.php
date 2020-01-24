<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function info($for_kpi = false)
    {
        if (!addon_installed('google_search_console')) {
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
    public function preprocess_raw_data($start_time, $end_time, &$data_buckets)
    {
        // Google does this for us
    }

    /**
     * Get Google Search Console data that is behind all our hook graphs.
     *
     * @param  integer $start_month Start month
     * @param  integer $end_month End month
     * @param  string $keyword Keyword
     * @return boolean Data
     */
    protected function get_data($_start_month, $_end_month, $keyword)
    {
        $sz = serialize([$_start_month, $_end_month, $keyword]);

        static $result = [];
        if (isset($result[$sz])) {
            return $result[$sz];
        }

        require_code('oauth');
        $access_token = refresh_oauth2_token('google_search_console', false);

        $base_url = get_base_url();
        $url = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($base_url) . '/searchAnalytics/query?access_token=' . urlencode($access_token);

        $start_year = 1970 + intval(round(floatval($_start_month) / 12.0));
        $start_month = ($_start_month % 12) + 1;
        $start_day = 1;

        $end_year = 1970 + intval(round(floatval($_end_month) / 12.0));
        $end_month = ($_end_month % 12) + 1;
        $days_in_month = intval(date('t', mktime(0, 0, 0, $end_month, 1, $end_year)));
        $end_day = $days_in_month;

        $_json = [
            'startDate' => strval($start_year) . '-' . str_pad(strval($start_month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($start_day), 2, '0', STR_PAD_LEFT),
            'endDate' => strval($end_year) . '-' . str_pad(strval($end_month), 2, '0', STR_PAD_LEFT) . '-' . str_pad(strval($end_day), 2, '0', STR_PAD_LEFT),
            'dimensions' => ['query'],
            'rowLimit' => 1000,
        ];

        if ($keyword !== null) {
            $_json['dimensionFilterGroups'] = [
                [
                    'groupType' => 'and',
                    'filters' => [
                        [
                            'dimension' => 'query',
                            'operator' => 'contains',
                            'expression' => $keyword,
                        ],
                    ],
                ],
            ];
        }

        $json = json_encode($_json);

        require_code('character_sets');
        $json = convert_to_internal_encoding($json, get_charset(), 'utf-8');

        $_result = http_get_contents($url, ['trigger_error' => false, 'convert_to_internal_encoding' => true, 'post_params' => [$json], 'raw_post' => true, 'raw_content_type' => 'application/json']);
        if ($_result === null) {
            attach_message('Failed to query the Google Search Console API', 'warn');
            return null;
        }
        $result[$sz] = json_decode($_result, true);
        return $result[$sz];
    }

    /**
     * Generate final data from preprocessed data.
     *
     * @param  string $bucket Data bucket we want data for
     * @param  string $pivot Pivot value
     * @param  array $filters Map of filters (including pivot if applicable)
     * @return array Final data in standardised map format
     */
    public function generate_final_data($bucket, $pivot, $filters)
    {
        // https://developers.google.com/webmaster-tools/search-console-api-original/v3/searchanalytics/query

        $_start_month = $filters[$bucket . '__month_range'][0];
        $_end_month = $filters[$bucket . '__month_range'][1];
        if (!empty($filters[$bucket . '__keyword'])) {
            $keyword = str_replace(['*', '?'], ['', ''], $filters[$bucket . '__keyword']);
        } else {
            $keyword = null;
        }

        $result = $this->get_data($_start_month, $_end_month, $keyword);
        if ($result === null) {
            return null;
        }

        $data = [];

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
