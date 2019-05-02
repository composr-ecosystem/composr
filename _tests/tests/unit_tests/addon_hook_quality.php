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
class addon_hook_quality_test_set extends cms_test_case
{
    public function testAddonTextParsing()
    {
        require_code('comcode_check');
        require_code('failure');

        set_throw_errors(true);

        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($addons as $addon => $ob) {
            $this->assertTrue(method_exists($ob, 'get_chmod_array'), 'Missing get_chmod_array for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_version'), 'Missing get_version for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_description'), 'Missing get_description for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_applicable_tutorials'), 'Missing get_applicable_tutorials for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_dependencies'), 'Missing get_dependencies for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_default_icon'), 'Missing get_default_icon for ' . $addon);
            $this->assertTrue(method_exists($ob, 'get_file_list'), 'Missing get_file_list for ' . $addon);

            $description = $ob->get_description();

            try {
                check_comcode($description);
            }
            catch (Exception $e) {
                $this->assertTrue(false, 'Failed to parse addon description for ' . $addon . ', ' . $e->getMessage());
            }
        }

        set_throw_errors(false);
    }
}
