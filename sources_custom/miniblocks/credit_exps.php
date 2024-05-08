<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('cms_homesite_support_credits')) {
    return do_template('RED_ALERT', ['_GUID' => '6i3v3cs3s5ia4gu7mqcegz643hqz5nb6', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite_suppoer_credits'))]);
}

if (!addon_installed('cms_homesite_tracker')) {
    return do_template('RED_ALERT', ['_GUID' => 'ce9dcb66775b5cc16da92f3ca43f209d', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite_tracker'))]);
}

if (!addon_installed('tickets')) {
    return do_template('RED_ALERT', ['_GUID' => 'tivtotrvmlpkmqizx5toryotzigbrq8f', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('tickets'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => 'qkf23refnl212qwqn7foixklt4fmz8l7', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
}
if (!addon_installed('points')) {
    return do_template('RED_ALERT', ['_GUID' => 'iknm2uc9sjay5y7j71mqgec3jnqpuloz', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('points'))]);
}

if (get_forum_type() != 'cns') {
    return do_template('RED_ALERT', ['_GUID' => '3umhxm898adwsf01425xck2ub40zj9v7', 'TEXT' => do_lang_tempcode('NO_CNS')]);
}

if (strpos(get_db_type(), 'mysql') === false) {
    return do_template('RED_ALERT', ['_GUID' => '4wr6c3yhtmnlnprymsibx35b7mjnuwm1', 'TEXT' => 'This works with MySQL only']);
}

$block_id = get_block_id($map);

$backburner_minutes = integer_format(intval(get_option('support_priority_backburner_minutes')));
$regular_minutes = integer_format(intval(get_option('support_priority_regular_minutes')));
$currency = get_option('currency', true);

require_lang('customers');

require_code('ecommerce');
require_code('hooks/systems/ecommerce/support_credits');

$ob = new Hook_ecommerce_support_credits();
$products = $ob->get_products();

$credit_kinds = [];
foreach ($products as $p => $v) {
    $num_credits = str_replace('_CREDITS', '', $p);
    if ((intval($num_credits) < 1) && ($GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM mantis_sponsorship_table WHERE user_id=' . strval(get_member())) === null)) {
        continue;
    }

    $price = $v[1];

    $msg = do_lang('BLOCK_CREDITS_EXP_INNER_MSG', strval($num_credits), $currency, [float_format($price), ecommerce_get_currency_symbol($currency)]);

    $credit_kinds[] = [
        'NUM_CREDITS' => $num_credits,
        'PRICE' => float_to_raw_string($price),

        'BACKBURNER_MINUTES' => $backburner_minutes,
        'REGULAR_MINUTES' => $regular_minutes,
    ];
}

$tpl = do_template('BLOCK_CREDIT_EXPS_INNER', [
    '_GUID' => '6c6134a1b7157637dae280b54e90a877',
    'BLOCK_ID' => $block_id,
    'CREDIT_KINDS' => $credit_kinds,
]);
$tpl->evaluate_echo();
