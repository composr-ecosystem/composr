<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class notifications_all_coded_test_set extends cms_test_case
{
    public function testAllNotificationsCoded()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        // Ensure all notification types used
        require_code('notifications');
        $hook_obs= find_all_hooks('systems', 'notifications');
        $notification_types = [];
        foreach (array_keys($hook_obs) as $hook) {
            require_code('hooks/systems/notifications/' . $hook);
            $hook_ob = object_factory('Hook_notification_' . $hook);
            $notification_types += $hook_ob->list_handled_codes();
        }

        require_code('files2');
        $php_path = find_php_path();
        $contents = get_directory_contents(get_file_base());
        foreach ($contents as $c) {
            if ((substr($c, -4) == '.php') && (basename($c) != 'errorlog.php') && (basename($c) != 'phpstub.php') && (basename($c) != 'permissioncheckslog.php')) {
                foreach (array_keys($notification_types) as $notification_type) {
                    $file = file_get_contents($c);
                    if (preg_match('#dispatch_notification\(\s*\'' . $notification_type . '\'#', $file) != 0) {
                        unset($notification_types[$notification_type]);
                    }
                }
            }
        }

        $allowed = array( // Adjust this to account for cases of notifications coded up in non-direct ways
                          'error_occurred_cron',
                          'error_occurred_missing_page',
                          'error_occurred_missing_reference',
                          'error_occurred_missing_reference_important',
                          'error_occurred_missing_resource',
                          'error_occurred_weather',
                          'error_occurred_rss',
                          'ticket_new_staff',
                          'ticket_reply_staff',
                          'catalogue_view_reports',
        );
        foreach (array_keys($notification_types) as $notification_type) {
            // Exceptions
            if (preg_match('#^(classifieds|catalogue_entry|catalogue_view_reports)__#', $notification_type) != 0) {
                continue;
            }

            $this->assertTrue(in_array($notification_type, $allowed), $notification_type . ' is unused');
        }
    }
}
