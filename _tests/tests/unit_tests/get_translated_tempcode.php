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
class get_translated_tempcode_test_set extends cms_test_case
{
    public function testMatchingFieldsAndTables()
    {
        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base());

        $_fields = $GLOBALS['SITE_DB']->query_select('db_meta', array('*'));
        $fields = array();
        foreach ($_fields as $field) {
            $table = $field['m_table'];
            $name = $field['m_name'];

            if (!isset($fields[$table])) {
                $fields[$table] = array();
            }
            $fields[$table][] = $name;
        }

        foreach ($files as $file) {
            if (substr($file, -4) == '.php') {
                $c = file_get_contents($file);

                $matches = array();
                $num_matches = preg_match_all('#get_translated_tempcode\(\'(\w+)\', [^,]*, \'(\w+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $table = $matches[1][$i];
                    $name = $matches[2][$i];
                    $this->assertTrue((isset($fields[$table])) && (in_array($name, $fields[$table])), 'Could not find ' . $table . ':' . $name . ', ' . $file);
                }
            }
        }
    }
}
