<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
        $username = 'admin';
        $password = 'wrongpassword';
        $login_array = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, apply_forum_driver_md5_variant($password, $username), $password);
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
        $login_array = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, apply_forum_driver_md5_variant($password, $username), $password);
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
        $this->assertTrue(has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'calendar', '2'));
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
        $http_result = cms_http_request(static_evaluate_tempcode(build_url(array('page' => ''), 'adminzone', array(), false, false, true)), array('trigger_error' => false));
        $this->assertTrue($http_result->message == '401', 'Expected 401 HTTP status but got ' . $http_result->message);
    }

    public function testCannotStealSession()
    {
        $fake_session_id = '1234543';

        $ips = array();
        $server_addr = get_ip_address(3, $_SERVER['SERVER_ADDR']);
        /*This now breaks the test rather than fixes it, on Mac OS X if (($server_addr == '0000:0000:0000:0000:0000:0000:*:*') && (get_local_hostname() == 'localhost')) {
            $server_addr = '127.0.0.*'; // DNS will resolve localhost using ipv4, regardless of what Apache self-reports, at least on my current dev machine -- ChrisG
        }*/
        $ips[$server_addr] = true;
        $ips['1.2.3.4'] = false;

        foreach ($ips as $ip => $pass_expected) { // We actually test both pass and fail, to help ensure our test is actually not somehow getting a failure from something else
            // Clean up
            $GLOBALS['SITE_DB']->query_delete('sessions', array('the_session' => $fake_session_id));

            $new_session_row = array(
                'the_session' => $fake_session_id,
                'last_activity' => time(),
                'member_id' => $GLOBALS['FORUM_DRIVER']->get_guest_id() + 1,
                'ip' => $ip,
                'session_confirmed' => 1,
                'session_invisible' => 1,
                'cache_username' => 'admin',
                'the_title' => '',
                'the_zone' => '',
                'the_page' => '',
                'the_type' => '',
                'the_id' => '',
            );
            $GLOBALS['SITE_DB']->query_insert('sessions', $new_session_row);
            persistent_cache_delete('SESSION_CACHE');

            require_code('files');
            $url = static_evaluate_tempcode(build_url(array('page' => '', 'keep_session' => $fake_session_id), 'adminzone', array(), false, false, true));
            $http_result = cms_http_request($url, array('trigger_error' => false));

            if ($pass_expected) {
                $this->assertTrue($http_result->message != '401', 'No access when expected');
            } else {
                $this->assertTrue($http_result->message == '401', 'Access when none expected');
            }
        }
    }
}
