<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_privacy_giftr extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info()
    {
        if (!addon_installed('giftr')) {
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
                'members_gifts' => [
                    'timestamp_field' => 'add_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['to_member_id', 'from_member_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_delete,
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
    public function serialise($table_name, $row)
    {
        $ret = parent::serialise($table_name, $row);

        switch ($table_name) {
            case 'members_gifts':
                $ret += [
                    'gift_id__dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('giftr', 'name', ['id' => $row['gift_id']]),
                ];
                break;
        }

        return $ret;
    }
}
