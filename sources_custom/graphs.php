<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sortable_tables
 */

function _generate_graph_id()
{
    return md5(uniqid('', true));
}

function _normalise_graph_dims(&$width, &$height)
{
    if ($width === null) {
        $width = '';
    }
    if ($height === null) {
        $height = '';
    }
    // ^ If both are blank, it'll be responsive

    if (is_numeric($width)) {
        $width = $width . 'px';
    }
    if (is_numeric($height)) {
        $height = $height . 'px';
    }
}

function _generate_graph_color_pool(&$color_pool)
{
    $color_pool[] = '#c24a4a';
    $color_pool[] = '#4a4ac2';
    $color_pool[] = '#c2c24a';
}

function _search_graph_color_pool($i, $color_pool)
{
    return $color_pool[$i % count($color_pool)];
}

// 1 measure of scattered data across two uneven dimensions
function graph_scatter_diagram($datapoints, $x_axis_label = '', $y_axis_label = '', $begin_at_zero = false, $color = null, $width = null, $height = null)
{
    if ($color === null) {
        $color_pool = [];
        _generate_graph_color_pool($color_pool);
        $color = $color_pool[0];
    }

    $id = _generate_graph_id();

    _normalise_graph_dims($width, $height);

    $_datapoints = [];
    foreach ($datapoints as $p) {
        $_datapoints[] = [
            'X' => @strval($p['x']),
            'Y' => @strval($p['y']),
            'TOOLTIP' => array_key_exists('tooltip', $p) ? $p['tooltip'] : '',
        ];
    }

    return do_template('GRAPH_SCATTER_DIAGRAM', [
        '_GUID' => 'a3fc255270253893b7550f18f9f94fca',
        'ID' => $id,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'X_AXIS_LABEL' => $x_axis_label,
        'Y_AXIS_LABEL' => $y_axis_label,
        'DATAPOINTS' => $_datapoints,
        'COLOR' => $color,
        'BEGIN_AT_ZERO' => $begin_at_zero,
    ]);
}

// Multiple measures across one even dimension (x) and one uneven dimension (y)
function graph_line_chart($datasets, $x_labels = null, $x_axis_label = '', $y_axis_label = '', $begin_at_zero = true, $show_data_labels = true, $color_pool = [], $width = null, $height = null)
{
    _generate_graph_color_pool($color_pool);

    if (is_mobile()) {
        $show_data_labels = false;
    }

    $id = _generate_graph_id();

    _normalise_graph_dims($width, $height);

    if (empty($datasets)) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    if ($x_labels === null) {
        $x_labels = range(0, count($datasets[0]['datapoints']));
    }

    $_datasets = [];
    foreach ($datasets as $i => $dataset) {
        if (count($dataset['datapoints']) != count($x_labels)) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        $datapoints = [];
        foreach ($dataset['datapoints'] as $p) {
            if (is_array($p)) {
                $datapoints[] = [
                    'VALUE' => @strval($p['value']),
                    'TOOLTIP' => array_key_exists('tooltip', $p) ? $p['tooltip'] : '',
                ];
            } else {
                $datapoints[] = [
                    'VALUE' => @strval($p),
                    'TOOLTIP' => '',
                ];
            }
        }

        $_datasets[] = [
            'LABEL' => $dataset['label'],
            'COLOR' => isset($dataset['color']) ? $dataset['color'] : _search_graph_color_pool($i, $color_pool),
            'DATAPOINTS' => $datapoints,
        ];
    }

    return do_template('GRAPH_LINE_CHART', [
        '_GUID' => '4a45757f02c5356c6b87a1c8d6366d49',
        'ID' => $id,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'X_LABELS' => $x_labels,
        'X_AXIS_LABEL' => $x_axis_label,
        'Y_AXIS_LABEL' => $y_axis_label,
        'DATASETS' => $_datasets,
        'BEGIN_AT_ZERO' => $begin_at_zero,
        'SHOW_DATA_LABELS' => $show_data_labels,
    ]);
}

// 1 measure across one small even dimension (different segments) and one uneven dimension (angle) [unlabelled dimensions]
function graph_pie_chart($datapoints, $show_data_labels = true, $color_pool = [], $width = null, $height = null)
{
    _generate_graph_color_pool($color_pool);

    if (is_mobile()) {
        $show_data_labels = false;
    }

    $id = _generate_graph_id();

    _normalise_graph_dims($width, $height);

    $i = 0;
    $_datapoints = [];
    foreach ($datapoints as $x => $p) {
        if (is_array($p)) {
            $_datapoints[] = [
                'LABEL' => $p['label'],
                'VALUE' => @strval($p['value']),
                'TOOLTIP' => array_key_exists('tooltip', $p) ? $p['tooltip'] : '',
                'COLOR' => _search_graph_color_pool($i, $color_pool),
            ];
        } else {
            $_datapoints[] = [
                'LABEL' => @strval($x),
                'VALUE' => @strval($p),
                'TOOLTIP' => '',
                'COLOR' => _search_graph_color_pool($i, $color_pool),
            ];
        }
        $i++;
    }

    return do_template('GRAPH_PIE_CHART', [
        '_GUID' => '24a351a8cc04f0777b2016ab2ede35cc',
        'ID' => $id,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'DATAPOINTS' => $_datapoints,
        'SHOW_DATA_LABELS' => $show_data_labels,
    ]);
}

// 1 measure across one large even dimension (x) and one uneven dimension (y)
function graph_bar_chart($datapoints, $x_axis_label = '', $y_axis_label = '', $begin_at_zero = true, $show_data_labels = true, $color_pool = [], $width = null, $height = null)
{
    _generate_graph_color_pool($color_pool);

    if (is_mobile()) {
        $show_data_labels = false;
    }

    $id = _generate_graph_id();

    _normalise_graph_dims($width, $height);

    $i = 0;
    $_datapoints = [];
    foreach ($datapoints as $x => $p) {
        if (is_array($p)) {
            $_datapoints[] = [
                'LABEL' => $p['label'],
                'VALUE' => @strval($p['value']),
                'TOOLTIP' => array_key_exists('tooltip', $p) ? $p['tooltip'] : '',
                'COLOR' => _search_graph_color_pool($i, $color_pool),
            ];
        } else {
            $_datapoints[] = [
                'LABEL' => @strval($x),
                'VALUE' => @strval($p),
                'TOOLTIP' => '',
                'COLOR' => _search_graph_color_pool($i, $color_pool),
            ];
        }
        $i++;
    }

    return do_template('GRAPH_BAR_CHART', [
        '_GUID' => '173df546b9bcb31ca064910e1952e484',
        'ID' => $id,
        'WIDTH' => $width,
        'HEIGHT' => $height,
        'X_AXIS_LABEL' => $x_axis_label,
        'Y_AXIS_LABEL' => $y_axis_label,
        'DATAPOINTS' => $_datapoints,
        'BEGIN_AT_ZERO' => $begin_at_zero,
        'SHOW_DATA_LABELS' => $show_data_labels,
    ]);
}
