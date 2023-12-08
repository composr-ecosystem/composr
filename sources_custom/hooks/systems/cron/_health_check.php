<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/**
 * Hook class.
 */
class Hook_cron__health_check
{
    /**
     * Run function for CRON hooks. Searches for tasks to perform.
     */
    public function run()
    {
        // Note that we have a leading "_" on the hook name so that it runs first (we run CRON hooks in sorted order)

        $last = get_value('last_health_check', null, true);
        $time = time();
        if (($last !== null) && (intval($last) > $time - intval(get_option('hc_cron_regularity')) * 60)) {
            return;
        }
        set_value('last_health_check', strval($time), true);

        require_code('health_check');

        $cron_notify_regardless = get_option('hc_cron_notify_regardless');

        $sections_to_run = (get_option('hc_cron_sections_to_run') == '') ? array() : explode(',', get_option('hc_cron_sections_to_run'));
        $passes = ($cron_notify_regardless == '1');
        $skips = ($cron_notify_regardless == '1');
        $manual_checks = false;

        $has_fails = false;
        $categories = run_health_check($has_fails, $sections_to_run, $passes, $skips, $manual_checks, false, null);

        if ((count($categories) > 0) || ($cron_notify_regardless == '1')) {
            $results = do_template('HEALTH_CHECK_RESULTS', array('_GUID' => 'b7bbb671bacc1a5eee03a71c3f1a1eac', 'CATEGORIES' => $categories));

            require_code('notifications');
            $subject = do_lang('HEALTH_CHECK_SUBJECT_' . ($has_fails ? 'fail' : 'misc'));
            $message = do_lang('HEALTH_CHECK_BODY', $results->evaluate());
            dispatch_notification('health_check', $has_fails ? '1' : '0', $subject, $message, null, A_FROM_SYSTEM_PRIVILEGED, $has_fails ? 1 : 4);
        }
    }
}
