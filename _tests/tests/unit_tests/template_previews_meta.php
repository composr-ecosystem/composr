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
class template_previews_meta_test_set extends cms_test_case
{
    protected $template_id;

    public function setUp()
    {
        parent::setUp();

        require_code('files2');
        require_code('lorem');
    }

    public function testNoIncorrectTitles()
    {
        $templates = [];

        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            $paths = [];
            $paths[] = get_file_base() . '/sources/hooks/systems/addon_registry/' . $hook . '.php';
            $paths[] = get_file_base() . '/sources_custom/hooks/systems/addon_registry/' . $hook . '.php';
            foreach ($paths as $path) {
                if (!is_file($path)) {
                    continue;
                }

                $c = file_get_contents($path);

                $matches = [];
                $num_matches = preg_match_all('#do_lorem_template\(\'(\w+)\', \[(.*)\](\)|, null, false, null)#Us', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $template = $matches[1][$i];
                    $code = $matches[2][$i];

                    // Exceptions
                    if (substr($template, 0, 19) == 'ECOM_PURCHASE_STAGE') {
                        continue;
                    }

                    $is_screen_template = (substr($template, -7) == '_SCREEN');

                    $matches2 = [];
                    $num_matches2 = preg_match_all('#\'(\w+)\' => lorem_screen_title\(\)#', $code, $matches2);
                    for ($j = 0; $j < $num_matches2; $j++) {
                        $param = $matches2[1][$j];
                        $is_param_named_title = ($param == 'TITLE');

                        $this->assertTrue($is_screen_template && $is_param_named_title, 'Suspicious case of lorem_screen_title for ' . $template . '/' . $param);
                    }
                }
            }
        }
    }

    public function testNoMissingPreviews()
    {
        $templates = [];

        $files = get_directory_contents(get_file_base() . '/themes/default/templates', get_file_base() . '/themes/default/templates', null, false, true, ['tpl']);
        foreach ($files as $path) {
            $templates[] = 'templates/' . basename($path);
        }

        $all_previews = find_all_previews__by_template();

        foreach ($templates as $t) {
            $this->assertFalse((!array_key_exists($t, $all_previews)), 'Missing preview for: ' . $t);
        }
    }

    public function testNoRedundantFunctions()
    {
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($hook));

            $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($hook));
            if (!method_exists($ob, 'tpl_previews')) {
                continue;
            }
            $used = array_unique($ob->tpl_previews());

            $code = cms_file_get_contents_safe(get_file_base() . '/' . $place . '/hooks/systems/addon_registry/' . $hook . '.php');

            $matches = [];
            $num_matches = preg_match_all('#function tpl_preview__(.*)\(#U', $code, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                // Exceptions
                if (in_array($matches[1][$i], [
                    'iframe',
                    'overlay',
                ])) {
                    continue;
                }

                $this->assertTrue(in_array($matches[1][$i], $used), 'Non-used screen function ' . $matches[1][$i]);
            }
        }
    }

    public function testNoDoublePreviews()
    {
        $all_used = [];

        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $ob) {
            if (!method_exists($ob, 'tpl_previews')) {
                continue;
            }
            $used = array_unique($ob->tpl_previews());
            foreach (array_keys($used) as $u) {
                $this->assertFalse(array_key_exists($u, $all_used), 'Double defined ' . $u);
            }
            $all_used += $used;
        }
    }
}
