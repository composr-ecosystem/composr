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
class config_options_in_templates_test_set extends cms_test_case
{
    public function testOptionsInTemplates()
    {
        global $GFILE_ARRAY;

        $addon_data = array();
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/addon_registry/' . $hook);
            $ob = object_factory('Hook_addon_registry_' . $hook);
            $files = $ob->get_file_list();

            foreach ($files as $file) {
                if (preg_match('#^themes/default/.*/.*\.(tpl|txt|css|xml|js)$#', $file) != 0) {
                    if (in_array($file, array(
                        'themes/default/templates_custom/BOOKING_START_SCREEN.tpl',
                        'themes/default/templates_custom/LOGIN_SCREEN.tpl',
                        'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_MAP_USERS.tpl',
                        'themes/default/templates_custom/FORM_SCREEN_INPUT_MAP_POSITION.tpl',
                    ))) {
                        continue;
                    }

                    $path = get_file_base() . '/' . $file;
                    $c = file_get_contents($path);

                    $matches = array();
                    $num_matches = preg_match_all('#\{\$CONFIG_OPTION,([^\{\},]*)\}#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $option = $matches[1][$i];
                        require_code('hooks/systems/config/' . $option);
                        $ob = object_factory('Hook_config_' . $option);
                        $details = $ob->get_details();
                        $ok = (($details['addon'] == $hook) || ($details['addon'] == 'core') || (substr($details['addon'], 0, 5) == 'core_'));
                        $this->assertTrue($ok, 'Template ' . $file . ' is using a config option ' . $option . ' from ' . $details['addon'] . ' without a guard');
                    }
                }
            }
        }
    }
}
