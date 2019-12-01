<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_privacy_buildr extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info()
    {
        if (!addon_installed('buildr')) {
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
                'w_inventory' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['item_owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_delete,
                ],
                'w_itemdef' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
                'w_items' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['copy_owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_delete,
                ],
                'w_members' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_delete,
                ],
                'w_messages' => [
                    'timestamp_field' => 'm_datetime',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['destination', 'originator_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
                'w_portals' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
                'w_rooms' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
                ],
                'w_travelhistory' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['member_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_delete,
                    'allowed_handle_methods' => PRIVACY_METHOD_delete,
                ],
                'w_realms' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD_leave,
                    'member_id_fields' => ['owner'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD_anonymise,
                    'allowed_handle_methods' => PRIVACY_METHOD_anonymise | PRIVACY_METHOD_delete,
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
            case 'w_messages':
                $ret += [
                    'location__room_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_realm' => $row['location_realm'], 'location_x' => $row['location_x'], 'location_y' => $row['location_y']]),
                    'location__realm_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'name', ['id' => $row['location_realm']]),
                ];
                break;

            case 'w_portals':
                $ret += [
                    'start_location__room_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_realm' => $row['start_location_realm'], 'location_x' => $row['start_location_x'], 'location_y' => $row['start_location_y']]),
                    'start_location__realm_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'name', ['id' => $row['start_location_realm']]),
                    'end_location__room_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_realm' => $row['end_location_realm'], 'location_x' => $row['end_location_x'], 'location_y' => $row['end_location_y']]),
                    'end_location__realm_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'name', ['id' => $row['end_location_realm']]),
                ];
                break;

            case 'w_rooms':
                $ret += [
                    'location__realm_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'name', ['id' => $row['location_realm']]),
                ];
                break;

            case 'w_travelhistory':
                $ret += [
                    'location__room_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_rooms', 'name', ['location_realm' => $row['realm'], 'location_x' => $row['x'], 'location_y' => $row['y']]),
                    'location__realm_dereferenced' => $GLOBALS['SITE_DB']->query_select_value_if_there('w_realms', 'name', ['id' => $row['realm']]),
                ];
                break;
        }

        return $ret;
    }

    /**
     * Delete a row.
     *
     * @param  ID_TEXT $table_name Table name
     * @param  array $row Row raw from the database
     */
    public function delete($table_name, $row)
    {
        require_lang('buildr');

        switch ($table_name) {
            case 'w_itemdef':
                require_code('buildr');
                require_code('buildr_action');
                delete_item_wrap($row['name']);
                break;

            case 'w_members':
                $GLOBALS['SITE_DB']->query_delete('w_inventory', ['item_owner' => $row['id']]);
                $GLOBALS['SITE_DB']->query_delete('w_items', ['copy_owner' => $row['id']]);
                $GLOBALS['SITE_DB']->query_delete('w_travelhistory', ['member_id' => $row['id']]);
                break;

            case 'w_rooms':
                require_code('buildr');
                require_code('buildr_action');
                delete_room($row['location_x'], $row['location_y'], $row['location_realm']);
                break;

            case 'w_realms':
                require_code('buildr');
                require_code('buildr_action');
                delete_realm($row['id']);
                break;

            default:
                parent::delete($table_name, $row);
                break;
        }
    }
}
