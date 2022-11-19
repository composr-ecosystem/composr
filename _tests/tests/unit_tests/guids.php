<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class guids_test_set extends cms_test_case
{
    public function testDuplicateGUIDs()
    {
        require_code('make_release');

        guid_scan_init();

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN, true, true, ['php']);
        foreach ($files as $i => $path) {
            $scan = guid_scan($path);
            if ($scan === null) {
                continue; // Was skipped
            }

            foreach ($scan['errors_duplicate'] as $error) {
                $this->assertTrue(false, $error);
            }
        }
    }
}
