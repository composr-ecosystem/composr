<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    early_access
 */

/**
 * Hook class.
 */
class Hook_privacy_early_access extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('early_access')) {
            return null;
        }

        return [
            'label' => 'early_access:EARLY_ACCESS_CODES',

            'description' => 'early_access:DESCRIPTION_PRIVACY_EARLY_ACCESS_CODES',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'early_access_codes' => [
                    'timestamp_field' => 'c_creation_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'c_created_by',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE | PRIVACY_METHOD__ANONYMISE,
                ],
            ],
        ];
    }

    /**
     * Serialise a row.
     *
     * @param  ID_TEXT $table_name Table name
     * @param  array $row Row raw from the database
     * @return array Row in a cleanly serialised format
     */
    public function serialise(string $table_name, array $row) : array
    {
        $ret = parent::serialise($table_name, $row);

        switch ($table_name) {
            case 'early_access_codes': // Join early_access_code_content
                require_code('content');

                $access_code_content = [];
                $rows = $GLOBALS['SITE_DB']->query_select('early_access_code_content', ['*'], ['a_access_code' => $row['c_access_code']]);
                foreach ($rows as $row) {
                    $row_i = $row;
                    list($title, , $info) = content_get_details($row['a_content_type'], $row['a_content_id'], false, true);
                    if ($title !== null) {
                        $row_i += [
                            'content_type__dereferenced' => do_lang($info['content_type_label']),
                            'content_title__dereferenced' => $title,
                        ];
                    }

                    $access_code_content[] = $row_i;
                }

                $ret['early_access_code_content'] = $access_code_content;
                break;
        }

        return $ret;
    }

    /**
     * Delete a row.
     *
     * @param  ID_TEXT $table_name Table name
     * @param  array $table_details Details of the table from the info function
     * @param  array $row Row raw from the database
     */
    public function delete(string $table_name, array $table_details, array $row)
    {
        switch ($table_name) {
            case 'early_access_codes': // Delete via the API
                require_code('early_access2');
                delete_early_access_code($row['c_access_code']);
                break;

            default:
                parent::delete($table_name, $table_details, $row);
                break;
        }
    }
}
