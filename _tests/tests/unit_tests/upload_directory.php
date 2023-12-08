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
class upload_directory_test_set extends cms_test_case
{
    public function testDirectoryLength()
    {
        $path = get_file_base() . '/uploads';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (is_dir($path . '/' . $f)) {
                $this->assertTrue(strlen($f) <= 23, '23 character maximum upload directory length (due to database filename length limitation), ' . $f);
            }
        }
        closedir($dh);
    }
}
