<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_spreadsheet_lists
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__nested_spreadsheet()
{
    require_lang('nested_spreadsheet');
}

/**
 * Get the spreadsheet/CPF structure for the site.
 *
 * @return array Structured data about spreadsheet files/CPFs
 */
function get_nested_spreadsheet_structure() : array
{
    if (get_forum_type() == 'cns') {
        require_code('cns_members');
        require_code('cns_groups');

        $_custom_fields = cns_get_all_custom_fields_match(cns_get_all_default_groups(true));
    } else {
        $_custom_fields = [];
    }

    static $spreadsheet_structure = [];
    if (!empty($spreadsheet_structure)) {
        return $spreadsheet_structure;
    }

    require_code('files_spreadsheets_read');

    $spreadsheet_files = [];
    if (file_exists(get_custom_file_base() . '/private_data')) {
        $dh = @opendir(get_custom_file_base() . '/private_data');
        if ($dh !== false) {
            while (($spreadsheet_filename = readdir($dh)) !== false) {
                if (is_spreadsheet_readable($spreadsheet_filename)) {
                    continue;
                }

                $sheet_reader = spreadsheet_open_read(get_file_base() . '/private_data/' . $spreadsheet_filename, null, CMS_Spreadsheet_Reader::ALGORITHM_UNNAMED_FIELDS);

                $header_row = $sheet_reader->read_row();

                if ($header_row !== false) {
                    // Initialise data for this forthcoming $spreadsheet_files entry
                    $spreadsheet_file = [];
                    $spreadsheet_file['headings'] = [];    // Unordered headings                               ?=>heading
                    $spreadsheet_file['data'] = [];        // Array of rows                                    ?=>[cols,col2,col3,...]

                    // Fill out 'headings'
                    $spreadsheet_file['headings'] = $header_row;

                    // Fill out 'data' and 'lists'
                    $vl_temp = $sheet_reader->read_row();
                    while ($vl_temp !== false) { // If there's nothing past the headings this loop never executes
                        $new_entry = [];
                        foreach ($header_row as $j => $heading) {
                            $new_entry[$heading] = $vl_temp[$j];
                        }
                        $spreadsheet_file['data'][] = $new_entry;

                        $vl_temp = $sheet_reader->read_row();
                    }

                    $spreadsheet_files[$spreadsheet_filename] = $spreadsheet_file;

                    $sheet_reader->close();
                } else {
                    $sheet_reader->close();
                    warn_exit('No header row found for "' . $spreadsheet_filename . '".', false, true);
                }
            }

            closedir($dh);
        }
    }
    $spreadsheet_structure['spreadsheet_files'] = $spreadsheet_files;

    $cpf_fields = [];
    foreach ($_custom_fields as $cf => $custom_field) {
        if (($custom_field['cf_type'] == 'list') || ($custom_field['cf_type'] == 'list_multi')) {
            $_value = explode('|', $custom_field['cf_default']); // $_value will come up as file|heading(optional)|order(optional)
            $spreadsheet_filename = $_value[0];

            if (is_spreadsheet_readable($spreadsheet_filename)) {
                if (!isset($_value[1])) {
                    $_value[1] = null;
                }
                if (!isset($_value[2])) {
                    $_value[2] = null;
                }
                if (!isset($_value[3])) {
                    $_value[3] = null;
                }
                $spreadsheet_heading = $_value[1];
                $spreadsheet_parent_filename = $_value[2];
                $spreadsheet_parent_heading = $_value[3];

                if (!array_key_exists($spreadsheet_filename, $spreadsheet_files)) { // Check referenced filename exists
                    if (!file_exists(get_custom_file_base() . '/private_data')) {
                        attach_message('Missing private_data directory for spreadsheet file storage.', 'warn', false, true);
                        break;
                    }

                    attach_message('Specified spreadsheet file, ' . $spreadsheet_filename . ', not found for "' . $custom_field['trans_name'] . '".', 'warn', false, true);
                    continue;
                }
                if (!in_array($spreadsheet_heading, $spreadsheet_files[$spreadsheet_filename]['headings'])) { // Check referenced heading exists
                    attach_message('Specified heading,' . $spreadsheet_heading . ' , not found in spreadsheet file for "' . $custom_field['trans_name'] . '".', 'warn', false, true);
                    continue;
                }
                if (($spreadsheet_parent_filename !== null) && ($spreadsheet_parent_heading !== null)) {
                    if (!array_key_exists($spreadsheet_parent_filename, $spreadsheet_files)) { // Check referenced filename exists
                        attach_message('Specified parent spreadsheet file, ' . $spreadsheet_parent_filename . ', not found for "' . $custom_field['trans_name'] . '".', 'warn', false, true);
                        $spreadsheet_parent_filename = null;
                        $spreadsheet_parent_heading = null;
                    }
                    if (!in_array($spreadsheet_parent_heading, $spreadsheet_files[$spreadsheet_parent_filename]['headings'])) { // Check referenced heading exists
                        attach_message('Specified parent heading not found in spreadsheet file for "' . $custom_field['trans_name'] . '".', 'warn', false, true);
                        $spreadsheet_parent_filename = null;
                        $spreadsheet_parent_heading = null;
                    }
                }

                if (isset($cpf_fields[$spreadsheet_heading])) {
                    attach_message('Specified heading,' . $spreadsheet_heading . ' ,used for more than one field.', 'warn', false, true);
                    continue;
                }

                if ($spreadsheet_parent_filename === null || $spreadsheet_parent_heading === null) {
                    if ($spreadsheet_parent_filename !== null || $spreadsheet_parent_heading !== null) {
                        attach_message('Must supply parent spreadsheet filename and parent heading or neither, in "' . $custom_field['trans_name'] . '".', 'warn', false, true);
                        $spreadsheet_parent_filename = null;
                        $spreadsheet_parent_heading = null;
                    }
                }

                $cpf_fields[$spreadsheet_heading] = [
                    'id' => $custom_field['id'],
                    'label' => get_translated_text($custom_field['cf_name'], $GLOBALS['FORUM_DB']),
                    'possible_fields' => ['field_' . strval($custom_field['id']), $spreadsheet_heading], // Form field names that this CPF may appear as (may not all be real CPFs)
                    'spreadsheet_filename' => $spreadsheet_filename,
                    'spreadsheet_heading' => $spreadsheet_heading,
                    'spreadsheet_parent_filename' => $spreadsheet_parent_filename,
                    'spreadsheet_parent_heading' => $spreadsheet_parent_heading,
                ];
            }
        }
    }
    $spreadsheet_structure['cpf_fields'] = $cpf_fields;

    return $spreadsheet_structure;
}

/**
 * Query the spreadsheet files.
 *
 * @param  ID_TEXT $spreadsheet_file Filename
 * @param  ?ID_TEXT $known_field_key Name of field we know (null: we know nothing special - i.e. no filtering)
 * @param  ?ID_TEXT $known_field_value Value of field we know (null: we know nothing special - i.e. no filtering)
 * @param  ?ID_TEXT $desired_field Name of field we want (null: all fields in an array)
 * @return array List of possibilities
 */
function get_spreadsheet_data_values(string $spreadsheet_file, ?string $known_field_key = null, ?string $known_field_value = null, ?string $desired_field = null) : array
{
    $map = [];
    if (($known_field_key !== null) && ($known_field_value !== null)) {
        $map[$known_field_key] = $known_field_value;
    }
    return get_spreadsheet_data_values__and($spreadsheet_file, $map, $desired_field);
}

/**
 * Query the spreadsheet files for multiple matching constraints at once.
 *
 * @param  ID_TEXT $spreadsheet_file Filename
 * @param  array $map Map of ANDd constraints
 * @param  ?ID_TEXT $desired_field Name of field we want (null: all fields in an array)
 * @return array List of possibilities
 */
function get_spreadsheet_data_values__and(string $spreadsheet_file, array $map, ?string $desired_field = null) : array
{
    $results = [];
    $spreadsheet_structure = get_nested_spreadsheet_structure();
    foreach ($spreadsheet_structure['spreadsheet_files'][$spreadsheet_file]['data'] as $row) {
        $okay = true;
        foreach ($map as $where_key => $where_value) {
            if ($row[$where_key] != $where_value) {
                $okay = false;
                break;
            }
        }
        if ($okay) {
            $results[] = ($desired_field === null) ? $row : $row[$desired_field];
        }
    }
    return array_unique($results);
}

/**
 * Get member CPFs against spreadsheet headings.
 *
 * @param  MEMBER $member_id Member ID
 * @return array Map of settings
 */
function get_members_spreadsheet_data_values(int $member_id) : array
{
    require_code('cns_members');
    $member_row = cns_get_custom_field_mappings($member_id);

    $out = [];

    $spreadsheet_structure = get_nested_spreadsheet_structure();
    foreach ($spreadsheet_structure['cpf_fields'] as $cpf_field) {
        $cpf_value = $member_row['field_' . strval($cpf_field['id'])];
        if (is_string($cpf_value)) {
            $out[$cpf_field['spreadsheet_heading']] = trim($cpf_value);
        }
    }

    return $out;
}
