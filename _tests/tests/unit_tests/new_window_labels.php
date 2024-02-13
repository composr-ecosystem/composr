<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class new_window_labels_test_set extends cms_test_case
{
    public function testConsistentSetGet()
    {
        require_code('files2');
        require_code('themes2');

        $paths = [];
        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            $paths = array_merge($paths, [
                get_file_base() . '/themes/' . $theme . '/templates',
                get_file_base() . '/themes/' . $theme . '/templates_custom',
            ]);
        }

        foreach ($paths as $path) {
            $files = get_directory_contents($path, $path, 0, false, false, ['tpl']);
            foreach ($files as $f) {
                $c = file_get_contents($f);

                $matches = [];

                $num_matches = preg_match_all('#<a[^<>]*title="[^"]*{!LINK_NEW_WINDOW}"[^<>]*>#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $m = $matches[0][$i];
                    $line_number = substr_count(substr($c, 0, strpos($c, $m)), "\n") + 1;
                    $this->assertTrue(strpos($m, 'target="_blank"') !== false || strpos($m, 'data-click-ui-open') !== false || strpos($m, 'js-click-open') !== false, 'Missing target="_blank" in ' . $f . ' on line ' . strval($line_number));
                }

                $num_matches = preg_match_all('#<a[^<>]*target="_blank"[^<>]*>#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $m = $matches[0][$i];
                    $line_number = substr_count(substr($c, 0, strpos($c, $m)), "\n") + 1;
                    $this->assertTrue(strpos($m, '{!LINK_NEW_WINDOW') !== false, 'Missing {!LINK_NEW_WINDOW} in ' . $f . ' on line ' . strval($line_number));
                }
            }
        }
    }
}
