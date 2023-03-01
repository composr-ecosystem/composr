<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class google_appengine_test_set extends cms_test_case
{
    public function testPregConstraint()
    {
        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_NONBUNDLED | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            if (preg_match('#preg_(replace|replace_callback|match|match_all|grep|split)\(\'(.)[^\']*(?<!\\\\)\\2[^\']*e#', $c) != 0) {
                $this->assertTrue(false, 'regexp /e not allowed (in ' . $path . ')');
            }

            /*
            Think Google AppEngine was since fixed, and we use this for symlink resolution
            if ((strpos($c, '\'SCRIPT_FILENAME\'') !== false) && ($path != 'sources/minikernel.php') && ($path != 'sources/global.php') && ($path != 'sources/global2.php') && ($path != 'sources/phpstub.php')) {
                $this->assertTrue(false, 'SCRIPT_FILENAME does not work stably across platforms (in ' . $path . ')');
            }
            */
        }
    }
}
