<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    data_mappr
 * @package    user_mappr
 */

/**
 * Hook class.
 */
class Hook_fields_float
{
    // ==============
    // Module: search
    // ==============

    /**
     * Get special Tempcode for inputting this field.
     *
     * @param  array $field The field details
     * @return ?array Specially encoded input detail rows (null: nothing special)
     */
    public function get_search_inputter($field)
    {
        $type = '_FLOAT';
        $extra = '';
        $display = array_key_exists('trans_name', $field) ? $field['trans_name'] : get_translated_text($field['cf_name']);

        $range_search = (option_value_from_field_array($field, 'range_search', 'off') == 'on');
        if ($range_search) {
            $type .= '_RANGE';
            $special = get_param_string('option_' . strval($field['id']) . '_from', '') . ';' . get_param_string('option_' . strval($field['id']) . '_to', '');
        } else {
            $special = get_param_string('option_' . strval($field['id']), '');
        }

        return array('NAME' => strval($field['id']) . $extra, 'DISPLAY' => $display, 'TYPE' => $type, 'SPECIAL' => $special);
    }

    /**
     * Get special SQL from POSTed parameters for this field.
     *
     * @param  array $field The field details
     * @param  integer $i We're processing for the ith row
     * @param string $table_alias Table alias for catalogue entry table
     * @return ?array Tuple of SQL details (array: extra trans fields to search, array: extra plain fields to search, string: an extra table segment for a join, string: the name of the field to use as a title, if this is the title, extra WHERE clause stuff) (null: nothing special)
     */
    public function inputted_to_sql_for_search($field, $i, $table_alias = 'r')
    {
        $range_search = (option_value_from_field_array($field, 'range_search', 'off') == 'on');
        if ($range_search) {
            return null;
        }

        return exact_match_sql($field, $i, 'float', null, $table_alias);
    }

    // ===================
    // Backend: fields API
    // ===================

    /**
     * Get some info bits relating to our field type, that helps us look it up / set defaults.
     *
     * @param  ?array $field The field details (null: new field)
     * @param  ?boolean $required Whether a default value cannot be blank (null: don't "lock in" a new default value) (may be passed as false also if we want to avoid "lock in" of a new default value, but in this case possible cleanup of $default may still happen where appropriate)
     * @param  ?string $default The given default value as a string (null: don't "lock in" a new default value) (blank: only "lock in" a new default value if $required is true)
     * @return array Tuple of details (row-type,default-value-to-use,db row-type)
     */
    public function get_field_value_row_bits($field, $required = null, $default = null)
    {
        unset($field);
        if ($required !== null) {
            if (($required) && ($default == '')) {
                $default = '0';
            }
        }
        return array('float_unescaped', $default, 'float');
    }

    /**
     * Convert a field value to something renderable.
     *
     * @param  array $field The field details
     * @param  mixed $ev The raw value
     * @return mixed Rendered field (Tempcode or string)
     */
    public function render_field_value($field, $ev)
    {
        require_lang('locations');

        $_cf_name = array_key_exists('trans_name', $field) ? $field['trans_name'] : get_translated_text($field['cf_name']);
        if (($_cf_name == do_lang('LATITUDE')) || ($_cf_name == do_lang('LONGITUDE')) || ($_cf_name == 'cms_latitude') || ($_cf_name == 'cms_longitude')) {
            if (is_object($ev)) {
                if ($ev->evaluate() == do_lang('NA_EM')) {
                    return ''; // Cleanup noisy data
                }
            }
        }

        if (is_object($ev)) {
            if ($ev->evaluate() == do_lang('NA_EM')) {
                return '';
            }

            return $ev;
        }

        if ($ev == '') {
            return '';
        }

        $float = floatval($ev);

        $decimal_points = intval(option_value_from_field_array($field, 'decimal_points', '2'));
        $decimal_points_behaviour = option_value_from_field_array($field, 'decimal_points_behaviour', 'dp');

        $ev = float_format($float, $decimal_points, $decimal_points_behaviour == 'trim');

        if (($decimal_points_behaviour == 'price') && (substr($ev, -3) == '.00')) {
            $ev = float_format($float, 0, false);
        }

        if (($GLOBALS['XSS_DETECT']) && (ocp_is_escaped($ev))) {
            ocp_mark_as_escaped($ev);
        }
        return $ev;
    }

    // ======================
    // Frontend: fields input
    // ======================

    /**
     * Get form inputter.
     *
     * @param  string $_cf_name The field name
     * @param  string $_cf_description The field description
     * @param  array $field The field details
     * @param  ?string $actual_value The actual current value of the field (null: none)
     * @param  boolean $new Whether this is for a new entry
     * @return ?Tempcode The Tempcode for the input field (null: skip the field - it's not input)
     */
    public function get_field_inputter($_cf_name, $_cf_description, $field, $actual_value, $new)
    {
        require_lang('locations');

        if ($actual_value === do_lang('NA')) {
            $actual_value = null;
        }

        $input_name = empty($field['cf_input_name']) ? ('field_' . strval($field['id'])) : $field['cf_input_name'];

        if ($_cf_name == do_lang('LONGITUDE') || $_cf_name == 'cms_longitude') { // Assumes there is a Latitude field too, although not critical
            $pretty_name = $_cf_name;
            $description = $_cf_description;
            $required = $field['cf_required'] == 1;

            $latitude = '';
            $longitude = '';

            global $LATITUDE;
            if ((isset($LATITUDE)) && ($LATITUDE != '') && ($LATITUDE != do_lang('NA'))) {
                $latitude = float_to_raw_string(floatval($LATITUDE), 10, true);
            }
            if ((isset($actual_value)) && ($actual_value != '') && ($actual_value != do_lang('NA'))) {
                $longitude = float_to_raw_string(floatval($actual_value), 10, true);
            }

            // To stop it crashing
            if ($latitude == '' && $longitude != '') {
                $latitude = '0';
            }
            if ($latitude != '' && $longitude == '') {
                $longitude = '0';
            }

            $input = do_template('FORM_SCREEN_INPUT_MAP_POSITION', array('_GUID' => '86d69d152d7bfd125e6216c9ac936cfd', 'REQUIRED' => $required, 'NAME' => $input_name, 'LATITUDE' => $latitude, 'LONGITUDE' => $longitude));
            $lang_string = 'MAP_POSITION_FIELD_field_' . strval($field['id']);
            $test = do_lang($lang_string, null, null, null, null, false);
            if (is_null($test)) {
                $lang_string = 'MAP_POSITION_FIELD';
            }
            return _form_input($input_name, do_lang_tempcode($lang_string), '', $input, $required, false);
        }

        if ($_cf_name == do_lang('LATITUDE')) { // Assumes there is a Longitude field too
            global $LATITUDE;
            $LATITUDE = $actual_value; // Store for when Longitude field is rendered - critical, else won't be entered
            return new Tempcode();
        }

        return form_input_float($_cf_name, $_cf_description, $input_name, (is_null($actual_value) || ($actual_value === '')) ? null : floatval($actual_value), $field['cf_required'] == 1);
    }

    /**
     * Find the posted value from the get_field_inputter field
     *
     * @param  boolean $editing Whether we were editing (because on edit, it could be a fractional edit)
     * @param  array $field The field details
     * @param  ?string $upload_dir Where the files will be uploaded to (null: do not store an upload, return null if we would need to do so)
     * @param  ?array $old_value Former value of field (null: none)
     * @return ?string The value (null: could not process)
     */
    public function inputted_to_field_value($editing, $field, $upload_dir = 'uploads/catalogues', $old_value = null)
    {
        require_lang('locations');

        $id = $field['id'];
        $tmp_name = 'field_' . strval($id);
        $default = STRING_MAGIC_NULL;
        $_cf_name = array_key_exists('trans_name', $field) ? $field['trans_name'] : get_translated_text($field['cf_name']);
        if ($_cf_name == do_lang('LATITUDE') || $_cf_name == 'cms_latitude') {
            $default = post_param_string('latitude', STRING_MAGIC_NULL);
        }
        if ($_cf_name == do_lang('LONGITUDE') || $_cf_name == 'cms_longitude') {
            $default = post_param_string('longitude', STRING_MAGIC_NULL);
        }

        $ret = post_param_string($tmp_name, $default);
        
        if (($ret != STRING_MAGIC_NULL) && ($ret != '')) {
            if (!is_numeric($ret)) {
                warn_exit(do_lang_tempcode('javascript:NOT_FLOAT', $ret));
            }
            $ret = float_to_raw_string(float_unformat($ret), 30);
        }

        if (($ret != STRING_MAGIC_NULL) && ($ret != '')) {
            $ret = float_to_raw_string(float_unformat($ret, $_cf_name == 'cms_latitude' || $_cf_name == 'cms_longitude'), 30);
        }

        return $ret;
    }
}
