<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class notifications_all_coded_test_set extends cms_test_case
{
    public function testAllNotificationsCoded()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        // Ensure all notification types used
        $hooks = find_all_hooks('systems', 'notifications');

        require_code('files2');

        $php_path = find_php_path();

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (basename($path) == 'phpstub.php') {
                continue;
            }

            foreach (array_keys($hooks) as $hook) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
                if (preg_match('#dispatch_notification\(\s*\'' . $hook . '\'#', $c) != 0) {
                    unset($hooks[$hook]);
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
            'catalogue_view_reports',
            'catalogue_entry',
            'classifieds',
        ];
        foreach (array_keys($hooks) as $hook) {
            $this->assertTrue(in_array($hook, $allowed), $hook . ' is unused');
        }
    }
}
