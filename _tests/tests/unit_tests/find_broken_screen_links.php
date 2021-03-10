<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php _find_broken_screen_links

// This covers internal links. Also see _broken_links, which does scanning for external links.

/**
 * Composr test case class (unit testing).
 */
class find_broken_screen_links_test_set extends cms_test_case
{
    // This test is not necessarily required to pass, but it may hint at issues; best just to make it pass anyway (it does at the time at writing)
    public function testScreenLinks()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $found = [];
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all("#build_url\(\['page'\s*=>\s*'(\w+)',\s*'type'\s*=>\s*'(\w+)'#", $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $page = $matches[1][$i];
                $type = $matches[2][$i];
                $all = $matches[0][$i];
                $found[$all] = [$page, $type]; // To de-duplicate
            }
        }

        foreach ($found as $all => $d) {
            list($page, $type) = $d;

            if ($page != '_SELF') {
                $zone = get_module_zone($page);
                if ($zone === null) {
                    continue;
                }

                $path = _get_module_path($zone, $page);
                $module_path = zone_black_magic_filterer((($zone == '') ? '' : (filter_naughty($zone) . '/')) . 'pages/modules/' . filter_naughty_harsh($page) . '.php', true);
                if (!is_file($module_path)) {
                    $module_path = zone_black_magic_filterer((($zone == '') ? '' : (filter_naughty($zone) . '/')) . 'pages/modules_custom/' . filter_naughty_harsh($page) . '.php', true);
                }
                if (!is_file($module_path)) {
                    //$this->assertTrue(false, 'Missing module ' . $zone . ':' . $page);    Maybe a forum module but CNS is not running, or a module in a non-installed zone
                    continue;
                }
                $c2 = cms_file_get_contents_safe(get_file_base() . '/' . $module_path);
                if (strpos($c2, "'{$type}'") === false) {
                    if ((strpos($c2, 'extends Standard_crud_module') !== false) && (in_array($type, ['add', '_add', 'edit', '_edit', '__edit', 'add_category', '_add_category', 'edit_category', '_edit_category', '__edit_category', 'add_other', '_add_other', 'edit_other', '_edit_other', '__edit_other']))) {
                        continue;
                    }

                    $this->assertTrue(false, 'Linking error with ' . $all);
                }
            }
        }
    }
}
