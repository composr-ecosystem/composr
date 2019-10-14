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
class forum_drivers_test_set extends cms_test_case
{
    public function testCompleteness()
    {
        // This is a hackerish way of avoiding abstract classes. Performance will be marginally better as we are checking things at test-time not run-time.

        $files = array();
        $dh = opendir(get_file_base() . '/sources_custom/forum');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.php') {
                $files[basename($f, '.php')] = 'sources_custom/forum/' . $f;
            }
        }
        closedir($dh);
        $dh = opendir(get_file_base() . '/sources/forum');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.php') {
                $files[basename($f, '.php')] = 'sources/forum/' . $f;
            }
        }
        closedir($dh);

        $functions = array();

        foreach ($files as $file => $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $functions[$file] = array();
            $matches = array();
            $num_matches = preg_match_all('#^\s*((protected|public) )?function (\w+)\(#m', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $functions[$file][] = $matches[3][$i];
            }
            sort($functions[$file]);
        }

        foreach ($functions as $file => $has) {
            if ($file == 'cns') {
                continue;
            }

            foreach ($functions['cns'] as $func) {
                $exceptions = array(
                    // Optional
                    'forum_md5',
                    'forum_create_cookie',

                    // Optional, guarded by forum_stub.php
                    '_get_displayname',
                    '_install_delete_custom_field',

                    // Defined with basic implementations in forum_stub.php anyway
                    'forum_layer_initialise',
                    'get_post_remaining_details',
                    'topic_is_threaded',

                    // Conversr-only
                    'init__forum__cns',
                    'cns_flood_control',
                );

                if (in_array($func, $exceptions)) {
                    continue;
                }

                $this->assertTrue(in_array($func, $has), 'Missing ' . $func . ' in ' . $file);
            }
        }
    }
}
