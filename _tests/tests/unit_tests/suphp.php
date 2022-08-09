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
class suphp_test_set extends cms_test_case
{
    public function testWritableDirectoriesWithPHP()
    {
        require_code('files2');
        require_code('file_permissions_check');

        $paths = get_directory_contents(get_file_base(), '', 0, true, false);
        $chmod_paths = get_chmod_array(false, true);

        foreach ($paths as $path) {
            foreach ($chmod_paths as $chmod_path) {
                if (preg_match('#^' . str_replace('\*\*', '[^/]+', preg_quote($chmod_path, '#')) . '$#', $path) != 0) {
                    $php_files = get_directory_contents(get_file_base() . '/' . $path, '', 0, false, true, ['php']);
                    $this->assertTrue(empty($php_files));
                }
            }
        }
    }
}
