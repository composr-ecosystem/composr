<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_privacy_referrals extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('referrals')) {
            return null;
        }

        return [
            'label' => 'referrals:REFERRALS',

            'description' => 'referrals:DESCRIPTION_PRIVACY_REFERRALS',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'referees_qualified_for' => [
                    'timestamp_field' => 'q_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'q_referrer',
                    'additional_member_id_fields' => ['q_referee'],
                    'ip_address_fields' => [],
                    'email_fields' => ['q_email_address'],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
                'referrer_override' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'o_referrer',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
    }
}
