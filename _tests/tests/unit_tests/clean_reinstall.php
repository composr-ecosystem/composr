<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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

        cms_ini_set('memory_limit', '-1');

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, array('php'));
        $files[] = 'install.php';
        foreach ($files as $i => $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $files[$i] = $c;
        }

        $privileges = $GLOBALS['SITE_DB']->query_select('privilege_list', array('the_name'));
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

        $tables = $GLOBALS['SITE_DB']->query_select('db_meta', array('DISTINCT m_table'));
        foreach ($tables as $table) {
            foreach ($files as $c) {
                if (strpos($c, 'drop_table_if_exists(\'' . $table['m_table'] . '\');') !== false) {
                    continue 2;
                }
            }

            $is_installer = (strpos(cms_file_get_contents_safe(get_file_base() . '/install.php', FILE_READ_LOCK), '\'' . $table['m_table'] . '\'') !== false);
            $this->assertTrue($is_installer, 'Could not find uninstall for table: ' . $table['m_table']);
        }
    }
}
