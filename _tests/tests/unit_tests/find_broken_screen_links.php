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

// This covers internal links. Also see _broken_links, which does scanning for external links.

/**
 * Composr test case class (unit testing).
 */
class find_broken_screen_links_test_set extends cms_test_case
{
    public function testBadlySpecified()
    {
        if (($this->only !== null) && ($this->only != 'testBadlySpecified')) {
            return;
        }

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            if (in_array($path, [
                'adminzone/pages/modules/admin.php',
                'sources/users_active_actions.php',
                'sources/notifications.php',
                'sources_custom/confluence.php',
            ])) {
                continue;
            }

            $c = file_get_contents(get_file_base() . '/' . $path);

            $matches = [];
            $num_matches = preg_match_all('#build_url\(\[\'page\' => \'([^\']*)\'.*\], (\'([^\']*)\'|get_module_zone\(\'([^\']*)\'\)|get_page_zone\(\'([^\']*)\'\))\)#', $c, $matches);

            for ($i = 0; $i < $num_matches; $i++) {
                $page = $matches[1][$i];
                $zone = $matches[3][$i];

                $is_module = (preg_match('#(\w+/)?pages/(mini)?modules(_custom)?/#', $path) != 0) || ($path == 'sources/crud_module.php');

                if ($is_module) {
                    $module_name = basename($path, '.php');

                    $this->assertTrue($page != $module_name, 'Self-reference to a module can be _SELF in ' . $path);
                    $this->assertTrue($page != $module_name || $zone == '_SELF', 'Self-zone-reference to a module can be _SELF in ' . $path);
                } else {
                    if ($path != 'sources_custom/workflows.php') { // Exception
                        $this->assertTrue($page != '_SELF', 'Should not use page self-references outside of a module in ' . $path);
                        $this->assertTrue($zone != '_SELF', 'Should not use zone self-references outside of a module in ' . $path);
                    }
                }

                if ($matches[4][$i] != '') {
                    $this->assertTrue($matches[4][$i] == $page, 'Mismatch between searched zone and page in ' . $path . ' (' . $matches[4][$i] . ' vs ' . $page . ')');

                    $this->assertTrue(strpos($matches[4][$i], '-') === false, 'Module names should have underscores not hyphens, regardless of what the URL says in ' . $path . ' (' . $matches[4][$i] . ')');
                }
                if ($matches[5][$i] != '') {
                    $this->assertTrue($matches[5][$i] == $page, 'Mismatch between searched zone and page in ' . $path . ' (' . $matches[4][$i] . ' vs ' . $page . ')');
                    $this->assertTrue(get_module_zone($matches[5][$i], 'modules', null, 'php', true, false) === null, 'Could have used get_module_zone instead of get_page_zone in ' . $path . ' (' . $matches[5][$i] . ')');
                }

                // Exceptions
                if (in_array($path, [
                    'site/pages/modules/search.php',
                    'sources/blocks/main_quotes.php',
                    'sources_custom/miniblocks/composr_homesite_featuretray.php',
                ])) {
                    continue;
                }
                if (preg_match('#^_tests/#', $path) != 0) {
                    continue;
                }

                $this->assertTrue(($zone == '' || $zone === '_SELF' || $page == '' || $page == 'login'), 'Should not hard-code zone names in ' . $path . ' (' . $zone . ':' . $page . ')');
            }
        }
    }

    // This test is not necessarily required to pass, but it may hint at issues; best just to make it pass anyway (it does at the time at writing) - and add exceptions as needed
    public function testScreenLinks()
    {
        if (($this->only !== null) && ($this->only != 'testScreenLinks')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $found = [];
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
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

                    $this->assertTrue(false, 'Linking error with ' . $all . ' in ' . $path);
                }
            }
        }
    }
}
