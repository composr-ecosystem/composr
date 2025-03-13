<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_privacy_cms_homesite extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        require_lang('cms_homesite');

        return [
            'label' => 'cms_homesite:CMS_SITES_INSTALLED',

            'description' => 'cms_homesite:DESCRIPTION_PRIVACY_CMS_SITES_INSTALLED',

            'cookies' => [
            ],

            'positive' => [
                [
                    'heading' => do_lang('PRIVACY_DATA_BREACH'),
                    'explanation' => do_lang_tempcode('PRIVACY_DATA_BREACH_EXPLANATION', escape_html(get_site_name())),
                ]
            ],

            'general' => [
            ],

            'database_records' => [
                'telemetry_errors' => [
                    'timestamp_field' => 'e_last_date_and_time',
                    'retention_days' => 90,
                    'retention_handle_method' => PRIVACY_METHOD__DELETE,
                    'owner_id_field' => null,
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => ['e_error_message'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
                ],
                'telemetry_sites' => [
                    'timestamp_field' => 'add_date_and_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => null,
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => ['website_url', 'website_name', 'software_version'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
                'telemetry_stats' => [
                    'timestamp_field' => 'date_and_time',
                    'retention_days' => 365,
                    'retention_handle_method' => PRIVACY_METHOD__DELETE,
                    'owner_id_field' => null,
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => ['count_members', 'count_daily_hits', 'software_version'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
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
        require_lang('chat');

        switch ($table_name) {
            case 'telemetry_sites':
                // If deleting a site, its stats and errors should also be deleted (a deletion of the site means removal from telemetry completely)
                parent::delete($table_name, $table_details, $row);
                $GLOBALS['SITE_DB']->query_delete('telemetry_stats', ['s_site' => $row['id']]);
                $GLOBALS['SITE_DB']->query_delete('telemetry_errors', ['e_site' => $row['id']]);
                break;

            default:
                parent::delete($table_name, $table_details, $row);
                break;
        }
    }
}
