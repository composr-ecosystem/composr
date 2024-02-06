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
class disk_usage_spec_test_set extends cms_test_case
{
    public function testUsage()
    {
        $size = 0;

        $all_files = [];

        $hooks = find_all_hooks('systems', 'addon_registry');
        ksort($hooks);
        foreach ($hooks as $hook => $dir) {
            if ($dir == 'sources_custom') {
                continue;
            }
            if ($this->only === 'core') {
                if (($hook != 'core') && (substr($hook, 0, 5) != 'core_')) {
                    continue;
                }
            }

            require_code('hooks/systems/addon_registry/' . $hook);
            $ob = object_factory('Hook_addon_registry_' . $hook, true);
            if ($ob !== null) {
                $files = $ob->get_file_list();

                foreach ($files as $path) {
                    $s = @filesize($path);
                    if (($s === null) || ($s === false)) {
                        if ($this->debug) {
                            $this->dump($path, 'This file was skipped as it could not be found or accessed.');
                        }
                        continue;
                    }

                    if ($s % 512 != 0) {
                        $s += 512; // Round up to nearest block
                    }
                    $s += 512; // Assume a block for directory entry data
                    $file_size = $s;
                    $all_files[$path] = $file_size;
                    $size += $file_size;
                }
            }
        }

        arsort($all_files);
        if ($this->debug) {
            var_dump(['Average file size' => array_sum($all_files) / count($all_files), 'Total files' => count($all_files)]);

            var_dump($all_files);
        }

        $size *= 2; // For quick installer

        $size += 5 * 1024 * 1024; // Some overhead for installer PHP code, etc

        $this->assertTrue($size < 180 * 1024 * 1024, 'Install size is ' . integer_format($size) . ', which is above the defined system requirements; update requirements and this test');
    }
}
