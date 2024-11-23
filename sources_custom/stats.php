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
 * Get Google Search Console data that is behind all our hook graphs.
 *
 * @param  ?integer $start_month Start month (null: a year ago)
 * @param  ?integer $end_month End month (null: today)
 * @param  ?string $keyword Keyword (null: no filter)
 * @param  ?URLPATH $url URL (null: base URL)
 * @return ?array Data (null: error)
 */
function get_google_search_console_data(?int $_start_month = null, ?int $_end_month = null, ?string $keyword = null, ?string $url = null) : ?array
{
    require_code('temporal');

    $sz = serialize([$_start_month, $_end_month, $keyword]);

    static $result = [];
    if (isset($result[$sz])) {
        return $result[$sz];
    }

    if ($url === null) {
        $url = get_base_url();
    }

    require_code('oauth');
    $access_token = refresh_oauth2_token('google_search_console', false);
    if ($access_token === null) {
        return null;
    }
    $url = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($url) . '/searchAnalytics/query?access_token=' . urlencode($access_token);

    if ($_start_month === null) {
        $_start_month = (to_epoch_interval_index(time(), 'months') - 12);
    }
    $start_year = 1970 + intval(round(floatval($_start_month) / 12.0));
    $start_month = ($_start_month % 12) + 1;
    $start_day = 1;

    if ($_end_month === null) {
        $_end_month = to_epoch_interval_index(time(), 'months');
    }
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

    $options = [
        'trigger_error' => false,
        'ignore_http_status' => true,
        'convert_to_internal_encoding' => true,
        'post_params' => $json,
        'raw_content_type' => 'application/json',
    ];
    $_result = cms_http_request($url, $options);

    $this_result = @json_decode($_result->data, true);

    if (!is_array($this_result)) {
        $errormsg = $_result->message;
        throw new Exception($errormsg);
    }

    if (array_key_exists('error', $this_result)) {
        $errormsg = $this_result['error']['errors'][0]['message'];
        throw new Exception($errormsg);
    }

    $result[$sz] = $this_result;

    return $this_result;
}
