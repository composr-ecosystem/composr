<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

// Built using this library: https://github.com/asakusuma/SugarCRM-REST-API-Wrapper-Class
// Dev guide: http://support.sugarcrm.com/Documentation/Sugar_Developer/Sugar_Developer_Guide_6.5/Application_Framework/Web_Services/

function init__sugarcrm()
{
    global $SUGARCRM;
    $SUGARCRM = null;

    require_lang('sugarcrm');
}

function sugarcrm_initialise_connection()
{
    global $SUGARCRM;

    if (!sugarcrm_configured()) {
        return false;
    }

    require_code('sugar_crm_lib');
    require_code('curl');

    $base_url = get_option('sugarcrm_base_url');
    $username = get_option('sugarcrm_username');
    $password = get_option('sugarcrm_password');

    $SUGARCRM = new SugarWrapper();

    $SUGARCRM->setUrl($base_url . '/service/v2/rest.php');
    $SUGARCRM->setUsername($username);
    $SUGARCRM->setPassword($password);

    try {
        if (!$SUGARCRM->connect()) {
            throw new Exception('Could not connect to SugarCRM');
        }
    } catch (Exception $e) {
        $SUGARCRM = null;

        require_code('failure');
        cms_error_log('SugarCRM: ' . $e->getMessage(), 'error_occurred_api');

        throw $e;
    }

    return true;
}

function sugarcrm_configured()
{
    $base_url = get_option('sugarcrm_base_url');
    $username = get_option('sugarcrm_username');

    return (!empty($base_url)) && (!empty($username));
}

function get_or_create_sugarcrm_account($company, $timestamp = null)
{
    global $SUGARCRM;

    if ($timestamp === null) {
        $timestamp = time();
    }

    $account_id = get_sugarcrm_account($company);

    if ($account_id === null) {
        $account_map = [
            ['name' => 'name', 'value' => $company],
            ['name' => 'date_entered', 'value' => timestamp_to_sugarcrm_date_string($timestamp)],
        ];
        sugarcrm_log_action('Accounts', [$account_map]);
        $response = $SUGARCRM->set(
            'Accounts',
            $account_map
        );
        $account_id = $response['id'];
    }

    return $account_id;
}

function get_sugarcrm_account($company)
{
    global $SUGARCRM;

    $response = $SUGARCRM->get(
        'Accounts',
        ['id'],
        [
            'where' => 'name=\'' . db_escape_string($company) . '\'',
        ]
    );

    if (isset($response[0])) {
        return $response[0]['id'];
    }
    return null;
}

function get_sugarcrm_contact($email_address, $account_id = null)
{
    global $SUGARCRM;

    //$where = 'email1=\'' . db_escape_string($email_address) . '\'';   Not queryable
    $where = "contacts.id IN (SELECT bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (eabr.email_address_id = ea.id) WHERE bean_module = 'Contacts' AND ea.email_address='" . db_escape_string($email_address) . "' AND eabr.deleted=0)";

    $response = $SUGARCRM->get(
        'Contacts',
        ['id', 'account_id', 'name', 'account_name'],
        [
            'where' => $where,
        ]
    );

    if ($account_id !== null) {
        // We have to do with filtering
        foreach ($response as $contact_details) {
            if ($contact_details['account_id'] === $account_id) {
                if (!isset($contact_details['account_name'])) {
                    $contact_details['account_name'] = $contact_details['name']; // For older versions of SugarCRM
                }

                return $contact_details;
            }
        }
        return null;
    }

    if (isset($response[0])) {
        return $response;
    }

    return null;
}

function deconstruct_long_name($username)
{
    $username = str_replace(['_', '-'], [' ', ' '], $username);

    if (strpos($username, ' ') === false) {
        $matches = [];
        $name_parts = [];
        $num_matches = preg_match_all('#([A-Z]+[^A-Z]*)#i', $username, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $name_parts[] = $matches[1][$i];
        }
    } else {
        $name_parts = explode(' ', $username);
    }

    $last_name = cms_mb_ucwords(trim(array_pop($name_parts), '0123456789'));
    $first_name = cms_mb_ucwords(implode(' ', $name_parts));

    return [$first_name, $last_name];
}

function timestamp_to_sugarcrm_date_string($timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function save_message_into_sugarcrm_as_configured($subject, $body, $from_email, $from_name, $attachments, $data, $posted_data, $timestamp = null)
{
    $sync_type = post_param_string('sugarcrm_messaging_sync_type', get_option('sugarcrm_messaging_sync_type'));
    $messaging_mappings = explode("\n", get_option('sugarcrm_messaging_mappings'));
    $ret = save_message_into_sugarcrm($sync_type, $messaging_mappings, $subject, $body, $from_email, $from_name, $attachments, $data, $posted_data, $timestamp);

    if ((get_option('sugarcrm_exclusive_messaging') == '1') || ((isset($posted_data['_sugarcrm_exclusive_messaging'])) && ($posted_data['_sugarcrm_exclusive_messaging'] == '1'))) {
        require_code('files2');
        clean_temporary_mail_attachments($attachments);
    }

    return $ret;
}

function save_composr_account_into_sugarcrm_as_configured($member_id, $timestamp = null)
{
    $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
    if ($username === null) {
        return null; // User already deleted
    }

    $email_address = $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id);

    $cpfs = read_composr_cpfs($member_id);

    $posted_data = $_POST + $_GET + $_COOKIE;

    $member_mappings = explode("\n", get_option('sugarcrm_member_mappings'));

    $sync_types = get_option('sugarcrm_member_sync_types');

    $contact_id = null;

    if (in_array($sync_types, ['contacts', 'both', 'both_guarded'])) {
        $contact_id = save_account_into_sugarcrm($member_id, $member_mappings, $username, null, null, $email_address, $cpfs, $posted_data, $timestamp);
    }

    if (in_array($sync_types, ['leads', 'leads_guarded', 'both', 'both_guarded'])) {
        // User metadata
        $attachments = [];
        if (addon_installed('securitylogging')) {
            require_code('lookup');
            $user_metadata_path = save_user_metadata(false, $member_id);
            $attachments[$user_metadata_path] = 'user_metadata.txt';
        }

        $body = do_lang('BODY_LEAD_FROM_ACCOUNT', $username);
        $data = [do_lang('AUTOMATIC_NOTE') => $body] + $cpfs;

        save_message_into_sugarcrm('leads', $member_mappings, '', $body, $email_address, $username, $attachments, $data, $posted_data, $timestamp, strpos($sync_types, '_guarded') !== false);

        require_code('files2');
        clean_temporary_mail_attachments($attachments);
    }

    return $contact_id;
}

function save_message_into_sugarcrm($sync_type, $mappings, $subject, $body, $from_email, $from_name, $attachments = [], $data = [], $posted_data = [], $timestamp = null, $guarded = false)
{
    /*
    Notes...

    When posting to SugarCRM API, SugarCRM won't do required-field validation.
    SugarCRM is basically a data dump at the low-level.
    Unrecognised values are silently-skipped.
    */

    global $SUGARCRM;

    if ($timestamp === null) {
        $timestamp = time();
    }

    $_sync_type = cms_ucfirst_ascii($sync_type);

    $lead_source = do_lang('DEFAULT_LEAD_SOURCE');

    // Metadata
    $data_extended = $data;
    foreach ($attachments as $file_path => $filename) {
        if ($filename == 'user_metadata.txt') {
            $metadata = json_decode(cms_file_get_contents_safe($file_path, FILE_READ_LOCK | FILE_READ_BOM), true);
            foreach ($metadata as $key => $val) {
                if (is_array($val)) {
                    if (!isset($val[0])) { // Not a list
                        $data_extended += $val;
                    }
                } else {
                    $data_extended[$key] = $val;
                }
            }
        }
    }

    // Find company name
    if (empty($data_extended['company'])) {
        $company = get_option('sugarcrm_default_company');
    } else {
        $company = $data_extended['company'];
    }
    unset($data['company']);

    // Find Contact (we will link to contact manually set up in SugarCRM or from a joined member - by binding to e-mail address as a key - or just won't find one which is fine, no auto-creation)
    $account_id = get_sugarcrm_account($company);
    if ($sync_type == 'leads') {
        $contact_details = get_sugarcrm_contact($from_email, $account_id);
        if (($contact_details === null) && ($account_id !== null)) {
            $contact_details = get_sugarcrm_contact($from_email); // Okay, we don't need to be so specific
        }
    }

    // Name fields
    list($first_name, $last_name) = deconstruct_long_name($from_name);

    // Create Case/Lead
    $sugarcrm_data = [
        'status' => ['name' => 'status', 'value' => 'New'],
    ];
    switch ($sync_type) {
        case 'cases':
            // Create Account if needed
            if ($account_id === null) {
                $account_id = get_or_create_sugarcrm_account($company, $timestamp);
            }

            $sugarcrm_data += [
                // These are for Case-only
                'account_id' => ['name' => 'account_id', 'value' => $account_id],
                'name' => ['name' => 'name', 'value' => or_unknown(($subject == '') ? $from_name : $subject)],
                'description' => ['name' => 'description', 'value' => $body],
                'priority' => ['name' => 'priority', 'value' => 'P2'],
                'date_entered' => ['name' => 'date_entered', 'value' => timestamp_to_sugarcrm_date_string($timestamp)],
            ];
            break;

        case 'leads':
            $sugarcrm_data += [
                // These are for Lead-only
                'account_name' => ['name' => 'account_name', 'value' => $company], // We don't use actual accounts for Leads, just a company name in a field named account_name
                'description' => ['name' => 'description', 'value' => $subject],
                'date_entered' => ['name' => 'date_entered', 'value' => timestamp_to_sugarcrm_date_string($timestamp)],

                'name' => ['name' => 'name', 'value' => or_unknown($from_name)], // SuiteCRM
                'first_name' => ['name' => 'first_name', 'value' => $first_name],
                'last_name' => ['name' => 'last_name', 'value' => or_unknown($last_name)],

                'email1' => ['name' => 'email1', 'value' => $from_email],

                'lead_source' => ['name' => 'lead_source', 'value' => $lead_source],
            ];
            break;
    }
    foreach ($mappings as $_mapping) {
        if (strpos($_mapping, '=') !== false) {
            list($mapping_from, $mapping_to) = array_map('trim', explode('=', $_mapping, 2));

            $matches = [];
            if (preg_match('#^\((.*)\)$#', $mapping_from, $matches) != 0) {
                $value = $matches[1];
            } elseif (preg_match('#^\[(.*)\]$#', $mapping_from, $matches) != 0) {
                $value = isset($posted_data[$matches[1]]) ? $posted_data[$matches[1]] : '';
            } else {
                $value = isset($data_extended[$mapping_from]) ? $data_extended[$mapping_from] : '';
                unset($data[$mapping_from]);
            }

            if ($value == '') {
                continue;
            }

            if ((isset($sugarcrm_data[$mapping_to])) && ($sugarcrm_data[$mapping_to]['value'] != '') && (!in_array($mapping_to, ['date_entered', 'priority', 'name', 'first_name', 'last_name', 'email1', 'lead_source']))) {
                if ($value != '') {
                    $label = post_param_string('label_for__' . $mapping_from, '');
                    if ($label != '') {
                        $value = $label . ': ' . $value;
                    }
                    $sugarcrm_data[$mapping_to]['value'] .= "\n\n" . $value;
                }
            } else {
                $sugarcrm_data[$mapping_to] = ['name' => $mapping_to, 'value' => $value];
            }
        }
    }
    if ($sync_type == 'leads') {
        // Any remaining fields should not be lost (for Cases though we put it all into 'description', as a case looks more like an e-mail, with a subject line and body)
        foreach ($data as $mapping_from => $value) {
            if ($value == '') {
                continue;
            }

            $label = post_param_string('label_for__' . $mapping_from, $mapping_from);
            if ((isset($sugarcrm_data['description'])) && ($sugarcrm_data['description']['value'] != '')) {
                $sugarcrm_data['description']['value'] .= "\n\n" . $label . ': ' . $value;
            } else {
                $sugarcrm_data['description'] = ['name' => 'description', 'value' => $label . ': ' . $value];
            }
        }

        $metadata_field = get_option('sugarcrm_lead_metadata_field');
        if (($metadata_field != '') && ($from_email != '')) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($from_email);
            if ($member_id !== null) {
                require_code('user_metadata_display');
                $metadata_url = generate_secure_user_metadata_display_url($member_id);
                $sugarcrm_data[$metadata_field] = ['name' => $metadata_field, 'value' => $metadata_url];
            }
        }

        // If there's an existing lead with this e-mail then put an addendum on the description to mention that
        /*$existing_leads = $SUGARCRM->search_by_module(        Opens too many files
            $from_email,
            ['Leads'],
            0,
            -1
        );
        $num_existing_leads = count($existing_leads['entry_list'][0]['records']);*/
        $existing_leads = $SUGARCRM->get(
            'Leads',
            ['id'],
            [
                'where' => 'leads.id in (SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address = \'' . db_escape_string($from_email) . '\')',
            ]
        );
        $num_existing_leads = count($existing_leads);
        if ($num_existing_leads > 0) {
            if ($guarded) {
                return false;
            }

            $sugarcrm_data['description']['value'] .= "\n\n" . do_lang('EXISTING_LEADS', integer_format($num_existing_leads, 0));
        }
    }
    sugarcrm_log_action($_sync_type, [array_values($sugarcrm_data)]);
    $response = $SUGARCRM->set(
        $_sync_type,
        array_values($sugarcrm_data)
    );
    $entity_id = $response['id'];

    // Create relationship between Contact (if exists) and Lead
    if ($sync_type == 'leads') {
        if (($from_email != '') && ($contact_details !== null)) {
            $contact_id = $contact_details[0]['id'];
            sugarcrm_log_action('set_relationship', ['Contacts', $contact_id, 'leads', [$entity_id]]);
            $SUGARCRM->set_relationship('Contacts', $contact_id, 'leads', [$entity_id]);
        }
    }

    // Create Contact underneath Case (for Lead it is part of the main set of Lead fields)
    if (($sync_type == 'cases') && ($last_name != '')) {
        $sugarcrm_data = [
            'account_id' => ['name' => 'account_id', 'value' => $account_id],
            'account_name' => ['name' => 'account_name', 'value' => $company],
            'first_name' => ['name' => 'first_name', 'value' => $first_name],
            'last_name' => ['name' => 'last_name', 'value' => $last_name],

            'emailAddress0' => ['name' => 'emailAddress0', 'value' => $from_email], // SuiteCRM
            'email1' => ['name' => 'email1', 'value' => $from_email],
        ];
        sugarcrm_log_action('Contacts', [array_values($sugarcrm_data)]);
        $response = $SUGARCRM->set(
            'Contacts',
            array_values($sugarcrm_data)
        );
        $contact_id = $response['id'];
        sugarcrm_log_action('set_relationship', [$_sync_type, $entity_id, 'contacts', [$contact_id]]);
        $SUGARCRM->set_relationship($_sync_type, $entity_id, 'contacts', [$contact_id]);
    }

    // Create Notes under Lead
    if ($sync_type == 'leads') {
        foreach ($attachments as $file_path => $filename) {
            $sugarcrm_data = [
                'name' => ['name' => 'name', 'value' => $filename],
                'description' => ['name' => 'description', 'value' => ''],
                'parent_type' => ['name' => 'parent_type', 'value' => 'Leads'],
                'parent_id' => ['name' => 'parent_id', 'value' => $entity_id],
            ];
            if ($contact_details !== null) {
                $sugarcrm_data['contact_id'] = ['name' => 'contact_id', 'value' => $contact_details[0]['id']];
            }
            sugarcrm_log_action('Notes', [array_values($sugarcrm_data)]);
            $response = $SUGARCRM->set(
                'Notes',
                array_values($sugarcrm_data)
            );
            $note_id = $response['id'];
            sugarcrm_log_action('set_note_attachment', [$note_id, base64_encode(cms_file_get_contents_safe($file_path, FILE_READ_LOCK)), $filename]);
            $SUGARCRM->set_note_attachment($note_id, base64_encode(cms_file_get_contents_safe($file_path, FILE_READ_LOCK)), $filename);
        }
    }

    // Create Documents underneath Case
    if ($sync_type == 'cases') {
        foreach ($attachments as $file_path => $filename) {
            $sugarcrm_data = [
                'document_name' => ['name' => 'document_name', 'value' => $filename],
                'revision' => ['name' => 'revision', 'value' => '1'],
            ];
            sugarcrm_log_action('Documents', [array_values($sugarcrm_data)]);
            $response = $SUGARCRM->set(
                'Documents',
                array_values($sugarcrm_data)
            );
            $document_id = $response['id'];
            sugarcrm_log_action('set_document_revision', [$document_id, $filename, $file_path, '1']);
            $SUGARCRM->set_document_revision($document_id, $filename, $file_path, '1');
            sugarcrm_log_action('set_relationship', [$_sync_type, $entity_id, 'documents', [$document_id]]);
            $SUGARCRM->set_relationship($_sync_type, $entity_id, 'documents', [$document_id]);
        }
    }

    return true;
}

function sugarcrm_log_action($action_type, $params)
{
    $log_path = get_custom_file_base() . '/data_custom/sugarcrm.log';
    if ((file_exists($log_path)) && (is_writable($log_path))) {
        require_code('files');
        $logfile = cms_fopen_text_write($log_path, true, 'ab');
        fwrite($logfile, "\n" . loggable_date() . ' -- ' . $action_type . '... ' . json_encode($params));
        flock($logfile, LOCK_UN);
        fclose($logfile);
    }
}

function save_account_into_sugarcrm($member_id, $mappings, $username, $first_name, $last_name, $email_address, $data, $posted_data, $timestamp = null)
{
    global $SUGARCRM;

    if ($timestamp === null) {
        $timestamp = time();
    }

    $lead_source = do_lang('DEFAULT_LEAD_SOURCE');

    $company_field = get_option('sugarcrm_composr_company_field');

    $company = isset($data[$company_field]) ? $data[$company_field] : get_option('sugarcrm_default_company');

    if ($username === null) {
        $username = $first_name . ' ' . $last_name;
    } else {
        list($_first_name, $_last_name) = deconstruct_long_name($username);
        if ($first_name === null) {
            $first_name = $_first_name;
        }
        if ($last_name === null) {
            $last_name = $_last_name;
        }
    }

    // Find/create Account
    $account_id = get_or_create_sugarcrm_account($company, $timestamp);

    // Find/create Contact
    $contact_details = get_sugarcrm_contact($email_address, $account_id);
    if ($contact_details === null) {
        $sugarcrm_data = [
            'account_id' => ['name' => 'account_id', 'value' => $account_id],
            'date_entered' => ['name' => 'date_entered', 'value' => timestamp_to_sugarcrm_date_string($timestamp)],

            'name' => ['name' => 'name', 'value' => $username], // SuiteCRM
            'first_name' => ['name' => 'first_name', 'value' => $first_name],
            'last_name' => ['name' => 'last_name', 'value' => $last_name],

            'emailAddress0' => ['name' => 'emailAddress0', 'value' => $email_address], // SuiteCRM
            'email1' => ['name' => 'email1', 'value' => $email_address],

            'lead_source' => ['name' => 'lead_source', 'value' => $lead_source],
        ];

        $metadata_field = get_option('sugarcrm_contact_metadata_field');
        if ($metadata_field != '') {
            require_code('user_metadata_display');
            $metadata_url = generate_secure_user_metadata_display_url($member_id);
            $sugarcrm_data[$metadata_field] = ['name' => $metadata_field, 'value' => $metadata_url];
        }

        foreach ($mappings as $_mapping) {
            if (strpos($_mapping, '=') !== false) {
                list($mapping_from, $mapping_to) = array_map('trim', explode('=', $_mapping, 2));

                $matches = [];
                if (preg_match('#^\((.*)\)$#', $mapping_from, $matches) != 0) {
                    $value = $matches[1];
                } elseif (preg_match('#^\[(.*)\]$#', $mapping_from, $matches) != 0) {
                    $value = isset($posted_data[$matches[1]]) ? $posted_data[$matches[1]] : '';
                } else {
                    $value = isset($data[$mapping_from]) ? $data[$mapping_from] : '';
                }

                if ($value == '') {
                    continue;
                }

                $sugarcrm_data[$mapping_to] = ['name' => $mapping_to, 'value' => $value];
            }
        }

        sugarcrm_log_action('Contacts', [array_values($sugarcrm_data)]);
        $response = $SUGARCRM->set(
            'Contacts',
            array_values($sugarcrm_data)
        );
        $contact_id = $response['id'];
    } else {
        $contact_id = $contact_details[0]['id'];
    }
    return $contact_id;
}

function or_unknown($str)
{
    return empty($str) ? do_lang('UNKNOWN') : $str;
}

function sync_contact_metadata_into_sugarcrm()
{
    $metadata_field = get_option('sugarcrm_contact_metadata_field');
    if ($metadata_field == '') {
        // Not configured
        return;
    }

    $company_field = get_option('sugarcrm_composr_company_field');

    global $SUGARCRM;

    require_code('user_metadata_display');

    // Find all local members with an e-mail address
    $sql = 'SELECT id,m_email_address FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . db_string_not_equal_to('m_email_address', '');
    $sql .= ' ORDER BY id';
    $start = 0;
    $max = 100;
    do {
        $rows = $GLOBALS['FORUM_DB']->query($sql, $max, $start);

        foreach ($rows as $row) {
            // For each member, write the metadata URL into SugarCRM

            $cpfs = read_composr_cpfs($row['id']);
            $company = isset($cpfs[$company_field]) ? $cpfs[$company_field] : get_option('sugarcrm_default_company');
            $account_id = get_sugarcrm_account($company);
            $contact_details = get_sugarcrm_contact($row['m_email_address'], $account_id);
            if (($contact_details === null) && ($account_id !== null)) {
                $contact_details = get_sugarcrm_contact($row['m_email_address']); // Okay, we don't need to be so specific
            }

            if ($contact_details !== null) {
                $metadata_url = generate_secure_user_metadata_display_url($row['id']);

                $sugarcrm_data = [
                    ['name' => 'id', 'value' => $contact_details[0]['id']],
                    ['name' => $metadata_field, 'value' => $metadata_url],
                ];
                sugarcrm_log_action('Contacts', [array_values($sugarcrm_data)]);
                $response = $SUGARCRM->set(
                    'Contacts',
                    array_values($sugarcrm_data)
                );
            }
        }

        $start += 100;
    } while (count($rows) == $max);
}

function sync_lead_metadata_into_sugarcrm()
{
    $metadata_field = get_option('sugarcrm_lead_metadata_field');
    if ($metadata_field == '') {
        // Not configured
        return;
    }

    global $SUGARCRM;

    require_code('user_metadata_display');

    // Find all local members with an e-mail address
    $sql = 'SELECT id,m_email_address FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE ' . db_string_not_equal_to('m_email_address', '');
    $sql .= ' ORDER BY id';
    $start = 0;
    $max = 100;
    do {
        $rows = $GLOBALS['FORUM_DB']->query($sql, $max, $start);

        foreach ($rows as $row) {
            // For each member, write the metadata URL into SugarCRM

            $where = "leads.id IN (SELECT bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (eabr.email_address_id = ea.id) WHERE bean_module = 'Leads' AND ea.email_address='" . db_escape_string($row['m_email_address']) . "' AND eabr.deleted=0) AND (leads.status='New' OR leads.status='Assigned') AND (leads." . $metadata_field . "='' OR leads." . $metadata_field . " IS NULL)";

            $response = $SUGARCRM->get(
                'Leads',
                ['id'],
                [
                    'where' => $where,
                ]
            );

            foreach ($response as $lead) {
                $metadata_url = generate_secure_user_metadata_display_url($row['id']);

                $sugarcrm_data = [
                    ['name' => 'id', 'value' => $lead['id']],
                    ['name' => $metadata_field, 'value' => $metadata_url],
                ];
                sugarcrm_log_action('Leads', [array_values($sugarcrm_data)]);
                $response = $SUGARCRM->set(
                    'Leads',
                    array_values($sugarcrm_data)
                );
            }
        }

        $start += 100;
    } while (count($rows) == $max);
}

function sync_newsletter_opt_into_sugarcrm()
{
    global $SUGARCRM;
    static $error_count = 0;

    $start = 0;
    $max = 100;
    do {
        $rows = $GLOBALS['FORUM_DB']->query_select('mail_opt_sync_queue', ['*'], [], ' AND processed_time IS NULL', $max, $start);

        foreach ($rows as $row) {
            $opt_out = ($row['opt'] != 'opt-in');

            // Find the e-mail address id
            $response = $SUGARCRM->get(
                'EmailAddresses',
                ['id'],
                [
                    'where' => "email_address='" . db_escape_string($row['email_address']) . "'",
                ]
            );
            if (!is_array($response) || !isset($response[0])) {
                sugarcrm_log_action('GET EmailAddresses', [$response]);
                if (_sugarcrm_opt_sync_error($error_count)) {
                    return;
                } else {
                    continue;
                }
            } else {
                $email_id = $response[0]['id'];
            }

            // Next, process the opt-in / opt-out
            $sugarcrm_data = [
                ['name' => 'id', 'value' => $email_id],
                ['name' => 'opt_out', 'value' => $opt_out ? 1 : 0],
                ['name' => 'confirm_opt_in', 'value' => $opt_out ? 'not-opt-in' : 'opt-in'],
            ];
            sugarcrm_log_action('EmailAddresses', [array_values($sugarcrm_data)]);
            $response = $SUGARCRM->set(
                'EmailAddresses',
                array_values($sugarcrm_data),
            );
            if (!is_array($response) || !isset($response['id'])) {
                sugarcrm_log_action('SET EmailAddresses', [$response]);
                if (_sugarcrm_opt_sync_error($error_count)) {
                    return;
                } else {
                    continue;
                }
            }

            $message = 'Automated message: The website processed an ' . $row['opt'] . ' for this e-mail address.';
            $contact_details = get_sugarcrm_contact($row['email_address']);

            // Add a note onto a Lead if there is one
            $existing_leads = $SUGARCRM->get(
                'Leads',
                ['id'],
                [
                    'where' => 'leads.id in (SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address = \'' . db_escape_string($row['email_address']) . '\')',
                ]
            );
            if (!is_array($existing_leads)) {
                sugarcrm_log_action('GET Leads', [$existing_leads]);
                if (_sugarcrm_opt_sync_error($error_count)) {
                    return;
                } else {
                    continue;
                }
            }
            foreach ($existing_leads as $lead) {
                $sugarcrm_data = [
                    'name' => ['name' => 'name', 'value' => 'Mailing-list ' . $row['opt']],
                    'description' => ['name' => 'description', 'value' => $message],
                    'parent_type' => ['name' => 'parent_type', 'value' => 'Leads'],
                    'parent_id' => ['name' => 'parent_id', 'value' => $lead['id']],
                ];
                sugarcrm_log_action('Notes', [array_values($sugarcrm_data)]);
                $response = $SUGARCRM->set(
                    'Notes',
                    array_values($sugarcrm_data)
                );
                if (!is_array($response) || (!isset($response['id']))) {
                    sugarcrm_log_action('SET Notes', [$response]);
                    if (_sugarcrm_opt_sync_error($error_count)) {
                        return;
                    } else {
                        continue;
                    }
                }
            }

            // Add a History note onto Contacts if there is any
            if ($contact_details !== null) {
                foreach ($contact_details as $contact) {
                    $sugarcrm_data = [
                        'name' => ['name' => 'name', 'value' => 'Mailing-list ' . $row['opt']],
                        'description' => ['name' => 'description', 'value' => $message],
                        'parent_type' => ['name' => 'parent_type', 'value' => 'Contacts'],
                        'contact_id' => ['name' => 'contact_id', 'value' => $contact['id']],
                    ];
                    sugarcrm_log_action('Notes', [array_values($sugarcrm_data)]);
                    $response = $SUGARCRM->set(
                        'Notes',
                        array_values($sugarcrm_data)
                    );
                    if (!is_array($response) || (!isset($response['id']))) {
                        sugarcrm_log_action('SET Notes', [$response]);
                        if (_sugarcrm_opt_sync_error($error_count)) {
                            return;
                        } else {
                            continue;
                        }
                    }
                }
            }

            // All is well; mark as processed
            $GLOBALS['SITE_DB']->query_update('mail_opt_sync_queue', ['processed_time' => time()], ['id' => $row['id']]);
        }

        $start += 100;
    } while (count($rows) == $max);
}

/**
 * Trigger this when an error occurred syncing opt statuses to SugarCRM.
 * A cron lock-out will be registered and a notification will be dispatched if 5 or more errors occur.
 *
 * @param  integer $error_count The number of errors so far (passed by reference) (< 0: forcefully trigger lock-out)
 * @return boolean Whether a lock-out occurred
 */
function _sugarcrm_opt_sync_error(int &$error_count) : bool
{
    $error_count++;

    // TODO: Needs ported to v10
    if (($error_count >= 5) || ($error_count <= 0)) {
        require_lang('sugarcrm');
        require_code('notifications');

        $mail = do_notification_lang('SUGARCRM_NEWSLETTER_OPT_SYNC_ERROR_MAIL', null, null, null, get_site_default_lang());
        dispatch_notification('error_occurred', 'error_occurred_api', do_lang('SUGARCRM_NEWSLETTER_OPT_SYNC_ERROR', null, null, null, get_site_default_lang()), $mail, null, A_FROM_SYSTEM_PRIVILEGED);
        set_value('sugarcrm_opt_sync_lock', '1', true);
        return true;
    }

    return false;
}

function read_composr_cpfs($member_id)
{
    require_code('cns_members');
    $_cpfs = cns_get_all_custom_fields_match_member($member_id);
    $cpfs = [];
    foreach ($_cpfs as $cpf_title => $cpf) {
        if ($cpf_title != do_lang('cns:SMART_TOPIC_NOTIFICATION')) {
            $cpfs[$cpf_title] = $cpf['RAW'];
        }
    }
    return $cpfs;
}
