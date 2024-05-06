<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('cms_homesite_tracker', $error_msg)) {
    return $error_msg;
}

if (!addon_installed('tickets')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('tickets')));
}
if (!addon_installed('stats')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('stats')));
}
if (!addon_installed('ecommerce')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
}
if (!addon_installed('points')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
}

if (get_forum_type() != 'cns') {
    warn_exit(do_lang_tempcode('NO_CNS'));
}

if (strpos(get_db_type(), 'mysql') === false) {
    warn_exit('This works with MySQL only');
}

$start = get_param_integer('start', 0);
$max = get_param_integer('max', 50);
if ($start == $max) {
    $max = $start + 50;
}
$spreadsheet = get_param_integer('spreadsheet', 0) == 1;
if ($spreadsheet) {
    $start = 0;
}
require_code('cns_members');
require_code('mantis');
require_lang('customers');
$title = get_screen_title('UNSPENT_SUPPORT_CREDITS');

$field_id = get_credits_profile_field_id();
if ($field_id !== null) {
    $field_id = strval($field_id);
    require_lang('cns');
    require_lang('stats');

    $uname = do_lang_tempcode('USERNAME');
    $ucredits = do_lang_tempcode('CREDITS');
    $ujoin = do_lang_tempcode('JOIN_DATE');
    $ulast = do_lang_tempcode('LAST_VISIT_TIME');

    $sortables = ['username' => $uname, 'credits' => $ucredits, 'join_date' => $ujoin, 'last_visit' => $ulast];
    $test = explode(' ', get_param_string('sort', 'username DESC', INPUT_FILTER_GET_COMPLEX), 2);
    if (count($test) == 1) {
        $test[1] = 'DESC';
    }
    list($sortable, $sort_order) = $test;
    if (((cms_strtoupper_ascii($sort_order) != 'ASC') && (cms_strtoupper_ascii($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
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
    $header_row = results_header_row([$uname, $ucredits, $ujoin, $ulast], $sortables, 'sort', $sortable . ' ' . $sort_order);
    $fields_values = new Tempcode();

    $total = 0;
    $i = 0;

    do {
        $old_limit = cms_set_time_limit(10);

        $sql = 'SELECT a.m_username AS m_username, a.m_join_time AS m_join_time, a.m_last_visit_time AS m_last_visit_time, b.mf_member_id AS mf_member_id, field_' . $field_id . ' FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_member_custom_fields b JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members a ON a.id=b.mf_member_id WHERE field_' . strval($field_id) . '>0 ORDER BY ' . $orderby . ' ' . $sort_order;
        $members = $GLOBALS['FORUM_DB']->query($sql, $max, $start);
        if ((empty($members)) && ($spreadsheet)) {
            $msg_tpl = inform_screen($title, do_lang_tempcode('NO_RESULTS_SORRY'));
            $msg_tpl->evaluate_echo();
            return;
        }

        foreach ($members as $member) {
            $credits = $member['field_' . $field_id];
            $member_id = intval($member['mf_member_id']);
            $member_name = $member['m_username'];
            $member_join_date = get_timezoned_date_time($member['m_join_time']);
            $member_visit_date = get_timezoned_date_time($member['m_last_visit_time']);

            if ($spreadsheet) {
                $spreadsheet_data = [];
                $sname = do_lang('USERNAME');
                $scredits = do_lang('CREDITS');
                $sjoin = do_lang('JOIN_DATE');
                $slast = do_lang('LAST_VISIT_TIME');
                $spreadsheet_data[] = [
                    $sname => $member_name,
                    $scredits => $credits,
                    $sjoin => $member_join_date,
                    $slast => $member_visit_date,
                ];
            } else {
                $member_linked = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($member_id, '', false);
                $fields_values->attach(results_entry([$member_linked, integer_format($credits, 0), $member_join_date, $member_visit_date], true));
            }

            $total += $credits;
            $i++;
        }

        $start += $max;

        cms_set_time_limit($old_limit);
    } while ($spreadsheet);

    if ($spreadsheet) {
        require_code('files_spreadsheets_write');
        $filename = 'unspent_credits.' . spreadsheet_write_default();
        $path = null;
        $sheet_writer = make_spreadsheet($path, $spreadsheet_data, $filename);
        $sheet_writer->output_and_exit($filename, true);
    }
    $msg = do_lang_tempcode('TOTAL_UNSPENT_SUPPORT_CREDITS', strval($total));
    $list = results_table(do_lang_tempcode('UNSPENT_SUPPORT_CREDITS'), $start, 'start', $max, 'max', $i, $header_row, $fields_values, $sortables, $sortable, $sort_order, 'sort', $msg);

    $tpl = do_template('SUPPORT_CREDITS_OUTSTANDING_SCREEN', ['_GUID' => '71dadee5485e17a56907d45fa2c53f23', 'TITLE' => $title, 'DATA' => $list]);
    $tpl->evaluate_echo();
} else {
    $msg_tpl = warn_screen($title, do_lang_tempcode('INVALID_FIELD_ID'));
    $msg_tpl->evaluate_echo();
    return;
}
