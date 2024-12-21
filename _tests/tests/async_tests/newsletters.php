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
class newsletters_test_set extends cms_test_case
{
    protected $news_id;

    public function setUp()
    {
        parent::setUp();

        require_code('newsletter');
        require_code('newsletter2');

        $this->news_id = add_newsletter('New Offer', 'The new offer of the week.');

        $this->assertTrue('New Offer' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('newsletters', 'title', ['id' => $this->news_id])));
    }

    public function testEditNewsletter()
    {
        edit_newsletter($this->news_id, 'Thanks', 'Thank you');

        $this->assertTrue('Thanks' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('newsletters', 'title', ['id' => $this->news_id])));
    }

    public function testVariableSubstitution()
    {
        $message_raw = 'abc {FORENAME} {SURNAME} {NAME} {EMAIL_ADDRESS} {SEND_ID} {X123}';
        $subject = 'def {FORENAME}';
        $forename = 'ghi';
        $surname = 'jkl';
        $name = 'mno';
        $email_address = 'pqr@example.com';
        $send_id = 'stu';
        $hash = 'vwx';
        $extra_mappings = [
            'x123' => 'yz'
        ];

        $message_wrapped = newsletter_prepare($message_raw, $subject, null, $forename, $surname, $name, $email_address, $send_id, $hash, $extra_mappings);

        // Comcode
        $got = preg_replace('#\][^\[\]]*\[/url\]#', '][/url]', $message_wrapped);
        $expected = "abc ghi jkl mno pqr@example.com stu yz\n\n\n-------------------------\n\n[font size=\"0.8\"]You can [url=\"unsubscribe\"][/url] from this newsletter[/font]\n\n";
        $this->assertTrue($got == $expected, 'Got: ' . $got . '; Expected: ' . $expected);

        // HTML
        $got = str_replace(' data-click-stats-event-track="{}"', '', preg_replace('# href="[^"]*"#', ' href=""', comcode_to_tempcode($message_wrapped, null, true)->evaluate()));
        $expected = "#^abc ghi jkl mno pqr@example.com stu yz<br /><br /><br /><hr />\n<span style=\"  font-size: 0.8em;\">You can <a class=\"user-link\" href=\"\" (rel=\"external\" target=\"_blank\"|target=\"_top\")( title=\"xxx \(this link will open in a new window\)\")?" . ">unsubscribe</a> from this newsletter</span><br /><br />$#";
        $this->assertTrue(preg_match($expected, $got) != 0, 'Got: ' . $got . '; Expected: ' . $expected);
    }

    public function tearDown()
    {
        delete_newsletter($this->news_id);

        parent::tearDown();
    }
}
