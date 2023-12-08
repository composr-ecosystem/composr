<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nested_cpf_csv_lists
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__nested_csv()
{
    require_lang('nested_csv');
}

/**
 * Get the CSV/CPF structure for the site.
 *
 * @return array Structured data about CSV files/CPFs
 */
function get_nested_csv_structure()
{
    if (get_forum_type() == 'cns') {
        require_code('cns_members');
        require_code('cns_groups');

        $_custom_fields = cns_get_all_custom_fields_match(cns_get_all_default_groups(true));
    } else {
        $_custom_fields = array();
    }

    static $csv_structure = array();
    if ($csv_structure !== array()) {
        return $csv_structure;
    }

    $csv_files = array();
    if (file_exists(get_custom_file_base() . '/private_data')) {
        $dh = @opendir(get_custom_file_base() . '/private_data');
        while (($csv_filename = readdir($dh)) !== false) {
            if (substr($csv_filename, -4) != '.csv') {
                continue;
            }

            $myfile = @fopen(get_file_base() . '/private_data/' . $csv_filename, 'rb');
            if ($myfile === false) {
                warn_exit('The CSV file "' . $csv_filename . '" could not be opened.');
            }

            $header_row = fgetcsv($myfile, 10000);

            if ($header_row !== false) {
                // Initialise data for this forthcoming $csv_files entry
                $csv_file = array();
                $csv_file['headings'] = array();    // Unordered headings                               ?=>heading
                $csv_file['data'] = array();        // Array of rows                                    ?=>array(cols,col2,col3,...)

                // Fill out 'headings'
                $csv_file['headings'] = $header_row;

                // Fill out 'data' and 'lists'
                $vl_temp = fgetcsv($myfile, 10000);
                if ($vl_temp !== false) {
                    $vl_temp = array_map('trim', $vl_temp);
                }
                while ($vl_temp !== false) { // If there's nothing past the headings this loop never executes
                    while (count($vl_temp) < count($header_row)) {
                        $vl_temp[] = ''; // Pad out row to be complete
                    }

                    $new_entry = array();
                    foreach ($header_row as $j => $heading) {
                        $new_entry[$heading] = $vl_temp[$j];
                    }
                    $csv_file['data'][] = $new_entry;

                    $vl_temp = fgetcsv($myfile, 10000);
                    if ($vl_temp !== false) {
                        $vl_temp = array_map('trim', $vl_temp);
                    }
                }

                $csv_files[$csv_filename] = $csv_file;
            } else {
                warn_exit('No header row found for "' . $csv_filename . '".');
            }

            fclose($myfile);
        }
    }
    $csv_structure['csv_files'] = $csv_files;

    $cpf_fields = array();
    foreach ($_custom_fields as $cf => $custom_field) {
        if (($custom_field['cf_type'] == 'list') || ($custom_field['cf_type'] == 'list_multi')) {
            $_value = explode('|', $custom_field['cf_default']); // $_value will come up as file|heading(optional)|order(optional)
            $csv_filename = $_value[0];

            if (substr(strtolower($csv_filename), -4) == '.csv') {
                if (!isset($_value[1])) {
                    $_value[1] = null;
                }
                if (!isset($_value[2])) {
                    $_value[2] = null;
                }
                if (!isset($_value[3])) {
                    $_value[3] = null;
                }
                $csv_heading = $_value[1];
                $csv_parent_filename = $_value[2];
                $csv_parent_heading = $_value[3];

                if (!array_key_exists($csv_filename, $csv_files)) { // Check referenced filename exists
                    if (!file_exists(get_custom_file_base() . '/private_data')) {
                        attach_message('Missing private_data directory for CSV file storage.', 'warn');
                        break;
                    }

                    attach_message('Specified CSV file, ' . $csv_filename . ', not found for "' . $custom_field['trans_name'] . '".', 'warn');
                    continue;
                }
                if (!in_array($csv_heading, $csv_files[$csv_filename]['headings'])) { // Check referenced heading exists
                    attach_message('Specified heading,' . $csv_heading . ' , not found in CSV file for "' . $custom_field['trans_name'] . '".', 'warn');
                    continue;
                }
                if ((!is_null($csv_parent_filename)) && (!is_null($csv_parent_heading))) {
                    if (!array_key_exists($csv_parent_filename, $csv_files)) { // Check referenced filename exists
                        attach_message('Specified parent CSV file, ' . $csv_parent_filename . ', not found for "' . $custom_field['trans_name'] . '".', 'warn');
                        $csv_parent_filename = null;
                        $csv_parent_heading = null;
                    }
                    if (!in_array($csv_parent_heading, $csv_files[$csv_parent_filename]['headings'])) { // Check referenced heading exists
                        attach_message('Specified parent heading not found in CSV file for "' . $custom_field['trans_name'] . '".', 'warn');
                        $csv_parent_filename = null;
                        $csv_parent_heading = null;
                    }
                }

                if (isset($cpf_fields[$csv_heading])) {
                    attach_message('Specified heading,' . $csv_heading . ' ,used for more than one field.', 'warn');
                    continue;
                }

                if ($csv_parent_filename === null || $csv_parent_heading === null) {
                    if ($csv_parent_filename !== null || $csv_parent_heading !== null) {
                        attach_message('Must supply parent CSV filename and parent heading or neither, in "' . $custom_field['trans_name'] . '".', 'warn');
                        $csv_parent_filename = null;
                        $csv_parent_heading = null;
                    }
                }

                $cpf_fields[$csv_heading] = array(
                    'id' => $custom_field['id'],
                    'label' => get_translated_text($custom_field['cf_name'], $GLOBALS['FORUM_DB']),
                    'possible_fields' => array('field_' . strval($custom_field['id']), $csv_heading), // Form field names that this CPF may appear as (may not all be real CPFs)
                    'csv_filename' => $csv_filename,
                    'csv_heading' => $csv_heading,
                    'csv_parent_filename' => $csv_parent_filename,
                    'csv_parent_heading' => $csv_parent_heading,
                );
            }
        }
    }
    $csv_structure['cpf_fields'] = $cpf_fields;

    return $csv_structure;
}

/**
 * Query the CSV files.
 *
 * @param  ID_TEXT $csv_file Filename
 * @param  ?ID_TEXT $known_field_key Name of field we know (null: we know nothing special - i.e. no filtering)
 * @param  ?ID_TEXT $known_field_value Value of field we know (null: we know nothing special - i.e. no filtering)
 * @param  ?ID_TEXT $desired_field Name of field we want (null: all fields in an array)
 * @return array List of possibilities
 */
function get_csv_data_values($csv_file, $known_field_key = null, $known_field_value = null, $desired_field = null)
{
    $map = array();
    if ((!is_null($known_field_key)) && (!is_null($known_field_value))) {
        $map[$known_field_key] = $known_field_value;
    }
    return get_csv_data_values__and($csv_file, $map, $desired_field);
}

/**
 * Query the CSV files for multiple matching constraints at once.
 *
 * @param  ID_TEXT $csv_file Filename
 * @param  array $map Map of ANDd constraints
 * @param  ?ID_TEXT $desired_field Name of field we want (null: all fields in an array)
 * @return array List of possibilities
 */
function get_csv_data_values__and($csv_file, $map, $desired_field = null)
{
    $results = array();
    $csv_structure = get_nested_csv_structure();
    foreach ($csv_structure['csv_files'][$csv_file]['data'] as $row) {
        $okay = true;
        foreach ($map as $where_key => $where_value) {
            if ($row[$where_key] != $where_value) {
                $okay = false;
                break;
            }
        }
        if ($okay) {
            $results[] = is_null($desired_field) ? $row : $row[$desired_field];
        }
    }
    return array_unique($results);
}

/**
 * Get member CPFs against CSV headings.
 *
 * @param  MEMBER $member_id Member ID
 * @return array Map of settings
 */
function get_members_csv_data_values($member_id)
{
    require_code('cns_members');
    $member_row = cns_get_custom_field_mappings($member_id);

    $out = array();

    $csv_structure = get_nested_csv_structure();
    foreach ($csv_structure['cpf_fields'] as $cpf_field) {
        $cpf_value = $member_row['field_' . strval($cpf_field['id'])];
        if (is_string($cpf_value)) {
            $out[$cpf_field['csv_heading']] = trim($cpf_value);
        }
    }

    return $out;
}
