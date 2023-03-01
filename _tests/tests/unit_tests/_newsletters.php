<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class _newsletters_test_set extends cms_test_case
{
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
        try {
            list($spam_report, $spam_score) = email_spam_check($mime_email);
            $this->assertTrue($spam_report !== null, 'Failed to retrieve spam report');
            $this->assertTrue($spam_score !== null, 'Failed to retrieve spam score');
        } catch (Exception $e) {
            $this->assertTrue(false, 'Failed to retrieve spam information: ' . $e->getMessage());
        }
    }
}
