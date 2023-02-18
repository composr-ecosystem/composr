<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class antispam_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('antispam');
    }

    public function testHeuristics()
    {
        // NOTE: Do not forget to manually test the heavy pasting heuristic as it is not covered in this test; paste a bunch of stuff in a form and see if post_data gets paste added to it.

        $_POST['foo_alien_code'] = '[link]http://example.com[/link] <a href="http://example.com">foo</a>';
        $_POST['foo_autonomous'] = '[font="Times New Roman"]foo[/font]';
        set_option('spam_heuristic_country', 'IN');
        $_GET['keep_country'] = 'IN';
        unset($_SERVER['HTTP_ACCEPT']);
        $_POST['foo_keywords'] = 'Foo ViagrA Bar';
        $_SERVER['HTTP_USER_AGENT'] = 'Spambot';

        require_code('antispam');
        list($confidence, $scoring) = calculation_internal_heuristic_confidence();

        $this->assertTrue(strpos($scoring, 'alien_code') !== false);
        $this->assertTrue(strpos($scoring, 'autonomous') !== false);
        $this->assertTrue(strpos($scoring, 'country') !== false);
        if (!is_cli()) {
            $this->assertTrue(strpos($scoring, 'header_absence') !== false);
        }
        $this->assertTrue(strpos($scoring, 'keywords') !== false);
        $this->assertTrue(strpos($scoring, 'links') !== false);
        $this->assertTrue(strpos($scoring, 'user_agents') !== false);

        if (is_guest()) {
            $this->assertTrue(strpos($scoring, 'guest') !== false);
        } else {
            $this->assertTrue(strpos($scoring, 'guest') === false);
        }
    }

    public function testRBL()
    {
        list($result) = check_rbl('rbl.efnetrbl.org', '127.0.0.1');
        $this->assertTrue($result != ANTISPAM_RESPONSE_ERROR);
    }

    public function testHTTPBL()
    {
        // Disabled by default as it requires a key.
        /*
        require_code('antispam');
        $key = '';

        $prev_stale = get_option('spam_stale_threshold');
        set_option('spam_stale_threshold', '20', 0);

        // Arrays of HTTPBL IP query, expected ANTISPAM_RESPONSE_*, expected confidence score (float, 0.0 - 1.0).
        $tests = [
            ['127.1.1.0', ANTISPAM_RESPONSE_UNLISTED, null], // Test unlisted
            ['127.1.1.1', ANTISPAM_RESPONSE_ACTIVE, ((1.0 / 255.0) * 4.0)], // Test threat type 1
            ['127.1.1.2', ANTISPAM_RESPONSE_ACTIVE, ((1.0 / 255.0) * 4.0)], // Test threat type 2
            ['127.1.1.4', ANTISPAM_RESPONSE_ACTIVE, ((1.0 / 255.0) * 4.0)], // Test threat type 4
            ['127.1.40.1', ANTISPAM_RESPONSE_ACTIVE, ((40.0 / 255.0) * 4.0)], // Test threat level 40
            ['127.1.80.1', ANTISPAM_RESPONSE_ACTIVE, ((80.0 / 255.0) * 4.0)], // Test threat level 80
            ['127.10.1.1', ANTISPAM_RESPONSE_ACTIVE, ((1.0 / 255.0) * 4.0)], // Test 10 days old
            ['127.40.1.1', ANTISPAM_RESPONSE_STALE, null], // Test 40 days old
        ];
        foreach ($tests as $test) {
            $spam_check = check_rbl($key . '.*.dnsbl.httpbl.org', $test[0]);
            $this->assertTrue(($spam_check[0] == $test[1]), 'Expected ' . $test[0] . ' test to return constant ' . strval($test[1]) . ' but instead got ' . strval($spam_check[0]));
            if (($test[2] === null) && ($spam_check[1] !== null)) {
                $this->assertTrue(false, 'Expected ' . $test[0] . ' test to return unlisted but instead got a confidence of ' . float_to_raw_string($spam_check[1]));
            } elseif (($test[2] !== null) && ($spam_check[1] === null)) {
                $this->assertTrue(($spam_check[1] == $test[2]), 'Expected ' . $test[0] . ' test to return a confidence of ' . float_to_raw_string($test[2]) . ' but instead got unlisted');
            } elseif (($test[2] !== null) && ($spam_check[1] !== null)) {
                $this->assertTrue(($spam_check[1] == $test[2]), 'Expected ' . $test[0] . ' test to return a confidence of ' . float_to_raw_string($test[2]) . ' but instead got ' . float_to_raw_string($spam_check[1]));
            }
        }

        set_option('spam_stale_threshold', $prev_stale, 0);
        */
    }

    public function testStopForumSpam()
    {
        list($result) = _check_stopforumspam('127.0.0.1');
        $this->assertTrue($result != ANTISPAM_RESPONSE_ERROR);
    }

    public function testTornevallSubmit()
    {
        $this->assertTrue(is_string(http_get_contents('https://api.tornevall.net/', ['timeout' => 20.0, 'trigger_error' => false]))); // Very rough, at least tells us URL still exists
    }

    public function testStopForumSpamSubmit()
    {
        $this->assertTrue(is_string(http_get_contents('https://www.stopforumspam.com/add.php', ['timeout' => 20.0, 'trigger_error' => false]))); // Very rough, at least tells us URL still exists
    }
}
