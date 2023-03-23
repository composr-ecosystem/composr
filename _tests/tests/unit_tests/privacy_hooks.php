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
                $this->assertTrue(in_array($table, $all_tables), 'Table unknown: ' . $table);

                $this->assertTrue(!isset($found_tables[$table]), 'Table defined more than once: ' . $table);

                $this->assertTrue($details['timestamp_field'] === null || is_string($details['timestamp_field']), 'Invalid timestamp field in ' . $table);
                $this->assertTrue($details['retention_days'] === null || is_integer($details['retention_days']), 'Invalid retention_days field in ' . $table);
                $this->assertTrue(is_integer($details['retention_handle_method']), 'Invalid retention_handle_method field in ' . $table);
                $this->assertTrue(is_array($details['member_id_fields']), 'Invalid member_id_fields field in ' . $table);
                $this->assertTrue(is_array($details['ip_address_fields']), 'Invalid ip_address_fields field in ' . $table);
                $this->assertTrue(is_array($details['email_fields']), 'Invalid email_fields field in ' . $table);
                $this->assertTrue(is_array($details['additional_anonymise_fields']), 'Invalid additional_anonymise_fields field in ' . $table);
                $this->assertTrue($details['extra_where'] === null || is_string($details['extra_where']), 'Invalid extra_where field in ' . $table);
                $this->assertTrue(is_integer($details['removal_default_handle_method']), 'Invalid removal_default_handle_method field in ' . $table);
                $this->assertTrue(is_integer($details['allowed_handle_methods']), 'Invalid allowed_handle_methods field in ' . $table);

                $this->assertTrue(($details['retention_handle_method'] == PRIVACY_METHOD__LEAVE) || (($details['allowed_handle_methods'] & $details['retention_handle_method']) != 0), 'Invalid retention_handle_method value in ' . $table);
                $this->assertTrue(($details['removal_default_handle_method'] == PRIVACY_METHOD__LEAVE) || (($details['allowed_handle_methods'] & $details['removal_default_handle_method']) != 0), 'Invalid removal_default_handle_method value in ' . $table);

                if ($details['retention_handle_method'] == PRIVACY_METHOD__LEAVE) {
                    $this->assertTrue($details['retention_days'] === null, 'retention_days should not be set for PRIVACY_METHOD__LEAVE, for ' . $table);
                } else {
                    $this->assertTrue($details['timestamp_field'] !== null, 'timestamp_field should be set for !PRIVACY_METHOD__LEAVE, for ' . $table);
                    $this->assertTrue($details['retention_days'] !== null, 'retention_days should be set for !PRIVACY_METHOD__LEAVE, for ' . $table);
                }

                $this->assertTrue(count($details['member_id_fields']) + count($details['ip_address_fields']) + count($details['email_fields']) + count($details['additional_anonymise_fields']) > 0, 'No personal data in ' . $table . ', so should not be defined');

                // Make comparison to what we want easier
                sort($details['member_id_fields']);
                sort($details['ip_address_fields']);
                sort($details['email_fields']);
                sort($details['additional_anonymise_fields']);
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
            $relevant_fields_time = [];
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
                if (preg_match('#^[\*\?]*(TIME)$#', $type) != 0) {
                    $relevant_fields_time[$name] = $type;
                }
            }
            if ($table == 'f_members') {
                $relevant_fields_member_id['id'] = '*AUTO';
            }
            if ($table == 'w_members') {
                $relevant_fields_member_id['id'] = 'MEMBER';
            }
            $total_fields = count($relevant_fields_member_id) + count($relevant_fields_ip_address) + count($relevant_fields_email);

            if (isset($found_tables[$table])) {
                $this->assertTrue($found_tables[$table]['timestamp_field'] === null || isset($all_fields[$found_tables[$table]['timestamp_field']]), 'Could not find ' . $found_tables[$table]['timestamp_field'] . ' field in ' . $table);

                $exceptions = [
                    'banned_ip',
                    'f_forums',
                    'f_group_member_timeouts',
                    'member_category_access',
                    'member_page_access',
                    'member_zone_access',
                    'newsletter_drip_send',
                    'newsletter_periodic',
                    'revisions',
                    'w_members',
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue($found_tables[$table]['timestamp_field'] !== null || empty($relevant_fields_time), 'Could have set a timestamp field for ' . $table . '[' . serialize($relevant_fields_time) . ']');
                }

                $this->assertTrue(array_keys($relevant_fields_member_id) == $found_tables[$table]['member_id_fields'], 'Member field mismatch for: ' . $table . ' (' . serialize($relevant_fields_member_id) . ')');

                $this->assertTrue(array_keys($relevant_fields_ip_address) == $found_tables[$table]['ip_address_fields'], 'IP address field mismatch for: ' . $table . ' (' . serialize($relevant_fields_ip_address) . ')');

                $exceptions = [
                    'f_forums',
                    'f_members',
                ];
                if (!in_array($table, $exceptions)) {
                    $this->assertTrue(array_keys($relevant_fields_email) == $found_tables[$table]['email_fields'], 'E-mail field mismatch for: ' . $table . ' (' . serialize($relevant_fields_email) . ')');
                }

                foreach ($found_tables[$table]['member_id_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_member_id[$name]), 'Could not find ' . $name . ' field in ' . $table);
                }
                foreach ($found_tables[$table]['ip_address_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_ip_address[$name]), 'Could not find ' . $name . ' field in ' . $table);
                }
                foreach ($found_tables[$table]['email_fields'] as $name) {
                    $this->assertTrue(isset($relevant_fields_email[$name]), 'Could not find ' . $name . ' field in ' . $table);
                }
                foreach ($found_tables[$table]['additional_anonymise_fields'] as $name) {
                    $this->assertTrue(isset($all_fields[$name]), 'Could not find ' . $name . ' field in ' . $table);
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
    }
}
