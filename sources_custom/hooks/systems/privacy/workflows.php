<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    workflows
 */

/**
 * Hook class.
 */
class Hook_privacy_workflows extends Hook_privacy_base
{
    /**
     * Find privacy details.
     *
     * @return ?array A map of privacy details in a standardised format (null: disabled)
     */
    public function info() : ?array
    {
        if (!addon_installed('workflows')) {
            return null;
        }

        return [
            'label' => 'workflows:WORKFLOWS',

            'description' => 'workflows:DESCRIPTION_PRIVACY_WORKFLOWS',

            'cookies' => [
            ],

            'positive' => [
            ],

            'general' => [
            ],

            'database_records' => [
                'workflow_content' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'original_submitter',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__ANONYMISE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
                ],
                'workflow_content_status' => [
                    'timestamp_field' => null,
                    'retention_days' => null,
                    'retention_handle_method' => PRIVACY_METHOD__LEAVE,
                    'owner_id_field' => 'approved_by',
                    'additional_member_id_fields' => [],
                    'ip_address_fields' => [],
                    'email_fields' => [],
                    'username_fields' => [],
                    'file_fields' => [],
                    'additional_anonymise_fields' => [],
                    'extra_where' => null,
                    'removal_default_handle_method' => PRIVACY_METHOD__ANONYMISE,
                    'removal_default_handle_method_member_override' => null,
                    'allowed_handle_methods' => PRIVACY_METHOD__ANONYMISE | PRIVACY_METHOD__DELETE,
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
            case 'workflow_content':
                require_code('content');
                list($title, , $info) = content_get_details($row['content_type'], $row['content_id']);
                $ret += [
                    'content_type__dereferenced' => do_lang($info['content_type_label']),
                    'content_title__dereferenced' => $title,
                ];
                $workflow_name = $GLOBALS['SITE_DB']->query_select_value_if_there('workflows', 'workflow_name', ['id' => $row['workflow_id']]);
                if ($workflow_name !== null) {
                    $ret += [
                        'workflow_id__dereferenced' => get_translated_text($workflow_name),
                    ];
                }
                break;

            case 'workflow_content_status':
                $title = null;
                $type = null;
                $workflow_content_rows = $GLOBALS['SITE_DB']->query_select('workflow_content', ['*'], ['id' => $row['workflow_content_id']], '', 1);
                if (array_key_exists(0, $workflow_content_rows)) {
                    require_code('content');
                    list($title, , $info) = content_get_details($workflow_content_rows[0]['content_type'], $workflow_content_rows[0]['content_id']);
                    $type = do_lang($info['content_type_label']);
                }
                $ret += [
                    'workflow_content_type__dereferenced' => $type,
                    'workflow_content_title__dereferenced' => $title,
                ];
                $workflow_approval_name = $GLOBALS['SITE_DB']->query_select_value_if_there('workflow_approval_points', 'workflow_approval_name', ['id' => $row['id']]);
                if ($workflow_approval_name !== null) {
                    $ret += [
                        'workflow_approval_point_id__dereferenced' => get_translated_text($workflow_approval_name),
                    ];
                }
                break;
        }

        return $ret;
    }
}
