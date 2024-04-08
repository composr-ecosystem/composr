<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _overused_globals_test_set extends cms_test_case
{
    public function testUnusedGlobals()
    {
        $matches = [];
        $found_globals = [];
        $documented_globals = [];
        $sanctified_globals = [];

        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_NONBUNDLED | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $done_for_file = [];

            $c = @cms_file_get_contents_safe(get_file_base() . '/' . $path);
            if ($c === false) {
                continue; // Probably a race condition between unit tests
            }
            if ($path != 'install.php') {
                if (strpos($c, '@chdir($FILE_BASE);') !== false) {
                    continue;
                }
            }

            // Front-end controller script, will have lots of globals

            // global $FOO
            $num_matches = preg_match_all('#^\s*global ([^;]*);#m', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $vars = explode(',', $matches[1][$i]);
                foreach ($vars as $var) {
                    $global = ltrim($var, ' $');

                    if (isset($done_for_file[$global])) {
                        continue;
                    }

                    if (!isset($found_globals[$global])) {
                        $found_globals[$global] = 0;
                    }
                    $found_globals[$global]++;

                    $done_for_file[$global] = true;
                }
            }

            // $GLOBALS['FOO']
            $num_matches = preg_match_all('#\$GLOBALS\[\'(\w+)\'\]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $global = $matches[1][$i];

                if (isset($done_for_file[$global])) {
                    continue;
                }

                if (!isset($found_globals[$global])) {
                    $found_globals[$global] = 0;
                }
                $found_globals[$global]++;

                $done_for_file[$global] = true;
            }

            // Global documentation, which makes a global 'sanctified' for use in many files
            $num_matches = preg_match_all('#@global\s+[^\s]+\s+\$(\w+)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $global = $matches[1][$i];
                $sanctified_globals[$global] = true;
            }
        }

        ksort($found_globals);

        $exceptions = [
            'SITE_INFO',
            'FILE_BASE',
            'LAST_TOPIC_ID',
            'LAST_TOPIC_IS_NEW',
            'RELATIVE_PATH',
            '_CREATED_FILES',
            '_MODIFIED_FILES',
            'METADATA',
        ];

        foreach ($found_globals as $global => $count) {
            if ((!isset($sanctified_globals[$global])) && (strpos($global, '_CACHE') === false) && (strpos($global, 'DEV_MODE') === false) && (!in_array($global, $exceptions))) {
                $this->assertTrue($count <= 6, 'Don\'t over-use global variables (' . $global . ', ' . integer_format($count) . ' files using).');
            }
        }
    }
}
