<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_privacy_patreon extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('patreon')) {
            return null;
        }

        return [
            'label' => 'patreon:PATREON',

            'description' => 'patreon:DESCRIPTION_PRIVACY_PATREON',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'patreon_patrons' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'p_member_id',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'additional_anonymise_fields' => ['p_name'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
    }
}
