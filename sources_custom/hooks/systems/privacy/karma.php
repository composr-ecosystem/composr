<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_privacy_karma extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('karma')) {
            return null;
        }

        return [
            'label' => 'karma:KARMA',

            'description' => 'karma:DESCRIPTION_PRIVACY_KARMA',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'karma' => [
                    'timestamp_field' => 'k_date_and_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'k_member_from',
                    'additional_member_id_fields' => ['k_member_to'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE | PRIVACY_METHOD__ANONYMISE,
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
            case 'karma':
                require_code('content');
                list($title, , $info) = content_get_details($row['k_content_type'], $row['k_content_id']);
                $ret += [
                    'content_type__dereferenced' => do_lang($info['content_type_label']),
                    'content_title__dereferenced' => $title,
                ];
                break;
        }

        return $ret;
    }
}
