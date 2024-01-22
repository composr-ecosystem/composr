<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

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

            foreach ($info['cookies'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('reason', $x), 'Invalid cookie name in ' . $hook . ' (' . serialize($x) . ')');
            }

            foreach ($info['positive'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('heading', $x) && array_key_exists('explanation', $x), 'Invalid positive message in ' . $hook . ' (' . serialize($x) . ')');
            }

            foreach ($info['general'] as $x) {
                $this->assertTrue($x === null || is_array($x) && array_key_exists('heading', $x) && array_key_exists('action', $x) && array_key_exists('reason', $x), 'Invalid general message in ' . $hook . ' (' . serialize($x) . ')');
            }

            foreach ($info['database_records'] as $table => $details) {
                $this->assertTrue(in_array($table, $all_tables), 'Table unknown: ' . $table . ' in hook ' . $hook);

                $this->assertTrue(!isset($found_tables[$table]), 'Table defined more than once: ' . $table . ' in hook ' . $hook);

                $this->assertTrue($details['timestamp_field'] === null || is_string($details['timestamp_field']), 'Invalid timestamp field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['retention_days'] === null || is_integer($details['retention_days']), 'Invalid retention_days field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['retention_handle_method']), 'Invalid retention_handle_method field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['owner_id_field'] === null || is_string($details['owner_id_field']), 'Invalid owner_id_field field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['additional_member_id_fields']), 'Invalid additional_member_id_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['ip_address_fields']), 'Invalid ip_address_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['email_fields']), 'Invalid email_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['username_fields']), 'Invalid username_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_array($details['additional_anonymise_fields']), 'Invalid additional_anonymise_fields field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue($details['extra_where'] === null || is_string($details['extra_where']), 'Invalid extra_where field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['removal_default_handle_method']), 'Invalid removal_default_handle_method field in ' . $table . ' in hook ' . $hook);
                $this->assertTrue(is_integer($details['allowed_handle_methods']), 'Invalid allowed_handle_methods field in ' . $table . ' in hook ' . $hook);

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

                // Make comparison to what we want easier for the next foreach loop
                sort($details['additional_member_id_fields']);
                sort($details['ip_address_fields']);
                sort($details['email_fields']);
                sort($details['username_fields']);
                sort($details['additional_anonymise_fields']);
                $details['hook'] = $hook; // FUDGE
                $info['database_records'][$table] = $details;
                $found_tables[$table] = $details;
            }

            $this->assertTrue(strpos(serialize($info), 'TODO') === false);
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
            $primary_key_fields = [];
            foreach ($all_fields as $name => $type) {
                if (preg_match('#^[\*\?]*(MEMBER)$#', $type) != 0) {
                    $relevant_fields_member_id[$name] = $type;
                }
                if (preg_match('#^[\*\?]*(IP)$#', $type) != 0) {
                    $relevant_fields_ip_address[$name] = $type;
                }
                if ((strpos($name, 'email') !== false) && (preg_match('#^[\*\?]*(SHORT_TEXT)$#', $type) != 0)) {
                    $relevant_fields_email[$name] = $type;
                }
                if ((strpos($name, 'username') !== false) && (preg_match('#^[\*\?]*(ID_TEXT)$#', $type) != 0)) {
                    $relevant_fields_username[$name] = $type;
                }
                if (preg_match('#^[\*\?]*(TIME)$#', $type) != 0) {
                    $relevant_fields_time[$name] = $type;
                }
                if (preg_match('#^\*#', $type) != 0) {
                    $primary_key_fields[$name] = $type;
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
                
                foreach ($found_tables[$table]['additional_anonymise_fields'] as $name) {
                    $this->assertTrue(isset($all_fields[$name]), 'Could not find ' . $name . ' additional_anonymise_fields field in ' . $table . ' in hook ' . $hook);
                }

                // Table => [table field names]
                $exceptions = [
                ];
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
            $this->dump($info_messages, 'Caution:');
        }
    }
}
