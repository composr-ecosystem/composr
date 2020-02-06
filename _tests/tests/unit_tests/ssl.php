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
class ssl_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        set_value('disable_ssl_for__' . get_base_url_hostname(), '1');
    }

    public function testHTTPSStatus()
    {
        if (whole_site_https()) {
            $this->assertTrue(false, 'Test can only run on HTTP site');
            return;
        }

        $session_id = $this->establish_admin_callback_session();

        if (is_local_machine()) {
            set_value('disable_ssl_for__' . get_base_url_hostname(), '1');
        }
        $test = http_get_contents('https://' . get_base_url_hostname(), ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0]);
        if ($test === null) {
            $this->assertTrue(false, 'SSL not running on this machine');
            return;
        }

        global $HTTPS_PAGES_CACHE;
        $HTTPS_PAGES_CACHE = null;

        set_value('disable_ssl_for__' . get_base_url_hostname(), '1');

        if (get_forum_type() == 'cns') {
            $page_link = 'forum:forumview';
            $page = 'forumview';
        } else {
            $page_link = ':recommend';
            $page = 'recommend';
        }

        // HTTPS (SSL) version
        $GLOBALS['SITE_DB']->query_insert('https_pages', ['https_page_name' => $page_link], false, true/*in case previous test didn't finish*/);
        $HTTPS_PAGES_CACHE = null;
        erase_persistent_cache();
        $url = build_url(['page' => $page], get_module_zone($page));
        $c = http_get_contents($url->evaluate(), ['cookies' => [get_session_cookie() => $session_id], 'convert_to_internal_encoding' => true, 'timeout' => 20.0]);
        if ($this->debug) {
            var_dump($c);
        }
        $matches = [];
        $num_matches = preg_match_all('#src="(http://[^"]*)#', $c, $matches);
        $bad_images = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $bad_images[] = $matches[1][$i];
        }
        $this->assertTrue($num_matches == 0, 'HTTPS version failed (HTTP embed[s] [e.g. image] found) on ' . $url->evaluate() . ' (' . implode(', ', $bad_images) . ')');

        // HTTP version
        $GLOBALS['SITE_DB']->query_delete('https_pages', ['https_page_name' => $page_link]);
        $HTTPS_PAGES_CACHE = null;
        erase_persistent_cache();
        $url = build_url(['page' => $page], get_module_zone($page));
        $c = http_get_contents($url->evaluate(), ['cookies' => [get_session_cookie() => $session_id], 'convert_to_internal_encoding' => true, 'timeout' => 20.0]);
        if ($this->debug) {
            var_dump($c);
        }
        $matches = [];
        $num_matches = preg_match_all('#src="(https://[^"]*)#', $c, $matches);
        $bad_images = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $bad_images[] = $matches[1][$i];
        }
        $this->assertTrue($num_matches == 0, 'HTTP version failed (HTTPS embed[s] [e.g. image] found) on ' . $url->evaluate() . ' (' . implode(', ', $bad_images) . ')');
    }

    public function tearDown()
    {
        set_value('disable_ssl_for__' . get_base_url_hostname(), '0');

        parent::tearDown();
    }
}
