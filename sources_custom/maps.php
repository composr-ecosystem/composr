<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    visualisation
 */

function init__maps()
{
    require_code('graphs');
}

function _normalise_map_dims(&$width, &$height)
{
    if ($width === null) {
        $width = '100%';
    }
    if ($height === null) {
        $height = '500px';
    }

    if (is_numeric($width)) {
        $width = $width . 'px';
    }
    if (is_numeric($height)) {
        $height = $height . 'px';
    }
}

function _generate_map_color_pool(&$color_pool)
{
    if ($color_pool === null) {
        $color_pool = [
            '#6FDEE2',
            '#34E200',
            '#738B0F',
            '#FFB000',
            '#E26074',
            '#FF0000',
        ];
    }

    if (empty($color_pool)) {
        fatal_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('130bdb18d3255c2399bbb060f0c7dd63')));
    }
}

function _search_map_color_pool($intensity, $max_intensity, $color_pool)
{
    if ($intensity === null) {
        $tmp_color_pool = [];
        _generate_graph_color_pool($tmp_color_pool);
        return $tmp_color_pool[0];
    }

    if ($max_intensity == 0) {
        return $color_pool[0];
    }

    //$color_index = @intval(($intensity / $max_intensity) * (count($color_pool) - 1));  Logarithmic better
    $color_index = @intval(round((log($intensity, 10.0) / log($max_intensity, 10.0)) * (count($color_pool) - 1)));

    return $color_pool[$color_index];
}

// Pins on a map (dependency: Google Maps)
function pins_on_map($data, $color_pool = null, $api_key = null, $width = null, $height = null)
{
    _generate_map_color_pool($color_pool);

    if ($api_key === null) {
        $api_key = get_option('google_apis_api_key');
    }

    $id = _generate_graph_id();

    _normalise_map_dims($width, $height);

    $max_intensity = 0;
    foreach ($data as $details) {
        $max_intensity = max($max_intensity, @intval($details['intensity']));
    }

    $_data = [];
    foreach ($data as $details) {
        $color = empty($details['color']) ? _search_map_color_pool(@cms_empty_safe($details['intensity']) ? null : @intval($details['intensity']), $max_intensity, $color_pool) : $details['color'];

        $_data[] = [
            'LATITUDE' => @strval($details['latitude']),
            'LONGITUDE' => @strval($details['longitude']),
            'COLOR' => $color,
            'INTENSITY' => @strval($details['intensity']),
            'LABEL' => $details['label'],
            'DESCRIPTION' => $details['description'],
        ];
    }

    return do_template('PINS_ON_MAP', [
        '_GUID' => 'f3392d43ffeca05bbd6a896769a11b69',
        'ID' => $id,
        'API_KEY' => $api_key,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'DATA' => $_data,
    ]);
}

// Regions on a map (dependency: Google Charts)
function countries_on_map($data, $intensity_label = 'Intensity', $color_pool = null, $show_labels = false, $api_key = null, $width = null, $height = null)
{
    _generate_map_color_pool($color_pool);

    if ($api_key === null) {
        $api_key = get_option('google_apis_api_key');
    }

    $id = _generate_graph_id();

    _normalise_map_dims($width, $height);

    $max_intensity = 0;
    foreach ($data as $details) {
        $max_intensity = max($max_intensity, @intval($details['intensity']));
    }

    $_data = [];
    foreach ($data as $details) {
        $_data[] = [
            'REGION' => $details['region'],
            'INTENSITY' => @strval($details['intensity']),
            'NORMALIZED_INTENSITY' => float_to_raw_string(@intval($details['intensity']) / $max_intensity),
            'DESCRIPTION' => $details['description'],
        ];
    }

    load_csp(['csp_allow_eval_js' => '1']); // Needed for its JSON implementation to work

    return do_template('COUNTRIES_ON_MAP', [
        '_GUID' => 'a21f3f25d345b36fec9e2a856627eb2a',
        'ID' => $id,
        'INTENSITY_LABEL' => $intensity_label,
        'SHOW_LABELS' => $show_labels,
        'API_KEY' => $api_key,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'DATA' => $_data,
        'COLOR_POOL' => $color_pool,
    ]);
}
