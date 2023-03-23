<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
                    'member_id_fields' => ['q_referee', 'q_referrer'],
                    'ip_address_fields' => [],
                    'email_fields' => ['q_email_address'],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
                'referrer_override' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'member_id_fields' => ['o_referrer'],
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

    /**
     * Serialise a row.
     *
     * @param  ID_TEXT $table_name Table name
     * @param  array $row Row raw from the database
     * @return array Row in a cleanly serialised format
     */
    public function serialise(string $table_name, array $row) : array
    {
        $ret = parent::serialise($table_name, $row);

        switch ($table_name) {
            case 'referees_qualified_for':
                $ret += [
                    'q_referee__dereferenced' => $GLOBALS['FORUM_DRIVER']->get_username($row['q_referee']),
                    'q_referrer__dereferenced' => $GLOBALS['FORUM_DRIVER']->get_username($row['q_referrer']),
                ];
                break;
        }

        return $ret;
    }
}
