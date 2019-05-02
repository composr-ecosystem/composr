<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_cron_disastr
{
    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  boolean $calculate_num_queued Calculate the number of items queued, if possible
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info($last_run, $calculate_num_queued)
    {
        if (!addon_installed('disastr')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return array(
            'label' => 'Disastr',
            'num_queued' => null,
            'minutes_between_runs' => 24 * 60,
        );
    }

    /**
     * Run function for system scheduler scripts. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run($last_run)
    {
        require_lang('disastr');

        // Get just disease that should spread and are enabled
        $diseases_to_spread = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'diseases WHERE (last_spread_time<(' . strval(time()) . '-(spread_rate*60*60)) OR last_spread_time=0) AND enabled=1');

        foreach ($diseases_to_spread as $disease) {
            // Select infected by the disease members
            $sick_by_disease_members = $GLOBALS['SITE_DB']->query_select('members_diseases', array('member_id'), array('sick' => 1, 'disease_id' => $disease['id']));

            $sick_members = array();
            foreach ($sick_by_disease_members as $sick_member) {
                $sick_members[] = $sick_member['member_id'];
            }
            $sick_members[] = $GLOBALS['FORUM_DRIVER']->get_guest_id();

            foreach ($sick_by_disease_members as $sick_member) {
                require_code('points2');
                require_lang('disastr');

                // Charge disease points
                charge_member($sick_member['member_id'], $disease['points_per_spread'], do_lang('DISEASE_GET') . ' "' . $disease['name'] . '"');

                // Pick a random friend to infect
                $friends_a = array();
                if (addon_installed('chat')) {
                    $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'chat_friends WHERE member_likes=' . strval($sick_member['member_id']) . ' OR member_liked=' . strval($sick_member['member_id']) . ' ORDER BY date_and_time');

                    // Get friends
                    foreach ($rows as $i => $row) {
                        if ($row['member_likes'] != $sick_member['member_id']) {
                            $friends_a[$row['member_likes']] = $row['member_likes'];
                        } else {
                            $friends_a[$row['member_liked']] = $row['member_liked'];
                        }
                    }
                }

                $friends_list = implode(',', $friends_a);
                $friends_healthy = array();
                foreach ($friends_a as $friend) {
                    if (!in_array($friend, $sick_members)) {
                        $friends_healthy[] = $friend;
                    }
                }

                $to_infect = array_rand($friends_healthy);

                if (isset($friends_healthy[$to_infect]) && ($friends_healthy[$to_infect] != 0)) {
                    $members_disease_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', array('*'), array('member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id']));

                    $insert = true;
                    $has_immunization = false;
                    if (isset($members_disease_rows[0])) {
                        // There is already a DB member disease record
                        $insert = false;
                        if ($members_disease_rows[0]['immunisation'] == 1) {
                            $has_immunization = true;
                        }
                    }

                    if (!$has_immunization) {
                        $_cure_url = build_url(array('page' => 'purchase', 'type' => 'pay', 'id' => 'CURE_' . strval($disease['id'])), get_module_zone('purchase'), array(), false, false, true);
                        $cure_url = $_cure_url->evaluate();

                        if ($insert) {
                            // Infect the member for the first time
                            $GLOBALS['SITE_DB']->query_insert('members_diseases', array('member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0));
                        } else {
                            // Infect the member again
                            $GLOBALS['SITE_DB']->query_update('members_diseases', array('member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0), array('member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id']), '', 1);
                        }

                        $message = do_notification_lang('DISEASES_MAIL_MESSAGE', $disease['name'], $disease['name'], array($cure_url, get_site_name()), get_lang($friends_healthy[$to_infect]));
                        dispatch_notification('got_disease', null, do_lang('DISEASES_MAIL_SUBJECT', get_site_name(), $disease['name'], null, get_lang($friends_healthy[$to_infect])), $message, array($friends_healthy[$to_infect]), A_FROM_SYSTEM_PRIVILEGED);

                        $sick_members[] = $friends_healthy[$to_infect];
                    }
                }
            }

            // Proceed with infecting a random but not immunised member (disease initiation)
            // =============================================================================

            // Get immunised members first
            $immunised_members_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', array('*'), array('disease_id' => $disease['id'], 'immunisation' => 1));
            $immunised_members = array();
            foreach ($immunised_members_rows as $im_member) {
                $immunised_members[] = $im_member['member_id'];
            }

            $sick_and_immunised_members = array();
            $sick_and_immunised_members = array_merge($sick_members, $immunised_members);

            // Create a CSV list of members to be avoided - sick and immunised members should be avoided!!!
            $avoid_members = implode(',', @array_map('strval', $sick_and_immunised_members));

            $avoid_members = (strlen($avoid_members) == 0) ? '0' : $avoid_members;

            // If there is a randomly selected members that can be infected, otherwise all of the members are already infected or immunised
            $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id NOT IN (' . $avoid_members . ') AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
            if (addon_installed('unvalidated')) {
                $sql .= ' AND m_validated=1';
            }
            $sql .= ' ORDER BY ' . db_function('RAND');
            $random_member = $GLOBALS['FORUM_DB']->query($sql, 1);
            if (isset($random_member[0])) {
                $members_disease_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', array('*'), array('member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id']));

                $insert = true;
                if (isset($members_disease_rows[0])) {
                    // There is already a db member disease record
                    $insert = false;
                }

                require_code('notifications');

                $_cure_url = build_url(array('page' => 'purchase', 'type' => 'pay', 'type_code' => 'CURE_' . strval($disease['id'])), get_module_zone('purchase'), array(), false, false, true);
                $cure_url = $_cure_url->evaluate();

                if ($insert) {
                    // Infect the member for the first time
                    $GLOBALS['SITE_DB']->query_insert('members_diseases', array('member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0));
                } else {
                    // Infect the member again
                    $GLOBALS['SITE_DB']->query_update('members_diseases', array('member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0), array('member_id' => strval($random_member[0]['id']), 'disease_id' => strval($disease['id'])), '', 1);
                }

                $message = do_notification_lang('DISEASES_MAIL_MESSAGE', $disease['name'], $disease['name'], array($cure_url, get_site_name()), get_lang($random_member[0]['id']));
                dispatch_notification('got_disease', null, do_lang('DISEASES_MAIL_SUBJECT', get_site_name(), $disease['name'], null, get_lang($random_member[0]['id'])), $message, array($random_member[0]['id']), A_FROM_SYSTEM_PRIVILEGED);
            }

            // Record disease spreading
            $GLOBALS['SITE_DB']->query_update('diseases', array('last_spread_time' => strval(time())), array('id' => strval($disease['id'])), '', 1);
        }
    }
}
