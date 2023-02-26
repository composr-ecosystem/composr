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
class clean_reinstall_test_set extends cms_test_case
{
    public function testOptions()
    {
        require_code('files2');

        disable_php_memory_limit();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $i => $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $files[$i] = $c;
        }

        /*
        Not realistic to test anymore now that we have optimised ourselves to delete via arrays. If we moved to hooks that would be better.
        $privileges = $GLOBALS['SITE_DB']->query_select('privilege_list', ['the_name']);
        foreach ($privileges as $privilege) {
            foreach ($files as $c) {
                if (strpos($c, 'delete_privilege(\'' . $privilege['the_name'] . '\');') !== false) {
                    continue 2;
                }
            }

            $c1 = cms_file_get_contents_safe(get_file_base() . '/sources/permissions3.php', FILE_READ_LOCK);
            $c2 = cms_file_get_contents_safe(get_file_base() . '/sources/cns_install.php', FILE_READ_LOCK);
            $_c2 = substr($c2, 0, strpos($c2, 'Uninstall Conversr'));
            $is_listed = (strpos($c1, '\'' . $privilege['the_name'] . '\'') !== false) || (strpos($_c2, '\'' . $privilege['the_name'] . '\'') !== false);
            $this->assertTrue($is_listed, 'Could not find uninstall for privilege: ' . $privilege['the_name']);
        }
        */

        /*
        We may now use dynamic code for this, doing multiple drops as once for performance. This test is not needed anyway as installation tests will pick up the problem.
        $tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table']);
        foreach ($tables as $table) {
            foreach ($files as $c) {
                if (strpos($c, 'drop_table_if_exists(\'' . $table['m_table'] . '\');') !== false) {
                    continue 2;
                }
            }

            $is_installer = (strpos(cms_file_get_contents_safe(get_file_base() . '/install.php', FILE_READ_LOCK), '\'' . $table['m_table'] . '\'') !== false);
            $this->assertTrue($is_installer, 'Could not find uninstall for table: ' . $table['m_table']);
        }*/
    }
}
