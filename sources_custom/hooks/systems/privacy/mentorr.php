<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
            'label' => 'mentorr:MENTORSHIP',

            'description' => 'mentorr:DESCRIPTION_PRIVACY_MENTORSHIP',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'members_mentors' => [
                    'timestamp_field' => 'date_and_time',
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'mentor_member_id',
                    'additional_member_id_fields' => ['member_id'],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__DELETE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__DELETE,
                ],
            ],
        ];
    }

    /**
     * Determine if, given the provided criteria and content, we have high confidence this individual owns the content.
     * You should run fill_in_missing_privacy_criteria before running this.
     *
     * @param  ID_TEXT $table_name The name of the database table
     * @param  array $table_details The details of the table from the privacy hook; can be modified for special behaviour
     * @param  array $row The raw database row
     * @param  ?MEMBER $member_id The given member ID in search criteria (null: not provided)
     * @param  string $username The given username in search criteria (blank: not provided)
     * @param  string $email_address The given email address in search criteria (blank: not provided)
     * @return boolean Whether we are confident this individual owns this content
     */
    public function is_owner(string $table_name, array $table_details, array $row, ?int $member_id, string $username, string $email_address) : bool
    {
        if ($table_name != 'members_mentors') {
            return parent::is_owner($table_name, $table_details, $row, $member_id, $username, $email_address);
        }

        $is_owner_1 = parent::is_owner($table_name, $table_details, $row, $member_id, $username, $email_address);
        if ($is_owner_1) {
            return true;
        }

        // The mentee can be considered owner as well, so check member ID against additional_member_id_fields
        if (($member_id !== null) && (!is_guest($member_id))) {
            foreach ($table_details['additional_member_id_fields'] as $member_field) {
                if (($row[$member_field] == $member_id)) {
                    return true;
                }
            }
        }

        return false;
    }
}
