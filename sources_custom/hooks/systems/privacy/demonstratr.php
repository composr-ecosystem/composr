<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    demonstratr
 */

/**
 * Hook class.
 */
class Hook_privacy_demonstratr extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('demonstratr')) {
            return null;
        }

        return [
            'label' => 'demonstratr:DEMONSTRATR_SITES_INSTALLED',

            'description' => 'demonstratr:DESCRIPTION_PRIVACY_DEMONSTRATR_SITES_INSTALLED',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'sites' => [
                    'timestamp_field' => 's_add_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 's_member_id',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__ANONYMISE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
                ],
                'sites_email' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => null,
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => ['s_email_from', 's_email_to'],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
    }
}
