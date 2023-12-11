<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

function get_referral_scheme_stats_for($referrer, $scheme_name, $raw = false)
{
    $num_total_by_referrer = count($GLOBALS['FORUM_DB']->query_select('f_invites', array('DISTINCT i_email_address'), array('i_inviter' => $referrer)));
    $num_total_qualified_by_referrer = $GLOBALS['SITE_DB']->query_select_value('referees_qualified_for', 'COUNT(*)', array('q_referee' => $referrer, 'q_scheme_name' => $scheme_name));

    if (!$raw) {
        $dif = $GLOBALS['SITE_DB']->query_select_value_if_there('referrer_override', 'o_referrals_dif', array('o_referrer' => $referrer, 'o_scheme_name' => $scheme_name));
        if (!is_null($dif)) {
            $num_total_qualified_by_referrer += $dif;
        }
    }

    return array($num_total_qualified_by_referrer, $num_total_by_referrer);
}

function assign_referral_awards($referee, $trigger)
{
    $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);

    $referee_username = $GLOBALS['FORUM_DRIVER']->get_username($referee);
    $referee_displayname = $GLOBALS['FORUM_DRIVER']->get_username($referee, true);
    $referee_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($referee);
    if ($referee_email == '') {
        return; // Weird situation! Somehow the member has no email address defined and hence we can't lookup the referral, can't normally happen
    }

    require_lang('referrals');
    require_code('notifications');

    $referrer = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_invites', 'i_inviter', array('i_email_address' => $referee_email), 'ORDER BY i_time');
    if (is_null($referrer)) { // Was not actually a referral, member joined site on own accord
        if ((isset($ini_file['global']['notify_if_join_but_no_referral'])) && ($ini_file['global']['notify_if_join_but_no_referral'] == '1')) {
            dispatch_notification(
                'referral_staff',
                null,
                do_lang(
                    'MAIL_REFERRALS__NONREFERRAL__TOSTAFF_SUBJECT',
                    $referee_username,
                    $referee_displayname
                ),
                do_notification_lang(
                    'MAIL_REFERRALS__NONREFERRAL__TOSTAFF_BODY',
                    comcode_escape($referee_username),
                    comcode_escape($referee_displayname)
                ),
                null,
                A_FROM_SYSTEM_PRIVILEGED
            );
        }

        return;
    }
    $referrer_username = $GLOBALS['FORUM_DRIVER']->get_username($referrer);
    if (is_null($referrer_username)) {
        return; // Deleted member
    }
    $referrer_displayname = $GLOBALS['FORUM_DRIVER']->get_username($referrer, true);
    if (is_guest($referrer)) {
        return;
    }
    $referrer_email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($referrer);

    foreach ($ini_file as $ini_file_section_name => $ini_file_section) {
        if ($ini_file_section_name != 'global') {
            $ini_file_section['name'] = $ini_file_section_name;

            list($num_total_qualified_by_referrer, $num_total_by_referrer) = get_referral_scheme_stats_for($referrer, $ini_file_section_name);
            $one_trigger_already = !is_null($GLOBALS['SITE_DB']->query_select_value_if_there('referees_qualified_for', 'q_referee', array('q_scheme_name' => $ini_file_section_name, 'q_referee' => $referee)));

            $qualified_trigger = _assign_referral_awards(
                $trigger,

                $ini_file_section_name,
                $ini_file_section,

                $referee, $referee_username, $referee_displayname, $referee_email, $one_trigger_already,
                $referrer, $referrer_username, $referrer_displayname, $referrer_email,

                $num_total_qualified_by_referrer, $num_total_by_referrer
            );
            //if ($qualified_trigger) break; // Only do the first qualified scheme, not multiple schemes    ACTUALLY we will allow multiple; no actual harm
        }
    }
}

function _assign_referral_awards(
    $trigger,

    $scheme_name,
    $scheme,

    $referee, $referee_username, $referee_displayname, $referee_email, $one_trigger_already,
    $referrer, $referrer_username, $referrer_displayname, $referrer_email,

    $num_total_qualified_by_referrer, $num_total_by_referrer
) {
    $scheme_title = isset($scheme['title']) ? $scheme['title'] : $scheme_name;

    $report_url = find_script('referrer_report') . '?scheme=' . urlencode($scheme_name) . '&csv=1';

    $referrer_is_qualified = referrer_is_qualified($scheme, $referrer);

    $notify_if_non_qualified = (isset($scheme['notify_if_non_qualified'])) && ($scheme['notify_if_non_qualified'] == '1');
    $notify_staff_if_non_qualified = (!isset($scheme['notify_staff_if_non_qualified'])) || ($scheme['notify_staff_if_non_qualified'] == '1');

    $qualified_trigger = (isset($scheme['referral_trigger__' . $trigger])) && ($scheme['referral_trigger__' . $trigger] == '1');
    if ($qualified_trigger) { // Valid referral
        $one_trigger_per_referee = (!isset($scheme['one_trigger_per_referee'])) || ($scheme['one_trigger_per_referee'] == '1');

        if ((!$one_trigger_per_referee) || (!$one_trigger_already)) {
            $GLOBALS['SITE_DB']->query_insert('referees_qualified_for', array(
                'q_referee' => $referee,
                'q_referrer' => $referrer,
                'q_scheme_name' => $scheme_name,
                'q_email_address' => $referee_email,
                'q_time' => time(),
                'q_action' => $trigger,
            ));
            $GLOBALS['FORUM_DB']->query_update('f_invites', array('i_taken' => 1), array('i_email_address' => $referee_email), '', 1);
            $num_total_qualified_by_referrer++;
            $num_total_by_referrer++;

            // Tell staff (referrer just completed a level)
            if (array_key_exists('level_' . strval($num_total_qualified_by_referrer), $scheme)) {
                $level_description = $scheme['level_' . strval($num_total_qualified_by_referrer)];
                if (($referrer_is_qualified) || ($notify_staff_if_non_qualified)) {
                    $subject_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_AWARD_SUBJECT' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_AWARD_SUBJECT';
                    if (do_lang($subject_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                        $subject_lang_string .= '__' . $scheme_name;
                    }
                    $subject = do_lang(
                        $subject_lang_string,
                        $level_description,
                        $referrer_username,
                        array(
                            $referee_username,
                            $scheme_title,
                            $referee_displayname,
                            $referrer_displayname,
                        )
                    );
                    $body_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_AWARD_BODY' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_AWARD_BODY';
                    if (do_lang($body_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                        $body_lang_string .= '__' . $scheme_name;
                    }
                    $body = do_notification_lang(
                        $body_lang_string,
                        comcode_escape($level_description),
                        comcode_escape($referrer_username),
                        array(
                            comcode_escape(integer_format($num_total_qualified_by_referrer)),
                            comcode_escape($referee_username),
                            $report_url,
                            comcode_escape(integer_format($num_total_by_referrer)),
                            comcode_escape($scheme_title),
                            comcode_escape($referee_displayname),
                            comcode_escape($referrer_displayname)
                        )
                    );
                    dispatch_notification(
                        'referral_staff',
                        null,
                        $subject,
                        $body,
                        null,
                        A_FROM_SYSTEM_PRIVILEGED
                    );
                }
            } else { // Tell staff (referrer is between levels / no level hit yet)
                if (($referrer_is_qualified) || ($notify_staff_if_non_qualified)) {
                    $subject_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_SUBJECT' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_SUBJECT';
                    if (do_lang($subject_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                        $subject_lang_string .= '__' . $scheme_name;
                    }
                    $subject = do_lang(
                        $subject_lang_string,
                        $referrer_username,
                        $referee_username,
                        array(
                            $scheme_title,
                            $referee_displayname,
                            $referrer_displayname
                        )
                    );
                    $body_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_BODY' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOSTAFF_BODY';
                    if (do_lang($body_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                        $body_lang_string .= '__' . $scheme_name;
                    }
                    $body = do_notification_lang(
                        $body_lang_string,
                        comcode_escape($referrer_username),
                        comcode_escape(integer_format($num_total_qualified_by_referrer)),
                        array(
                            comcode_escape($referee_username),
                            $report_url,
                            comcode_escape(integer_format($num_total_by_referrer)),
                            comcode_escape($scheme_title),
                            comcode_escape($referee_displayname),
                            comcode_escape($referrer_displayname)
                        )
                    );
                    dispatch_notification(
                        'referral_staff',
                        null,
                        $subject,
                        $body,
                        null,
                        $referrer
                    );
                }
            }

            // Tell referrer they got a referrer, but don't mention any awards explicitly regardless if achieved yet (because the staff will do this when they're ready with the award)
            if (($referrer_is_qualified) || ($notify_if_non_qualified)) {
                $subject_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOREFERRER_SUBJECT' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOREFERRER_SUBJECT';
                if (do_lang($subject_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $subject_lang_string .= '__' . $scheme_name;
                }
                $subject = do_lang(
                    $subject_lang_string,
                    $referee_username,
                    $scheme_title,
                    array(
                        $referee_displayname,
                        $referrer_username,
                        $referrer_displayname
                    )
                );
                $body_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOREFERRER_BODY' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_QUALIFIEDREFERRAL__TOREFERRER_BODY';
                if (do_lang($body_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $body_lang_string .= '__' . $scheme_name;
                }
                $body = do_notification_lang(
                    $body_lang_string,
                    comcode_escape($referrer_username),
                    comcode_escape(integer_format($num_total_qualified_by_referrer)),
                    array(
                        comcode_escape($referee_username),
                        comcode_escape(integer_format($num_total_by_referrer)),
                        comcode_escape($scheme_title),
                        comcode_escape($referee_displayname),
                        comcode_escape($referrer_displayname)
                    )
                );
                dispatch_notification(
                    'referral',
                    null,
                    $subject,
                    $body,
                    array($referrer),
                    A_FROM_SYSTEM_PRIVILEGED
                );
            }
        }
    } else {
        if ($trigger == 'join') { // Ready for FUTURE qualification
            // Say if first step of referral happened (non-qualified referral), even if we've set not to award them for it
            if (($referrer_is_qualified) || ($notify_staff_if_non_qualified)) {
                $subject_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOSTAFF_SUBJECT' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOSTAFF_SUBJECT';
                if (do_lang($subject_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $subject_lang_string .= '__' . $scheme_name;
                }
                $subject = do_lang(
                    $subject_lang_string,
                    $referrer_username,
                    $referee_username,
                    array(
                        $scheme_title,
                        $referee_displayname,
                        $referrer_displayname
                    )
                );
                $body_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOSTAFF_BODY' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOSTAFF_BODY';
                if (do_lang($body_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $body_lang_string .= '__' . $scheme_name;
                }
                $body = do_notification_lang(
                    $body_lang_string,
                    comcode_escape($referrer_username),
                    comcode_escape($referee_username),
                    array(
                        $report_url,
                        comcode_escape(integer_format($num_total_qualified_by_referrer)),
                        comcode_escape(integer_format($num_total_by_referrer)),
                        comcode_escape($scheme_title),
                        comcode_escape($referee_displayname),
                        comcode_escape($referrer_displayname)
                    )
                );
                dispatch_notification(
                    'referral_staff',
                    null,
                    $subject,
                    $body,
                    null,
                    A_FROM_SYSTEM_PRIVILEGED
                );
            }
            if (($referrer_is_qualified) || ($notify_if_non_qualified)) {
                $subject_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOREFERRER_SUBJECT' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOREFERRER_SUBJECT';
                if (do_lang($subject_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $subject_lang_string .= '__' . $scheme_name;
                }
                $subject = do_lang(
                    $subject_lang_string,
                    $referee_username,
                    $scheme_title,
                    array(
                        $referee_displayname,
                        $referrer_username,
                        $referrer_displayname
                    )
                );
                $body_lang_string = $referrer_is_qualified ? 'MAIL_REFERRALS__QUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOREFERRER_BODY' : 'MAIL_REFERRALS__NONQUALIFIEDREFERRER_NONQUALIFIEDREFERRAL__TOREFERRER_BODY';
                if (do_lang($body_lang_string . '__' . $scheme_name, null, null, null, null, false) !== null) {
                    $body_lang_string .= '__' . $scheme_name;
                }
                $body = do_notification_lang(
                    $body_lang_string,
                    comcode_escape($referrer_username),
                    comcode_escape($referee_username),
                    array(
                        comcode_escape(integer_format($num_total_qualified_by_referrer)),
                        comcode_escape(integer_format($num_total_by_referrer)),
                        comcode_escape($scheme_title),
                        comcode_escape($referee_displayname),
                        comcode_escape($referrer_displayname)
                    )
                );
                dispatch_notification(
                    'referral',
                    null,
                    $subject,
                    $body,
                    array($referrer),
                    A_FROM_SYSTEM_PRIVILEGED
                );
            }
        }
    }

    // Run any actioning code defined in hooks
    $hooks = find_all_hooks('systems', 'referrals');
    foreach (array_keys($hooks) as $hook) {
        require_code('hooks/systems/referrals/' . $hook);
        $ob = object_factory('Hook_referrals_' . $hook, true);
        if ($ob !== null) {
            $ob->fire_referral($trigger, $referrer, $referrer_is_qualified, $referee, $qualified_trigger, $num_total_qualified_by_referrer, $num_total_by_referrer, $one_trigger_already);
        }
    }

    return $qualified_trigger;
}

function referrer_is_qualified($scheme, $member_id)
{
    if (is_guest($member_id)) {
        return false;
    }

    $is_qualified_override = $GLOBALS['SITE_DB']->query_select_value_if_there('referrer_override', 'o_is_qualified', array('o_referrer' => $member_id, 'o_scheme_name' => $scheme['name']));
    if (!is_null($is_qualified_override)) {
        return ($is_qualified_override == 1);
    }

    if ((isset($scheme['referrer_qualified_for__all'])) && ($scheme['referrer_qualified_for__all'] == '1')) {
        return true;
    }

    require_code('cns_members');
    $primary_group = cns_get_member_primary_group($member_id);

    $groups = $GLOBALS['FORUM_DRIVER']->get_members_groups($member_id);

    $positive = 0;
    $negative = 0;

    foreach ($groups as $group_id) {
        if ((isset($scheme['referrer_qualified_for__group_' . strval($group_id)])) && ($scheme['referrer_qualified_for__group_' . strval($group_id)] == '1')) {
            $positive++;
        } else {
            $negative++;
        }

        if ((isset($scheme['referrer_qualified_for__primary_group_' . strval($group_id)])) && ($scheme['referrer_qualified_for__primary_group_' . strval($group_id)] == '1')) {
            if ($group_id == $primary_group) {
                $positive++;
            } else {
                $negative++;
            }
        }
    }

    if ((isset($scheme['referrer_qualified_for__event'])) && ($scheme['referrer_qualified_for__event'] == '1')) {
        if (!is_null($GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM ' . get_table_prefix() . 'calendar_events WHERE (e_submitter=' . strval($member_id) . ' OR e_member_calendar=' . strval($member_id) . ') AND validated=1'))) {
            $positive++;
        } else {
            $negative++;
        }
    }

    if (addon_installed('shopping')) {
        if ((isset($scheme['referrer_qualified_for__misc_purchase'])) && ($scheme['referrer_qualified_for__misc_purchase'] == '1')) {
            if (!is_null($GLOBALS['SITE_DB']->query_value_if_there('SELECT id FROM ' . get_table_prefix() . 'shopping_order WHERE c_member=' . strval($member_id) . ' AND ' . (db_string_equal_to('order_status', 'payment_received') . ' OR ' . db_string_equal_to('order_status', 'dispatched'))))) {
                $positive++;
            } else {
                $negative++;
            }
        }

        foreach (array_keys($scheme) as $key) {
            $matches = array();

            if (preg_match('#^referrer\_qualified\_for\_\_purchase\_(\d+)$#', $key, $matches) != 0) {
                if (!is_null($GLOBALS['SITE_DB']->query_value_if_there('SELECT o.id FROM ' . get_table_prefix() . 'shopping_order o JOIN ' . get_table_prefix() . 'shopping_order_details d ON o.id=d.order_id WHERE p_id=' . strval(intval($matches[1])) . ' AND c_member=' . strval($member_id) . ' AND ' . (db_string_equal_to('order_status', 'payment_received') . ' OR ' . db_string_equal_to('order_status', 'dispatched'))))) {
                    $positive++;
                } else {
                    $negative++;
                }
            }
        }
    }

    $referrer_qualification_logic = isset($scheme['referrer_qualification_logic']) ? $scheme['referrer_qualification_logic'] : 'OR';
    if ($referrer_qualification_logic == 'OR') {
        return ($positive > 0);
    } else {
        return ($negative == 0);
    }
}

function referrer_report_script($ret = false)
{
    $scheme_name = get_param_string('scheme', 'standard_scheme');
    $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);
    if (!isset($ini_file[$scheme_name])) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $scheme = $ini_file[$scheme_name];
    $scheme['name'] = $scheme_name;

    $scheme_title = isset($scheme['title']) ? $scheme['title'] : $scheme_name;

    $member_id = get_param_integer('member_id', null);
    $is_self = ($member_id === get_member());
    if ((!has_zone_access(get_member(), 'adminzone')) && (!$is_self)) {
        access_denied('ZONE_ACCESS', 'adminzone');
    }

    if (!is_null($member_id)) {
        if (!referrer_is_qualified($scheme, $member_id)) {
            warn_exit(do_lang_tempcode($is_self ? '_MEMBER_NOT_ON_REFERRAL_SCHEME' : 'MEMBER_NOT_ON_REFERRAL_SCHEME', escape_html($scheme_title)));
        }
    }

    require_lang('referrals');
    $csv = (get_param_integer('csv', 0) == 1);

    $where = db_string_not_equal_to('i_email_address', '') . ' AND i_inviter<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
    if ($member_id !== null) {
        $where .= ' AND referrer.id=' . strval($member_id);
    }

    $max = get_param_integer('max', $csv ? 10000 : 30);
    $start = get_param_integer('start', 0);

    $dif = $GLOBALS['SITE_DB']->query_select_value_if_there('referrer_override', 'o_referrals_dif', array('o_referrer' => $member_id, 'o_scheme_name' => $scheme_name));
    if ($dif == 0) {
        $dif = null;
    }

    // Show records
    $data = array();
    $table = 'f_invites i LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members referrer ON referrer.id=i_inviter LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members referee ON referee.m_email_address=i_email_address';
    $referrals = $GLOBALS['FORUM_DB']->query(
        'SELECT i_time AS time,referrer.id AS referrer_id,referrer.m_username AS referrer,referrer.m_email_address AS referrer_email,referee.id AS referee_id,referee.m_username AS referee,i_email_address AS referee_email,i_taken AS qualified
        FROM ' .
        $GLOBALS['FORUM_DB']->get_table_prefix() . $table .
        ' WHERE ' .
        $where .
        (can_arbitrary_groupby() ? ' GROUP BY i_email_address' : '') . ' ORDER BY i_time DESC',
        $max,
        $start
    );
    $max_rows = $GLOBALS['FORUM_DB']->query_select_value('referees_qualified_for', 'COUNT(*)', ($member_id !== null) ? array('q_referrer' => $member_id) : null) * 2;
    if ((count($referrals) == 0) && (is_null($dif))) {
        inform_exit(do_lang_tempcode('NO_ENTRIES'), true);
    }
    foreach ($referrals as $ref) {
        $data_row = array();
        $data_row[do_lang('DATE_TIME')] = get_timezoned_date($ref['time'], true, true, false, true);
        if (is_null($member_id)) {
            if ($csv) {
                $deleted = true;
                $data_row[do_lang('TYPE_REFERRER')] = is_null($ref['referrer']) ? do_lang($deleted ? 'REFEREE_DELETED' : 'REFEREE_NOT_SIGNED_UP') : $ref['referrer'];
            } else {
                $data_row[do_lang('TYPE_REFERRER')] = is_null($ref['referrer_id']) ? '' : strval($ref['referrer_id']);
            }
            if (has_privilege(get_member(), 'member_maintenance')) {
                $data_row[do_lang('TYPE_REFERRER') . ' (' . do_lang('EMAIL_ADDRESS') . ')'] = $ref['referrer_email'];
            }
            if (is_null($ref['referrer_id'])) {
                $data_row[do_lang('QUALIFIED_REFERRER', $scheme_title)] = do_lang('NA');
            } else {
                $data_row[do_lang('QUALIFIED_REFERRER', $scheme_title)] = do_lang(referrer_is_qualified($scheme, $ref['referrer_id']) ? 'YES' : 'NO');
            }
        }

        $qualifications = array();
        if (($ref['qualified'] == 1) && (!is_null($ref['referee_id']))) { // Clarify, are they really qualified?
            $qualifications = $GLOBALS['SITE_DB']->query_select('referees_qualified_for', array('q_time', 'q_action'), array('q_referee' => $ref['referee_id'], 'q_scheme_name' => $scheme_name));
            if (count($qualifications) == 0) {
                $ref['qualified'] = 0; // Not actually qualified for this scheme
            }
        }

        $deleted = false;
        if (is_null($ref['referee'])) {
            $deleted = !is_null($GLOBALS['SITE_DB']->query_select_value_if_there('actionlogs', 'id', array('the_type' => 'DELETE_MEMBER', 'param_a' => strval($ref['referee']))));
        }
        if ($csv) {
            $data_row[do_lang('REFEREE')] = is_null($ref['referee']) ? do_lang($deleted ? 'REFEREE_DELETED' : 'REFEREE_NOT_SIGNED_UP') : $ref['referee'];
        } else {
            $data_row[do_lang('REFEREE')] = is_null($ref['referee_id']) ? '' : strval($ref['referee_id']);
        }
        if (has_privilege(get_member(), 'member_maintenance')) {
            $data_row[do_lang('REFEREE') . ' (' . do_lang('EMAIL_ADDRESS') . ')'] = is_null($ref['referee_email']) ? '' : $ref['referee_email'];
        }

        $data_row[do_lang('QUALIFIED_REFERRAL', $scheme_name)] = do_lang(($ref['qualified'] == 1) ? 'YES' : 'NO');

        $data_row[do_lang('ACTION')] = do_lang('cns:JOINED');

        if ($ref['qualified'] == 0) {
            $data[] = $data_row;
        } else { // Show each individual qualification action
            foreach ($qualifications as $qual) {
                $data_row_x = $data_row;

                $test = do_lang('REFERRAL_TRIGGER__' . $qual['q_action'], null, null, null, null, false);
                if (!is_null($test)) {
                    $qual['q_action'] = $test;
                }
                $data_row_x[do_lang('ACTION')] = $qual['q_action'];
                $data_row_x[do_lang('QUALIFIED_REFERRAL', $scheme_name)] = do_lang('QUALIFY_BY_THIS');

                $data_row_x[do_lang('DATE_TIME')] = get_timezoned_date($qual['q_time']);

                $data[] = $data_row_x;
            }

            $data[] = $data_row;
        }
    }

    // Manual adjustment
    if (!is_null($dif)) {
        $data_row = array();
        $data_row[do_lang('DATE_TIME')] = do_lang('NA');

        if (is_null($member_id)) {
            $data_row[do_lang('TYPE_REFERRER')] = '';

            if (has_privilege(get_member(), 'member_maintenance')) {
                $data_row[do_lang('TYPE_REFERRER') . ' (' . do_lang('EMAIL_ADDRESS') . ')'] = '';
            }

            $data_row[do_lang('QUALIFIED_REFERRER', $scheme_name)] = do_lang('YES');
        }

        $data_row[do_lang('REFEREE')] = '';
        if (has_privilege(get_member(), 'member_maintenance')) {
            $data_row[do_lang('REFEREE') . ' (' . do_lang('EMAIL_ADDRESS') . ')'] = do_lang('NA');
        }

        $data_row[do_lang('QUALIFIED_REFERRAL', $scheme_name)] = do_lang('NA');

        if ($dif < 0) {
            $data_row[do_lang('ACTION')] = do_lang('REFERRAL_TRIGGER__ADJUSTMENT_NEGATIVE', integer_format($dif));

            $data[] = $data_row;
        } else {
            for ($i = 0; $i < $dif; $i++) {
                $data_row[do_lang('ACTION')] = do_lang('REFERRAL_TRIGGER__ADJUSTMENT');

                $data[] = $data_row;
            }
        }
    }

    // Show results
    if ($csv) {
        require_code('files2');
        make_csv($data, (is_null($member_id) ? get_site_name() : $GLOBALS['FORUM_DRIVER']->get_username($member_id)) . ' referrals.csv');
    } else {
        require_code('templates_results_table');

        $fields_title = new Tempcode();
        $fields = new Tempcode();
        foreach ($data as $i => $data_row) {
            if ($i == 0) {
                $fields_title->attach(results_field_title(array_keys($data_row)));
            }
            foreach ($data_row as $key => $val) {
                if (($key == do_lang('REFEREE')) || ($key == do_lang('TYPE_REFERRER'))) {
                    if ($val == '') {
                        $val = do_lang('UNKNOWN');
                    } else {
                        $val = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink(intval($val), true);
                    }
                }
                $data_row[$key] = escape_html($val);
            }
            $fields->attach(results_entry($data_row, false));
        }

        $results_table = results_table(do_lang('REFERRALS'), $start, 'start', $max, 'max', $max_rows, $fields_title, $fields);

        if ($ret) {
            return $results_table;
        }

        $title = get_screen_title('_REFERRALS', true, array(escape_html($scheme_title)));

        $out = new Tempcode();
        $out->attach($title);
        $out->attach($results_table);

        $out = globalise($out, null, '', true);
        $out->evaluate_echo();
    }

    return null;
}
