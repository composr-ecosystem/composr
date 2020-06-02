<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('classified_ads')) {
    return do_template('RED_ALERT', ['_GUID' => 'y1h3lkkxrcy2mjehmt7na6n0p6c4ed23', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('classified_ads'))]);
}

if (!addon_installed('catalogues')) {
    return do_template('RED_ALERT', ['_GUID' => 'xosodjlsl900rswpx7vw68a7xg2tgjod', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('catalogues'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => 'hf9170z7ri5420765w7qecb5lkqr7i4t', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
}

require_lang('classifieds');

if (!isset($map['param'])) {
    $map['param'] = 'classifieds';
}
$catalogue_name = $map['param'];

$show_free = ((isset($map['show_free'])) && ($map['show_free'] == '1'));

$prices = $GLOBALS['SITE_DB']->query_select('ecom_classifieds_prices', ['*'], ['c_catalogue_name' => $catalogue_name], 'ORDER BY c_price');

$data = [];
foreach ($prices as $price) {
    if ((!$show_free) && ($price['c_price'] == 0.0)) {
        continue;
    }

    $data[] = [
        'PRICE' => float_to_raw_string($price['c_price']),
        'CURRENCY' => get_option('currency'),
        'LABEL' => get_translated_text($price['c_label']),
    ];
}

echo static_evaluate_tempcode(do_template('CLASSIFIEDS', ['_GUID' => '7216f4a435534cc609344101c8ea3031', 'DATA' => $data]));
