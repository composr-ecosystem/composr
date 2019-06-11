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
class newsletters_test_set extends cms_test_case
{
    protected $news_id;

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

    public function testSpamCheck()
    {
        $mime_email = 'From: John Doe <example@example.com>
MIME-Version: 1.0
Content-Type: multipart/mixed;
        boundary="XXXXboundary text"

This is a multipart message in MIME format.

--XXXXboundary text
Content-Type: text/plain

this is the body text

--XXXXboundary text
Content-Type: text/plain;
Content-Disposition: attachment;
        filename="test.txt"

this is the attachment text

--XXXXboundary text--';

        require_code('mail');
        require_code('mail2');
        list($spam_report, $spam_score, $raw, $http_response) = email_spam_check($mime_email);

        $this->assertTrue($spam_report !== null, 'Failed to retrieve spam report [' . serialize($raw) . ', ' . serialize($http_response) . ']');
        $this->assertTrue($spam_score !== null, 'Failed to retrieve spam score [' . serialize($raw) . ', ' . serialize($http_response) . ']');
    }

    public function tearDown()
    {
        delete_newsletter($this->news_id);

        parent::tearDown();
    }
}
