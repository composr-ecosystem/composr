<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class notification_classifications_test_set extends cms_test_case
{
    public function testAllNotificationsCoded()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        if (($this->only !== null) && ($this->only != 'testAllNotificationsCoded')) {
            return;
        }

        // Ensure all notification types used
        require_code('notifications');
        $hook_obs = find_all_hook_obs('systems', 'notifications', 'Hook_notification_');
        $notification_types = [];
        foreach ($hook_obs as $hook_ob) {
            $notification_types += $hook_ob->list_handled_codes();
        }

        require_code('files2');

        $php_path = find_php_path();

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (basename($path) == 'phpstub.php') {
                continue;
            }

            foreach (array_keys($notification_types) as $notification_type) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
                if (preg_match('#dispatch_notification\(\s*\'' . $notification_type . '\'#', $c) != 0) {
                    unset($notification_types[$notification_type]);
                }
            }
        }

        $allowed = [
            // Adjust this to account for cases of notifications coded up in non-direct ways
            'error_occurred_cron',
            'error_occurred_missing_page',
            'error_occurred_missing_reference',
            'error_occurred_missing_reference_important',
            'error_occurred_missing_resource',
            'error_occurred_api',
            'error_occurred_rss',
            'ticket_new_staff',
            'ticket_reply_staff',
        ];
        foreach (array_keys($notification_types) as $notification_type) {
            // Exceptions
            if (preg_match('#^(classifieds|catalogue_entry|catalogue_view_reports)__#', $notification_type) != 0) {
                continue;
            }

            $this->assertTrue(in_array($notification_type, $allowed), $notification_type . ' is unused');
        }
    }

    public function testNoPointlessNotificationHookSeparation()
    {
        if (($this->only !== null) && ($this->only != 'testNoPointlessNotificationHookSeparation')) {
            return;
        }

        $code = [];
        $hooks = find_all_hooks('systems', 'notifications');
        foreach ($hooks as $hook => $hook_dir) {
            $file = cms_file_get_contents_safe(get_file_base() . '/' . $hook_dir . '/hooks/systems/notifications/' . $hook . '.php');

            $file = preg_replace('#Hook_notification_\w+#', '', $file);
            $file = preg_replace('#\$list.*#', '', $file);

            $ok = !array_key_exists($file, $code);
            $this->assertTrue($ok, $hook . ' can be merged' . ($ok ? '' : (' (with ' . $code[$file] . ')')));
            $code[$file] = $hook;
        }
    }
}
