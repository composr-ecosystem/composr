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
class upload_directory_test_set extends cms_test_case
{
    public function testDirectoryLength()
    {
        $path = get_file_base() . '/uploads';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (is_dir($path . '/' . $file)) {
                $this->assertTrue(strlen($file) <= 23, '23 character maximum upload directory length (due to database filename length limitation), ' . $file);
            }
        }
        closedir($dh);
    }
}
