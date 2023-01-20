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
