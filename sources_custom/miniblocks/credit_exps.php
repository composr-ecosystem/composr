<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$backburner_minutes = integer_format(intval(get_option('support_priority_backburner_minutes')));
$regular_minutes = integer_format(intval(get_option('support_priority_regular_minutes')));
$s_currency = get_option('currency', true);
if (is_null($s_currency)) {
    $s_currency = 'USD';
}

require_lang('customers');

require_code('ecommerce');
require_code('hooks/systems/ecommerce/support_credits');

$ob = new Hook_ecommerce_support_credits();
$products = $ob->get_products();

$credit_kinds = array();
foreach ($products as $p => $v) {
    $num_credits = str_replace('_CREDITS', '', $p);
    if ((intval($num_credits) < 1) && (is_null($GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM mantis_sponsorship_table WHERE user_id=' . strval(get_member()))))) {
        continue;
    }

    $msg = do_lang('BLOCK_CREDITS_EXP_INNER_MSG', strval($num_credits), strval($s_currency), float_format($v[1]));

    $credit_kinds[] = array(
        'S_CURRENCY' => $s_currency,
        'NUM_CREDITS' => $num_credits,
        'PRICE' => float_format($v[1]),

        'BACKBURNER_MINUTES' => $backburner_minutes,
        'REGULAR_MINUTES' => $regular_minutes,
    );
}

$tpl = do_template('BLOCK_CREDIT_EXPS_INNER', array('_GUID' => '6c6134a1b7157637dae280b54e90a877', 'CREDIT_KINDS' => $credit_kinds));
$tpl->evaluate_echo();
