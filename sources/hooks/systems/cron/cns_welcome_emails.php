<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    welcome_emails
 */

/**
 * Hook class.
 */
class Hook_cron_cns_welcome_emails
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        //if (!running_script('execute_temp')) return;
        $time_now = time();
        //$time_now = 1335726076;
        $last_cron_time = intval(get_value('last_welcome_mail_time', null, true));
        if ($last_cron_time == 0) {
            $last_cron_time = $time_now - 24 * 60 * 60 * 7;
        }
        set_value('last_welcome_mail_time', strval($time_now), true);
        //$last_cron_time = $time_now - 60 * 60 * 1; Useful for debugging

        require_code('mail');
        require_code('cns_welcome_emails');

        $GLOBALS['NO_DB_SCOPE_CHECK'] = true;
        $mails = $GLOBALS['SITE_DB']->query_select('f_welcome_emails', array('*'));
        $GLOBALS['NO_DB_SCOPE_CHECK'] = false;
        foreach ($mails as $mail) {
            $send_seconds_after_joining = $mail['w_send_time'] * 60 * 60;

            $members = array();

            $newsletter_style = false;

            // By newsletter
            if (($mail['w_newsletter'] !== null) && (addon_installed('newsletter'))) {
                $newsletter_style = true;

                // Think of it like this, m_join_time (members join time) must between $last_cron_time and $time_now, but offset back by $send_seconds_after_joining
                $where = ' WHERE join_time>' . strval($last_cron_time - $send_seconds_after_joining) . ' AND join_time<=' . strval($time_now - $send_seconds_after_joining) . ' AND (the_level=3 OR the_level=4) AND newsletter_id=' . strval($mail['w_newsletter']);
                $members = array_merge($members, $GLOBALS['SITE_DB']->query('SELECT s.email AS m_email_address,the_password,n_forename,n_surname,n.id,join_time AS m_join_time FROM ' . get_table_prefix() . 'newsletter_subscribe s JOIN ' . get_table_prefix() . 'newsletter_subscribers n ON n.email=s.email ' . $where . ' GROUP BY s.email'));
            } // By usergroup
            elseif ((!is_null($mail['w_usergroup'])) && (get_forum_type() == 'cns')) {
                $where = ' WHERE join_time>' . strval($last_cron_time - $send_seconds_after_joining) . ' AND join_time<=' . strval($time_now - $send_seconds_after_joining) . ' AND um.usergroup_id=' . strval($mail['w_usergroup']);
                $query = 'SELECT m.id as id, m.m_email_address AS m_email_address,m.m_username AS m_username,um.join_time AS m_join_time FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_group_join_log as um JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members as m ON m.id=um.member_id ' . $where;
                $_members = $GLOBALS['FORUM_DB']->query($query);
                foreach ($_members as $member) {
                    $ok = false;
                    switch ($mail['w_usergroup_type']) {
                        case '':
                            $ok = in_array($mail['w_usergroup'], $GLOBALS['FORUM_DRIVER']->get_members_groups($member['id'])); // If member still in the group
                            break;
                        case 'primary':
                            $ok = ($GLOBALS['FORUM_DRIVER']->get_member_row_field($member['id'], 'm_primary_group') == $mail['w_usergroup']); // If member still in the group
                            break;
                        case 'secondary':
                            $ok = !is_null($GLOBALS['FORUM_DB']->query_select_value_if_there('f_group_members', 'gm_member_id', array('gm_group_id' => $mail['w_usergroup'], 'gm_member_id' => $member['id'], 'gm_validated' => 1)));
                            break;
                    }
                    if ($ok) {
                        $members[] = $member;
                    }
                }
            } // By general membership
            elseif ((is_null($mail['w_newsletter'])) && (is_null($mail['w_usergroup']))) {
                // Think of it like this, m_join_time (members join time) must between $last_cron_time and $time_now, but offset back by $send_seconds_after_joining
                $where = ' WHERE m_join_time>' . strval($last_cron_time - $send_seconds_after_joining) . ' AND m_join_time<=' . strval($time_now - $send_seconds_after_joining);
                if (get_option('allow_email_from_staff_disable') == '1') {
                    $where .= ' AND m_allow_emails_from_staff=1';
                }
                $where .= ' AND ' . db_string_not_equal_to('m_email_address', '');
                if ($send_seconds_after_joining != 0) {
                    $where .= ' AND ' . db_string_equal_to('m_validated_email_confirm_code', '');
                    if (addon_installed('unvalidated')) {
                        $where .= ' AND m_validated=1';
                    }
                }
                $query = 'SELECT m_email_address,m_username,id,m_join_time FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members' . $where;
                $members = array_merge($members, $GLOBALS['FORUM_DB']->query($query));
            }

            foreach ($members as $member) {
                list($subject, $message, $is_html, $name) = cns_prepare_welcome_email($mail, $member);

                if (get_value('notification_safety_testing') === '1') {
                    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('logged_mail_messages', 'm_date_and_time', array('m_subject' => $subject, 'm_to_email' => serialize(array($member['m_email_address']))));
                    if (!is_null($test)) {
                        if ($test > $member['m_join_time']) {
                            fatal_exit(do_lang('INTERNAL_ERROR') . ' [' . $member['m_email_address'] . ']');
                        }
                        // otherwise they probably just resubscribed and hence bumped their time
                    }
                }

                // Send e-mail
                mail_wrap($subject, $message, array($member['m_email_address']), $name, '', '', 3, null, false, null, true, $is_html);
            }
        }
    }
}
