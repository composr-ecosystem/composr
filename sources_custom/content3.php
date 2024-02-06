<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    content_oop_api
 */

/**
 * CMS API base class.
 *
 * @package content_oop_api
 */
abstract class CMS_API_object
{
    public $entity_id = null;
    public $properties = null;

    public function __construct($entity_id)
    {
        $this->entity_id = $entity_id;
    }

    public function get($property_label)
    {
        return $this->properties[$property_label];
    }

    public function set($property_label, $value)
    {
        $this->properties[$property_label] = $value;
    }
}

function catalogue_find_options($field, $catalogue_name, $where = '')
{
    if (!addon_installed('catalogues')) {
        return [];
    }

    $sx = $catalogue_name . '__' . $field;
    static $capi_catalogue_options = [];
    if (isset($capi_catalogue_options[$sx])) {
        return $capi_catalogue_options[$sx];
    }

    $cf_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_fields', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('cf_name') => $field, 'c_name' => $catalogue_name]);
    $rows = $GLOBALS['SITE_DB']->query_select('catalogue_efv_short s JOIN ' . get_table_prefix() . 'catalogue_entries e ON e.id=s.ce_id', ['s.*'], ['c_name' => $catalogue_name, 'cf_id' => $cf_id], $where . ' ORDER BY cv_value');
    $out = [];
    foreach ($rows as $row) {
        $out[$row['ce_id']] = $row['cv_value'];
    }
    $capi_catalogue_options[$sx] = $out;

    return $out;
}

function catalogue_query_select($catalogue_name, $select, $where = [], $filters = '', $max = null, $start = 0)
{
    if (!addon_installed('catalogues')) {
        return [];
    }

    static $capi_catalogue_query_cache = [];
    $sz = serialize([$catalogue_name, $select, $where, $filters, $max, $start]);
    if (isset($capi_catalogue_query_cache[$sz])) {
        return $capi_catalogue_query_cache[$sz];
    }

    require_code('catalogues');

    require_code('filtercode');

    foreach ($where as $key => $val) {
        if ($filters != '') {
            $filters .= ',';
        }
        $filters .= $key . '=' . (is_string($val) ? $val : strval($val));
    }

    list($extra_join, $extra_where) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($filters), 'catalogue_entry', $catalogue_name);

    $query = 'SELECT DISTINCT r.* FROM ' . get_table_prefix() . 'catalogue_entries r' . implode('', $extra_join) . ' WHERE ' . db_string_equal_to('c_name', $catalogue_name) . $extra_where;
    $rows = $GLOBALS['SITE_DB']->query($query, $max, $start);
    $out = [];
    foreach ($rows as $_row) {
        $values = get_catalogue_entry_field_values($catalogue_name, $_row['id']);
        $row = [];
        foreach ($values as $_val) {
            $key = get_translated_text($_val['cf_name']);
            if ((in_array($key, $select)) || (in_array('*', $select))) {
                $val = isset($_val['effective_value_pure']) ? $_val['effective_value_pure'] : $_val['effective_value'];
                $row[$key] = $val;
            }
        }
        foreach (['id'] as $key) {
            if ((in_array($key, $select)) || (in_array('*', $select))) {
                $row[$key] = $_row[$key];
            }
        }
        $out[] = $row;
    }

    $capi_catalogue_query_cache[$sz] = $out;
    return $out;
}

function catalogue_query_select_count($catalogue_name, $where = [], $filters = '')
{
    if (!addon_installed('catalogues')) {
        return 0;
    }

    require_code('catalogues');

    require_code('filtercode');

    foreach ($where as $key => $val) {
        if ($filters != '') {
            $filters .= ',';
        }
        $filters .= $key . '=' . (is_string($val) ? $val : strval($val));
    }

    list($extra_join, $extra_where) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($filters), 'catalogue_entry', $catalogue_name);

    $query = 'SELECT COUNT(DISTINCT r.id) FROM ' . get_table_prefix() . 'catalogue_entries r' . implode('', $extra_join) . ' WHERE ' . db_string_equal_to('c_name', $catalogue_name) . $extra_where;
    return $GLOBALS['SITE_DB']->query_value_if_there($query);
}

/**
 * CMS API catalogue base class.
 *
 * @package content_oop_api
 */
abstract class CMS_API_catalogue_object extends CMS_API_object
{
    public $field_refs = [];

    public function __construct($entity_id, $missing_ok = false)
    {
        if (!addon_installed('catalogues')) {
            return;
        }

        static $capi_catalogue_object_cache = [];
        if (!isset($capi_catalogue_object_cache[$entity_id])) {
            $rows = $GLOBALS['SITE_DB']->query_select('catalogue_entries', ['*'], ['id' => $entity_id], '', 1);
            if (!array_key_exists(0, $rows)) {
                if ($missing_ok) {
                    return;
                }
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            if ($rows[0]['c_name'] != $this->catalogue) {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
            }

            $this->properties = $rows[0];

            require_code('catalogues');
            $values = get_catalogue_entry_field_values($this->catalogue, $entity_id);
            foreach ($values as $_val) {
                $key = get_translated_text($_val['cf_name']);
                $val = isset($_val['effective_value_pure']) ? $_val['effective_value_pure'] : $_val['effective_value'];
                $this->properties[$key] = $val;

                $this->field_refs[$key] = $_val;
            }

            $capi_catalogue_object_cache[$entity_id] = [$this->properties, $this->field_refs];
        } else {
            list($this->properties, $this->field_refs) = $capi_catalogue_object_cache[$entity_id];
        }

        parent::__construct($entity_id);
    }

    protected function _keyfield_entity_id_convert($id, $field)
    {
        $cf_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_fields', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('cf_name') => $field, 'c_name' => $this->catalogue]);
        return $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_efv_short', 'ce_id', ['cf_id' => $cf_id, 'cv_value' => $id]);
    }

    public function set($property_label, $val)
    {
        if (!addon_installed('catalogues')) {
            return;
        }

        if (!isset($this->field_refs[$property_label])) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        $__val = $this->field_refs[$property_label];

        $id = $this->entity_id;
        $type = $__val['cf_type'];
        $field_id = $__val['cf_id'];

        $ob = get_fields_hook($type);
        list(, , $sup_table_name) = $ob->get_field_value_row_bits($__val);

        if (substr($sup_table_name, -6) == '_trans') {
            $_val = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_efv_' . $sup_table_name, 'cv_value', ['cf_id' => $field_id, 'ce_id' => $id]);
            if ($_val === null) {
                $_val = insert_lang_comcode('cv_value', $val, 3);
            } else {
                $_val = lang_remap_comcode('cv_value', $_val, $val);
            }

            $GLOBALS['SITE_DB']->query_update('catalogue_efv_' . $sup_table_name, $_val, ['cf_id' => $field_id, 'ce_id' => $id], '', 1);
        } else {
            if ($sup_table_name == 'float') {
                $smap = ['cv_value' => (($val === null) || ($val == '')) ? null : floatval($val)];
            } elseif ($sup_table_name == 'integer') {
                $smap = ['cv_value' => (($val === null) || ($val == '')) ? null : intval($val)];
            } else {
                $smap = ['cv_value' => $val];
            }
            $GLOBALS['SITE_DB']->query_update('catalogue_efv_' . $sup_table_name, $smap, ['cf_id' => $field_id, 'ce_id' => $id], '', 1);
        }

        parent::set($property_label, $val);
    }
}

/**
 * CMS API database base class.
 *
 * @package content_oop_api
 */
abstract class CMS_API_database_object extends CMS_API_object
{
    public $table = null;

    public function __construct($entity_id)
    {
        static $capi_database_object_cache = [];
        $sx = serialize([$this->table, $entity_id]);

        if (isset($capi_database_object_cache[$sx])) {
            $this->properties = $capi_database_object_cache[$sx];
        } else {
            $rows = $GLOBALS['SITE_DB']->query_select($this->table, ['*'], ['id' => $entity_id]);
            if (!isset($rows[0])) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $this->properties = $rows[0];

            $capi_database_object_cache[$sx] = $this->properties;
        }

        parent::__construct($entity_id);
    }

    public function set($property_label, $value)
    {
        $GLOBALS['SITE_DB']->query_update($this->table, [$property_label => $value], ['id' => $this->entity_id], '', 1);
        parent::set($property_label, $value);
    }
}
