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
class addon_screenshots_test_set extends cms_test_case
{
    public function testNoUnmatchedScreenshots()
    {
        $dh = opendir(get_file_base() . '/data_custom/images/addon_screenshots');
        while (($file = readdir($dh)) !== false) {
            if ((substr($file, -5) != '.html') && ($file[0] != '.')) {
                $hook = preg_replace('#\..*$#', '', $file);
                $this->assertTrue(addon_installed($hook, false, false, false), 'Unrecognised addon screenshot: ' . $file);
            }
        }
        closedir($dh);
    }

    public function testNoMissingScreenshots()
    {
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            if ($place == 'sources_custom') {
                require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($hook));
                $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($hook));

                if ($ob === null) {
                    fatal_exit('Could not initiate ' . $hook);
                }

                $exists = false;
                foreach (['png', 'gif', 'jpg', 'jpeg'] as $ext) {
                    if (is_file(get_file_base() . '/data_custom/images/addon_screenshots/' . $hook . '.' . $ext)) {
                        $exists = true;
                    }
                }

                if ($ob->get_category() != 'Development') {
                    // These are defined as exceptions where we won't enforce our screenshot rule
                    if (in_array($hook, [
                        'enhanced_spreadsheets',
                        'pagination_protection',
                    ])) {
                        continue;
                    }

                    $this->assertTrue($exists, 'Missing addon screenshot: ' . $hook);
                }
            }
        }
    }
}
