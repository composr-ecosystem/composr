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
class modularisation_test_set extends cms_test_case
{
    public function setUp()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        parent::setUp();
    }

    public function testModularisation()
    {
        global $GFILE_ARRAY;

        $addon_data = array();
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/addon_registry/' . $hook);
            $ob = object_factory('Hook_addon_registry_' . $hook);
            $files = $ob->get_file_list();
            $counts = array_count_values($files);
            foreach ($counts as $file => $count) {
                $this->assertTrue($count == 1, 'Double referenced within ' . $hook . ': ' . $file);
            }
            $addon_data[$hook] = $files;
        }

        $seen = array();
        foreach ($addon_data as $d) {
            foreach ($d as $file) {
                if ((array_key_exists($file, $seen)) && (strpos($file, '_custom/') === false)) {
                    $this->assertTrue(false, 'Double referenced: ' . $file);
                }
                $seen[$file] = 1;
            }
        }

        safe_ini_set('memory_limit', '500M');

        $GFILE_ARRAY = array();
        $this->do_dir();
        $unput_files = array();
        foreach ($GFILE_ARRAY as $path) {
            if ($path == 'sources_custom/hooks/systems/content_meta_aware/temp_test.php') {
                continue;
            }

            if (strpos($path, '/diff/') !== false) {
                continue;
            }

            $found = false;
            foreach ($addon_data as $addon_name => $addon_files) {
                foreach ($addon_files as $fileindex => $file) {
                    if ($file == $path) {
                        if (substr($file, -4) == '.php') {
                            $data = file_get_contents(get_file_base() . '/' . $file);
                            if ((strpos($data, 'ocProducts') !== false || !should_ignore_file($file, IGNORE_NONBUNDLED_SCATTERED)) && ($file != '_config.php') && ($file != 'data_custom/errorlog.php') && ($file != 'tracker/config_inc.php')) {
                                $matches = array();
                                $m_count = preg_match_all('#@package\s+(\w+)#', $data, $matches);
                                if (($m_count != 0) && ($matches[1][0] != $addon_name) && (@$matches[1][1] != $addon_name/*HACKHACK: should ideally do a loop, but we'll assume max of 2 packages for now*/)) {
                                    $this->assertTrue(false, '@package wrong for <a href="txmt://open?url=file://' . htmlentities(get_file_base() . '/' . $file) . '">' . htmlentities($path) . '</a> (should be ' . $addon_name . ')');
                                } elseif ($m_count == 0) {
                                    $this->assertTrue(false, 'No @package for <a href="txmt://open?url=file://' . htmlentities(get_file_base() . '/' . $file) . '">' . htmlentities($path) . '</a> (should be ' . $addon_name . ')');
                                }
                            }
                        }

                        $found = true;

                        unset($addon_files[$fileindex]);
                        $addon_data[$addon_name] = $addon_files;
                        break 2;
                    }
                }
            }
            if (!$found) {
                $data = @file_get_contents(get_file_base() . '/' . $path);
                $matches = array();
                $m_count = preg_match('#@package\s+(\w+)#', $data, $matches);
                if ($m_count != 0) {
                    $unput_files[$matches[1]][] = $path;
                } else {
                    $this->assertTrue(false, 'Could not find the addon for... \'' . htmlentities($path) . '\',');
                }
            }
        }

        ksort($unput_files);
        foreach ($unput_files as $addon => $paths) {
            echo '<br /><strong>' . htmlentities($addon) . '</strong>';
            foreach ($paths as $path) {
                $this->assertTrue(false, 'Could not find the addon for... \'' . $path . '\',');
            }
        }
        foreach ($addon_data as $addon_name => $addon_files) {
            if (!file_exists(get_file_base() . '/sources/hooks/systems/addon_registry/' . $addon_name . '.php') && !file_exists(get_file_base() . '/sources_custom/hooks/systems/addon_registry/' . $addon_name . '.php')) {
                $this->assertTrue(false, 'Addon registry files missing / referenced twice... \'sources/hooks/systems/addon_registry/' . $addon_name . '.php\',');
            }
            foreach ($addon_files as $file) {
                if (($file != 'data_custom/execute_temp.php') && ($file != 'data_custom/firewall_rules.txt') && (!file_exists($file))) {
                    $this->assertTrue(false, 'Addon files missing... \'' . htmlentities($file) . '\',');
                }
            }
        }
    }

    private function do_dir($dir = '')
    {
        global $GFILE_ARRAY;

        require_code('files');

        $full_dir = get_file_base() . '/' . $dir;
        $dh = opendir($full_dir);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (preg_match('#^tracker/uploads/#', $dir . $file) != 0) {
                continue;
            }

            $ignore = ((preg_match('#^docs#', $dir . $file) == 0) ? IGNORE_CUSTOM_DIR_GROWN_CONTENTS : 0) | IGNORE_NONBUNDLED_EXTREMELY_SCATTERED | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_NON_EN_SCATTERED_LANGS | IGNORE_BUNDLED_UNSHIPPED_VOLATILE | IGNORE_REVISION_FILES;
            //$ignore = IGNORE_NONBUNDLED_EXTREMELY_SCATTERED | IGNORE_CUSTOM_THEMES | IGNORE_NON_EN_SCATTERED_LANGS | IGNORE_BUNDLED_UNSHIPPED_VOLATILE; Uncomment for more careful testing
            if (should_ignore_file($dir . $file, $ignore, 0)) {
                continue;
            }

            $is_dir = is_dir($full_dir . $file);

            if ($is_dir) {
                $this->do_dir($dir . $file . '/');
            } else {
                $GFILE_ARRAY[] = $dir . $file;
            }
        }
    }
}
