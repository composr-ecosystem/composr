<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_privacy_disastr extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('disastr')) {
            return null;
        }

        return [
            'label' => 'disastr:DISASTR_TITLE',

            'description' => 'disastr:DESCRIPTION_PRIVACY_DISASTR',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'members_diseases' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'member_id',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
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
            case 'members_diseases':
                $ret += [
                    'disease_id__dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('diseases', 'name', ['id' => $row['disease_id']]),
                ];
                break;
        }

        return $ret;
    }
}
