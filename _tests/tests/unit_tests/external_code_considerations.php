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

// See https://compo.sr/tracker/view.php?id=651

/**
 * Composr test case class (unit testing).
 */
class external_code_considerations_test_set extends cms_test_case
{
    public function testConsistency()
    {
        $a = cms_file_get_contents_safe(get_file_base() . '/.editorconfig', FILE_READ_LOCK);
        $matches = array();
        $does_match = (preg_match('#git checkout -- (.*)\(ends\)#s', $a, $matches) != 0);
        $this->assertTrue($does_match);
        if ($does_match) {
            $a = str_replace("\n#", '', str_replace("\\\n#", '', $matches[1]));
            $parts_editorconfig = preg_split('#\s+#', trim($a));

            $b = cms_file_get_contents_safe(get_file_base() . '/line_count.sh', FILE_READ_LOCK);
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
                'data_custom/latest_activity.bin',
                'data_custom/permission_checks.log',
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
                'themes/default/javascript_custom/activity_feed.js',
                'themes/default/javascript_custom/buildr.js',
                'themes/default/javascript_custom/comcode_flip_tag.js',
                'themes/default/javascript_custom/composr_homesite_support_credits.js',
                'themes/default/javascript_custom/data_mappr.js',
                'themes/default/javascript_custom/facebook_support.js',
                'themes/default/javascript_custom/google_search.js',
                'themes/default/javascript_custom/idolisr.js',
                'themes/default/javascript_custom/image_slider.js',
                'themes/default/javascript_custom/iotds.js',
                'themes/default/javascript_custom/jestr.js',
                'themes/default/javascript_custom/core_rich_media.js',
                'themes/default/javascript_custom/password_censor.js',
                'themes/default/javascript_custom/shoutr.js',
                'themes/default/javascript_custom/google_analytics.js',
                'themes/default/javascript_custom/twitter_feed_integration_block.js',
                'themes/default/javascript_custom/user_mappr.js',
                'themes/default/javascript_custom/workflows.js',
                'themes/default/javascript_custom/booking.js',
                'themes/default/javascript_custom/index.html',
                'themes/default/javascript_custom/facebook.js',
                'themes/default/javascript_custom/calculatr.js',
                'themes/default/javascript_custom/global.js',
                'themes/default/css_custom/activities.css',
                'themes/default/css_custom/buildr.css',
                'themes/default/css_custom/community_billboard.css',
                'themes/default/css_custom/gifts.css',
                'themes/default/css_custom/index.html',
                'themes/default/css_custom/iotds.css',
                'themes/default/css_custom/shoutbox.css',
                'themes/default/css_custom/tester.css',
                'themes/default/css_custom/tracker.css',
                'themes/default/css_custom/tutorials.css',
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
                while (($file = readdir($dh)) !== false) {
                    if ($file[0] == '.') {
                        continue;
                    }

                    if (is_numeric(get_file_extension($file))) {
                        continue; // Backup file
                    }

                    if (in_array($dir . '/' . $file, $skip)) {
                        continue;
                    }

                    if ($subdirs_only && !is_dir(get_file_base() . '/' . $dir . '/' . $file)) {
                        continue;
                    }

                    if (is_dir(get_file_base() . '/' . $dir . '/' . $file)) {
                        $files = scandir(get_file_base() . '/' . $dir . '/' . $file);
                        if (($files == array('.', '..')) || ($files == array('.', '..', '.DS_Store'))) {
                            // Empty directory
                            continue;
                        }
                    }

                    $this->assertTrue(in_array($dir . '/' . $file, $all_parts), 'Should be included in line_count.sh/.editorconfig or set as a skip exception in this test: ' . $dir . '/' . $file);
                }
                closedir($dh);
            }
        }
    }
}
