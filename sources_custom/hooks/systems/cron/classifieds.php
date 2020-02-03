<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_cron_classifieds
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        if (!addon_installed('catalogues')) {
            return;
        }

        if (!$GLOBALS['SITE_DB']->table_exists('classifieds_prices')) {
            return;
        }

        $last = get_value('last_classified_refresh');
        $time = time();
        if ((!is_null($last)) && (intval($last) > $time - 60 * 60)) {
            return; // Don't do more than once per hour
        }

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        $start = 0;
        do {
            $entries = $GLOBALS['SITE_DB']->query_select('catalogue_entries e JOIN ' . get_table_prefix() . 'classifieds_prices p ON p.c_catalogue_name=e.c_name', array('e.*'), array('ce_validated' => 1), '', 1000, $start);
            foreach ($entries as $entry) {
                if ($entry['ce_last_moved'] == $entry['ce_add_date']) {
                    require_code('classifieds');
                    initialise_classified_listing($entry);
                }

                // Expiring
                if ($entry['ce_last_moved'] < $time) { // We have stolen use of the standard Composr "ce_last_moved" property as a "next move" property
                    $GLOBALS['SITE_DB']->query_update('catalogue_entries', array('ce_validated' => 0), array('id' => $entry['id']), '', 1);
                    decache('main_cc_embed');
                    decache('main_recent_cc_entries');
                    require_code('catalogues2');
                    calculate_category_child_count_cache($entry['cc_id']);
                } elseif (($entry['ce_last_moved'] < $time + 60 * 60 * 24) && ($entry['ce_last_moved'] > $time + 60 * 60 * 23)) { /* one hour time window; assumes CRON runs at least once per hour */
                    // Expiring in 24 hours
                    require_code('notifications');
                    require_lang('classifieds');

                    $member_id = $entry['ce_submitter'];
                    $renew_url = build_url(array('page' => 'classifieds', 'type' => 'adverts', 'id' => $member_id), get_module_zone('classifieds'));

                    require_code('catalogues');
                    $data_map = get_catalogue_entry_map($entry, null, 'CATEGORY', 'DEFAULT', null, null, array(0));
                    $ad_title = $data_map['FIELD_0_PLAIN'];
                    if (is_object($ad_title)) {
                        $ad_title = $ad_title->evaluate();
                    }

                    $subject_line = do_lang('SUBJECT_CLASSIFIED_ADVERT_EXPIRING', $ad_title, get_site_name(), null, get_lang($member_id), false);
                    $mail = do_notification_lang('MAIL_CLASSIFIED_ADVERT_EXPIRING', $ad_title, comcode_escape(get_site_name()), comcode_escape($renew_url->evaluate()), get_lang($member_id), false);

                    // Send actual notification
                    dispatch_notification('classifieds__' . $entry['c_name'], '', $subject_line, $mail, array($member_id), A_FROM_SYSTEM_PRIVILEGED);
                }
            }

            $start += 1000;
        } while (count($entries) == 1000);

        set_value('last_classified_refresh', strval($time));
    }
}
