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
class _cqc_nonbundled_test_set extends cms_test_case
{
    public function testNonBundled()
    {
        cms_set_time_limit(300);

        require_code('third_party_code');

        $to_scan = [];

        $hooks = find_all_hooks('systems', 'addon_registry');
        ksort($hooks);
        foreach ($hooks as $hook => $dir) {
            if ($dir == 'sources') {
                continue;
            }

            require_code('hooks/systems/addon_registry/' . $hook);
            $ob = object_factory('Hook_addon_registry_' . $hook, true);
            if ($ob !== null) {
                $files = $ob->get_file_list();

                foreach ($files as $path) {
                    if (substr($path, -4) == '.php') {
                        // Exceptions
                        $exceptions = array_merge(list_untouchable_third_party_directories(), [
                        ]);
                        if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                            continue;
                        }
                        $exceptions = array_merge(list_untouchable_third_party_files(), [
                            'sources_custom/hooks/systems/startup/tapatalk.php',
                            'sources_custom/phpstub.php',

                            // Lots of data
                            'sources_custom/string_scan.php',
                            '_tests/tests/unit_tests/_lang_spelling_epic.php',
                        ]);
                        if (in_array($path, $exceptions)) {
                            continue;
                        }

                        $to_scan[$path] = filesize(get_file_base() . '/' . $path);
                    }
                }
            }
        }

        $to_scan = array_keys($to_scan);

        define('PER_RUN', 20);
        $count = count($to_scan);
        for ($i = 0; $i < $count; $i += PER_RUN) {
            $url = get_base_url() . '/_tests/codechecker/codechecker.php?api=1&todo=1';
            $url = $this->extend_cqc_call($url);
            for ($j = $i; $j < $i + PER_RUN; $j++) {
                if (!isset($to_scan[$j])) {
                    break;
                }

                $url .= '&to_use[' . strval($j - $i) . ']=' . urlencode($to_scan[$j]);
            }
            $result = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 10000.0]);
            foreach (explode('<br />', $result) as $line) {
                // Exceptions
                if (strpos($line, 'Could not find function') !== false) {
                    continue;
                }
                if (strpos($line, 'Could not find class') !== false) {
                    continue;
                }
                if (strpos($line, 'Could not find interface') !== false) {
                    continue;
                }
                if (strpos($line, 'Sources files should not contain loose code') !== false) {
                    continue;
                }
                if (strpos($line, 'Class names should start with an upper case letter') !== false) {
                    continue;
                }
                if (strpos($line, 'Variable \'map\' referenced before initialised') !== false) {
                    continue;
                }
                if (strpos($line, ' comment found') !== false) {
                    continue;
                }
                if (strpos($line, ' has a return with a value, and the function doesn\'t return a value') !== false) {
                    continue;
                }
                if (strpos($line, ' is in non-canonical format') !== false) {
                    continue;
                }

                $this->assertTrue($this->should_filter_cqc_line($line), $line);
            }
        }
    }
}
