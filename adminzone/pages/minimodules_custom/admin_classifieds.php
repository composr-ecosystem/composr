<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_lang('classifieds');
require_lang('catalogues');

require_javascript('checking');
require_javascript('editing');

// Handle result, if set
if (count($_POST) != 0) {
    foreach (array_keys($_POST) as $key) {
        $matches = array();
        if (preg_match('#^catalogue\_(existing|new)\_(\d*)$#', $key, $matches) != 0) {
            $catalogue = post_param_string('catalogue_' . $matches[1] . '_' . $matches[2], '');
            $days = post_param_string('days_' . $matches[1] . '_' . $matches[2], '');
            $label = post_param_string('label_' . $matches[1] . '_' . $matches[2], '');
            $price = post_param_string('price_' . $matches[1] . '_' . $matches[2], '');

            if (($catalogue != '') && ($days != '') && ($label != '') && ($price != '')) {
                if ($matches[1] == 'existing') {
                    $_label = $GLOBALS['SITE_DB']->query_select_value_if_there('classifieds_prices', 'c_label', array('id' => intval($matches[2])));
                    if (is_null($_label)) {
                        $matches[1] = 'new'; // Was lost, so add as new
                    }
                }
                if ($matches[1] == 'existing') {
                    // Edit
                    $GLOBALS['SITE_DB']->query_update(
                        'classifieds_prices',
                        array(
                            'c_catalogue_name' => $catalogue,
                            'c_days' => intval($days),
                            'c_price' => floatval($price),
                        ) + lang_remap('c_label', $_label, $label),
                        array('id' => intval($matches[2])),
                        '',
                        1
                    );
                } else {
                    // Add
                    $GLOBALS['SITE_DB']->query_insert(
                        'classifieds_prices',
                        array(
                            'c_catalogue_name' => $catalogue,
                            'c_days' => intval($days),
                            'c_price' => floatval($price),
                        ) + insert_lang('c_label', $label, 2)
                    );
                }
            } else {
                if ($matches[1] == 'existing') {
                    // Delete
                    $GLOBALS['SITE_DB']->query_delete('classifieds_prices', array('id' => intval($matches[2])), '', 1);
                }
            }
        }
    }

    attach_message(do_lang_tempcode('SUCCESS', 'inform'));
}

$title = get_screen_title('CLASSIFIEDS');

$_prices = $GLOBALS['SITE_DB']->query_select('classifieds_prices', array('*'), null, 'ORDER BY c_catalogue_name,c_days,c_price');
$prices = array();
foreach ($_prices as $_price) {
    $prices[] = array(
        'PRICE_CATALOGUE' => $_price['c_catalogue_name'],
        'PRICE_DAYS' => strval($_price['c_days']),
        'PRICE_LABEL' => get_translated_text($_price['c_label']),
        'PRICE_PRICE' => float_to_raw_string($_price['c_price']),
        'ID' => 'existing_' . strval($_price['id']),
    );
}
// 10 more
for ($i = 0; $i < 10; $i++) {
    $prices[] = array(
        'PRICE_CATALOGUE' => '',
        'PRICE_DAYS' => '',
        'PRICE_LABEL' => '',
        'PRICE_PRICE' => '',
        'ID' => 'new_' . strval($i),
    );
}

$_catalogues = $GLOBALS['SITE_DB']->query_select('catalogues', array('c_name', 'c_title'), null, 'ORDER BY c_name');
$catalogues = array();
foreach ($_catalogues as $_catalogue) {
    if (substr($_catalogue['c_name'], 0, 1) == '_') {
        continue;
    }

    $catalogues[$_catalogue['c_name']] = get_translated_text($_catalogue['c_title']);
}

$ret = do_template('CLASSIFIEDS_PRICING_SCREEN', array('_GUID' => '8fd97a8bc88dfdd5c8455d41d290ae56', 'TITLE' => $title, 'SUBMIT_ICON' => 'buttons__save', 'SUBMIT_NAME' => do_lang_tempcode('SAVE'), 'CATALOGUES' => $catalogues, 'PRICES' => $prices, 'POST_URL' => get_self_url()));
$ret->evaluate_echo();
