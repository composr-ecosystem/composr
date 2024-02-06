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
class forum_drivers_test_set extends cms_test_case
{
    public function testCompleteness()
    {
        // This is a hackerish way of avoiding abstract classes. Performance will be marginally better as we are checking things at test-time not run-time.

        $files = [];
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

        $file_functions = [];

        foreach ($files as $file => $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $file_functions[$file] = [];
            $matches = [];
            $num_matches = preg_match_all('#^\s*((protected|public) )?function (\w+)\((.*)\)#m', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $function = $matches[3][$i];
                $file_functions[$file][$function] = $matches[4][$i];
            }
            ksort($file_functions[$file]);
        }

        foreach ($file_functions as $file => $functions) {
            if ($file == 'cns') {
                continue; // CNS is the baseline we are comparing against
            }

            foreach ($file_functions['cns'] as $function => $cns_parameters) {
                $exceptions = [
                    // Optional
                    'create_login_cookie',
                    'authorise_cookie_login',

                    // Defined with basic implementations in forum_stub.php anyway
                    'forum_layer_initialise',
                    'get_post_remaining_details',
                    'topic_is_threaded',
                    'get_displayname',
                    'install_delete_custom_field',

                    // Conversr-only
                    'init__forum__cns',
                    'cns_flood_control',
                ];

                $present = array_key_exists($function, $functions);

                if (!in_array($function, $exceptions)) {
                    $this->assertTrue($present, 'Missing ' . $function . ' in ' . $file);

                    continue;
                }

                if ($present) {
                    $exceptions = [
                        // Extra $tempcode_okay parameter in CNS
                        '_forum_url',
                        '_join_url',
                        '_member_pm_url',
                        '_users_online_url',
                        'member_home_url',
                        'post_url',
                        'topic_url',

                        // Various filter parameters in CNS
                        '_get_members_groups',
                        '_get_usergroup_list',
                        'find_emoticons',
                        'get_forum_topic_posts',
                        'get_matching_members',
                        'show_forum_topics',

                        // Extra $tempcode_okay parameter in CNS & Various filter parameters in CNS
                        '_member_profile_url',

                        // Miscellaneous extra parameters in CNS
                        'find_topic_id_for_topic_identifier',
                        'make_post_forum_topic',
                    ];
                    if (in_array($function, $exceptions)) {
                        continue;
                    }

                    $this->assertTrue($cns_parameters == $functions[$function], 'Inconsistent parameters for ' . $function . ' in ' . $file);
                }
            }
        }
    }
}
