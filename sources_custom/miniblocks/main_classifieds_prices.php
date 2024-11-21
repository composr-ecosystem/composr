<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    classified_ads
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('classified_ads')) {
    return do_template('RED_ALERT', ['_GUID' => '9f069bc8cd075e8bb0e6d653208a0a8c', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('classified_ads'))]);
}

if (!addon_installed('catalogues')) {
    return do_template('RED_ALERT', ['_GUID' => '2bb675ffc41f5df9a3ade64d25c1d755', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('catalogues'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => '3316b32121235a6890629b838cd400bc', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
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
