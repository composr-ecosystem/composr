<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_cron_classifieds
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
        if (!addon_installed('classified_ads')) {
            return null;
        }

        if (!addon_installed('catalogues')) {
            return null;
        }
        if (!addon_installed('ecommerce')) {
            return null;
        }

        // Calculate on low priority
        if ($calculate_num_queued === null) {
            $calculate_num_queued = true;
        }

        if ($calculate_num_queued) {
            $table = 'catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices p ON p.c_catalogue_name=e.c_name';
            $num_queued = $GLOBALS['SITE_DB']->query_select_value($table, 'COUNT(*)', ['ce_validated' => 1], ' AND ce_last_moved<' . strval(time()));
        } else {
            $num_queued = null;
        }

        return [
            'label' => 'Classified listings expiry',
            'num_queued' => $num_queued,
            'minutes_between_runs' => 60,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        $time_now = time();

        $max = 300;

        $start = 0;
        do {
            $old_limit = cms_set_time_limit(TIME_LIMIT_EXTEND__CRAWL);

            $table = 'catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices p ON p.c_catalogue_name=e.c_name';
            $entries = $GLOBALS['SITE_DB']->query_select($table, ['e.*'], ['ce_validated' => 1], ' AND ce_last_moved<' . strval(time() + 60 * 60 * 24), 300, $start);
            foreach ($entries as $entry) {
                if ($entry['ce_last_moved'] == $entry['ce_add_date']) {
                    require_code('classifieds');
                    initialise_classified_listing($entry);
                }

                // Expiring
                if ($entry['ce_last_moved'] < $time_now) { // We have stolen use of the standard Composr "ce_last_moved" property as a "next move" property
                    $GLOBALS['SITE_DB']->query_update('catalogue_entries', ['ce_validated' => 0], ['id' => $entry['id']], '', 1);
                    delete_cache_entry('main_cc_embed');
                    delete_cache_entry('main_recent_cc_entries');
                    require_code('catalogues2');
                    calculate_category_child_count_cache($entry['cc_id']);
                } elseif (($entry['ce_last_moved'] < $time_now + 60 * 60 * 24) && ($entry['ce_last_moved'] > $time_now + 60 * 60 * 23)) { /* one hour time window; assumes the system scheduler runs at least once per hour */
                    // Expiring in 24 hours
                    require_code('notifications');
                    require_lang('classifieds');

                    $member_id = $entry['ce_submitter'];
                    $renew_url = build_url(['page' => 'classifieds', 'type' => 'browse', 'id' => $member_id], get_module_zone('classifieds'));

                    require_code('catalogues');
                    $data_map = get_catalogue_entry_map($entry, null, 'CATEGORY', 'DEFAULT', null, null, [0]);
                    $ad_title = $data_map['FIELD_0_PLAIN'];
                    if (is_object($ad_title)) {
                        $ad_title = $ad_title->evaluate();
                    }

                    $subject_line = do_lang('SUBJECT_CLASSIFIED_ADVERT_EXPIRING', $ad_title, get_site_name(), null, get_lang($member_id), false);
                    $mail = do_notification_lang('MAIL_CLASSIFIED_ADVERT_EXPIRING', $ad_title, comcode_escape(get_site_name()), comcode_escape($renew_url->evaluate()), get_lang($member_id), false);

                    // Send actual notification
                    dispatch_notification('classifieds__' . $entry['c_name'], '', $subject_line, $mail, [$member_id], A_FROM_SYSTEM_PRIVILEGED);
                }
            }

            $start += 300;

            cms_set_time_limit($old_limit);
        } while (!empty($entries));
    }
}
