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

function initialise_classified_listing(&$row)
{
    $free_days = $GLOBALS['SITE_DB']->query_select_value_if_there('ecom_classifieds_prices', 'MAX(c_days)', [
        'c_catalogue_name' => $row['c_name'],
        'c_price' => 0.00,
    ]);
    $row['ce_last_moved'] = $row['ce_add_date'];
    if ($free_days !== null) {
        $row['ce_last_moved'] += $free_days * 60 * 60 * 24;
    }
    $GLOBALS['SITE_DB']->query_update('catalogue_entries', ['ce_last_moved' => $row['ce_last_moved']], ['id' => $row['id']], '', 1);
}
