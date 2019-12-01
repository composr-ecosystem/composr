<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function info()
    {
        if (!addon_installed('composr_tutorials')) {
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
                'tutorials_external' => [
                    'timestamp_field' => 't_add_date',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['t_submitter'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => ['t_author'],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
            ],
        ];
    }
}
