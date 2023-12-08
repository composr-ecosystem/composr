<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class external_code_considerations_test_set extends cms_test_case
{
    public function testConsistency()
    {
        $a = file_get_contents(get_file_base() . '/.editorconfig');
        $matches = array();
        $does_match = (preg_match('#git checkout -- (.*)\(ends\)#s', $a, $matches) != 0);
        $this->assertTrue($does_match);
        if ($does_match) {
            $a = str_replace("\n#", '', str_replace("\\\n#", '', $matches[1]));
            $parts_editorconfig = preg_split('#\s+#', trim($a));

            $b = file_get_contents(get_file_base() . '/line_count.sh');
            $start = 'grep -v "\(';
            $start_pos = strpos($b, $start);
            $this->assertTrue($start_pos !== false);
            $start_pos += strlen($start);
            $end = '\).*" | ';
            $end_pos = strpos($b, $end);
            $this->assertTrue($end_pos !== false);
            $_parts_line_count = explode('\|', substr($b, $start_pos, $end_pos - $start_pos));
            $parts_line_count = array();
            $skip = array(
                '.*/.htaccess',
                '.*/index.html',
            );
            foreach ($_parts_line_count as $regexp_part) {
                $part = stripslashes($regexp_part);
                $has_dotslash = substr($part, 0, 2) == './';
                if ($has_dotslash) {
                    $part = substr($part, 2);
                }

                if (in_array($part, $skip)) {
                    continue;
                }

                $this->assertTrue($has_dotslash, 'Missing ./ for ' . $part);

                $parts_line_count[] = $part;
            }

            $all_parts = array_merge($parts_editorconfig, $parts_line_count);

            // Check consistency
            foreach ($parts_editorconfig as $part) {
                $this->assertTrue(in_array($part, $parts_line_count), 'Should be included in line_count.sh, as it is in .editorconfig: ' . $part);
            }
            foreach ($parts_line_count as $part) {
                $this->assertTrue(in_array($part, $parts_editorconfig), 'Should be included in .editorconfig, as it is in line_count.sh: ' . $part);
            }

            // Check files exists
            $skip = array(
                'data_custom/latest_activity.txt',
                'data_custom/permissioncheckslog.php',
                'data_custom/execute_temp.php',
                'docs/api',
                '_old',
                'nbproject',
            );
            foreach ($all_parts as $part) {
                if (in_array($part, $skip)) {
                    continue;
                }

                if ($part == 'data_custom/ckeditor') {
                    continue;
                }

                $this->assertTrue(file_exists(get_file_base() . '/' . $part), 'Missing file/directory: ' . $part);
            }

            // Check libraries excluded
            $skip = array(
                'themes/default/javascript_custom/activities.js',
                'themes/default/javascript_custom/activities_state.js',
                'themes/default/javascript_custom/booking.js',
                'themes/default/javascript_custom/custom_globals.js',
                'themes/default/javascript_custom/index.html',
                'themes/default/javascript_custom/shake.js',
                'themes/default/javascript_custom/shoutbox.js',
                'themes/default/javascript_custom/text_ghosts.js',
                'themes/default/javascript_custom/facebook.js',
                'themes/default/css_custom/activities.css',
                'themes/default/css_custom/buildr.css',
                'themes/default/css_custom/community_billboard.css',
                'themes/default/css_custom/gifts.css',
                'themes/default/css_custom/index.html',
                'themes/default/css_custom/iotds.css',
                'themes/default/css_custom/shoutbox.css',
                'themes/default/css_custom/tester.css',
                'themes/default/css_custom/tracker.css',
                'sources_custom/blocks',
                'sources_custom/composr_mobile_sdk',
                'sources_custom/database',
                'sources_custom/forum',
                'sources_custom/hooks',
                'sources_custom/locations',
                'sources_custom/miniblocks',
            );
            $dirs = array(
                'themes/default/javascript_custom' => false,
                'themes/default/css_custom' => false,
                'sources_custom' => true,
            );
            foreach ($dirs as $dir => $subdirs_only) {
                $dh = opendir(get_file_base() . '/' . $dir);
                while (($f = readdir($dh)) !== false) {
                    if ($f[0] == '.') {
                        continue;
                    }

                    if (is_numeric(get_file_extension($f))) {
                        continue; // Backup file
                    }

                    if (in_array($dir . '/' . $f, $skip)) {
                        continue;
                    }

                    if ($subdirs_only && !is_dir(get_file_base() . '/' . $dir . '/' . $f)) {
                        continue;
                    }

                    if (is_dir(get_file_base() . '/' . $dir . '/' . $f)) {
                        $contents = scandir(get_file_base() . '/' . $dir . '/' . $f);
                        if (($contents == array('.', '..')) || ($contents == array('.', '..', '.DS_Store'))) {
                            // Empty directory
                            continue;
                        }
                    }

                    $this->assertTrue(in_array($dir . '/' . $f, $all_parts), 'Should be included in line_count.sh/.editorconfig or set as a skip exception in this test: ' . $dir . '/' . $f);
                }
                closedir($dh);
            }
        }
    }
}
