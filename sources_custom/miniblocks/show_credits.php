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
    return do_template('RED_ALERT', ['_GUID' => '89c903c3bb7a548e87bb1965a87ee527', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite_support_credits'))]);
}

if (!addon_installed('tickets')) {
    return do_template('RED_ALERT', ['_GUID' => 'cd74f185a75b518290c97a39e7b3d298', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('tickets'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => '6bfadb11bb945d2c93a24407943d988c', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
}
if (!addon_installed('points')) {
    return do_template('RED_ALERT', ['_GUID' => '26c105b0494d5e1e9205c3d3929fe9e1', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('points'))]);
}

if (get_forum_type() != 'cns') {
    return do_template('RED_ALERT', ['_GUID' => 'f0697248d1df590c82286e3122447761', 'TEXT' => do_lang_tempcode('NO_CNS')]);
}

if (strpos(get_db_type(), 'mysql') === false) {
    return do_template('RED_ALERT', ['_GUID' => '6097150b7ba557b9a137a83a88eabbb8', 'TEXT' => 'This works with MySQL only']);
}

$existing_customer = !is_guest() && ($GLOBALS['SITE_DB']->query_select_value_if_there('credit_purchases', 'num_credits', ['member_id' => get_member()]) !== null);

require_lang('customers');
require_lang('tickets');
require_code('tickets');

$credits = intval(get_cms_cpf('support_credits'));

$professional_support_url = build_url(['page' => 'professional_support']);

if ($credits == 0) {
    $whats_this = do_lang_tempcode('SHOW_CREDITS_WHATS_THIS', escape_html($professional_support_url->evaluate()));
} else {
    $whats_this = new Tempcode();
}

if ($credits == 0) {
    $credits_msg = do_lang_tempcode('SHOW_CREDITS_NO_CREDITS');
    $help_url = build_url(['page' => 'tut_software_feedback']);
    $no_credits_link = do_lang_tempcode('SHOW_CREDITS_NO_CREDITS_LINK', escape_html($help_url->evaluate()));
} else {
    $credits_msg = do_lang_tempcode('SHOW_CREDITS_SOME_CREDITS', escape_html(integer_format($credits, 0)), escape_html($professional_support_url->evaluate()));
    $no_credits_link = new Tempcode();
}

$query = '';
$topic_filters = [];
$restrict = strval(get_member()) . '\_%';
$restrict_description = do_lang('SUPPORT_TICKET') . ': #' . $restrict;
$topic_filters[] = 't_cache_first_title LIKE \'' . db_encode_like($restrict) . '\'';
$topic_filters[] = 't_description LIKE \'' . db_encode_like($restrict_description) . '\'';
foreach ($topic_filters as $topic_filter) {
    if ($query != '') {
        $query .= ' + ';
    }
    $query .= '(SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics WHERE t_forum_id=' . strval(get_ticket_forum_id(null, false)) . ' AND ' . $topic_filter . ' AND t_is_open=1)';
}
$tickets_url = build_url(['page' => 'tickets', 'type' => 'browse'], get_module_zone('tickets'));
$tickets_open = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT ' . $query, false, true);
$tickets_open_msg = do_lang_tempcode('SHOW_CREDITS_TICKETS_OPEN', escape_html(integer_format($tickets_open, 0)), escape_html($tickets_url->evaluate()));

$tpl = do_template('SHOW_CREDITS_BAR', [
    '_GUID' => '43e6e18c180cda2e6f4627d2a2bb8677',

    'WHATS_THIS' => $whats_this,

    'CREDITS_MSG' => $credits_msg,
    '_CREDITS' => strval($credits),
    'CREDITS' => integer_format($credits),
    'NO_CREDITS_LINK' => $no_credits_link,

    'TICKETS_OPEN_MSG' => $tickets_open_msg,
    '_TICKETS_OPEN' => strval($tickets_open),
    'TICKETS_OPEN' => integer_format($tickets_open),
]);
$tpl->evaluate_echo();
