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
class _ua_detection_test_set extends cms_test_case
{
    public function setUp()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }
    public function testBotDetection()
    {
        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        $session_id = $this->establish_admin_callback_session();

        $url = build_url(['page' => 'forumview'], 'forum');

        require_code('cns_topics');
        require_code('cns_posts');
        require_code('cns_forums');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_posts_action3');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        $topic_id = cns_make_topic(db_get_first_id(), 'Test', '', 1, 1, 0, 0, null, null, false);
        cns_make_post($topic_id, 'Welcome', 'Welcome to the posts', 0, false, null, 0, null, null, null, null, null, null, null, true, true, null, true, '', null, false, false, false);

        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'ignore_http_status' => true, 'trigger_error' => false, 'ua' => 'bingbot', 'cookies' => [get_session_cookie() => $session_id]]);
        $pass = ($data !== null && strpos($data, 'findpost') === false);
        if (($this->debug) && (!$pass)) {
            @var_dump($data);
        }
        $this->assertTrue($pass, 'Found findpost as bingbot in ' . $url->evaluate());

        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'ignore_http_status' => true, 'trigger_error' => false, 'cookies' => [get_session_cookie() => $session_id]]);
        $pass = ($data !== null && strpos($data, 'findpost') !== false);
        if (($this->debug) && (!$pass)) {
            @var_dump($data);
        }
        $this->assertTrue($pass, 'Could not find findpost in ' . $url->evaluate());
    }

    public function testMobileDetection()
    {
        $url = build_url(['page' => ''], '');

        // Phone
        $uas = [
            'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko; googleweblight) Chrome/38.0.1025.166 Mobile Safari/535.19', // Android
            'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25', // iPhone
        ];
        foreach ($uas as $ua) {
            $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'ua' => $ua, 'timeout' => 20.0]);
            $this->assertTrue($data !== null && strpos($data, '>Mobile version') === false, 'Issue with ' . $ua);
            $this->assertTrue($data !== null && strpos($data, '>Non-Mobile version') !== false, 'Issue with ' . $ua);
        }

        // Desktop
        $uas = [
            'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.10136', // Edge
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.85 Safari/537.36', // Chrome
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:34.0) Gecko/20100101 Firefox/34.0', // Firefox
            'Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.0) Opera 7.02 Bork-edition [en]', // Opera
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)', // IE
            'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25', // iPad
        ];
        foreach ($uas as $ua) {
            $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'ua' => $ua, 'timeout' => 20.0, 'ignore_http_status' => true]);
            $pass = ($data !== null && strpos($data, '>Mobile version') !== false);
            if (($this->debug) && (!$pass)) {
                @var_dump($data);
            }
            $this->assertTrue($pass, 'Issue with ' . $ua);
            $pass = ($data !== null && strpos($data, '>Non-Mobile version') === false);
            if (($this->debug) && (!$pass)) {
                @var_dump($data);
            }
            $this->assertTrue($pass, 'Issue with ' . $ua);
        }
    }
}
