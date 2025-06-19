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
class config_options_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testMissingOptions()
    {
        if (($this->only !== null) && ($this->only != 'testMissingOptions')) {
            return;
        }

        require_code('files2');

        $matches = [];
        $found = [];

        $hooks = find_all_hooks('systems', 'config');
        ksort($hooks);

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['php', 'tpl', 'txt', 'css', 'js', 'xml']);
        $files[] = 'install.php';

        foreach ($files as $path) {
            if ((in_safe_mode()) && (should_ignore_file($path, IGNORE_NONBUNDLED))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $file_type = get_file_extension($path);

            if ($file_type == 'php') {
                $num_matches = preg_match_all('#get_(ecommerce_option|theme_option|option|option_with_overrides)\(\'([^\']+)\'[\),]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $hook = $matches[2][$i];

                    if (empty($found[$hook])) {
                        $found[$hook] = ($matches[1][$i] == 'theme_option');
                    }
                }
            }

            if ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js' || $file_type == 'xml') {
                $num_matches = preg_match_all('#\{\$(CONFIG|THEME|ECOMMERCE)_OPTION[^\w,\{\}]*,(\w+)[\},]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $hook = $matches[2][$i];

                    if (empty($found[$hook])) {
                        $found[$hook] = ($matches[1][$i] == 'THEME');
                    }
                }
            }

            if ($file_type == 'js') {
                $num_matches = preg_match_all('#\$cms\.configOption\(\'(\w+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $hook = $matches[1][$i];

                    if (empty($found[$hook])) {
                        $found[$hook] = false;
                    }
                }
            }
        }

        ksort($found);

        // Find missing
        foreach ($found as $hook => $as_theme_image) {
            // Exceptions
            if (in_array($hook, [
                'optionname', // Example in Code Book

                // LEGACY Removed config options that still exist in upgrade code for migration purposes
                'primary_paypal_email',
                'payment_gateway_test_username',
                'call_home',
                'send_error_emails_developers',
            ])) {
                continue;
            }
            if (($as_theme_image) && (in_array($hook, [
                'author',
                'capability_administrative',
                'capability_block_layouts',
                'capability_emails',
                'capability_printing',
                'cms_version',
                'copyright_attribution',
                'dependencies',
                'enable_logowizard',
                'enable_themewizard',
                'incompatibilities',
                'language',
                'licence',
                'logo_x_offset',
                'logo_y_offset',
                'organisation',
                'themewizard_built_with_source_theme',
                'themewizard_built_with_algorithm',
                'themewizard_built_with_seed',
                'themewizard_built_with_dark',
                'setupwizard__install_profile',
                'setupwizard__lock_addons_on',
                'setupwizard__lock_show_content_tagging',
                'setupwizard__lock_show_content_tagging_inline',
                'setupwizard__lock_show_screen_actions',
                'setupwizard__lock_single_public_zone',
                'setupwizard__provide_cms_advert_choice',
                'site_name_font_size',
                'site_name_font_size_nonttf',
                'site_name_font_size_small',
                'site_name_font_size_small_non_ttf',
                'site_name_split',
                'site_name_split_gap',
                'site_name_x_offset',
                'site_name_y_offset',
                'site_name_y_offset_small',
                'supports_themewizard_equations',
                'themewizard_images',
                'themewizard_images_no_wild',
                'title',
                'version',
            ]))) {
                continue;
            }
            if ((in_safe_mode()) && (in_array($hook, [
                'facebook_uid',
                'facebook_appid',
            ]))) {
                continue;
            }

            $this->assertTrue(isset($hooks[$hook]), 'Missing referenced config option (.php): ' . $hook);
        }

        // Find unused
        foreach (array_keys($hooks) as $hook) {
            if (in_array($hook, [
                // Works via prefixing/suffixing/etc
                'error_handling_database_strict',
                'error_handling_deprecated',
                'error_handling_notices',
                'error_handling_warnings',
                'points_ADD_BANNER',
                'points_ADD_CALENDAR_EVENT',
                'points_ADD_DOWNLOAD',
                'points_ADD_IMAGE',
                'points_ADD_NEWS',
                'points_ADD_POLL',
                'points_ADD_QUIZ',
                'points_ADD_VIDEO',
                'points_COMCODE_PAGE_ADD',
                'sms_high_limit',
                'sms_high_trigger_limit',
                'sms_low_limit',
                'sms_low_trigger_limit',
                'is_on_template_cache',

                // Not used by default, but made for addons
                'points_per_currency_unit',
                'payment_gateway_vpn_password',
            ])) {
                continue;
            }

            $this->assertTrue(isset($found[$hook]), 'Config option unused: ' . $hook);
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
            'ecommerce' => 'boolean',
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
                    if (($setting == 'explanation') && ($details[$setting] === null)) {
                        continue;
                    }

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

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            if ((strpos($path, 'pages/modules/purchase.php') !== false)) { // Contains eCommerce config option upgrade code which must use get_option
                continue;
            }
            if ((strpos($path, 'hooks/systems/trusted_sites/ecommerce.php') !== false)) { // Not allowed to use get_ecommerce_option here; breaks the entire site
                continue;
            }

            $file_type = get_file_extension($path);

            if ($file_type == 'php' || $file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                if ($file_type == 'php') {
                    if ((strpos($c, 'get_option') === false) && (strpos($c, 'get_theme_option') === false) && (strpos($c, 'get_ecommerce_option') === false)) {
                        continue;
                    }
                } elseif ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                    if ((strpos($c, 'CONFIG_OPTION') === false) && (strpos($c, 'THEME_OPTION') === false) && (strpos($c, 'ECOMMERCE_OPTION') === false)) {
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
                            $this->assertTrue((strpos($c, 'get_option(\'' . $hook . '\'') === false) && (strpos($c, 'get_ecommerce_option(\'' . $hook . '\'') === false), $hook . ' must be accessed as a theme option (.php): ' . $path);
                        } else {
                            $this->assertTrue((strpos($c, 'get_theme_option(\'' . $hook . '\'') === false) || ($hook == 'description'), $hook . ' must not be accessed as a theme option (.php): ' . $path);
                        }

                        if (!empty($details['ecommerce'])) {
                            $this->assertTrue((strpos($c, 'get_option(\'' . $hook . '\'') === false) && (strpos($c, 'get_theme_option(\'' . $hook . '\'') === false), $hook . ' must be accessed as an eCommerce option (.php): ' . $path);
                        } else {
                            $this->assertTrue((strpos($c, 'get_ecommerce_option(\'' . $hook . '\'') === false), $hook . ' must not be accessed as an eCommerce option (.php): ' . $path);
                        }
                    } elseif ($file_type == 'tpl' || $file_type == 'txt' || $file_type == 'css' || $file_type == 'js') {
                        if (!empty($details['theme_override'])) {
                            $this->assertTrue((preg_match('#\{\$CONFIG_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0) && (preg_match('#\{\$ECOMMERCE_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0), $hook . ' must be accessed as a theme option: ' . $path);
                        } else {
                            $this->assertTrue((preg_match('#\{\$THEME_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0) || ($hook == 'description'), $hook . ' must not be accessed as a theme option: ' . $path);
                        }

                        if (!empty($details['ecommerce'])) {
                            $this->assertTrue((preg_match('#\{\$CONFIG_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0) && (preg_match('#\{\$THEME_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0), $hook . ' must be accessed as an eCommerce option: ' . $path);
                        } else {
                            $this->assertTrue((preg_match('#\{\$ECOMMERCE_OPTION[^\w,\{\}]*,' . $hook . '\}#', $c) == 0), $hook . ' must not be accessed as an eCommerce option: ' . $path);
                        }
                    }
                }
            }
        }
    }

    public function testSaneDefaults()
    {
        if (($this->only !== null) && ($this->only != 'testSaneDefaults')) {
            return;
        }

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
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

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();

            $default = $ob->get_default();

            switch ($details['type']) {
                case 'list':
                case 'theme_image':
                    $this->assertTrue(!cms_empty_safe($details['list_options']), 'List options expected, for ' . $hook);
                    break;

                default:
                    $this->assertTrue(cms_empty_safe($details['list_options']), 'No list options expected, for ' . $hook);
                    break;
            }
        }
    }

    public function testCorrectPublicSetting()
    {
        if (($this->only !== null) && ($this->only != 'testCorrectPublicSetting')) {
            return;
        }

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES, true, true, ['js']);
        $found = [];
        foreach ($files as $path) {
            if ((in_safe_mode()) && (should_ignore_file($path, IGNORE_NONBUNDLED))) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            $num_matches = preg_match_all('#\$cms\.configOption\(\'(\w+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $hook = $matches[1][$i];

                if (empty($found[$hook])) {
                    $found[$hook] = false;
                }
            }
        }

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $hook_ob) {
            $details = $hook_ob->get_details();
            $public = !empty($details['public']);
            if ($public) {
                $this->assertTrue(isset($found[$hook]), 'Config option is public but not used in JS: ' . $hook);
            } else {
                $this->assertTrue(!isset($found[$hook]), 'Config option is not public but is used in JS: ' . $hook);
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
            $this->assertTrue($count < 100, $category . ' has as much as ' . integer_format($count)); // max_input_vars would not like a high number
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
        $categories_found = [];
        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $details = $ob->get_details();
            $categories_found[cms_strtolower_ascii($details['category'])] = true;
        }
        ksort($categories_found);

        // Find all categories by hooks
        $categories = find_all_hooks('systems', 'config_categories');
        ksort($categories);

        $this->assertTrue(array_keys($categories_found) === array_keys($categories), 'Missing: ' . implode(', ', array_diff(array_keys($categories_found), array_keys($categories))));
    }

    // LEGACY: Should be removed in future versions
    public function testConfigHashClashes()
    {
        $hashes = [];

        $hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($hooks as $hook => $ob) {
            $hash = substr(md5($hook), 0, 8);
            $this->assertTrue(!array_key_exists($hash, $hashes), 'Config option ' . $hook . ' has the same hashed name as ' . (array_key_exists($hash, $hashes) ? $hashes[$hash] : 'Unknown') . '; one of these should be renamed to avoid conflicts on configuration edit screens.');
            $hashes[$hash] = $hook;
        }
    }
}
