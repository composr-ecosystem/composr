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
class google_appengine_test_set extends cms_test_case
{
    public function testPregConstraint()
    {
        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base());
        $files[] = 'install.php';
        foreach ($files as $file) {
            if ((substr($file, -4) == '.php') && (!should_ignore_file($file, IGNORE_BUNDLED_VOLATILE | IGNORE_NONBUNDLED_SCATTERED | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $contents = file_get_contents(get_file_base() . '/' . $file);

                if (preg_match('#preg_(replace|replace_callback|match|match_all|grep|split)\(\'(.)[^\']*(?<!\\\\)\\2[^\']*e#', $contents) != 0) {
                    $this->assertTrue(false, 'regexp /e not allowed (in ' . $file . ')');
                }

                /*
                Think Google AppEngine was since fixed, and we use this for symlink resolution
                if ((strpos($contents, '\'SCRIPT_FILENAME\'') !== false) && (basename($file) != 'minikernel.php') && (basename($file) != 'global.php') && (basename($file) != 'global2.php') && (basename($file) != 'phpstub.php')) {
                    $this->assertTrue(false, 'SCRIPT_FILENAME does not work stably across platforms (in ' . $file . ')');
                }
                */
            }
        }
    }

    // We must be under 1000 templates, due to a GAE limit
    public function testAdviceConstraint()
    {
        $tpl_counts = array();
        $file_counts = array();
        $directory_counts = array();
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            if ($place == 'sources_custom') {
                continue;
            }

            if (in_array($hook, array(
                'installer',
                'backup',
                'uninstaller',
                'ldap',
                'themewizard',
                'setupwizard',
                'stats',
                'import',
            ))) {
                continue;
            }

            require_code('hooks/systems/addon_registry/' . $hook);
            $hook_ob = object_factory('Hook_addon_registry_' . $hook);

            $files = $hook_ob->get_file_list();
            $file_counts[$hook] = count($files);

            $tpl_count = 0;
            foreach ($files as $file) {
                if (substr($file, -4) == '.tpl') {
                    $tpl_count++;
                }

                if (!isset($directory_counts[dirname($file)])) {
                    $directory_counts[dirname($file)] = 0;
                }
                $directory_counts[dirname($file)]++;

                $path = get_file_base() . '/' . $file;
                if (is_file($path)) {
                    $this->assertTrue(filesize($path) <= 32 * 1024 * 1024, '32MB is the maximum file size: ' . $file . ' is ' . integer_format(filesize($path)));
                }
            }
            $tpl_counts[$hook] = $tpl_count;
        }

        $tpl_total = 1;
        $file_total = 100; // Just an arbitrary amount that we will assume are not in any particular addon
        foreach ($tpl_counts as $hook => $tpl_count) {
            $tpl_total += $tpl_count;
            $file_total += $file_counts[$hook];
        }

        // Any large directories?
        foreach ($directory_counts as $dir => $count) {
            if ($dir == 'themes/default/templates') {
                continue;
            }
            $this->assertTrue($count <= 1000, 'Must be less than 1000 files in any directory (except templates, which is checked separately): ' . $dir);
        }

        // The user is advised they must take one big away and two small (or another big)
        $set_big = array(
            'calendar',
            'chat',
            'ecommerce',
            'shopping',
            'galleries',
            'pointstore',
        );
        $set_small = array(
            'authors',
            'banners',
            'downloads',
            'polls',
            'quizzes',
            'tickets',
            'newsletter',
            'wiki',
        );
        foreach ($set_big as $big) {
            foreach ($set_small as $small1) {
                foreach ($set_small as $small2) {
                    foreach ($set_small as $small3) {
                        foreach ($set_small as $small4) {
                            if (count(array_unique(array($small1, $small2, $small3, $small4))) < 4) {
                                continue;
                            }

                            $custom_tpl_total = $tpl_total - $tpl_counts[$big] - $tpl_counts[$small1] - $tpl_counts[$small2] - $tpl_counts[$small3] - $tpl_counts[$small4];
                            $custom_file_total = $file_total - $file_counts[$big] - $file_counts[$small1] - $file_counts[$small2] - $file_counts[$small3] - $file_counts[$small4];

                            $this->assertTrue($custom_tpl_total <= 1000, 'Must be less than 1000 templates for given addon advice (removing unsupported and also ' . $big . '&' . $small1 . '&' . $small2 . '&' . $small3 . '&' . $small4 . ') [' . strval($custom_tpl_total) . ']');

                            $this->assertTrue($custom_file_total <= 10000, 'Must be less than 10000 files for given addon advice (removing unsupported and also ' . $big . '&' . $small1 . '&' . $small2 . '&' . $small3 . '&' . $small4 . ') [' . strval($custom_file_total) . ']');
                        }
                    }
                }
            }
        }
    }
}
