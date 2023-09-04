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

// "aaa" as we want it to run first, else files not correctly modularised won't be tested

/**
 * Composr test case class (unit testing).
 */
class aaa_modularisation_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        cms_ini_set('memory_limit', '500M');
    }

    public function testDefaultIconsExist()
    {
        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            $icon_file = $ob->get_default_icon();
            $this->assertTrue(is_file(get_file_base() . '/' . $icon_file), $icon_file . ' is missing');
        }
    }

    public function testModularisation()
    {
        // Read in all addons, while checking for any double referencing within a single hook...

        $addon_data = [];
        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            $files = $ob->get_file_list();

            $counts = array_count_values($files);
            foreach ($counts as $file => $count) {
                $this->assertTrue($count == 1, 'Double referenced within ' . $hook . ': ' . $file);
            }

            $addon_data[$hook] = $files;
        }

        // Check for double referencing across addons, and that double referencing IS correctly done for icons...

        $seen = [];
        foreach ($addon_data as $addon_name => $d) {
            if ($addon_name == 'core_all_icons') {
                continue;
            }

            foreach ($d as $path) {
                $this->assertTrue(((!array_key_exists($path, $seen)) || (strpos($path, '_custom/') !== false)), 'Double referenced: ' . $path);
                $seen[$path] = true;

                if (preg_match('#^themes/default/images/(icons|icons_monochrome)/#', $path) != 0) {
                    $this->assertTrue(in_array($path, $addon_data['core_all_icons']), 'All icons must be in core_all_icons addon: ' . $path);

                    $matches = [];
                    if (preg_match('#^themes/default/images/icons/(.*)$#', $path, $matches) != 0) {
                        $this->assertTrue(in_array('themes/default/images/icons_monochrome/' . $matches[1], $d), 'Missing icons_monochrome equivalent to: ' . $path);
                    } else {
                        preg_match('#^themes/default/images/icons_monochrome/(.*)$#', $path, $matches);
                        $this->assertTrue(in_array('themes/default/images/icons/' . $matches[1], $d), 'Missing icons equivalent to: ' . $path);
                    }
                }
            }
        }

        // Check core_all_icons files also in other addons

        foreach ($addon_data['core_all_icons'] as $path) {
            if ($path == 'sources/hooks/systems/addon_registry/core_all_icons.php') {
                continue;
            }
            if (preg_match('#^themes/default/images/(icons|icons_monochrome)/spare/#', $path) != 0) {
                continue;
            }

            $ok = false;

            foreach ($addon_data as $addon_name => $d) {
                if ($addon_name == 'core_all_icons') {
                    continue;
                }

                if (in_array($path, $d)) {
                    $ok = true;
                    break;
                }
            }

            $this->assertTrue($ok, 'Files in core_all_icons generally must also be distributed in exactly one other addon [the owner addon of that icon]]: ' . $path);
        }

        // Check no symlinks (breaks archive extraction on Windows)...

        foreach ($addon_data as $addon_files) {
            foreach ($addon_files as $_path) {
                $this->assertTrue(!is_link(get_file_base() . '/' . $_path), 'We do not want symlinks in the repository: ' . $_path);
            }
        }

        // Check declared packages in files against the addon they're supposed to be within, and for files not including in any addon...

        require_code('files2');
        require_code('third_party_code');
        $unput_files = []; // A map of non-existent packages to a list in them
        $ignore = IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_UPLOADS | IGNORE_FLOATING | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_REVISION_FILES;
        //$ignore = IGNORE_FLOATING | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_UNSHIPPED_VOLATILE; Uncomment for more careful testing
        $files = get_directory_contents(get_file_base(), '', $ignore);
        $forum_drivers = get_directory_contents(get_file_base() . '/sources/forum', '', 0, false, true, ['php']);
        foreach ($forum_drivers as &$forum_driver) {
            $forum_driver = basename($forum_driver, '.php');
        }

        $exceptions = array_merge(list_untouchable_third_party_directories(), [
            'themes/admin/images_custom', // If admin sprites are generated
        ]);

        foreach ($files as $path) {
            // Exceptions
            if (preg_match('#^(' . implode('|', $forum_drivers) . ')/#i', $path) != 0) {
                continue;
            }
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }

            $found = false;
            foreach ($addon_data as $addon_name => $addon_files) {
                foreach ($addon_files as $fileindex => $_path) {
                    if ($_path == $path) {
                        if (substr($_path, -4) == '.php') {
                            $data = cms_file_get_contents_safe(get_file_base() . '/' . $_path);
                            $check_package = $this->should_check_package($data, $path);

                            if ($check_package) {
                                $matches = [];
                                $m_count = preg_match_all('#@package\s+(\w+)#', $data, $matches);
                                $problem = ($m_count != 0) && ($matches[1][0] != $addon_name) && (@$matches[1][1] != $addon_name/*FUDGE: should ideally do a loop, but we'll assume max of 2 packages for now*/);
                                $this->assertTrue(!$problem, '@package wrong for <a href="txmt://open?url=file://' . htmlentities(get_file_base() . '/' . $_path) . '">' . htmlentities($path) . '</a> (should be ' . $addon_name . ')');
                                if (!$problem) {
                                    $this->assertTrue($m_count > 0, 'No @package for <a href="txmt://open?url=file://' . htmlentities(get_file_base() . '/' . $_path) . '">' . htmlentities($path) . '</a> (should be ' . $addon_name . ')');
                                }
                            }
                        }

                        $found = true;

                        unset($addon_files[$fileindex]); // Marks it found for the "List any missing files" check
                        $addon_data[$addon_name] = $addon_files;
                        break 2;
                    }
                }
            }
            if (!$found) {
                $data = cms_file_get_contents_safe(get_file_base() . '/' . $path);
                $check_package = $this->should_check_package($data, $path);

                if ($check_package) {
                    $matches = [];
                    $m_count = preg_match('#@package\s+(\w+)#', $data, $matches);
                    if ($m_count != 0) {
                        $unput_files[$matches[1]][] = $path;
                    }
                }

                $this->assertTrue(false, 'Could not find the addon for... \'' . $path . '\',');
            }
        }

        // List any missing files...

        foreach ($addon_data as $addon_name => $addon_files) {
            $ok = addon_installed($addon_name, false, false, false);
            $this->assertTrue($ok, 'Addon registry files missing / referenced twice... \'sources/hooks/systems/addon_registry/' . $addon_name . '.php\',');
            foreach ($addon_files as $path) {
                if ($path == 'data_custom/execute_temp.php') {
                    continue;
                }

                $this->assertTrue(file_exists($path), 'Addon files missing... \'' . htmlentities($path) . '\',');
            }
        }

        // List (by addon) any alien files that did actually have a @package...

        ksort($unput_files);
        foreach ($unput_files as $addon_name => $paths) {
            echo '<br /><strong>' . htmlentities($addon_name) . '</strong>';
            foreach ($paths as $path) {
                $this->assertTrue(false, 'Could not find the addon for... \'' . $path . '\',');
            }
        }
    }

    public function testCustomNonCustomFiles()
    {
        // List any _custom files in bundled addons, or non-custom files in non-bundled addons...
        require_code('addons');
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            $hook_path = get_file_base() . '/' . $place . '/hooks/systems/addon_registry/' . filter_naughty_harsh($hook) . '.php';
            $addon_info = read_addon_info($hook, false, null, null, $hook_path);

            foreach (array_map('cms_strtolower_ascii', $addon_info['files']) as $file) {
                // Normalise
                if (strpos($file, '/') !== false) {
                    $dir = dirname($file);
                    $filename = basename($file);
                } else {
                    $dir = '';
                    $filename = $file;
                }

                // FUDGE: Exceptions
                $exceptions = [
                    '_tests/',
                    'uploads/',
                    'docs/',
                    'buildr/',
                    'aps/',
                    'mobiquo/',
                    'tracker/',
                    'exports/',
                    'data_custom/firewall_rules.txt', // bundled as-is
                    'data_custom/errorlog.php', // bundled as blank
                    'data_custom/execute_temp.php', // bundled but actually taking contents of execute_temp.php.bundle
                    'themes/default/images/icons/',
                    'themes/default/images/icons_monochrome/',
                ];
                foreach ($exceptions as $untouchable) {
                    if (strpos($file, $untouchable) !== false) {
                        continue 2;
                    }
                }

                if (($dir != '') && preg_match('#^(?!index\.html$)(?!\.htaccess$).*$#i', $filename) != 0) {
                    if ($place == 'sources') {
                        $this->assertTrue((preg_match('#^.*_custom(/.*)?$#i', $dir) == 0), 'The bundled addon ' . $hook . ' has a file that appears to be non-bundled: ' . $file);
                    } elseif ($place == 'sources_custom') {
                        $this->assertTrue((preg_match('#^.*_custom(/.*)?$#i', $dir) != 0), 'The non-bundled addon ' . $hook . ' has a file that appears to be bundled: ' . $file);
                    }
                }
            }
        }
    }

    public function should_check_package($data, $path)
    {
        if (strpos($data, 'ocProducts') === false) {
            return false;
        }

        return true;
    }
}
