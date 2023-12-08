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
class catalogues_test_set extends cms_test_case
{
    protected $field_ids;
    protected $category_id;

    public function setUp()
    {
        parent::setUp();

        require_code('catalogues');
        require_code('catalogues2');

        if ($GLOBALS['SITE_DB']->query_select_value_if_there('catalogues', 'c_name', array('c_name' => 'test_catalogue')) !== null) { // In case test didn't clean up before
            actual_delete_catalogue('test_catalogue');
        }

        actual_add_catalogue('test_catalogue', 'Catalogue Title', 'Catalogue Description', C_DT_FIELDMAPS, 0, '', 0);

        $this->field_ids = array();
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'short_text Title', 'short_text Description', 'short_text');
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'short_trans Title', 'short_trans Description', 'short_trans');
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'long_text Title', 'long_text Description', 'long_text');
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'long_trans Title', 'long_trans Description', 'long_trans');
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'integer Title', 'integer Description', 'integer');
        $this->field_ids[] = actual_add_catalogue_field('test_catalogue', 'float Title', 'float Description', 'float');

        actual_edit_catalogue_field($this->field_ids[0], 'test_catalogue', 'short_text Title', 'short_text Description', 1, 1, 1, 1, '', 1);

        actual_edit_catalogue('test_catalogue', 'test_catalogue', 'Catalogue Title', 'Catalogue Description', C_DT_FIELDMAPS, '', 0, 0, 'never', null);

        $this->category_id = actual_add_catalogue_category('test_catalogue', 'Title', 'Description', '', null, '', 30, 60, null, null, null);
        $this->assertTrue('test_catalogue' == $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'c_name', array('id' => $this->category_id)));

        actual_edit_catalogue_category($this->category_id, 'Title', 'Description', '', null, '', '', '', 30, 60, null, 0);
        $this->assertTrue('test_catalogue' == $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'c_name', array('id' => $this->category_id)));
    }

    public function testCatalogueEntryCycle()
    {
        $map = array(
            $this->field_ids[0] => 'Test Value 1',
            $this->field_ids[1] => 'Test Value 2',
            $this->field_ids[2] => 'Test Value 3',
            $this->field_ids[3] => 'Test Value 4',
            $this->field_ids[4] => '1',
            $this->field_ids[5] => '1.23',
        );
        $id = actual_add_catalogue_entry($this->category_id, 0/*We do not want a notification*/, '', 1, 1, 1, $map);

        $map = array(
            $this->field_ids[0] => 'Test Value 1b',
            $this->field_ids[1] => 'Test Value 2b',
            $this->field_ids[2] => 'Test Value 3b',
            $this->field_ids[3] => 'Test Value 4b',
            $this->field_ids[4] => '2',
            $this->field_ids[5] => '2.34',
        );
        actual_edit_catalogue_entry($id, $this->category_id, 0/*We do not want a notification*/, '', 1, 1, 1, $map);

        $entry_rows = $GLOBALS['SITE_DB']->query_select('catalogue_entries', array('*'), array('id' => $id), '', 1);

        $tpl_map = get_catalogue_entry_map($entry_rows[0], null, 'PAGE', 'DEFAULT');

        foreach (array_values($map) as $i => $expected) {
            $value = $tpl_map['FIELD_' . strval($i)];
            if (is_object($value)) {
                $value = $value->evaluate();
            }
            $this->assertTrue($value == $expected, 'Got ' . $value);
        }

        actual_delete_catalogue_entry($id);
    }

    public function tearDown()
    {
        actual_delete_catalogue_category($this->category_id, false);

        actual_delete_catalogue('test_catalogue');

        parent::tearDown();
    }
}
