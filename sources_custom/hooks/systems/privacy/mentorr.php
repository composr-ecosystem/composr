<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    mentorr
 */

/**
 * Hook class.
 */
class Hook_privacy_mentorr extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('mentorr')) {
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
                'members_mentors' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'member_id_fields' => ['member_id', 'mentor_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
    }
}
