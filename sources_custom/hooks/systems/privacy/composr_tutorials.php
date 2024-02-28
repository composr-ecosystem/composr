<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_privacy_composr_tutorials extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'label' => 'TUTORIALS',

            'description' => 'tutorials:DESCRIPTION_PRIVACY_TUTORIALS',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'tutorials_external' => [
                    'timestamp_field' => 't_add_date',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 't_submitter',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'additional_anonymise_fields' => ['t_author'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__ANONYMISE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
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
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        require_lang('tutorials');

        switch ($table_name) {
            case 'tutorials_external':
                parent::delete($table_name, $table_details, $row);
                $GLOBALS['SITE_DB']->query_delete('tutorials_external_tags', ['t_id' => $row['id']]);

                log_it('DELETE_TUTORIAL', strval($row['id']), $row['t_title']);

                @unlink(get_custom_file_base() . '/uploads/website_specific/tutorial_sigs.bin');
                break;

            default:
                parent::delete($table_name, $table_details, $row);
                break;
        }
    }
}
