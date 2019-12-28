<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * The UI for a points profile.
 *
 * @param  MEMBER $member_id_of The ID of the member who is being viewed
 * @param  ?MEMBER $member_id_viewing The ID of the member who is doing the viewing (null: current member)
 * @return Tempcode The UI
 */
function points_profile($member_id_of, $member_id_viewing)
{
    require_code('points');
    require_css('points');
    require_lang('points');
    require_lang('customers');

    require_javascript('checking');

    // Get info about viewing/giving user
    if (!is_guest($member_id_viewing)) {
        $viewer_gift_points_available = get_gift_points_to_give($member_id_viewing);
    }

    // Get info about viewed user
    $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id_of, true, USERNAME_GUEST_AS_DEFAULT | USERNAME_DEFAULT_ERROR);
    $profile_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id_of, true);

    // Get point info
    $point_info = point_info($member_id_of);

    $points_records = [];
    $additional_fields = [];

    // Run points hooks
    $hooks = find_all_hooks('modules', 'points');
    foreach (array_keys($hooks) as $hook) {
        require_code('hooks/modules/points/' . filter_naughty_harsh($hook));
        $object = object_factory('Hook_points_' . filter_naughty_harsh($hook), true);
        if ($object === null) {
            continue;
        }
        $_array = $object->points_profile($member_id_of, $member_id_viewing, $point_info);
        if (!is_null($_array)) {
            if (array_key_exists('POINTS_EACH', $_array)) {
                array_push($points_records, $_array);
            } else {
                array_push($additional_fields, $_array);
            }
        }
    }

    // Get additional points info
    $points_used = points_used($member_id_of);
    $remaining = available_points($member_id_of);
    $gift_points_used = get_gift_points_used($member_id_of); //$_point_info['gift_points_used'];
    $gift_points_available = get_gift_points_to_give($member_id_of);

    $to = points_get_transactions('to', $member_id_of, $member_id_viewing);
    $from = points_get_transactions('from', $member_id_of, $member_id_viewing);

    // If we're staff, we can show the charge log too
    $chargelog_details = new Tempcode();
    if (has_privilege($member_id_viewing, 'view_charge_log')) {
        $start = get_param_integer('charge_start', 0);
        $max = get_param_integer('charge_max', intval(get_option('point_logs_per_page')));
        $sortables = ['date_and_time' => do_lang_tempcode('DATE'), 'amount' => do_lang_tempcode('AMOUNT')];
        $test = explode(' ', get_param_string('sort', 'date_and_time DESC', INPUT_FILTER_GET_COMPLEX), 2);
        if (count($test) == 1) {
            $test[1] = 'DESC';
        }
        list($sortable, $sort_order) = $test;
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('chargelog', 'COUNT(*)', ['member_id' => $member_id_of]);
        $rows = $GLOBALS['SITE_DB']->query_select('chargelog', ['*'], ['member_id' => $member_id_of], 'ORDER BY ' . $sortable . ' ' . $sort_order, $max, $start);
        $charges = new Tempcode();
        $from_name = get_site_name();
        $to_name = $GLOBALS['FORUM_DRIVER']->get_username($member_id_of, true);
        require_code('templates_results_table');
        $header_row = results_header_row([do_lang_tempcode('DATE'), do_lang_tempcode('AMOUNT'), do_lang_tempcode('FROM'), do_lang_tempcode('TO'), do_lang_tempcode('REASON')], $sortables, 'sort', $sortable . ' ' . $sort_order);
        foreach ($rows as $myrow) {
            $date = get_timezoned_date_time($myrow['date_and_time']);
            $amount = $myrow['amount'];
            $reason = get_translated_tempcode('chargelog', $myrow, 'reason');

            $charges->attach(results_entry([$date, integer_format($amount), $from_name, $to_name, $reason], true));
        }
        $chargelog_details = results_table(do_lang_tempcode('CHARGES'), $start, 'charge_start', $max, 'charge_max', $max_rows, $header_row, $charges, $sortables, $sortable, $sort_order, 'sort');
    }

    // Show giving form
    if (is_guest($member_id_viewing)) {
        $give_template = do_lang_tempcode('POINTS_MUST_LOGIN');
    } else {
        $have_negative_gift_points = has_privilege($member_id_viewing, 'have_negative_gift_points');
        $enough_ok = (($viewer_gift_points_available > 0) || ($have_negative_gift_points));
        $give_ok = (($member_id_viewing != $member_id_of) || (has_privilege($member_id_viewing, 'give_points_self')));
        if (($enough_ok) && ($give_ok)) {
            // Show how many points are available also
            $give_url = build_url(['page' => 'points', 'type' => 'give', 'id' => $member_id_of], get_module_zone('points'));
            $give_template = do_template('POINTS_GIVE', [
                '_GUID' => 'a7663fab037412fd4e6a6404a4291939',
                'GIVE_URL' => $give_url,
                'MEMBER' => strval($member_id_of),
                'VIEWER_GIFT_POINTS_AVAILABLE' => $have_negative_gift_points ? '' : integer_format($viewer_gift_points_available),
            ]);
        } else {
            $give_template = do_lang_tempcode('PE_LACKING_GIFT_POINTS');
        }
        if (!$give_ok) {
            $give_template = new Tempcode();
        }
    }

    return do_template('POINTS_PROFILE', array_merge(
        [
            '_GUID' => '900deaa0bba64762271ca63bf1606d87',

            'MEMBER' => strval($member_id_of),
            'PROFILE_URL' => $profile_url,
            'USERNAME' => $username,

            'POINTS_RECORDS' => $points_records,

            'POINTS_USED' => integer_format($points_used),
            'REMAINING' => integer_format($remaining),
            'GIFT_POINTS_USED' => integer_format($gift_points_used),
            'GIFT_POINTS_AVAILABLE' => integer_format($gift_points_available),
            'TO' => $to,
            'FROM' => $from,
            'CHARGELOG_DETAILS' => $chargelog_details,
            'GIVE' => $give_template,
        ],
        $additional_fields
    ));
}
