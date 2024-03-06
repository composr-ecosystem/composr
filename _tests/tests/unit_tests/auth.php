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
class auth_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('users');

        $GLOBALS['SITE_DB']->query_delete('sessions');
    }

    public function testNoBackdoor()
    {
        $this->assertTrue(empty($GLOBALS['SITE_INFO']['backdoor_ip']), 'Backdoor to IP address present, may break other tests');
        
        $message = 'You may encounter failures if using a proxy such as Cloudflare.';
        $this->dump($message, 'INFO:');
    }

    public function testBadPasswordDoesFail()
    {
        $username = $this->get_canonical_username('admin');
        $password = 'wrongpassword';
        $login_array = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
        $member_id = $login_array['id'];
        $this->assertTrue($member_id === null, 'Expected no member ID, but got one.');
        $this->assertTrue(
            isset($login_array['error']) &&
            is_object($login_array['error']) &&
            (static_evaluate_tempcode($login_array['error']) == do_lang('MEMBER_BAD_PASSWORD') || static_evaluate_tempcode($login_array['error']) == do_lang('MEMBER_INVALID_LOGIN')),
            'Expected a bad password or invalid login error, but instead got ' . static_evaluate_tempcode($login_array['error'])
        );
    }

    public function testUnknownUsernameDoesFail()
    {
        $username = 'nosuchuser';
        $password = '';
        $login_array = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
        $member_id = $login_array['id'];
        $this->assertTrue($member_id === null, 'Expected no member ID, but we got one.');
    }

    public function testZoneAccessDoesFail()
    {
        $this->assertTrue(has_zone_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), ''), 'Expected guest access to the root zone, but did not have it.');
        $this->assertTrue(!has_zone_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'adminzone'), 'Expected no guest access to the adminzone zone, but we had it.');
    }

    public function testPageAccessDoesFail()
    {
        $this->assertTrue(has_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'feedback', ''), 'Expected guest access to the feedback page, but did not have it.');
        $this->assertTrue(!has_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'admin_commandr', 'adminzone'), 'Expected no guest access to Commandr, but we had it.');
    }

    public function testCategoryAccessDoesFail()
    {
        $second_calendar_category = $GLOBALS['SITE_DB']->query_select_value('calendar_types', 'id', [], ' AND id<>' . strval(db_get_first_id()));
        $this->assertTrue(has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'calendar', strval($second_calendar_category)), 'Does not have access to category #' . strval($second_calendar_category));
        $this->assertTrue(!has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'calendar', '1'), 'Expected no guest access to the system command category, but we had it.'); // System-command category
    }

    public function testPrivilegeDoesFail()
    {
        $this->assertTrue(has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'submit_lowrange_content'), 'Expected guest to have submit_lowrange_content privilege, but they did not.');
        $this->assertTrue(!has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'bypass_validation_highrange_content'), 'Expected guest not to have bypass_validation_highrange_content privilege, but they had it.');
    }

    public function testAdminZoneDoesFail()
    {
        require_code('files');
        $http_result = cms_http_request(static_evaluate_tempcode(build_url(['page' => '', 'keep_su' => 'Guest'], 'adminzone', [], false, false, true)), ['trigger_error' => false]);
        $this->assertTrue($http_result->message == '401', 'Expected 401 HTTP status when accessing the Admin Zone as Guest SU, but got ' . $http_result->message);
    }

    public function testCannotStealSession()
    {
        $ips = [];
        $ips[get_ip_address(3, get_server_external_looparound_ip())] = true;
        $ips[get_ip_address(3, '1.2.3.4')] = false;

        require_code('crypt');

        foreach ($ips as $ip => $pass_expected) { // We actually test both pass and fail, to help ensure our test is actually not somehow getting a failure from something else
            $fake_session_id = get_secure_random_string();

            // Clean up
            $GLOBALS['SITE_DB']->query_delete('sessions', ['the_session' => $fake_session_id]);

            $new_session_row = [
                'the_session' => $fake_session_id,
                'last_activity' => time(),
                'member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id() + 1,
                'ip' => $ip,
                'session_confirmed' => 1,
                'session_invisible' => 1,
                'cache_username' => $this->get_canonical_username('admin'),
                'the_title' => '',
                'the_zone' => '',
                'the_page' => '',
                'the_type' => '',
                'the_id' => '',
            ];
            $GLOBALS['SITE_DB']->query_insert('sessions', $new_session_row);
            persistent_cache_delete('SESSION_CACHE');

            require_code('files');
            $url = static_evaluate_tempcode(build_url(['page' => ''], 'adminzone', [], false, false, true));
            $http_result = cms_http_request($url, ['ignore_http_status' => true, 'trigger_error' => false, 'cookies' => [get_session_cookie() => $fake_session_id]]);

            if ($pass_expected) {
                $success = ($http_result->message != '401');
                if ((!$success) && ($this->debug)) {
                    var_dump($http_result);
                }
                $this->assertTrue($success, 'No access when expected for ' . $ip . ' with ' . $fake_session_id . ' (this might be failing if you are using Cloudflare or a proxy)');
            } else {
                $success = ($http_result->message == '401');
                if ((!$success) && ($this->debug)) {
                    var_dump($http_result);
                }
                $this->assertTrue($success, 'Access when none expected for ' . $ip . ' with ' . $fake_session_id);
            }
        }
    }
}
