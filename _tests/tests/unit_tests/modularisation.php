<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class modularisation_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        cms_ini_set('memory_limit', '500M');
    }

    public function testModularisation()
    {
        // Read in all addons, while checking for any double referencing within a single hook...

        $addon_data = array();
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

        $seen = array();
        foreach ($addon_data as $addon_name => $d) {
            if ($addon_name == 'all_icons') {
                continue;
            }

            foreach ($d as $path) {
                $this->assertTrue(((!array_key_exists($path, $seen)) || (strpos($path, '_custom/') !== false)), 'Double referenced: ' . $path);
                $seen[$path] = true;

                if (preg_match('#^themes/default/images/(icons|icons_monochrome)/#', $path) != 0) {
                    $this->assertTrue(in_array($path, $addon_data['all_icons']), 'All icons must be in all_icons addon: ' . $path);

                    $matches = array();
                    if (preg_match('#^themes/default/images/icons/(.*)$#', $path, $matches) != 0) {
                        $this->assertTrue(in_array('themes/default/images/icons_monochrome/' . $matches[1], $d), 'Missing icons_monochrome equivalent to: ' . $path);
                    } else {
                        preg_match('#^themes/default/images/icons_monochrome/(.*)$#', $path, $matches);
                        $this->assertTrue(in_array('themes/default/images/icons/' . $matches[1], $d), 'Missing icons equivalent to: ' . $path);
                    }
                }
            }
        }

        // Check all_icons files also in other addons
        foreach ($addon_data['all_icons'] as $path) {
            if ($path == 'sources/hooks/systems/addon_registry/all_icons.php') {
                continue;
            }
            if (preg_match('#^themes/default/images/(icons|icons_monochrome)/spare/#', $path) != 0) {
                continue;
            }

            $ok = false;

            foreach ($addon_data as $addon_name => $d) {
                if ($addon_name == 'all_icons') {
                    continue;
                }

                if (in_array($path, $d)) {
                    $ok = true;
                    break;
                }
            }

            $this->assertTrue($ok, 'Files in all_icons generally must also be distributed in exactly one other addon [the owner addon of that icon]]: ' . $path);
        }

        // Check declared packages in files against the addon they're supposed to be within, and for files not including in any addon...

        require_code('files2');
        $unput_files = array(); // A map of non-existent packages to a list in them
        $ignore = IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_UPLOADS | IGNORE_FLOATING | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_REVISION_FILES;
        //$ignore = IGNORE_FLOATING | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_UNSHIPPED_VOLATILE; Uncomment for more careful testing
        $files = get_directory_contents(get_file_base(), '', $ignore);
        foreach ($files as $path) {
            if ($path == 'sources_custom/hooks/systems/content_meta_aware/temp_test.php') {
                continue;
            }

            $found = false;
            foreach ($addon_data as $addon_name => $addon_files) {
                foreach ($addon_files as $fileindex => $_path) {
                    if ($_path == $path) {
                        if (substr($_path, -4) == '.php') {
                            $data = cms_file_get_contents_safe(get_file_base() . '/' . $_path, false);
                            $check_package = $this->should_check_package($data, $path);

                            if ($check_package) {
                                $matches = array();
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
                $data = cms_file_get_contents_safe(get_file_base() . '/' . $_path, false);
                $check_package = $this->should_check_package($data, $path);

                if ($check_package) {
                    $matches = array();
                    $m_count = preg_match('#@package\s+(\w+)#', $data, $matches);
                    if ($m_count != 0) {
                        $unput_files[$matches[1]][] = $path;
                    }
                }

                $this->assertTrue($m_count == 0, 'Could not find the addon for... \'' . htmlentities($path) . '\',');
            }
        }

        // List any missing files...

        foreach ($addon_data as $addon_name => $addon_files) {
            $ok = (file_exists(get_file_base() . '/sources/hooks/systems/addon_registry/' . $addon_name . '.php') || file_exists(get_file_base() . '/sources_custom/hooks/systems/addon_registry/' . $addon_name . '.php'));
            $this->assertTrue($ok, 'Addon registry files missing / referenced twice... \'sources/hooks/systems/addon_registry/' . $addon_name . '.php\',');
            foreach ($addon_files as $path) {
                if ($path == 'data_custom/execute_temp.php') {
                    continue;
                }

                $this->assertTrue(file_exists($path), 'Addon files missing... \'' . htmlentities($path) . '\',');
            }
        }

        // List any alien files...

        ksort($unput_files);
        foreach ($unput_files as $addon => $paths) {
            echo '<br /><strong>' . htmlentities($addon) . '</strong>';
            foreach ($paths as $path) {
                $this->assertTrue(false, 'Could not find the addon for... \'' . $path . '\',');
            }
        }
    }

    public function should_check_package($data, $path)
    {
        if (strpos($data, 'ocProducts') === false) {
            return false;
        }

        return !should_ignore_file($path, IGNORE_SHIPPED_VOLATILE);
    }
}
