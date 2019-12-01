<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_privacy_composr_homesite extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info()
    {
        if (!addon_installed('composr_homesite')) {
            return null;
        }

        return [
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
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['s_member_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
                'sites_email' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => ['s_email_to'],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
            ],
        ];
    }
}
