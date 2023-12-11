<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class core_fields_test_set extends cms_test_case
{
    public function testCoreFields()
    {
        require_code('fields');
        require_code('form_templates');
        require_code('database_search');

        $fields = find_all_hooks('systems', 'fields');
        foreach (array_keys($fields) as $field) {
            require_code('hooks/systems/fields/' . $field);
            $ob = object_factory('Hook_fields_' . $field);
            if (method_exists($ob, 'get_field_types')) {
                $types = $ob->get_field_types();
            } else {
                $types = array($field => '');
            }

            foreach (array_keys($types) as $type) {
                $field = array(
                    'id' => 1,
                    'c_name' => 'hosted',
                    'cf_type' => $type,
                    'cf_default' => '',
                    'cf_name' => 'Test',
                    'cf_required' => 0,
                );

                $test = $ob->get_search_inputter($field);
                $this->assertTrue(is_null($test) || is_array($test), 'Failed get_search_inputter for ' . $type);

                $test = $ob->inputted_to_sql_for_search($field, 1);
                $this->assertTrue(is_null($test) || is_array($test), 'Failed inputted_to_sql_for_search for ' . $type);

                $test = $ob->get_field_value_row_bits($field);
                $this->assertTrue(is_array($test), 'Failed get_field_value_row_bits for ' . $type);

                $test = $ob->render_field_value($field, 'test', 0, null);
                $this->assertTrue(is_string($test) || is_object($test), 'Failed render_field_value for ' . $type);

                $test = $ob->get_field_inputter('Test', 'Description.', $field, '', true);
                $this->assertTrue(is_null($test) || is_object($test) || is_array($test), 'Failed get_field_inputter for ' . $type);

                $test = $ob->inputted_to_field_value(false, $field);
                $this->assertTrue(is_null($test) || is_string($test), 'Failed inputted_to_field_value for ' . $type);
            }
        }
    }
}
