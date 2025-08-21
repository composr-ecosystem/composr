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

/**
 * Hook class.
 */
class Hook_cron_cms_homesite_tracker
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
        if (!addon_installed('cms_homesite_tracker')) {
            return null;
        }
        if (!addon_installed('points')) {
            return null;
        }

        return [
            'label' => 'Issue tracker',
            'num_queued' => null,
            'minutes_between_runs' => 5,
            'enabled_by_default' => true,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        require_code('files2');

        // Run Mantis Cron (must be done in the shell as required by Mantis)
        shell_exec(find_php_path() . ' ' . get_custom_file_base() . '/tracker/scripts/cronjob.php');
        shell_exec(find_php_path() . ' ' . get_custom_file_base() . '/tracker/scripts/send_emails.php');

        // We expect any missed processes are handled manually, otherwise this could get ugly trying to process them all
        if ($last_run === null) {
            $last_run = time() - (60 * 5);
        }

        require_code('points_escrow__sponsorship');
        require_code('mantis');

        // Process sponsorship escrows (must be the first operation so escrows can be updated before we process changes to issue statuses)
        $max = 100;
        $start = 0;
        do {
            $rows = $GLOBALS['SITE_DB']->query_parameterised('SELECT * FROM mantis_bug_history_table WHERE ' . db_string_equal_to('field_name', 'sponsorship_total') . ' AND date_modified>={last_run}', ['last_run' => $last_run], $max, $start);
            foreach ($rows as $row) {
                $old_value = intval($row['old_value']);
                $new_value = intval($row['new_value']);

                $result = null;
                if (($old_value == 0) && ($new_value > 0)) {
                    $result = escrow_create_sponsorship($row['bug_id'], $new_value, $row['user_id']);
                } elseif (($old_value > 0) && ($new_value > 0)) {
                    $result = escrow_edit_sponsorship($row['bug_id'], $row['user_id'], $new_value, $row['user_id']);
                } elseif (($old_value > 0) && ($new_value == 0)) {
                    $result = escrow_cancel_sponsorship($row['bug_id'], $row['user_id'], $row['user_id']);
                }

                // Update sponsorship status in Mantis
                $GLOBALS['SITE_DB']->query_parameterised('UPDATE mantis_sponsorship_table SET paid={paid} WHERE user_id={user_id} AND bug_id={bug_id}', [
                    'paid' => ($result === null) ? 1 : 2,
                    'user_id' => $row['user_id'],
                    'bug_id' => $row['bug_id'],
                ]);
            }

            $start += $max;
        } while (!empty($rows));

        // Process status-based points
        $max = 100;
        $start = 0;
        do {
            $rows = $GLOBALS['SITE_DB']->query_parameterised('SELECT * FROM mantis_bug_history_table WHERE ' . db_string_equal_to('field_name', 'status') . ' AND date_modified>={last_run}', ['last_run' => $last_run], $max, $start);
            foreach ($rows as $row) {
                $old_value = intval($row['old_value']);
                $new_value = intval($row['new_value']);

                // Process sponsorship points (status changes; we do not support handling deleted issues)
                $result = [[], false];
                if (($old_value != 80) && ($new_value == 80)) { // Resolved
                    $result = escrow_complete_all_sponsorships($row['bug_id']);
                } elseif (($old_value != 90) && ($new_value == 90)) { // Closed
                    $result = escrow_cancel_all_sponsorships($row['bug_id'], 'The issue was closed');
                }

                // Process tracker issue points
                if (($old_value != 80) && ($new_value == 80)) { // Resolved
                    $result = award_tracker_points($row['bug_id']);
                } elseif (($old_value == 80) && ($new_value != 80)) { // Previously resolved but not anymore
                    reverse_tracker_points($row['bug_id']);
                }
            }

            $start += $max;
        } while (!empty($rows));
    }
}
