<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _notification_classifications_test_set extends cms_test_case
{
    public function testReasonableHooksCount()
    {
        if (($this->only !== null) && ($this->only != 'testReasonableHooksCount')) {
            return;
        }

        $hooks = find_all_hooks('systems', 'notifications');

        $cnt_main = 0;
        foreach ($hooks as $hook_dir) {
            if (strpos($hook_dir, '_custom') === false) {
                $cnt_main++;
            }
        }
        $this->assertTrue($cnt_main <= 50, 'We have a lot of notification hooks now (' . integer_format($cnt_main) . '), this will hurt performance (memory usage on notifications UI in particular)');

        $cnt_all = count($hooks);
        $this->assertTrue($cnt_all <= 70, 'We have a lot of notification hooks now (' . integer_format($cnt_all) . '), this will hurt performance (memory usage on notifications UI in particular)');
    }

    public function testAllNotificationsCoded()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        if (($this->only !== null) && ($this->only != 'testAllNotificationsCoded')) {
            return;
        }

        // Ensure all notification codes used
        require_code('notifications');
        $hook_obs = find_all_hook_obs('systems', 'notifications', 'Hook_notification_');
        $notification_codes = [];
        foreach ($hook_obs as $hook_ob) {
            $notification_codes += $hook_ob->list_handled_codes();
        }

        require_code('files2');

        $php_path = find_php_path();

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (basename($path) == 'phpstub.php') {
                continue;
            }

            foreach (array_keys($notification_codes) as $notification_code) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
                if (preg_match('#dispatch_notification\(\s*\'(\w+:)?' . $notification_code . '\'#', $c) != 0) {
                    unset($notification_codes[$notification_code]);
                }
            }
        }

        $allowed = [
            // Adjust this to account for cases of notifications coded up in non-direct ways
            'ticket_new_staff',
            'ticket_reply_staff',
        ];
        foreach (array_keys($notification_codes) as $notification_code) {
            // Exceptions
            if (preg_match('#^(classifieds|catalogue_entry|catalogue_view_reports)__#', $notification_code) != 0) {
                continue;
            }

            $this->assertTrue(in_array($notification_code, $allowed), $notification_code . ' is unused');
        }
    }

    public function testNonOptimalNotificationCodes()
    {
        require_code('notifications');
        $hook_obs = find_all_hook_obs('systems', 'notifications', 'Hook_notification_');
        $notification_codes = [];
        foreach ($hook_obs as $hook => $hook_ob) {
            $notification_codes = array_keys($hook_ob->list_handled_codes());

            if (count($notification_codes) == 1) {
                $notification_code = $notification_codes[0];

                // Exception: programmatically generated notification codes
                if (strpos($notification_code, '__') !== false) {
                    continue;
                }

                $this->assertTrue($notification_code == $hook, 'Needless notification code/filename inconsistency: ' . $hook . ' vs ' . $notification_code);
            }
        }
    }

    public function testNoPointlessNotificationHookSeparation()
    {
        if (($this->only !== null) && ($this->only != 'testNoPointlessNotificationHookSeparation')) {
            return;
        }

        $code = [];
        $packages = [];
        $hooks = find_all_hooks('systems', 'notifications');
        foreach ($hooks as $hook => $hook_dir) {
            $contents = cms_file_get_contents_safe(get_file_base() . '/' . $hook_dir . '/hooks/systems/notifications/' . $hook . '.php');

            // By addon...

            $addon = '';
            $matches = [];
            if (preg_match('#@package\s+(\w+)#', $contents, $matches) != 0) {
                $addon = $matches[1];
                if ($addon != 'core_cns') {
                    //$addon = preg_replace('#^core_\w+$#', 'core', $addon);    Nah, means merging stuff that doesn't semantically fit well together
                }
            }
            if (strpos($contents, 'Hook_notification__Staff') !== false) {
                $addon .= '__staff';
            }

            if (!array_key_exists($addon, $packages)) {
                $packages[$addon] = [];
            }
            $packages[$addon][] = $hook;

            // By code similarity...

            $contents = preg_replace('#Hook_notification_\w+#', '', $contents);
            $contents = preg_replace('#\$list.*#', '', $contents);

            if (!array_key_exists($contents, $code)) {
                $code[$contents] = [];
            }
            $code[$contents][] = $hook;
        }

        /*
        If merging notification codes that are in the 'critical path' (i.e. need to be able to run quickly), call dispatch_notification with the file:code syntax.
        */

        foreach ($code as $contents => $codes) {
            sort($codes);

            // Exceptions
            // (None right now)

            $this->assertTrue(count($codes) == 1, json_encode($codes) . ' can all be merged into a single hook based on code similarity');
        }

        foreach ($packages as $addon => $codes) {
            sort($codes);

            // Exceptions
            if ($codes == ["core_staff", "error_occurred"]) { // error_occurred is too complex to want to mix in
                continue;
            }
            if ($codes == ["ticket_assigned_staff", "ticket_new_staff", "ticket_reply", "ticket_reply_staff"]) { // Too complex to want to mix together
                continue;
            }
            if ($codes == ["comment_posted", "like"]) { // comment_posted is too complex to want to mix in
                continue;
            }
            if ($codes == ["cns_pts", "cns_topic"]) { // cns_topic is too complex to want to mix in
                continue;
            }

            $this->assertTrue(count($codes) == 1, json_encode($codes) . ' can all be merged into a single hook based on addon being ' . $addon);
        }
    }
}
