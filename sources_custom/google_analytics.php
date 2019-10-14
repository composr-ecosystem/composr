<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_analytics
 */

/*
Documentation...

Intro: https://developers.google.com/analytics/devguides/reporting/embed/v1/
Samples: https://ga-dev-tools.appspot.com/embed-api/
Embed API reference: https://developers.google.com/analytics/devguides/reporting/embed/v1/component-reference
Metrics and dimension reference: https://developers.google.com/analytics/devguides/reporting/core/dimsmets
*/

function init__google_analytics()
{
    load_csp(array('csp_allow_eval_js' => '1')); // Needed for its JSON implementation to work
}

function google_analytics_initialise($weak_test = false)
{
    $property_id = get_value('ga_property_id', null, true);
    if ($property_id === null) {
        $msg = 'You need to set the Google Analytics <strong>View ID</strong> (this is different from the property ID which starts with <kbd>UA</kbd>) in Commandr with... <kbd>:set_value(\'ga_property_id\', \'SOME_NUMBER\');</kbd>';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }

    if ((get_option('google_apis_client_id') == '') || (get_option('google_apis_client_secret') == '')) {
        $msg = 'You need to configure the Google Client ID & Client Secret in the configuration';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }

    require_code('oauth');
    if ($weak_test) {
        $refresh_token = get_oauth_refresh_token('google_analytics');
        if ($refresh_token === null) {
            $msg = 'You need to configure the Google Analytics oAuth connection';
            return paragraph(make_string_tempcode($msg), 'red-alert');
        }

        return $refresh_token;
    } else {
        $access_token = refresh_oauth2_token('google_analytics', false);
        if ($access_token === null) {
            $msg = 'You need to configure the Google Analytics oAuth connection';
            return paragraph(make_string_tempcode($msg), 'red-alert');
        }
    }

    return $access_token;
}

function enumerate_google_analytics_metrics()
{
    $ret = array(
        'hits' => 'Hits',
        'bounces' => 'Bounces',
        'duration' => 'Session duration',
        'read_time' => 'Read time',
        'speed' => 'Speed',
        'browsers' => 'Browsers',
        'operating_systems' => 'Operating systems',
        'device_types' => 'Device types',
        'screen_sizes' => 'Screen sizes',
        'countries' => 'Countries',
        'ages' => 'Ages',
        'genders' => 'Genders',
        'languages' => 'Languages',
        'interests_affinities' => 'Interests: affinities',
        'interests_markets' => 'Interests: markets',
        'interests_other' => 'Interests: other',
        'referrers' => 'Referrers',
        'referrers_social' => 'Referrals: social',
        'referral_mediums' => 'Referrals: mediums',
        'entry_pages' => 'Entry pages',
        'exit_pages' => 'Exit pages',
        'popular_pages' => 'Popular pages',
    );

    require_code('oauth');
    $refresh_token = get_oauth_refresh_token('google_search_console');
    if ($refresh_token !== null) {
        $ret += array(
            'keywords' => 'Search keywords',
        );
    }

    return $ret;
}

function render_google_analytics($metric = '*', $id = null, $days = 31, $access_token = null)
{
    // Initialise, but only if not already done so
    if ($access_token === null) {
        $result = google_analytics_initialise();
        if (is_object($result)) {
            return $result;
        } else {
            $access_token = $result;
        }
    }

    require_javascript('google_analytics');

    // Tab view
    if (($metric === null) || (strpos($metric, ',') !== false) || ($metric == '*')) {
        return _render_google_analytics_tabs($metric, $days, $access_token);
    }

    // Direct chart view
    return _render_google_analytics_chart($metric, $id, $days, false, $access_token);
}

function _render_google_analytics_tabs($metric, $days, $access_token)
{
    $all_metrics = enumerate_google_analytics_metrics();

    if ($metric === null) {
        $metrics = array(
            'hits',
            'speed',
            'browsers',
            'device_types',
            'screen_sizes',
            'countries',
            'languages',
            'referrers',
            'referrers_social',
            'referral_mediums',
            'popular_pages',
        );
        if (array_key_exists('keywords', $all_metrics)) {
            $metrics[] = 'keywords';
        }
    } else {
        $metrics = ($metric == '*') ? array_keys($all_metrics) : explode(',', $metric);
    }

    $tab_contents = array();
    $tabs = array();

    $i = 0;
    foreach ($metrics as $metric) {
        $metric_title = $all_metrics[$metric];

        $tab_contents[] = array(
            'TITLE' => $metric_title,
            'CONTENT' => _render_google_analytics_chart($metric, fix_id($metric_title), $days, ($i != 0), $access_token),
        );

        $tabs[] = $metric_title;

        $i++;
    }

    return do_template('GOOGLE_ANALYTICS_TABS', array(
        '_GUID' => 'cc3382bab5e34421b05dd6f30343e4fc',
        'TABS' => $tabs,
        'TAB_CONTENTS' => $tab_contents,
        'SWITCH_TIME' => null,
        'PASS_ID' => 'ga',
    ));
}

function _render_google_analytics_chart($metric, $id, $days, $under_tab, $access_token)
{
    if ($id === null) {
        $id = md5(uniqid('', true));
    }

    if ($metric == 'keywords') {
        return _render_google_search_console_keywords($id, $days, $under_tab);
    }

    $property_id = get_value('ga_property_id', null, true);

    $extra = '';
    switch ($metric) {
        case 'hits':
            $metrics = array('ga:sessions', 'ga:users', 'ga:hits', 'ga:socialInteractions');
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'bounces':
            $metrics = array('ga:bounceRate');
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'duration':
            $metrics = array('ga:avgSessionDuration');
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'read_time':
            $metrics = array('ga:avgTimeOnPage');
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'speed':
            $metrics = array('ga:avgPageLoadTime');
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'browsers':
            $metrics = array('ga:sessions');
            $dimension = 'ga:browser';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' =>  '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'operating_systems':
            $metrics = array('ga:sessions');
            $dimension = 'ga:operatingSystem';
            $chart_type = 'PIE';
            break;

        case 'device_types':
            $metrics = array('ga:sessions');
            $dimension = 'ga:deviceCategory';
            $chart_type = 'PIE';
            break;

        case 'screen_sizes':
            $metrics = array('ga:sessions');
            $dimension = 'ga:screenResolution';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'countries':
            $metrics = array('ga:sessions');
            $dimension = 'ga:country';
            //$chart_type = 'GEO'; Does not work well unfortunately due to not having any logarithmic scale (so almost all countries are the same colour)
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'ages':
            $metrics = array('ga:sessions');
            $dimension = 'ga:userAgeBracket';
            $chart_type = 'PIE';
            break;

        case 'genders':
            $metrics = array('ga:sessions');
            $dimension = 'ga:userGender';
            $chart_type = 'PIE';
            break;

        case 'languages':
            $metrics = array('ga:sessions');
            $dimension = 'ga:language';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'interests_affinities':
            $metrics = array('ga:sessions');
            $dimension = 'ga:interestAffinityCategory';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 20,
            );
            break;

        case 'interests_markets':
            $metrics = array('ga:sessions');
            $dimension = 'ga:interestInMarketCategory';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 20,
            );
            break;

        case 'interests_other':
            $metrics = array('ga:sessions');
            $dimension = 'ga:interestOtherCategory';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 20,
            );
            break;

        case 'referrers':
            $metrics = array('ga:sessions');
            $dimension = 'ga:source';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'referrers_social':
            $metrics = array('ga:sessions');
            $dimension = 'ga:socialNetwork';
            $chart_type = 'PIE';
            break;

        case 'referral_mediums':
            $metrics = array('ga:sessions');
            $dimension = 'ga:medium';
            $chart_type = 'PIE';
            break;

        case 'entry_pages':
            $metrics = array('ga:sessions');
            $dimension = 'ga:landingPagePath';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'exit_pages':
            $metrics = array('ga:sessions');
            $dimension = 'ga:exitPagePath';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        case 'popular_pages':
            $metrics = array('ga:sessions');
            $dimension = 'ga:pageTitle';
            $chart_type = 'COLUMN';
            $extra = array(
                'sort' => '-ga:sessions',
                'max-results' => 10,
            );
            break;

        default:
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    return do_template('GOOGLE_ANALYTICS', array(
        '_GUID' => 'e783bf8d946c14dc3766a06ed93635fb',
        'ID' => $id,
        'UNDER_TAB' => $under_tab,
        'PROPERTY_ID' => strval($property_id),
        'ACCESS_TOKEN' => $access_token,
        'DAYS' => strval($days),
        'DIMENSION' => $dimension,
        'METRICS' => $metrics,
        'EXTRA' => $extra,
        'CHART_TYPE' => $chart_type,
    ));
}

// https://developers.google.com/webmaster-tools/search-console-api-original/v3/searchanalytics/query#dimensionFilterGroups.filters.dimension

function _render_google_search_console_keywords($id, $days, $under_tab)
{
    if ($id === null) {
        $id = md5(uniqid('', true));
    }

    require_code('oauth');
    $access_token = refresh_oauth2_token('google_search_console', false);
    if ($access_token === null) {
        $msg = 'You need to configure the Google Search Console oAuth connection';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }

    $base_url = get_base_url();
    $url = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($base_url) . '/searchAnalytics/query?access_token=' . urlencode($access_token);

    $_json = array(
        'startDate' => date('Y-m-d', time() - 60 * 60 * 24 * $days),
        'endDate' => date('Y-m-d'),
        'dimensions' => array('query'),
        'rowLimit' => 30,
    );
    $json = json_encode($_json);

    require_code('character_sets');
    $json = convert_to_internal_encoding($json, get_charset(), 'utf-8');

    $trigger_error = (!$under_tab) && (get_page_name() == 'admin_stats');
    $_result = http_get_contents($url, array('convert_to_internal_encoding' => true, 'trigger_error' => $trigger_error, 'post_params' => array($json), 'raw_post' => true, 'raw_content_type' => 'application/json'));
    if ($_result === null) {
        $msg = 'Failed to query the Google Search Console API';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }
    $result = json_decode($_result, true);

    require_code('templates_columned_table');

    $_header_row = array(
        'Query',
        'Clicks',
        'Impressions',
        'Click-through rate',
        'Position',
    );
    $header_row = columned_table_header_row($_header_row);

    $rows = new Tempcode();
    foreach ($result['rows'] as $row) {
        $values = array(
            $row['keys'][0],
            integer_format(intval($row['clicks'])),
            integer_format(intval($row['impressions'])),
            float_format($row['ctr'] * 100.0) . '%',
            integer_format(intval($row['position'])),
        );
        $rows->attach(columned_table_row($values, true));
    }

    $table = do_template('COLUMNED_TABLE', array('_GUID' => '00c9731002eea4822ca91d21a4d25fc0', 'HEADER_ROW' => $header_row, 'ROWS' => $rows));

    return do_template('GOOGLE_SEARCH_CONSOLE_KEYWORDS', array(
        '_GUID' => 'c7ff763455bd1fc31bab7a70c6859127',
        'ID' => $id,
        'TABLE' => $table,
        'DAYS' => strval($days),
    ));
}
