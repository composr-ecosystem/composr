<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class newsletter_test_set extends cms_test_case
{
    public $news_id;

    public function setUp()
    {
        parent::setUp();

        require_code('newsletter');

        $this->news_id = add_newsletter('New Offer', 'The new offer of the week.');

        $this->assertTrue('New Offer' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('newsletters', 'title', array('id' => $this->news_id))));
    }

    public function testEditNewsletter()
    {
        edit_newsletter($this->news_id, 'Thanks', 'Thank you');

        $this->assertTrue('Thanks' == get_translated_text($GLOBALS['SITE_DB']->query_select_value('newsletters', 'title', array('id' => $this->news_id))));
    }

    public function testVariableSubstitution()
    {
        // Comcode to plain text...

        $message = 'abc {forename} {surname} {name} {email_address} {sendid} {123}';
        $subject = 'def {forename}';
        $forename = 'ghi';
        $surname = 'jkl';
        $name = 'mno';
        $email_address = 'pqr@example.com';
        $sendid = 'stu';
        $hash = 'vwx';
        $extra_mappings = array(
            '123' => 'yz'
        );

        $wrapped = do_template('NEWSLETTER_DEFAULT_FCOMCODE', array('CONTENT' => $message, 'LANG' => fallback_lang(), 'SUBJECT' => $subject), null, false, null, '.txt', 'text');

        $newsletter_message_substituted = newsletter_variable_substitution($wrapped->evaluate(), $subject, $forename, $surname, $name, $email_address, $sendid, $hash, $extra_mappings);

        require_code('mail');
        $rendered = comcode_to_clean_text($newsletter_message_substituted);

        $expected = "abc ghi jkl mno pqr@example.com stu yz\n\n-------------------------\n\nYou can unsubscribe from this newsletter at: http://localhost/composr-copy/newsletter/unsub/tu.htm?hash=vwx";
        $this->assertTrue($rendered == $expected);

        // Comcode to HTML...

        $rendered = static_evaluate_tempcode(comcode_to_tempcode($newsletter_message_substituted, null, true));
        $expected = "abc ghi jkl mno pqr@example.com stu yz<br /><br /><br /><hr />\n<span style=\"  font-size: 0.8em;\">You can unsubscribe from this newsletter at: <a class=\"user_link\" href=\"http://localhost/composr-copy/newsletter/unsub/tu.htm?hash=vwx\" target=\"_top\">http://localhost/composr-copy/newsletter/unsub/tu.htm?hash=vwx</a></span><br /><br />";
        $this->assertTrue($rendered == $expected);
    }

    public function tearDown()
    {
        delete_newsletter($this->news_id);

        parent::tearDown();
    }
}
