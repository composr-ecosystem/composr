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
class tutorial_nav_paths_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        set_option('yeehaw', '0');

        // Flush main caches after changing yeehaw option (if test fails try refreshing, there are internal caches that are not cleared here)
        require_code('caches3');
        erase_persistent_cache();
        erase_cached_language();

        require_all_lang();
    }

    public function testConfigOptionNames()
    {
        $config_hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        $config_options = [];
        foreach ($config_hooks as $file => $ob) {
            $details = $ob->get_details();
            $config_options[do_lang($details['human_name'])] = 'Admin Zone > Setup > Configuration > ' . strip_tags(do_lang('CONFIG_CATEGORY_' . $details['category'])) . ' > ' . strip_tags(do_lang($details['group']));
        }
        $config_options['Timezone'] = 'Admin Zone > Setup > Configuration > Site options > Internationalisation';

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK);

                $matches = [];
                $num_matches = preg_match_all('#"([^"]*)" configuration option[^s]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $option = trim($matches[1][$i], '\\');
                    $this->assertTrue(isset($config_options[$option]), 'Missing configuration option, ' . $option . ' in ' . $file);
                }

                $matches = [];
                $num_matches = preg_match_all('#"([^"]*)" configuration option \((Admin Zone > Setup > Configuration > [^\)]*)\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $option = trim($matches[1][$i], '\\');
                    if (isset($config_options[$option])) {
                        $config_path = $matches[2][$i];
                        $this->assertTrue($config_options[$option] == $config_path, 'Wrong config option path for ' . $option . ' in ' . $file . '; got  ' . $config_path . ' expected ' . $config_options[$option]);
                    }
                }
            }
        }
        closedir($dh);
    }

    public function testInvalidPaths()
    {
        $config_hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');

        $paths_found = [];

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK);

                $matches = [];
                $num_matches = preg_match_all('#Admin Zone > Setup > Configuration > (\w[\w /]+\w)( > (\w[\w /\-:]+\w))?#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $category = $matches[1][$i];

                    if (in_array($category, [
                        'Installation Options',
                        'Configure field filters',
                        'Configure breadcrumb overrides',
                        'Configure advanced banning',
                        'Composr upgrader',
                        'RSS/Atom Feeds',
                        'Code Editor',
                    ])) {
                        continue;
                    }

                    $group = isset($matches[3][$i]) ? $matches[3][$i] : '';
                    $total = $matches[0][$i];

                    $paths_found[$total] = [$category, $group];
                }

                $matches = [];
                $num_matches = preg_match_all('#[^>] Content Management > #', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $this->assertTrue(false, 'Start CMS zone breadcrumbs from the Admin Zone');
                }
            }
        }
        closedir($dh);

        foreach ($paths_found as $total => $_parts) {
            list($category, $group) = $_parts;

            $ok = false;

            if ($category == 'Installation Options') {
                $ok = true;
            }

            foreach ($config_hooks as $file => $ob) {
                $details = $ob->get_details();

                if ((strip_tags(do_lang('CONFIG_CATEGORY_' . $details['category'])) == $category) && (($group == '') || (strip_tags(do_lang($details['group'])) == $group))) {
                    $ok = true;
                    break;
                }
            }

            $this->assertTrue($ok, 'Could not find ' . $total . '; category="' . $category . '"; group="' . $group . '"');
        }
    }
}
