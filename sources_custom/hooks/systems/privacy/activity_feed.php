<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_privacy_activity_feed extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('activity_feed')) {
            return null;
        }

        return [
            'label' => 'ACTIVITY',

            'description' => 'activity_feed:DESCRIPTION_PRIVACY_ACTIVITY',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'activities' => [
                    'timestamp_field' => 'a_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'a_member_id',
                    'additional_member_id_fields' => ['a_also_involving'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE
                ],
            ],
        ];
    }
}
