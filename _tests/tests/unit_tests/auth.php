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
    }

    public function testBadPasswordDoesFail()
    {
        $username = $this->get_canonical_username('admin');
        $password = 'wrongpassword';
        $login_array = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
        $member_id = $login_array['id'];
        $this->assertTrue($member_id === null);
        $this->assertTrue(
            isset($login_array['error']) &&
            is_object($login_array['error']) &&
            (static_evaluate_tempcode($login_array['error']) == do_lang('MEMBER_BAD_PASSWORD') || static_evaluate_tempcode($login_array['error']) == do_lang('MEMBER_INVALID_LOGIN'))
        );
    }

    public function testUnknownUsernameDoesFail()
    {
        $username = 'nosuchuser';
        $password = '';
        $login_array = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
        $member_id = $login_array['id'];
        $this->assertTrue($member_id === null);
    }

    public function testZoneAccessDoesFail()
    {
        $this->assertTrue(has_zone_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), ''));
        $this->assertTrue(!has_zone_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'adminzone'));
    }

    public function testPageAccessDoesFail()
    {
        $this->assertTrue(has_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'feedback', ''));
        $this->assertTrue(!has_page_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'admin_commandr', 'adminzone'));
    }

    public function testCategoryAccessDoesFail()
    {
        $second_calendar_category = $GLOBALS['SITE_DB']->query_select_value('calendar_types', 'id', [], ' AND id<>' . strval(db_get_first_id()));
        $this->assertTrue(has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'calendar', strval($second_calendar_category)), 'Does not have access to category #' . strval($second_calendar_category));
        $this->assertTrue(!has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'calendar', '1')); // System-command category
    }

    public function testPrivilegeDoesFail()
    {
        $this->assertTrue(has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'submit_lowrange_content'));
        $this->assertTrue(!has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'bypass_validation_highrange_content'));
    }

    public function testAdminZoneDoesFail()
    {
        require_code('files');
        $http_result = cms_http_request(static_evaluate_tempcode(build_url(['page' => '', 'keep_su' => 'Guest'], 'adminzone', [], false, false, true)), ['trigger_error' => false]);
        $this->assertTrue($http_result->message == '401', 'Expected 401 HTTP status but got ' . $http_result->message);
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
                $this->assertTrue($success, 'No access when expected for ' . $ip . ' with ' . $fake_session_id);
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
