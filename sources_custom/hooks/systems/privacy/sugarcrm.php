<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_privacy_sugarcrm extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        require_lang('sugarcrm');

        return [
            'label' => 'sugarcrm:SUGARCRM',

            'description' => 'sugarcrm:DESCRIPTION_PRIVACY_SUGARCRM',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
                [
                    'heading' => do_lang('INFORMATION_STORAGE'),
                    'action' => do_lang_tempcode('PRIVACY_ACTION_sugarcrm'),
                    'reason' => do_lang_tempcode('PRIVACY_REASON_sugarcrm'),
                ],
            ],

            'database_records' => [
                'mail_opt_sync_queue' => [
                    'timestamp_field' => 'add_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => null,
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => ['email_address'],
                    'username_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ]
            ],
        ];
    }
}
