<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
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

        return [
            'label' => 'Disastr diseases',
            'num_queued' => null,
            'minutes_between_runs' => 24 * 60,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_lang('disastr');

        // Get just disease that should spread and are enabled
        $diseases_to_spread = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'diseases WHERE (last_spread_time<(' . strval(time()) . '-(spread_rate*60*60)) OR last_spread_time=0) AND enabled=1');

        foreach ($diseases_to_spread as $disease) {
            // Select infected by the disease members
            $sick_by_disease_members = $GLOBALS['SITE_DB']->query_select('members_diseases', ['member_id'], ['sick' => 1, 'disease_id' => $disease['id']]);

            $sick_members = [];
            foreach ($sick_by_disease_members as $sick_member) {
                $sick_members[] = $sick_member['member_id'];
            }
            $sick_members[] = $GLOBALS['FORUM_DRIVER']->get_guest_id();

            foreach ($sick_by_disease_members as $sick_member) {
                require_code('points2');
                require_lang('disastr');

                // Charge disease points
                points_debit_member($sick_member['member_id'], do_lang('DISEASE_GET') . ' "' . $disease['name'] . '"', $disease['points_per_spread'], 0, 0, false);

                // Pick a random friend to infect
                $friends_a = [];
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
                $friends_healthy = [];
                foreach ($friends_a as $friend) {
                    if (!in_array($friend, $sick_members)) {
                        $friends_healthy[] = $friend;
                    }
                }

                if (empty($friends_healthy)) {
                    continue; // Everyone is already sick; nothing to do
                }

                $to_infect = array_rand($friends_healthy);

                if (isset($friends_healthy[$to_infect]) && ($friends_healthy[$to_infect] != 0)) {
                    $members_disease_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id']]);

                    $insert = true;
                    $has_immunisation = false;
                    if (isset($members_disease_rows[0])) {
                        // There is already a DB member disease record
                        $insert = false;
                        if ($members_disease_rows[0]['immunisation'] == 1) {
                            $has_immunisation = true;
                        }
                    }

                    if (!$has_immunisation) {
                        $_cure_url = build_url(['page' => 'purchase', 'type' => 'pay', 'id' => 'CURE_' . strval($disease['id'])], get_module_zone('purchase'), [], false, false, true);
                        $cure_url = $_cure_url->evaluate();

                        if ($insert) {
                            // Infect the member for the first time
                            $GLOBALS['SITE_DB']->query_insert('members_diseases', ['member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0]);
                        } else {
                            // Infect the member again
                            $GLOBALS['SITE_DB']->query_update('members_diseases', ['member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0], ['member_id' => $friends_healthy[$to_infect], 'disease_id' => $disease['id']], '', 1);
                        }

                        $message = do_notification_lang('DISEASES_MAIL_MESSAGE', $disease['name'], $disease['name'], [$cure_url, get_site_name()], get_lang($friends_healthy[$to_infect]));
                        dispatch_notification('got_disease', null, do_lang('DISEASES_MAIL_SUBJECT', get_site_name(), $disease['name'], null, get_lang($friends_healthy[$to_infect])), $message, [$friends_healthy[$to_infect]], A_FROM_SYSTEM_PRIVILEGED);

                        $sick_members[] = $friends_healthy[$to_infect];
                    }
                }
            }

            // Proceed with infecting a random but not immunised member (disease initiation)
            // =============================================================================

            // Get immunised members first
            $immunised_members_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['disease_id' => $disease['id'], 'immunisation' => 1]);
            $immunised_members = [];
            foreach ($immunised_members_rows as $im_member) {
                $immunised_members[] = $im_member['member_id'];
            }

            $sick_and_immunised_members = [];
            $sick_and_immunised_members = array_merge($sick_members, $immunised_members);

            // Create a comma-delimited list of members to be avoided - sick and immunised members should be avoided!!!
            $avoid_members = implode(',', @array_map('strval', $sick_and_immunised_members));

            $avoid_members = (strlen($avoid_members) == 0) ? '0' : $avoid_members;

            // If there is a randomly selected members that can be infected, otherwise all of the members are already infected or immunised
            $sql = 'SELECT id FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND id NOT IN (' . $avoid_members . ') AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
            if (addon_installed('validation')) {
                $sql .= ' AND m_validated=1';
            }
            $sql .= ' ORDER BY ' . db_function('RAND');
            $random_member = $GLOBALS['FORUM_DB']->query($sql, 1);
            if (isset($random_member[0])) {
                $members_disease_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id']]);

                $insert = true;
                if (isset($members_disease_rows[0])) {
                    // There is already a db member disease record
                    $insert = false;
                }

                require_code('notifications');

                $_cure_url = build_url(['page' => 'purchase', 'type' => 'pay', 'type_code' => 'CURE_' . strval($disease['id'])], get_module_zone('purchase'), [], false, false, true);
                $cure_url = $_cure_url->evaluate();

                if ($insert) {
                    // Infect the member for the first time
                    $GLOBALS['SITE_DB']->query_insert('members_diseases', ['member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0]);
                } else {
                    // Infect the member again
                    $GLOBALS['SITE_DB']->query_update('members_diseases', ['member_id' => strval($random_member[0]['id']), 'disease_id' => $disease['id'], 'sick' => 1, 'cure' => 0, 'immunisation' => 0], ['member_id' => strval($random_member[0]['id']), 'disease_id' => strval($disease['id'])], '', 1);
                }

                $message = do_notification_lang('DISEASES_MAIL_MESSAGE', $disease['name'], $disease['name'], [$cure_url, get_site_name()], get_lang($random_member[0]['id']));
                dispatch_notification('got_disease', null, do_lang('DISEASES_MAIL_SUBJECT', get_site_name(), $disease['name'], null, get_lang($random_member[0]['id'])), $message, [$random_member[0]['id']], A_FROM_SYSTEM_PRIVILEGED);
            }

            // Record disease spreading
            $GLOBALS['SITE_DB']->query_update('diseases', ['last_spread_time' => strval(time())], ['id' => strval($disease['id'])], '', 1);
        }
    }
}
