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
class config_test_set extends cms_test_case
{
    public function testSaneDefaults()
    {
        if (($this->only !== null) && ($this->only != 'testSaneDefaults')) {
            return;
        }

        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();

            $default = $ob->get_default();

            switch ($details['type']) {
                case 'integer':
                    $this->assertTrue((empty($default)) || (strval(intval($default)) == $default), 'Integer fields expect integer values, for ' . $hook);
                    break;

                case 'float':
                    $this->assertTrue((empty($default)) || (is_numeric($default)), 'Float fields expect numeric values, for ' . $hook);
                    break;

                case 'tick':
                    $this->assertTrue((empty($default)) || (in_array($default, ['0', '1'])), 'Tick fields expect boolean values, for ' . $hook);
                    break;
            }
        }
    }

    public function testNoBadLists()
    {
        if (($this->only !== null) && ($this->only != 'testNoBadLists')) {
            return;
        }

        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();

            $default = $ob->get_default();

            switch ($details['type']) {
                case 'list':
                    $this->assertTrue(!cms_empty_safe($details['list_options']), 'List options expected, for ' . $hook);
                    break;

                default:
                    $this->assertTrue(cms_empty_safe($details['list_options']), 'No list options expected, for ' . $hook);
                    break;
            }
        }
    }

    public function testMissingOptions()
    {
        if (($this->only !== null) && ($this->only != 'testMissingOptions')) {
            return;
        }

        require_code('files2');

        $matches = [];
        $done = [];

        $hooks = find_all_hooks('systems', 'config');

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
        $files[] = 'install.php';
        foreach ($files as $path) {
            if ((in_safe_mode()) && (should_ignore_file($path, IGNORE_NONBUNDLED))) {
                continue;
            }

            $file_type = get_file_extension($path);

            if ($file_type == 'php') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                $num_matches = preg_match_all('#get_option\(\'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $hook = $matches[1][$i];

                    if (isset($done[$hook])) {
                        continue;
                    }

                    $this->assertTrue(isset($hooks[$hook]), 'Missing referenced config option (.php): ' . $hook);

                    $done[$hook] = true;
                }
            }

            if ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                $num_matches = preg_match_all('#\{\$CONFIG_OPTION[^\w,\{\}]*,(\w+)\}#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $hook = $matches[1][$i];

                    // Exceptions
                    if ($hook == 'optionname') { // Example in Code Book
                        continue;
                    }

                    if (isset($done[$hook])) {
                        continue;
                    }

                    if ((in_safe_mode()) && (in_array($hook, ['facebook_uid', 'facebook_appid']))) {
                        continue;
                    }

                    $this->assertTrue(isset($hooks[$hook]), 'Missing referenced config option (' . $file_type . '): ' . $hook);

                    $done[$hook] = true;
                }
            }
        }
    }

    public function testConfigHookCompletenessAndConsistency()
    {
        if (($this->only !== null) && ($this->only != 'testConfigHookCompletenessAndConsistency')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');

        $settings_needed = [
            'human_name' => 'string',
            'type' => 'string',
            'category' => 'string',
            'group' => 'string',
            'explanation' => 'string',
            'shared_hosting_restricted' => 'string',
            'list_options' => 'string',
            'addon' => 'string',
            'required' => 'boolean',
            'public' => 'boolean',
        ];
        $settings_optional = [
            'theme_override' => 'boolean',
            'order_in_category_group' => 'integer',
            'maintenance_code' => 'string',
            'explanation_param_a' => 'string',
            'explanation_param_b' => 'string',
        ];

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();

            foreach ($settings_needed as $setting => $type) {
                $this->assertTrue(array_key_exists($setting, $details), 'Missing setting: ' . $setting . ' in ' . $hook);
                if (array_key_exists($setting, $details)) {
                    $this->assertTrue(gettype($details[$setting]) == $type, 'Incorrect data type for: ' . $setting . ' in ' . $hook);
                }
            }
            foreach ($settings_optional as $setting => $type) {
                if (array_key_exists($setting, $details)) {
                    $this->assertTrue(gettype($details[$setting]) == $type, 'Incorrect data type for: ' . $setting . ' in ' . $hook);
                }
            }

            foreach (array_keys($details) as $setting) {
                $this->assertTrue(array_key_exists($setting, $settings_needed) || array_key_exists($setting, $settings_optional), 'Unknown setting: ' . $setting);
            }

            $path = get_file_base() . '/sources/hooks/systems/config/' . $hook . '.php';
            if (!is_file($path)) {
                $path = get_file_base() . '/sources_custom/hooks/systems/config/' . $hook . '.php';
            }
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            $expected_addon = preg_replace('#^.*@package\s+(\w+).*$#s', '$1', $c);
            $this->assertTrue($details['addon'] == $expected_addon, 'Addon mismatch for ' . $hook);

            $this->assertTrue($details['addon'] != 'core', 'Don\'t put config options in core, put them in core_configuration - ' . $hook);
        }

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $file_type = get_file_extension($path);

            if ($file_type == 'php' || $file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                if ($file_type == 'php') {
                    if ((strpos($c, 'get_option') === false) && (strpos($c, 'get_theme_option') === false)) {
                        continue;
                    }
                } elseif ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                    if ((strpos($c, 'CONFIG_OPTION') === false) && (strpos($c, 'THEME_OPTION') === false)) {
                        continue;
                    }
                }

                foreach ($hooks as $hook => $ob) {
                    if ($hook == 'description') {
                        // Special case - we have a config option named 'description', and also a theme setting named 'description' -- they are separate
                    }

                    if (strpos($c, $hook) === false) {
                        continue;
                    }

                    $details = $ob->get_details();

                    if ($file_type == 'php') {
                        if (!empty($details['theme_override'])) {
                            $this->assertTrue((strpos($c, 'get_option(\'' . $hook . '\'') === false), $hook . ' must be accessed as a theme option (.php): ' . $path);
                        } else {
                            $this->assertTrue((strpos($c, 'get_theme_option(\'' . $hook . '\'') === false) || ($hook == 'description'), $hook . ' must not be accessed as a theme option (.php): ' . $path);
                        }
                    } elseif ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                        if (!empty($details['theme_override'])) {
                            $this->assertTrue((preg_match('#\{\$CONFIG_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0), $hook . ' must be accessed as a theme option: ' . $path);
                        } else {
                            $this->assertTrue((preg_match('#\{\$THEME_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0) || ($hook == 'description'), $hook . ' must not be accessed as a theme option: ' . $path);
                        }
                    }
                }
            }
        }
    }

    public function testListConfigConsistency()
    {
        if (($this->only !== null) && ($this->only != 'testListConfigConsistency')) {
            return;
        }

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();
            if ($details['type'] == 'list') {
                $list = explode('|', $details['list_options']);
                $default = $ob->get_default();

                if ($default === null) {
                    continue;
                }

                $this->assertTrue(in_array($default, $list), 'Inconsistent list default in ' . $hook);
            }
        }
    }

    public function testReasonablePerCategory()
    {
        if (($this->only !== null) && ($this->only != 'testReasonablePerCategory')) {
            return;
        }

        $categories = [];

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();
            if (!isset($categories[$details['category']])) {
                $categories[$details['category']] = 0;
            }
            $categories[$details['category']]++;
        }

        foreach ($categories as $category => $count) {
            if (in_array($category, ['TRANSACTION_FEES'])) { // Exceptions
                continue;
            }

            $this->assertTrue($count > 3, $category . ' only has ' . integer_format($count));
            $this->assertTrue($count < 105, $category . ' has as much as ' . integer_format($count)); // max_input_vars would not like a high number
        }
    }

    public function testConsistentGroupOrdering()
    {
        if (($this->only !== null) && ($this->only != 'testConsistentGroupOrdering')) {
            return;
        }

        $categories = [];

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();
            if (!isset($categories[$details['category']])) {
                $categories[$details['category']] = [];
            }
            if (!isset($categories[$details['category']][$details['group']])) {
                $categories[$details['category']][$details['group']] = [];
            }
            $categories[$details['category']][$details['group']][] = $details;
        }

        foreach ($categories as $category => $group) {
            foreach ($group as $group_name => $options) {
                $has_orders = null;
                $orders = [];

                foreach ($options as $option) {
                    $_has_orders = isset($option['order_in_category_group']);
                    if ($has_orders !== null) {
                        if ($has_orders != $_has_orders) {
                            $this->assertTrue(false, "'category' => '" . $category . "'" . ', ' . "'group' => '" . $group_name . "'" . ', has inconsistent ordering settings (some set, some not)');
                            break;
                        }
                    } else {
                        $has_orders = $_has_orders;
                    }

                    if ($has_orders) {
                        if (isset($orders[$option['order_in_category_group']])) {
                            $this->assertTrue(false, $category . '/' . $group_name . ' has duplicated order for ' . strval($option['order_in_category_group']));
                        }

                        $orders[$option['order_in_category_group']] = true;
                    }
                }
            }
        }
    }

    public function testConsistentCategoriesSet()
    {
        if (($this->only !== null) && ($this->only != 'testConsistentCategoriesSet')) {
            return;
        }

        // Find all categories by searching
        $hooks = find_all_hooks('systems', 'config');
        $categories_found = [];
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();
            $categories_found[strtolower($details['category'])] = true;
        }
        ksort($categories_found);

        // Find all categories by hooks
        $categories = find_all_hooks('systems', 'config_categories');
        ksort($categories);

        $this->assertTrue(array_keys($categories_found) === array_keys($categories));
    }
}
