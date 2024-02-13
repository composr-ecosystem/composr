<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_privacy_community_billboard extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('community_billboard')) {
            return null;
        }

        return [
            'label' => 'community_billboard:COMMUNITY_BILLBOARD',

            'description' => 'community_billboard:DESCRIPTION_PRIVACY_COMMUNITY_BILLBOARD',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'community_billboard' => [
                    'timestamp_field' => 'order_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'member_id',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
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
        if (!addon_installed('community_billboard')) {
            return;
        }

        require_lang('community_billboard');

        switch ($table_name) {
            case 'community_billboard':
                require_code('community_billboard');
                delete_community_billboard_message($row['id']);
                break;

            default:
                parent::delete($table_name, $table_details, $row);
                break;
        }
    }
}
