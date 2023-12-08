<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class _cqc_nonbundled_test_set extends cms_test_case
{
    public function testNonBundled()
    {
        $to_scan = array();

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

                foreach ($files as $file) {
                    if (substr($file, -4) == '.php') {
                        // Exceptions
                        $no_go_dirs = array(
                            'sources_custom/aws',
                            'sources_custom/swift_mailer',
                            'tracker',
                            'vendor',
                            'sources_custom/spout',
                            'sources_custom/geshi',
                            'sources_custom/getid3',
                            'sources_custom/ILess',
                            'sources_custom/Transliterator',
                            'sources_custom/sabredav',
                            'sources_custom/photobucket',
                            'sources_custom/programe',
                            '_tests/simpletest',
                            'mobiquo/lib',
                            'mobiquo/smartbanner',
                            '_tests/codechecker',
                            'sources_custom/Cloudinary',
                            'sources_custom/facebook',
                            'data_custom/upload-crop',
                            'exports',
                            'sources_custom/composr_mobile_sdk/ios/ApnsPHP',
                        );
                        if (preg_match('#^(' . implode('|', $no_go_dirs) . ')/#', $file) != 0) {
                            continue;
                        }
                        if (in_array($file, array(
                            'sources_custom/sugar_crm_lib.php',
                            'sources_custom/curl.php',
                            'sources_custom/geshi.php',
                            '_tests/libs/mf_parse.php',
                            'data_custom/errorlog.php',
                            'data_custom/execute_temp.php',
                            'sources_custom/browser_detect.php',
                            'sources_custom/twitter.php',
                            '_config.php',
                            'sources_custom/hooks/systems/startup/tapatalk.php',
                        ))) {
                            continue;
                        }

                        $to_scan[] = $file;
                    }
                }
            }
        }

        define('PER_RUN', 20);
        $count = count($to_scan);
        for ($i = 0; $i < $count; $i += PER_RUN) {
            $url = get_base_url() . '/_tests/codechecker/code_quality.php?api=1&to_use=';
            $done_one = false;
            for ($j = $i; $j < $i + PER_RUN; $j++) {
                if (!isset($to_scan[$j])) {
                    break;
                }

                if ($done_one) {
                    $url .= ':';
                }
                $url .= urlencode($to_scan[$j]);
                $done_one = true;
            }
            $result = http_download_file($url, null, true, false, 'Composr', null, null, null, null, null, null, null, null, 10000.0);
            foreach (explode('<br />', $result) as $line) {
                // Exceptions
                if (strpos($line, 'Could not find function') !== false) {
                    continue;
                }
                if (strpos($line, 'Could not find class') !== false) {
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

                $this->assertTrue(((trim($line) == '') || (substr($line, 0, 5) == 'SKIP:') || (stripos($line, 'in v11') !== false) || (substr($line, 0, 5) == 'DONE ') || (substr($line, 0, 6) == 'FINAL ') || ((strpos($line, 'comment found') !== false) && (strpos($line, '#') !== false)) || (strpos($line, 'FUDGE') !== false) || (strpos($line, 'LEGACY') !== false) || (strpos($line, 'It is best to only have') !== false)), $line);
            }
        }
    }
}
