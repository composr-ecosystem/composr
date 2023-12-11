<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class addon_setupwizard_test_set extends cms_test_case
{
    public function testPresenceDefinedForAllAddons()
    {
        $admin_setupwizard = file_get_contents(get_file_base() . '/adminzone/pages/modules/admin_setupwizard.php');

        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $type) {
            if ($type == 'sources_custom') {
                continue;
            }
            if (substr($hook, 0, 5) == 'core_') {
                continue;
            }

            $this->assertTrue(strpos($admin_setupwizard, '\'' . $hook . '\'') !== false, 'Addon presence not defined in Setup Wizard: ' . $hook);
        }
    }
}
