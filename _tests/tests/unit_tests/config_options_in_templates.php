<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class config_options_in_templates_test_set extends cms_test_case
{
    public function testOptionsInTemplates()
    {
        global $GFILE_ARRAY;

        $addon_data = [];
        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            $files = $ob->get_file_list();

            foreach ($files as $path) {
                if (preg_match('#^themes/default/.*/.*\.(tpl|txt|css|xml|js)$#', $path) != 0) {
                    if (in_array($path, [
                        'themes/default/templates_custom/BOOKING_START_SCREEN.tpl',
                        'themes/default/templates_custom/LOGIN_SCREEN.tpl',
                        'themes/default/templates_custom/BLOCK_MAIN_GOOGLE_MAP_USERS.tpl',
                        'themes/default/templates_custom/FORM_SCREEN_INPUT_MAP_POSITION.tpl',
                        'themes/default/templates_custom/BLOCK_CREDIT_EXPS_INNER.tpl',
                        'themes/default/templates/COMMENTS_POSTING_FORM_CAPTCHA.tpl',
                        'themes/default/templates/CATALOGUE_products_ENTRY_SCREEN.tpl',
                        'themes/default/templates/CATALOGUE_products_GRID_ENTRY_WRAP.tpl',
                        'themes/default/templates/CATALOGUE_products_CATEGORY_SCREEN.tpl',
                        'themes/default/templates/ECOM_SHOPPING_CART_SCREEN.tpl',
                        'themes/default/javascript_custom/shoutr.js',
                    ])) {
                        continue;
                    }

                    $path = get_file_base() . '/' . $path;
                    $c = cms_file_get_contents_safe($path);

                    $matches = [];
                    $num_matches = preg_match_all('#\{\$CONFIG_OPTION,([^\{\},]*)\}#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $option = $matches[1][$i];
                        require_code('hooks/systems/config/' . filter_naughty_harsh($option, true));
                        $ob = object_factory('Hook_config_' . filter_naughty_harsh($option, true));
                        $details = $ob->get_details();
                        $ok = (($details['addon'] == $hook) || ($details['addon'] == 'core') || (substr($details['addon'], 0, 5) == 'core_'));
                        $this->assertTrue($ok, 'Template ' . $path . ' is using a config option ' . $option . ' from ' . $details['addon'] . ' without a guard');
                    }
                }
            }
        }
    }
}
