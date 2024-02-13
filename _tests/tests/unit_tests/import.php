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
class import_test_set extends cms_test_case
{
    public function testImportCyclicDependencies()
    {
        require_code('import');
        require_code('zones');

        require_lang('import');

        // Test to see if any imports have a cyclic dependency
        $hooks = find_all_hook_obs('modules', 'admin_import', 'Hook_import_');
        foreach ($hooks as $hook => $obj) {
            $info = $obj->info();
            if (isset($info['import'])) {
                $imports = $info['import'];
                $dependencies = isset($info['dependencies']) ? $info['dependencies'] : null;
                $sort = sort_imports_by_dependencies($imports, $dependencies, true);
                $this->assertTrue(($sort !== null), 'Import hook ' . $hook . ' probably has a cyclic dependency.');
            }
        }
    }
}
