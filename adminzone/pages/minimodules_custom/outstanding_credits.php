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

$start = get_param_integer('start', 0);
$max = get_param_integer('max', 50);
if ($start == $max) {
    $max = $start + 50;
}
$csv = get_param_integer('csv', 0) == 1;
if ($csv) {
    require_code('files2');
    if (php_function_allowed('set_time_limit')) {
        @set_time_limit(0);
    }
    $start = 0;
    $max = 10000;
}
require_code('cns_members');
require_code('mantis');
require_lang('customers');
$title = get_screen_title('UNSPENT_SUPPORT_CREDITS');

$field_id = get_credits_profile_field_id();
if (!is_null($field_id)) {
    $field_id = strval($field_id);
    require_lang('cns');
    require_lang('stats');

    $uname = do_lang_tempcode('USERNAME');
    $ucredits = do_lang_tempcode('CREDITS');
    $ujoin = do_lang_tempcode('JOIN_DATE');
    $ulast = do_lang_tempcode('LAST_VISIT_TIME');

    $sortables = array('username' => $uname, 'credits' => $ucredits, 'join_date' => $ujoin, 'last_visit' => $ulast);
    $test = explode(' ', get_param_string('sort', 'username DESC'), 2);
    if (count($test) == 1) {
        $test[1] = 'DESC';
    }
    list($sortable, $sort_order) = $test;
    if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
        log_hack_attack_and_exit('ORDERBY_HACK');
    }
    inform_non_canonical_parameter('sort');
    $orderby = 'field_' . $field_id;
    switch ($sortable) {
        case 'username':
            $orderby = 'm_username';
            break;
        case 'join_date':
            $orderby = 'm_join_time';
            break;
        case 'last_visit':
            $orderby = 'm_last_visit_time';
            break;
        case 'credits':
        default:
            $orderby = 'field_' . $field_id;
            break;
    }

    require_code('templates_results_table');
    $fields_title = results_field_title(array($uname, $ucredits, $ujoin, $ulast), $sortables, 'sort', $sortable . ' ' . $sort_order);
    $fields_values = new Tempcode();

    $sql = 'SELECT a.m_username AS m_username, a.m_join_time AS m_join_time, a.m_last_visit_time AS m_last_visit_time, b.mf_member_id AS mf_member_id, field_' . $field_id . ' FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields b JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members a ON a.id=b.mf_member_id WHERE field_' . strval($field_id) . '>0 ORDER BY ' . $orderby . ' ' . $sort_order;
    $members = $GLOBALS['FORUM_DB']->query($sql, $max, $start);
    if (count($members) < 1) {
        $msg_tpl = inform_screen($title, do_lang_tempcode('NO_RESULTS_SORRY'));
        $msg_tpl->evaluate_echo();
        return;
    }
    $total = 0;
    $i = 0;
    foreach ($members as $member) {
        $credits = $member['field_' . $field_id];
        $member_id = intval($member['mf_member_id']);
        $member_name = $member['m_username'];
        $member_join_date = get_timezoned_date($member['m_join_time']);
        $member_visit_date = get_timezoned_date($member['m_last_visit_time']);

        if ($csv) {
            $csv_data = array();
            $sname = do_lang('USERNAME');
            $scredits = do_lang('CREDITS');
            $sjoin = do_lang('JOIN_DATE');
            $slast = do_lang('LAST_VISIT_TIME');
            $csv_data[] = array(
                $sname => $member_name,
                $scredits => $credits,
                $sjoin => $member_join_date,
                $slast => $member_visit_date
            );
        }

        $member_linked = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($member_id, false, '', false);
        $fields_values->attach(results_entry(array($member_linked, integer_format($credits), $member_join_date, $member_visit_date, true), true));
        $total += $credits;
        $i++;
    }
    if ($csv) {
        make_csv($csv_data, 'unspent_credits.csv');
    }
    $msg = do_lang_tempcode('TOTAL_UNSPENT_SUPPORT_CREDITS', strval($total));
    $list = results_table(do_lang_tempcode('UNSPENT_SUPPORT_CREDITS'), $start, 'start', $max, 'max', $i, $fields_title, $fields_values, $sortables, $sortable, $sort_order, 'sort', $msg);

    $tpl = do_template('SUPPORT_CREDITS_OUTSTANDING_SCREEN', array('_GUID' => '71dadee5485e17a56907d45fa2c53f23', 'TITLE' => $title, 'DATA' => $list));
    $tpl->evaluate_echo();
} else {
    $msg_tpl = warn_screen($title, do_lang_tempcode('INVALID_FIELD_ID'));
    $msg_tpl->evaluate_echo();
    return;
}
