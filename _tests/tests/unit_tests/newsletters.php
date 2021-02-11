<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
class newsletters_test_set extends cms_test_case
{
    protected $news_id;

    public function setUp()
    {
        parent::setUp();

        require_code('newsletter');

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
        // Comcode to plain text...

        $message = 'abc {forename} {surname} {name} {email_address} {send_id} {123}';
        $subject = 'def {forename}';
        $forename = 'ghi';
        $surname = 'jkl';
        $name = 'mno';
        $email_address = 'pqr@example.com';
        $send_id = 'stu';
        $hash = 'vwx';
        $extra_mappings = [
            '123' => 'yz'
        ];

        $wrapped = do_template('NEWSLETTER_DEFAULT_FCOMCODE', ['CONTENT' => $message, 'LANG' => fallback_lang(), 'SUBJECT' => $subject], null, false, null, '.txt', 'text');

        $newsletter_message_substituted = newsletter_variable_substitution($wrapped->evaluate(), $subject, $forename, $surname, $name, $email_address, $send_id, $hash, $extra_mappings);
        $rendered = strip_comcode($newsletter_message_substituted);
        $rendered = preg_replace('#\([^)]*hash=[^)]*\)#', '(hash=vwx)', $rendered);

        $expected = "abc ghi jkl mno pqr@example.com stu yz\n\n-------------------------\n\nYou can unsubscribe (hash=vwx) from this newsletter";
        $this->assertTrue($rendered == $expected);

        // Comcode to HTML...

        $rendered = static_evaluate_tempcode(comcode_to_tempcode($newsletter_message_substituted, null, true));
        $rendered = preg_replace('# href="[^"]*"#', ' href=""', $rendered);
        $expected = "abc ghi jkl mno pqr@example.com stu yz<br /><br /><br /><hr />\n<span style=\"  font-size: 0.8em;\">You can <a class=\"user-link\" href=\"\" target=\"_top\">unsubscribe</a> from this newsletter</span><br /><br />";
        $this->assertTrue($rendered == $expected);
    }

    public function tearDown()
    {
        delete_newsletter($this->news_id);

        parent::tearDown();
    }
}
