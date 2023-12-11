<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$existing_customer = !is_guest() && !is_null($GLOBALS['SITE_DB']->query_select_value_if_there('credit_purchases', 'num_credits', array('member_id' => get_member())));

require_lang('customers');
require_lang('tickets');
require_code('tickets');
require_code('tickets2');

$credits = intval(get_cms_cpf('support_credits'));

$professional_support_url = build_url(array('page' => 'professional_support'), '_SEARCH');

if ($credits == 0) {
    $whats_this = do_lang_tempcode('SHOW_CREDITS_WHATS_THIS', escape_html($professional_support_url->evaluate()));
} else {
    $whats_this = new Tempcode();
}

if ($credits == 0) {
    $credits_msg = do_lang_tempcode('SHOW_CREDITS_NO_CREDITS');
    $help_link = build_url(array('page' => 'tut_software_feedback'), '_SEARCH');
    $no_credits_link = do_lang_tempcode('SHOW_CREDITS_NO_CREDITS_LINK', escape_html($help_link->evaluate()));
} else {
    $credits_msg = do_lang_tempcode('SHOW_CREDITS_SOME_CREDITS', escape_html(integer_format($credits)), escape_html($professional_support_url->evaluate()));
    $no_credits_link = new Tempcode();
}

$query = '';
$topic_filters = array();
$restrict = strval(get_member()) . '\_%';
$restrict_description = do_lang('SUPPORT_TICKET') . ': #' . $restrict;
$topic_filters[] = 't_cache_first_title LIKE \'' . db_encode_like($restrict) . '\'';
$topic_filters[] = 't_description LIKE \'' . db_encode_like($restrict_description) . '\'';
foreach ($topic_filters as $topic_filter) {
    if ($query != '') {
        $query .= ' + ';
    }
    $query .= '(SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics WHERE t_forum_id=' . strval(get_ticket_forum_id(null, null, false)) . ' AND ' . $topic_filter . ' AND t_is_open=1)';
}
$tickets_url = build_url(array('page' => 'tickets', 'type' => 'browse'), get_module_zone('tickets'));
$tickets_open = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT ' . $query, false, true);
$tickets_open_msg = do_lang_tempcode('SHOW_CREDITS_TICKETS_OPEN', escape_html(integer_format($tickets_open)), escape_html($tickets_url->evaluate()));

$tpl = do_template('SHOW_CREDITS_BAR', array(
    '_GUID' => '43e6e18c180cda2e6f4627d2a2bb8677',

    'WHATS_THIS' => $whats_this,

    'CREDITS_MSG' => $credits_msg,
    'CREDITS' => integer_format($credits),
    '_CREDITS' => strval($credits),
    'NO_CREDITS_LINK' => $no_credits_link,

    'TICKETS_OPEN_MSG' => $tickets_open_msg,
    'TICKETS_OPEN' => integer_format($tickets_open),
));
$tpl->evaluate_echo();
