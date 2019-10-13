<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class config_lang_strings_test_set extends cms_test_case
{
    protected $id;

    public function testStrings()
    {
        $hooks = find_all_hooks('systems', 'config');
        $options = array();
        foreach (array_keys($hooks) as $hook) {
            $path = get_file_base() . '/sources/hooks/systems/config/' . filter_naughty_harsh($hook) . '.php';
            if (!file_exists($path)) {
                $path = get_file_base() . '/sources_custom/hooks/systems/config/' . filter_naughty_harsh($hook) . '.php';
            }
            $code = cms_file_get_contents_safe($path, false);

            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();
            $options[] = $details;

            $this->assertTrue(strpos($code, "@package    " . $details['addon']) !== false, 'Addon definition mismatch in ' . $hook);
        }
        require_all_lang();
        foreach ($options as $option) {
            $test = do_lang($option['human_name'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Could not load string: ' . $option['human_name']);

            $test = do_lang($option['explanation'], null, null, null, null, false);
            $test2 = do_lang('CONFIG_GROUP_DEFAULT_DESCRIP_' . $option['group'], null, null, null, null, false);
            $this->assertTrue($test !== null || $test2 !== null, 'Error on: ' . $option['explanation']);

            /* Actually this is allowed to be missing
            $test = do_lang($option['explanation'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Error on: ' . $option['explanation']);
            */

            $test = do_lang('CONFIG_CATEGORY_' . $option['category'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Error on: CONFIG_CATEGORY_' . $option['category']);

            $test = do_lang($option['group'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Error on: ' . $option['group']);
        }
    }
}
