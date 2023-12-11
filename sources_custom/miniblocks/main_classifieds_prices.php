<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_lang('classifieds');

if (!isset($map['param'])) {
    $map['param'] = 'classifieds';
}
$catalogue_name = $map['param'];

$show_free = ((isset($map['show_free'])) && ($map['show_free'] == '1'));

$prices = $GLOBALS['SITE_DB']->query_select('classifieds_prices', array('*'), array('c_catalogue_name' => $catalogue_name), 'ORDER BY c_price');

$data = array();
foreach ($prices as $price) {
    if ((!$show_free) && ($price['c_price'] == 0.0)) {
        continue;
    }

    $data[] = array(
        'PRICE' => float_format($price['c_price'], 2),
        'LABEL' => get_translated_text($price['c_label']),
    );
}

echo static_evaluate_tempcode(do_template('CLASSIFIEDS', array('_GUID' => '7216f4a435534cc609344101c8ea3031', 'DATA' => $data)));
