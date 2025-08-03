<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// TODO: add a test to ensure all cookie names used via cms_setcookie or $cms.setCookie (including non-bundled addons) are defined in a cookies array in a hook

/**
 * Composr test case class (unit testing).
 */
class privacy_hooks_test_set extends cms_test_case
{
    public function testHookIntegrity()
    {
        $info_messages = [];

        require_code('privacy');

        $all_tables = collapse_1d_complexity('m_table', $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table']));
        $found_tables = [];

        $hook_obs = find_all_hook_obs('systems', 'privacy', 'Hook_privacy_');
        foreach ($hook_obs as $hook => $hook_ob) {
            $info = $hook_ob->info();

            if ($info === null) {
                continue;
            }

            $this->assertTrue(((isset($info['label'])) && (($info['label'] === null) || (is_string($info['label'])))), 'Invalid label property in hook ' . $hook);
            $this->assertTrue(((isset($info['description'])) && (($info['description'] === null) || (is_string($info['description'])))), 'Invalid description property in hook ' . $hook);

            $this->assertTrue((isset($info['label']) && (do_lang($info['label'], null, null, null, null, false) !== null)), 'The label property in hook ' . $hook . ' is not a valid language codename.');
            $this->assertTrue((isset($info['description']) && (do_lang($info['description'], null, null, null, null, false) !== null)), 'The description property in hook ' . $hook . ' is not a valid language codename.');

            foreach ($info['cookies'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('reason', $x) && array_key_exists('category', $x) && in_array($x['category'], ['ESSENTIAL', 'PERSONALIZATION', 'ANALYTICS', 'MARKETING', 'UNCATEGORIZED']), 'Invalid cookie definition in ' . $hook . ' (' . serialize($x) . ')');
            }

            foreach ($info['positive'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('heading', $x) && array_key_exists('explanation', $x), 'Invalid positive message in ' . $hook . ' (' . serialize($x) . ')');
            }

            foreach ($info['general'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('heading', $x) && array_key_exists('action', $x) && array_key_exists('reason', $x), 'Invalid general message in ' . $hook . ' (' . serialize($x) . ')');
            }

            $required_table_details = [
                'timestamp_field',
                'retention_days',
                'retention_handle_method',
                'owner_id_field',
                'additional_member_id_fields',
                'ip_address_fields',
                'email_fields',
                'username_fields',
                'file_fields',
                'additional_anonymise_fields',
                'extra_where',
                'removal_default_handle_method',
                'removal_default_handle_method_member_override',
                'allowed_handle_methods',
            ];

            $must_include_cannot_purge_lang = false;

            foreach ($info['database_records'] as $table => $details) {
                $this->assertTrue(in_array($table, $all_tables), 'Table unknown: ' . $table . ' in hook ' . $hook);

                $this->assertTrue(!isset($found_tables[$table]), 'Table defined more than once: ' . $table . ' in hook ' . $hook);

                $all_ok = true;
                foreach ($required_table_details as $r_table) {
                    $ok = array_key_exists($r_table, $details);
                    $this->assertTrue($ok, 'Missing property ' . $r_table . ' on table details in ' . $table . ' in hook ' . $hook . '. All other checks skipped for this table (ignore any errors about this not being defined in a hook).');

                    if (!$ok) {
                        $all_ok = false;
                    }
                }
                if (!$all_ok) { // Cannot proceed with other checks if there are missing properties.
                    continue;
                }

                $this->assertTrue($details['timestamp_field'] === null || is_string($details['timestamp_field']), 'Invalid timestamp field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['retention_days'] === null || is_integer($details['retention_days']), 'Invalid retention_days field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['retention_handle_method']), 'Invalid retention_handle_method field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['owner_id_field'] === null || is_string($details['owner_id_field']), 'Invalid owner_id_field field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['additional_member_id_fields']), 'Invalid additional_member_id_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['ip_address_fields']), 'Invalid ip_address_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['email_fields']), 'Invalid email_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['username_fields']), 'Invalid username_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['file_fields']), 'Invalid file_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['additional_anonymise_fields']), 'Invalid additional_anonymise_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['extra_where'] === null || is_string($details['extra_where']), 'Invalid extra_where field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['removal_default_handle_method']), 'Invalid removal_default_handle_method setting in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['allowed_handle_methods']), 'Invalid allowed_handle_methods setting in ' . $table . ' in hook ' . $hook);
                $this->assertTrue((($details['removal_default_handle_method_member_override'] === null) || is_integer($details['removal_default_handle_method_member_override'])), 'Invalid removal_default_handle_method_member_override setting in ' . $table . ' in hook ' . $hook);

                $this->assertTrue(($details['retention_handle_method'] == PRIVACY_METHOD__LEAVE) || (($details['allowed_handle_methods'] & $details['retention_handle_method']) != 0), 'Invalid retention_handle_method value in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(($details['removal_default_handle_method'] == PRIVACY_METHOD__LEAVE) || (($details['allowed_handle_methods'] & $details['removal_default_handle_method']) != 0), 'Invalid removal_default_handle_method value in ' . $table . ' in hook ' . $hook);

                if ($details['retention_handle_method'] == PRIVACY_METHOD__LEAVE) {
                    $this->assertTrue($details['retention_days'] === null, 'retention_days should not be set for PRIVACY_METHOD__LEAVE, for ' . $table . ' in hook ' . $hook);
                } else {
                    $this->assertTrue($details['timestamp_field'] !== null, 'timestamp_field should be set for !PRIVACY_METHOD__LEAVE, for ' . $table . ' in hook ' . $hook);
                    $this->assertTrue($details['retention_days'] !== null, 'retention_days should be set for !PRIVACY_METHOD__LEAVE, for ' . $table . ' in hook ' . $hook);
                }

                $this->assertTrue((($details['owner_id_field'] !== null) || (!empty($details['additional_member_id_fields'])) || (!empty($details['ip_address_fields'])) || (!empty($details['email_fields'])) || (!empty($details['username_fields'])) || (!empty($details['additional_anonymise_fields']))), 'No personal data fields defined in ' . $table . ' in hook ' . $hook . ', so table should not be defined.');

                $exceptions = [
                    'f_forums',
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue(((count($details['additional_member_id_fields']) == 0) || ($details['owner_id_field'] !== null)), 'additional_member_id_fields fields defined, but no owner_id_field defined, in ' . $table . ' in hook ' . $hook);
                }

                if (($details['removal_default_handle_method_member_override'] !== null) || ($details['extra_where'] !== null)) {
                    $must_include_cannot_purge_lang = true;
                }

                // Make comparison to what we want easier for the next foreach loop
                sort($details['additional_member_id_fields']);
                sort($details['ip_address_fields']);
                sort($details['email_fields']);
                sort($details['username_fields']);
                sort($details['file_fields']);
                sort($details['additional_anonymise_fields']);
                $details['hook'] = $hook; // FUDGE
                $info['database_records'][$table] = $details;
                $found_tables[$table] = $details;
            }

            $this->assertTrue(strpos(serialize($info), 'TODO') === false, 'TODO found in info for hook ' . $hook);

            // We need to check to be sure we're specifying certain data cannot be purged on the member screen
            $lang = do_lang($info['description'], null, null, null, null, false);
            if ($lang !== null) {
                if ($must_include_cannot_purge_lang) {
                    $this->assertTrue((strpos($lang, 'cannot be purged') !== false), 'Description string for hook ' . $hook . ' must contain information about what data will not be purged or can only be purged by staff (based on extra_where and/or removal_default_handle_method_member_override). The string should include "cannot be purged" somewhere in it to assert.');
                } else {
                    $this->assertTrue((strpos($lang, 'cannot be purged') === false), 'Not necessary to include "cannot be purged" language in the description for hook ' . $hook);
                }
            }
        }

        foreach ($all_tables as $table) {
            if ($table == 'temp_test') {
                continue;
            }

            $all_fields = collapse_2d_complexity('m_name', 'm_type', $GLOBALS['SITE_DB']->query_select('db_meta', ['m_name', 'm_type'], ['m_table' => $table], 'ORDER BY m_name'));
            $relevant_fields_member_id = [];
            $relevant_fields_ip_address = [];
            $relevant_fields_email = [];
            $relevant_fields_username = [];
            $relevant_fields_time = [];
            $relevant_fields_url = [];
            $fields_should_anonymise = [];
            $primary_key_fields = [];
            foreach ($all_fields as $name => $type) {
                if (preg_match('#^[\*\?]*(MEMBER)$#', $type) != 0) {
                    $relevant_fields_member_id[$name] = $type;
                }
                if (preg_match('#^[\*\?]*(IP)$#', $type) != 0) {
                    $relevant_fields_ip_address[$name] = $type;
                }
                if ((strpos($name, 'email') !== false) && ((preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0) || (preg_match('#^[\*\?]*(ID_TEXT)$#', $type) != 0) || (preg_match('#^[\*\?]*(SERIAL)$#', $type) != 0))) {
                    $relevant_fields_email[$name] = $type;
                }
                if ((strpos($name, 'username') !== false) && (preg_match('#^[\*\?]*(ID_TEXT)$#', $type) != 0)) {
                    $relevant_fields_username[$name] = $type;
                }
                if (preg_match('#^[\*\?]*(TIME)$#', $type) != 0) {
                    $relevant_fields_time[$name] = $type;
                }
                if (preg_match('#^[\*\?]*(URLPATH)$#', $type) != 0) {
                    $relevant_fields_url[$name] = $type;
                }
                if (preg_match('#^\*#', $type) != 0) {
                    $primary_key_fields[$name] = $type;
                }

                // Some additional potentially sensitive fields
                if ((strpos($name, 'pass') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Passwords
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'username') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // SHORT_TEXT usernames are typically not usernames pointing to members
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'name') !== false) && (((strpos($name, 'first') !== false)) || ((strpos($name, 'last') !== false)) || ((strpos($name, 'full') !== false)) || ((strpos($name, 'real') !== false)) || ((strpos($name, 'legal') !== false))) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Real names
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'address') !== false) && (strpos($name, 'email') === false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Physical addresses
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'salt') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Salts, such as for passwords
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'hash') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Hashed passwords
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'code') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Verification codes
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'sess') !== false) && (preg_match('#^[\*\?]*(ID_TEXT)$#', $type) != 0)) { // Session IDs
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'token') !== false) && ((preg_match('#^[\*\?]*(ID_TEXT)$#', $type) != 0) || (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0))) { // Tokens, such as CSRF
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'phone') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Phone numbers
                    $fields_should_anonymise[$name] = $type;
                }
                if ((strpos($name, 'credit') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) { // Credit card numbers
                    $fields_should_anonymise[$name] = $type;
                }
            }
            if ($table == 'f_members') {
                $relevant_fields_member_id['id'] = '*AUTO';
            }
            if ($table == 'w_members') {
                $relevant_fields_member_id['id'] = 'MEMBER';
            }
            $total_fields = count($relevant_fields_member_id) + count($relevant_fields_ip_address) + count($relevant_fields_email) + count($relevant_fields_username);

            if (isset($found_tables[$table])) {
                $hook = $found_tables[$table]['hook']; // FUDGE

                $this->assertTrue($found_tables[$table]['timestamp_field'] === null || isset($all_fields[$found_tables[$table]['timestamp_field']]), 'Could not find ' . $found_tables[$table]['timestamp_field'] . ' field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($found_tables[$table]['owner_id_field'] === null || isset($all_fields[$found_tables[$table]['owner_id_field']]), 'Could not find ' . $found_tables[$table]['owner_id_field'] . ' field in ' . $table . ' in hook ' . $hook);

                $exceptions = [
                    'banned_ip',
                    'f_forums',
                    'newsletter_drip_send',
                    'newsletter_periodic',
                    'revisions',
                    'w_members',
                    'site_messages'
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue($found_tables[$table]['timestamp_field'] !== null || empty($relevant_fields_time), 'Could have set a timestamp field for ' . $table . ' in hook ' . $hook . '[' . serialize($relevant_fields_time) . ']');
                }

                $exceptions = [
                    'f_forums'
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue($found_tables[$table]['owner_id_field'] !== null || empty($relevant_fields_member_id), 'Could have set an owner ID field for ' . $table . ' in hook ' . $hook . '[' . serialize($relevant_fields_member_id) . ']');
                }

                $this->assertTrue((array_keys($relevant_fields_member_id) == $found_tables[$table]['additional_member_id_fields']) || (($found_tables[$table]['owner_id_field'] !== null) && (in_array($found_tables[$table]['owner_id_field'], array_keys($relevant_fields_member_id)))), 'Additional member field mismatch for: ' . $table . ' in hook ' . $hook . ' (' . serialize($relevant_fields_member_id) . ')');

                $this->assertTrue(array_keys($relevant_fields_ip_address) == $found_tables[$table]['ip_address_fields'], 'IP address field mismatch for: ' . $table . ' in hook ' . $hook . ' (' . serialize($relevant_fields_ip_address) . ')');

                $exceptions = [
                    'f_forums',
                    'f_members',
                    'failedlogins',
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue(array_keys($relevant_fields_email) == $found_tables[$table]['email_fields'], 'E-mail field mismatch for: ' . $table . ' in hook ' . $hook . ' (' . serialize($relevant_fields_email) . ')');
                }
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue(array_keys($relevant_fields_username) == $found_tables[$table]['username_fields'], 'Username field mismatch for: ' . $table . ' in hook ' . $hook . ' (' . serialize($relevant_fields_username) . ')');
                }
                /*
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue(array_keys($relevant_fields_url) == $found_tables[$table]['file_fields'], 'File field mismatch for: ' . $table . ' in hook ' . $hook . ' (' . serialize($relevant_fields_url) . ')');
                }
                */

                foreach ($found_tables[$table]['additional_member_id_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_member_id[$name]), 'Could not find ' . $name . ' additional_member_id_fields field in ' . $table . ' in hook ' . $hook);
                    $this->assertTrue(($found_tables[$table]['owner_id_field'] !== $name), 'Field owner_id_field ' . $name . ' is also defined in additional_member_id_fields in ' . $table . ' in hook ' . $hook);
                }
                foreach ($found_tables[$table]['ip_address_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_ip_address[$name]), 'Could not find ' . $name . ' ip_address_fields field in ' . $table . ' in hook ' . $hook);
                }
                foreach ($found_tables[$table]['email_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_email[$name]), 'Could not find ' . $name . ' email_fields field in ' . $table . ' in hook ' . $hook);
                }

                $exceptions = [
                    'failedlogins',
                ];
                if (!in_array($table, $exceptions)) {
                    foreach ($found_tables[$table]['username_fields'] as $name) {
                        $this->assertTrue(isset($relevant_fields_username[$name]), 'Could not find ' . $name . ' username_fields field in ' . $table . ' in hook ' . $hook);
                    }
                }

                foreach ($found_tables[$table]['file_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_url[$name]), 'Could not find ' . $name . ' file_fields field in ' . $table . ' in hook ' . $hook);
                }
                foreach ($found_tables[$table]['additional_anonymise_fields'] as $name) {
                    $this->assertTrue(isset($all_fields[$name]), 'Could not find ' . $name . ' additional_anonymise_fields field in ' . $table . ' in hook ' . $hook);
                }

                // Table => [table field names]
                $exceptions = [];
                if (($found_tables[$table]['removal_default_handle_method'] == PRIVACY_METHOD__ANONYMISE) || (($found_tables[$table]['allowed_handle_methods'] & PRIVACY_METHOD__ANONYMISE) != 0)) {
                    if (($found_tables[$table]['owner_id_field'] !== null) && ((!isset($exceptions[$table])) || (!in_array($found_tables[$table]['owner_id_field'], $exceptions[$table])))) {
                        if (($found_tables[$table]['allowed_handle_methods'] & PRIVACY_METHOD__DELETE) != 0) {
                            // This is ugly but we don't have any other way to put out a warning
                            if (isset($primary_key_fields[$found_tables[$table]['owner_id_field']])) {
                                $info_messages[] = 'PRIVACY_METHOD__ANONYMISE is specified in ' . $table . ' in hook ' . $hook . ', but field ' . $found_tables[$table]['owner_id_field'] . ' is a key. Since PRIVACY_METHOD__DELETE is also an allowed action, any records where criteria matches this field will instead be deleted.';
                            }
                        } else {
                            $this->assertTrue((!isset($primary_key_fields[$found_tables[$table]['owner_id_field']])), 'PRIVACY_METHOD__ANONYMISE is specified in ' . $table . ' in hook ' . $hook . ', but field ' . $found_tables[$table]['owner_id_field'] . ' is a key. Furthermore, PRIVACY_METHOD__DELETE is not defined as an allowed action. Therefore, if criteria matches the indicated field, purging will bail out on error!');
                        }
                    }

                    $user_fields_array = [
                        'additional_member_id_fields',
                        'ip_address_fields',
                        'email_fields',
                        'username_fields',
                        'additional_anonymise_fields'
                    ];
                    foreach ($user_fields_array as $user_field) {
                        foreach ($found_tables[$table][$user_field] as $name) {
                            if ((isset($exceptions[$table])) && (in_array($name, $exceptions[$table]))) {
                                continue;
                            }

                            if (($found_tables[$table]['allowed_handle_methods'] & PRIVACY_METHOD__DELETE) != 0) {
                                // This is ugly but we don't have any other way to put out a warning
                                if (isset($primary_key_fields[$name])) {
                                    $info_messages[] = 'PRIVACY_METHOD__ANONYMISE is specified in ' . $table . ' in hook ' . $hook . ', but field ' . $name . ' is a key. Since PRIVACY_METHOD__DELETE is also an allowed action, any records where criteria matches this field will instead be deleted.';
                                }
                            } else {
                                $this->assertTrue((!isset($primary_key_fields[$name])), 'PRIVACY_METHOD__ANONYMISE is specified in ' . $table . ' in hook ' . $hook . ', but field ' . $name . ' is a key. Furthermore, PRIVACY_METHOD__DELETE is not defined as an allowed action. Therefore, if criteria matches the indicated field, purging will bail out on error!');
                            }
                        }
                    }
                }

                // Exceptions are table => [fields]
                $exceptions = [
                    'digestives_tin' => ['d_code_category'],
                    'ecom_invoices' => ['i_processing_code'],
                    'notifications_enabled' => ['l_code_category'],
                    'w_rooms' => ['password_fail_message'],
                    'device_token_details' => ['token_type'],
                    'telemetry_errors' => ['e_error_hash'],
                    'achievements_progress' => ['ap_qualification_hash'],
                ];
                foreach ($fields_should_anonymise as $name => $type) {
                    if ((isset($exceptions[$table])) && (in_array($name, $exceptions[$table]))) {
                        continue;
                    }
                    if (in_array($name, $found_tables[$table]['additional_anonymise_fields'])) {
                        continue;
                    }

                    $this->assertTrue(false, 'Potentially sensitive field detected which should possibly be added to additional_anonymise_fields: ' . $name . ' in ' . $table . ' in hook ' . $hook);
                }

                // Exceptions are table => [fields]
                $exceptions = [
                    'authors' => ['url'],
                    'banners' => ['site_url'],
                    'download_downloads' => ['url_redirect'],
                    'hackattack' => ['url', 'referer_url'],
                    'logged' => ['website_url'],
                    'may_feature' => ['url'],
                    'telemetry_errors' => ['e_website_url'],
                    'stats' => ['referer_url'],
                    'stats_link_tracker' => ['c_url'],
                    'tutorials_external' => ['t_url'],
                    'trackbacks' => ['trackback_url'],
                    'logged_mail_messages' => ['m_url'],
                    'telemetry_sites' => ['website_url'],
                ];
                foreach ($relevant_fields_url as $name => $type) {
                    if ((isset($exceptions[$table])) && (in_array($name, $exceptions[$table]))) {
                        continue;
                    }
                    if (in_array($name, $found_tables[$table]['file_fields'])) {
                        continue;
                    }

                    $this->assertTrue(false, 'Possible field may need to be added to file_fields if it might reference a file from the uploads/ directory: ' . $name . ' in ' . $table . ' in hook ' . $hook . '. Add this to the test exceptions if it will never reference a file from the uploads/ directory.');
                }
                foreach ($found_tables[$table]['file_fields'] as $name) {
                    if ((isset($exceptions[$table])) && (in_array($name, $exceptions[$table]))) {
                        $this->assertTrue(false, 'File field specified which is listed as an exception in this test: ' . $name . ' in ' . $table . ' in hook ' . $hook . '. Remove from exceptions if this field may reference an uploads/ file. Otherwise, remove from file_fields.');
                        continue;
                    }

                    $this->assertTrue(array_key_exists($name, $relevant_fields_url), 'Specified file field is not a URLPATH: ' . $name . ' in ' . $table . ' in hook ' . $hook . '.');
                }
            } else {
                $exceptions = [
                    'news_rss_cloud',
                ];
                if ((!in_array($table, $exceptions)) && ((get_forum_type() == 'cns') || (substr($table, 0, 2) != 'f_') && ($table != 'content_privacy__members'))) {
                    $this->assertTrue($total_fields == 0, 'Should be defined in a privacy hook: ' . $table);
                }
            }
        }

        if (count($info_messages) > 0) {
            $this->dump($info_messages, 'INFO:');
        }
    }
}
