<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
    protected $new_members;

    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('newsletter_no_members')) {
            return null;
        }

        if (!addon_installed('newsletter')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        // Calculate on low priority
        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        if ($calculate_num_queued) {
            $query = 'SELECT m_email_address FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . db_string_equal_to('m_validated_email_confirm_code', '');
            if (addon_installed('unvalidated')) {
                $query .= ' AND m_validated=1';
            }
            if ($last_run !== null) {
                $query .= ' AND m_join_time>' . strval($last_run);
            }

            $this->new_members = $GLOBALS['FORUM_DB']->query($query);
            if (!empty($this->new_members)) {
                $or_list = '';
                foreach ($this->new_members as $new_member) {
                    if ($or_list != '') {
                        $or_list .= ' OR ';
                    }
                    $or_list .= db_string_equal_to('email', $new_member['m_email_address']);
                }
                $num_queued = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'newsletter_subscribe WHERE ' . $or_list);
            } else {
                $num_queued = 0;
            }
        } else {
            $num_queued = null;
        }

        return [
            'label' => 'Delete newsletter users who are also members',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 0,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        if (!empty($this->new_members)) {
            $or_list = '';
            foreach ($this->new_members as $new_member) {
                if ($or_list != '') {
                    $or_list .= ' OR ';
                }
                $or_list .= db_string_equal_to('email', $new_member['m_email_address']);
            }
            //$GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'newsletter_subscribers WHERE ' . $or_list);   Leave the main account
            $GLOBALS['SITE_DB']->query('DELETE FROM ' . get_table_prefix() . 'newsletter_subscribe WHERE ' . $or_list, null, 0, false, true); // Customise this line to remove them only from a specific newsletter
        }
    }
}
