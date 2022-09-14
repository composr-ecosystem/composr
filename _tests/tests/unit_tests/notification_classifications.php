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

    public function testNonOptimalNotificationTypes()
    {
        require_code('notifications');
        $hook_obs = find_all_hook_obs('systems', 'notifications', 'Hook_notification_');
        $notification_types = [];
        foreach ($hook_obs as $hook => $hook_ob) {
            $notification_types = array_keys($hook_ob->list_handled_codes());

            if (count($notification_types) == 1) {
                $notification_type = $notification_types[0];

                // Exception: programattically generated notification types
                if (strpos($notification_type, '__') !== false) {
                    continue;
                }

                $this->assertTrue($notification_type == $hook, 'Needless notification type/filename inconsistency: ' . $hook . ' vs ' . $notification_type);
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
        Actually it is a bad idea to try and merge all the notification types into a smaller number of hook files as it breaks the optimisation of automatically finding the correct file based on matching filename and hook type.
        Theoretically we could find a new optimisation, like specifying the hook file in the notification dispatch call, but right now there is no strong incentive for this as our number of hooks has been brought down to a reasonable level - not worth the work/overhead-complexity.
        So, it is COMMON to keep adding stuff to the exceptions listed in the below sections.
        */

        foreach ($code as $contents => $types) {
            sort($types);

            // Exceptions
            if ($types == ["adminzone_dashboard_accessed", "auto_ban", "hack_attack"]) { // Used in high-stakes fast-speed situations
                continue;
            }

            $this->assertTrue(count($types) == 1, json_encode($types) . ' can all be merged into a single hook based on code similarity');
        }

        foreach ($packages as $addon => $types) {
            sort($types);

            // Exceptions
            if ($types == ["adminzone_dashboard_accessed","auto_ban","error_occurred","hack_attack","spam_check_block"]) { // Used in high-stakes fast-speed situations
                continue;
            }
            if ($types == ["chat","cns_friend_birthday","member_entered_chatroom"]) { // Used too commonly
                continue;
            }
            if ($types == ["ticket_assigned_staff","ticket_new_staff","ticket_reply","ticket_reply_staff"]) { // Too complex
                continue;
            }
            if ($types == ["comment_posted","like"]) { // Used too commonly
                continue;
            }
            if ($types == ["cns_pts","cns_topic"]) { // cns_topic used too commonly
                continue;
            }

            $this->assertTrue(count($types) == 1, json_encode($types) . ' can all be merged into a single hook based on addon being ' . $addon);
        }
    }
}
