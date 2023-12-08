<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
        require_code('users');

        $GLOBALS['SITE_DB']->query_delete('sessions');

        parent::setUp();
    }

    public function testNoBackdoor()
    {
        $this->assertTrue(empty($GLOBALS['SITE_INFO']['backdoor_ip']), 'Backdoor to IP address present, may break other tests');
    }

    public function testBadPasswordDoesFail()
    {
        $username = $this->get_canonical_username('admin');
        $password = 'wrongpassword';
        $login_array = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, apply_forum_driver_md5_variant($password, $username), $password);
        $member = $login_array['id'];
        $this->assertTrue(is_null($member));
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
        $member = $login_array['id'];
        $this->assertTrue(is_null($member));
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
        $this->assertTrue(has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'forums', '1'));
        $this->assertTrue(!has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'forums', '6'));
    }

    public function testPrivilegeDoesFail()
    {
        $this->assertTrue(has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'submit_lowrange_content'));
        $this->assertTrue(!has_privilege($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'bypass_validation_highrange_content'));
    }

    public function testAdminZoneDoesFail()
    {
        require_code('files');
        $result = http_download_file(static_evaluate_tempcode(build_url(array('page' => ''), 'adminzone', null, false, false, true)), null, false);
        global $HTTP_MESSAGE;
        $this->assertTrue($HTTP_MESSAGE == '401');
    }

    public function testCannotStealSession()
    {
        $fake_session_id = '1234543';

        $ips = array();
        $server_addr = get_ip_address(3, cms_srv('SERVER_ADDR'));
        $alt_server_addr = get_ip_address(3, cms_gethostbyname(preg_replace('#:.*#', '', cms_srv('HTTP_HOST'))));
        /*This now breaks the test rather than fixes it, on MacOSX if (($server_addr == '0000:0000:0000:0000:0000:0000:*:*') && (cms_srv('HTTP_HOST') == 'localhost')) {
            $server_addr = '127.0.0.*'; // DNS will resolve localhost using ipv4, regardless of what Apache self-reports, at least on my current dev machine -- ChrisG
        }*/
        $ips[$server_addr] = true;
        $ips[$alt_server_addr] = true;
        $ips['1.2.3.4'] = false;

        $has_pass = false;

        foreach ($ips as $ip => $pass_expected) { // We actually test both pass and fail, to help ensure our test is actually not somehow getting a failure from something else
            // Clean up
            $GLOBALS['SITE_DB']->query_delete('sessions', array('the_session' => $fake_session_id));

            $new_session_row = array(
                'the_session' => $fake_session_id,
                'last_activity' => time(),
                'member_id' => 2,
                'ip' => $ip,
                'session_confirmed' => 1,
                'session_invisible' => 1,
                'cache_username' => $this->get_canonical_username('admin'),
                'the_title' => '',
                'the_zone' => '',
                'the_page' => '',
                'the_type' => '',
                'the_id' => '',
            );
            $GLOBALS['SITE_DB']->query_insert('sessions', $new_session_row);
            persistent_cache_delete('SESSION_CACHE');

            require_code('files');
            $url = static_evaluate_tempcode(build_url(array('page' => '', 'keep_session' => $fake_session_id), 'adminzone', null, false, false, true));
            $result = http_download_file($url, null, false);

            global $HTTP_MESSAGE;
            if ($pass_expected) {
                if ($HTTP_MESSAGE != '401') {
                    $has_pass = true;
                }
            } else {
                $this->assertTrue($HTTP_MESSAGE == '401', 'Expected error but got access, for IP ' . $ip);
            }
        }

        $this->assertTrue($has_pass, 'Expected access but did not get it for any attempted IPs');
    }
}
