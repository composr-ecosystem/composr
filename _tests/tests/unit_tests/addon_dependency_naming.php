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
class addon_dependency_naming_test_set extends cms_test_case
{
    public function testAddonDependencyNaming()
    {
        $special_dep_codes = [
            'MySQL',
            'System scheduler',
            //'CNS', We want people to write conversr
            'Conversr',
            'SSL',
            'PHP curl extension',
            'PHP simplexml extension',
            'PHP openssl extension',
            'PHP sessions extension',
            'PHP xml extension',
            'PHP zip extension',
            'PHP mbstring extension',
            'PHP pdo_mysql extension',
        ];

        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($addons as $addon_name => $ob) {
            $dependencies = $ob->get_dependencies();
            $deps = array_merge($dependencies['requires'], $dependencies['recommends'], $dependencies['conflicts_with']);
            foreach ($deps as $dep) {
                $ok = (in_array($dep, $special_dep_codes)) || (addon_installed($dep)) || (preg_match('#^(php) [\d\.]+$#i', $dep) != 0) || (preg_match('#^(ie|safari) [\d\.]+\+$#i', $dep) != 0);
                $this->assertTrue($ok, 'Unknown addon dependency, ' . $dep . ', in ' . $addon_name);
            }
        }
    }
}
