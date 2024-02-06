<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class module_names_defined_test_set extends cms_test_case
{
    public function testModuleNamesDefined()
    {
        require_all_lang();

        $zones = find_all_zones(true);
        foreach ($zones as $zone) {
            $pages = find_all_pages($zone, 'modules') + find_all_pages($zone, 'modules_custom');
            foreach (array_keys($pages) as $page) {
                $str = 'MODULE_TRANS_NAME_' . $page;
                $this->assertTrue(do_lang($str, null, null, null, null, false) !== null, 'Not defined... ' . $str);
            }
        }
    }
}
