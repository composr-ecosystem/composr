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

/**
 * Composr test case class (unit testing).
 */
class httpauth_test_set extends cms_test_case
{
    public function testHttpAuth()
    {
        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        if (!file_exists(get_file_base() . '/.htaccess')) {
            $this->assertTrue(false, 'Test only works if the default .htaccess file is present.');
            return;
        }

        $username = $this->get_canonical_username('admin');

        // Set up our test
        $old_pwd = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_pass_hash_salted', ['m_username' => $username]);
        $old_scheme = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_password_compat_scheme', ['m_username' => $username]);
        $old_email = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_email_address', ['m_username' => $username]);
        $GLOBALS['FORUM_DB']->query_update('f_members', ['m_password_compat_scheme' => 'httpauth', 'm_pass_hash_salted' => $username, 'm_email_address' => 'test@example.com'], ['m_username' => $username]);

        $url = build_url(['page' => 'members', 'type' => 'view'], get_module_zone('members'));

        set_option('httpauth_is_enabled', '1');

        if ($this->debug) {
            require_code('failure');

            set_throw_errors(true);
            try {
                $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'auth' => [$username, ''], 'trigger_error' => true]);
            } catch (Exception $e) {
                $this->dump($e->getMessage(), 'ERROR');
            } finally {
                set_option('httpauth_is_enabled', '0');
                $GLOBALS['FORUM_DB']->query_update('f_members', ['m_pass_hash_salted' => $old_pwd, 'm_password_compat_scheme' => $old_scheme, 'm_email_address' => $old_email], ['m_username' => $username]);
            }
            set_throw_errors(false);

            $this->dump($data, 'OUTPUT');
        } else {
            $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'auth' => [$username, ''], 'trigger_error' => false]);
        }

        set_option('httpauth_is_enabled', '0');

        $GLOBALS['FORUM_DB']->query_update('f_members', ['m_pass_hash_salted' => $old_pwd, 'm_password_compat_scheme' => $old_scheme, 'm_email_address' => $old_email], ['m_username' => $username]);

        $this->assertTrue(is_string($data) && (strpos($data, '<span class="fn nickname">' . $username . '</span>') !== false), 'Expected to see the ' . $username . ' profile, but did not.');
    }
}
