<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

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
    load_csp(['csp_allow_eval_js' => '1']); // Needed for its JSON implementation to work
}

function google_analytics_initialise($weak_test = false)
{
    $property_id = get_option('ga_property_view_id');
    if ($property_id == '') {
        $msg = 'You need to set the Google Analytics <strong>View ID</strong> (this is different from the property ID which starts with <kbd>UA</kbd>).';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }

    if ((get_option('google_apis_client_id') == '') || (get_option('google_apis_client_secret') == '')) {
        $msg = 'You need to configure the Google Client ID & Client Secret in the configuration.';
        return paragraph(make_string_tempcode($msg), 'red-alert');
    }

    require_code('oauth');

    if ($weak_test) {
        static $refresh_token = null;
        if ($refresh_token === null) {
            $refresh_token = get_oauth_refresh_token('google_analytics');
            if ($refresh_token === null) {
                $msg = 'You need to configure the Google Analytics oAuth connection.';
                return paragraph(make_string_tempcode($msg), 'red-alert');
            }
        }
        return $refresh_token;
    }

    static $access_token = null;
    if ($access_token === null) {
        $access_token = refresh_oauth2_token('google_analytics', false);
        if ($access_token === null) {
            $msg = 'You need to configure the Google Analytics oAuth connection.';
            return paragraph(make_string_tempcode($msg), 'red-alert');
        }
    }
    return $access_token;
}

function enumerate_google_analytics_metrics()
{
    $ret = [
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
    ];

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
        $metrics = [
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
        ];
    } else {
        $metrics = ($metric == '*') ? array_keys($all_metrics) : explode(',', $metric);
    }

    $tab_contents = [];
    $tabs = [];

    $i = 0;
    foreach ($metrics as $metric) {
        $metric_title = $all_metrics[$metric];

        $tab_contents[] = [
            'TITLE' => $metric_title,
            'CONTENT' => _render_google_analytics_chart($metric, fix_id($metric_title), $days, ($i != 0), $access_token),
        ];

        $tabs[] = $metric_title;

        $i++;
    }

    return do_template('GOOGLE_ANALYTICS_TABS', [
        '_GUID' => 'cc3382bab5e34421b05dd6f30343e4fc',
        'TABS' => $tabs,
        'TAB_CONTENTS' => $tab_contents,
        'SWITCH_TIME' => null,
        'PASS_ID' => 'ga',
    ]);
}

function _render_google_analytics_chart($metric, $id, $days, $under_tab, $access_token)
{
    if ($id === null) {
        $id = md5(uniqid('', true));
    }

    $property_id = get_option('ga_property_view_id');

    $extra = [];
    switch ($metric) {
        case 'hits':
            $metrics = ['ga:sessions', 'ga:users', 'ga:hits', 'ga:socialInteractions'];
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'bounces':
            $metrics = ['ga:bounceRate'];
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'duration':
            $metrics = ['ga:avgSessionDuration'];
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'read_time':
            $metrics = ['ga:avgTimeOnPage'];
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'speed':
            $metrics = ['ga:avgPageLoadTime'];
            $dimension = 'ga:date';
            $chart_type = 'LINE';
            break;

        case 'browsers':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:browser';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' =>  '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'operating_systems':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:operatingSystem';
            $chart_type = 'PIE';
            break;

        case 'device_types':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:deviceCategory';
            $chart_type = 'PIE';
            break;

        case 'screen_sizes':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:screenResolution';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'countries':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:country';
            //$chart_type = 'GEO'; Does not work well unfortunately due to not having any logarithmic scale (so almost all countries are the same colour)
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'ages':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:userAgeBracket';
            $chart_type = 'PIE';
            break;

        case 'genders':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:userGender';
            $chart_type = 'PIE';
            break;

        case 'languages':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:language';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'interests_affinities':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:interestAffinityCategory';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 20,
            ];
            break;

        case 'interests_markets':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:interestInMarketCategory';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 20,
            ];
            break;

        case 'interests_other':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:interestOtherCategory';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 20,
            ];
            break;

        case 'referrers':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:source';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'referrers_social':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:socialNetwork';
            $chart_type = 'PIE';
            break;

        case 'referral_mediums':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:medium';
            $chart_type = 'PIE';
            break;

        case 'entry_pages':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:landingPagePath';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'exit_pages':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:exitPagePath';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        case 'popular_pages':
            $metrics = ['ga:sessions'];
            $dimension = 'ga:pageTitle';
            $chart_type = 'COLUMN';
            $extra = [
                'sort' => '-ga:sessions',
                'max-results' => 10,
            ];
            break;

        default:
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    return do_template('GOOGLE_ANALYTICS', [
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
    ]);
}
