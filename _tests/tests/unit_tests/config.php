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
class config_test_set extends cms_test_case
{
    public function testSaneDefaults()
    {
        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . $hook);
            $ob = object_factory('Hook_config_' . $hook);
            $details = $ob->get_details();

            $default = $ob->get_default();

            switch ($details['type']) {
                case 'integer':
                    $this->assertTrue((empty($default)) || (strval(intval($default)) == $default), 'Integer fields expect integer values');
                    break;

                case 'float':
                    $this->assertTrue((empty($default)) || (is_numeric($default)), 'Float fields expect numeric values');
                    break;

                case 'tick':
                    $this->assertTrue((empty($default)) || (in_array($default, array('0', '1'))), 'Tick fields expect boolean values');
                    break;
            }
        }
    }

    public function testAddonCategorisationConsistency()
    {
        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . $hook);
            $ob = object_factory('Hook_config_' . $hook);
            $details = $ob->get_details();

            $path = get_file_base() . '/sources/hooks/systems/config/' . $hook . '.php';
            if (!is_file($path)) {
                $path = get_file_base() . '/sources_custom/hooks/systems/config/' . $hook . '.php';
            }
            $file_contents = file_get_contents($path);

            $expected_addon = preg_replace('#^.*@package\s+(\w+).*$#s', '$1', $file_contents);
            $this->assertTrue($details['addon'] == $expected_addon, 'Addon mismatch for ' . $hook);

            $this->assertTrue($details['addon'] != 'core', 'Don\'t put config options in core, put them in core_configuration - ' . $hook);
        }
    }

    public function testListConfigConsistency()
    {
        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . $hook);
            $ob = object_factory('Hook_config_' . $hook);
            $details = $ob->get_details();
            if ($details['type'] == 'list') {
                $list = explode('|', $details['list_options']);
                $default = $ob->get_default();

                if ($default === null) {
                    continue;
                }

                $this->assertTrue(in_array($default, $list));
            }
        }
    }

    public function testReasonablePerCategory()
    {
        $categories = array();

        $hooks = find_all_hooks('systems', 'config');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();
            if (!isset($categories[$details['category']])) {
                $categories[$details['category']] = 0;
            }
            $categories[$details['category']]++;
        }

        foreach ($categories as $category => $count) {
            $this->assertTrue($count > 3, $category . ' only has ' . integer_format($count));
            $this->assertTrue($count < 160, $category . ' has as much as ' . integer_format($count)); // max_input_vars would not like a high number
        }
    }

    public function testConsistentGroupOrdering()
    {
        $categories = array();

        $hooks = find_all_hooks('systems', 'config');
        foreach ($hooks as $hook => $hook_type) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();
            if (!isset($categories[$details['category']])) {
                $categories[$details['category']] = array();
            }
            if (!isset($categories[$details['category']][$details['group']])) {
                $categories[$details['category']][$details['group']] = array();
            }
            $categories[$details['category']][$details['group']][] = $details;
        }

        foreach ($categories as $category => $group) {
            foreach ($group as $group_name => $options) {
                $has_orders = null;
                $orders = array();

                foreach ($options as $option) {
                    $_has_orders = isset($option['order_in_category_group']);
                    if ($has_orders !== null) {
                        if ($has_orders != $_has_orders) {
                            $this->assertTrue(false, $category . '/' . $group_name . ' has inconsistent ordering settings (some set, some not)');
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
        // Find all categories by searching
        $hooks = find_all_hooks('systems', 'config');
        $categories_found = array();
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $option = $ob->get_details();
            $categories_found[strtolower($option['category'])] = true;
        }
        ksort($categories_found);

        // Find all categories by hooks
        $categories = find_all_hooks('systems', 'config_categories');
        ksort($categories);

        $this->assertTrue(array_keys($categories_found) === array_keys($categories));
    }
}
