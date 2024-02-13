<?php /*

Composr is free to use or incorporate this into Composr and assert any copyright.
This notification hook was created using the classifieds notification hook as a template.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  None asserted
 * @package    downloads_followup_email
 */

/**
 * Hook class.
 */
class Hook_cron_downloads_followup_email
{
    protected const INITIAL_BACK_TIME = 60 * 60 * 48; // 2 days back for first set of e-mails seems reasonable

    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('downloads_followup_email')) {
            return null;
        }

        if (!addon_installed('downloads')) {
            return null;
        }

        $time_now = time();
        if ($last_run === null) {
            $last_run = $time_now - self::INITIAL_BACK_TIME;
        }

        // Calculate on low priority
        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        return [
            'label' => 'Send download follow-up e-mails',
            'num_queued' => $calculate_num_queued ? $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(DISTINCT member_id) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'download_logging WHERE member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND date_and_time>' . strval($last_run)) : 0,
            'minutes_between_runs' => 60 * 24,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        /*
        If we just installed, reinstalled after uninstalling more than 2 days ago, or if the system scheduler
        stopped working for more than 2 days we will not generate e-mails for downloads prior to the previous
        48 hours to prevent sending stale notifications for download actions that are not recent.
        */
        $time_now = time();
        if ($last_run === null) {
            $last_run = $time_now - self::INITIAL_BACK_TIME;
        }

        require_lang('downloads_followup_email');

        $debug = (get_param_integer('debug', 0) == 1);

        if ($debug) {
            echo 'downloads_followup_email: current-timestamp / last-timestamp / difference = ' . strval($time_now) . ' / ' . strval($last_run) . ' / ' . float_to_raw_string(round((($time_now - $last_run) / 60 / 60), 2)) . ' hours' . "\n";
        }

        // Set the templates names to use. Use CUSTOM template if it exists, else use the default template.
        $theme = 'default';
        if (find_template_place('DOWNLOADS_FOLLOWUP_EMAIL_CUSTOM', $theme, '.tpl', 'templates') === null) {
            $mail_template = 'DOWNLOADS_FOLLOWUP_EMAIL';
        } else {
            $mail_template = 'DOWNLOADS_FOLLOWUP_EMAIL_CUSTOM';
        }
        if (find_template_place('DOWNLOADS_FOLLOWUP_EMAIL_CUSTOM', $theme, '.tpl', 'templates') === null) {
            $download_list_template = 'DOWNLOADS_FOLLOWUP_EMAIL_DOWNLOAD_LIST';
        } else {
            $download_list_template = 'DOWNLOADS_FOLLOWUP_EMAIL_DOWNLOAD_LIST_CUSTOM';
        }

        // Get all distinct member IDs (except for guest) from download_logging table where the date_and_time is newer than the last runtime of this hook (or last 48 hours if hook hasn't been run recently)
        $query = 'SELECT DISTINCT member_id FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'download_logging WHERE member_id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' AND date_and_time>' . strval($last_run) . ' ORDER BY member_id';
        if ($debug) {
            echo 'downloads_followup_email: distinct user query = ' . $query . "\n";
        }

        $max = 50;
        $start = 0;

        do {
            $old_limit = cms_set_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);

            $member_ids = $GLOBALS['SITE_DB']->query($query, $max, $start);

            // For each distinct member ID, send a download follow-up notification
            foreach ($member_ids as $id) {
                // Create template object to hold download list
                $download_list = new Tempcode();
                $member_id = $id['member_id'];
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, false, USERNAME_DEFAULT_NULL);
                if ($username === null) {
                    continue;
                }
                $lang = get_lang($member_id);
                $zone = get_module_zone('downloads');
                $count = 0;

                if ($debug) {
                    echo 'downloads_followup_email: preparing notification to ID #' . strval($member_id) . ' ($username) language=' . $lang . "\n";
                }

                // Do a query to get list of download IDs the current member ID has downloaded since last run and place them in a content variable
                $query = 'SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'download_logging WHERE member_id=' . strval($member_id) . ' AND date_and_time>' . strval($last_run);
                if ($debug) {
                    echo 'downloads_followup_email: download IDs query = ' . $query . "\n";
                }
                $downloads = $GLOBALS['SITE_DB']->query($query);
                foreach ($downloads as $download) {
                    // Do a query to get download names and generate links
                    $the_download = $GLOBALS['SITE_DB']->query_select('download_downloads', ['*'], ['id' => $download['id']], '', 1);
                    $root = get_param_integer('root', db_get_first_id(), true);
                    $map = ['page' => 'downloads', 'type' => 'entry', 'id' => $download['id'], 'root' => ($root == db_get_first_id()) ? null : $root];
                    $the_download_url = static_evaluate_tempcode(build_url($map, $zone));
                    $name = get_translated_text($the_download[0]['name']);

                    if ($debug) {
                        echo 'downloads_followup_email: download query = ' . $query . "\n";
                    }
                    if ($debug) {
                        echo 'downloads_followup_email: download name / download filename / download url = ' . $name . ' / ' . $the_download[0]['original_filename'] . ' / ' . $the_download_url . "\n";
                    }

                    $download_list->attach(do_template($download_list_template, ['DOWNLOAD_NAME' => $name, 'DOWNLOAD_FILENAME' => $the_download[0]['original_filename'], 'DOWNLOAD_URL' => $the_download_url]));
                    $count++;
                }
                $s = ''; // Can be used to pluralise the word download in the subject line in the language .ini file if we have more than one download (better than using download(s))
                if ($count > 1) {
                    $s = 's';
                }
                $subject_line = do_lang('SUBJECT_DOWNLOADS_FOLLOWUP_EMAIL', get_site_name(), $username, $s, $lang, false);
                // Pass download count, download list, and member ID to template.
                $message = static_evaluate_tempcode(do_notification_template($mail_template, ['MEMBER_ID' => strval($member_id), 'DOWNLOAD_LIST' => $download_list, 'DOWNLOAD_COUNT' => strval($count)]));

                if ($debug) {
                    echo 'downloads_followup_email: sending notification (if user allows download followup notifications) to ID #' . strval($member_id) . ' (' . $username . ')' . "\n";
                }
                if ($debug) {
                    echo 'downloads_followup_email: notifications enabled = ' . (notifications_enabled('downloads_followup_email', null, $member_id) ? 'true' : 'false') . "\n";
                }

                // Send actual notification
                require_code('notifications');
                dispatch_notification('downloads_followup_email', '', $subject_line, $message, [$member_id], A_FROM_SYSTEM_PRIVILEGED);
            }

            $start += $max;

            cms_set_time_limit($old_limit);
        } while (!empty($member_ids));
    }
}
