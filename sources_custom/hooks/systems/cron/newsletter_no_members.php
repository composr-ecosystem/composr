<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    newsletter_no_members
 */

/**
 * Hook class.
 */
class Hook_cron_newsletter_no_members
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        $query = 'SELECT m_email_address FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . db_string_equal_to('m_validated_email_confirm_code', '');
        if (addon_installed('unvalidated')) {
            $query .= ' AND m_validated=1';
        }
        if (get_param_integer('backlog', 0) != 1) {
            $query .= ' AND m_join_time>' . strval(time() - 60 * 60 * 24);
        }
        $new_members = $GLOBALS['FORUM_DB']->query($query);
        if (count($new_members) > 0) {
            $or_list = '';
            foreach ($new_members as $new_member) {
                if ($or_list != '') {
                    $or_list .= ' OR ';
                }
                $or_list .= db_string_equal_to('email', $new_member['m_email_address']);
            }
            //$GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'newsletter_subscribers WHERE ' . $or_list);   Leave the main account
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'newsletter_subscribe WHERE ' . $or_list, null, null, false, true); // Customise this line to remove them only from a specific newsletter
        }
    }
}
