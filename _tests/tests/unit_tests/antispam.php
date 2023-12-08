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
class antispam_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('antispam');
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
        $this->assertTrue(is_string(http_download_file('https://www.tornevall.net/', null, false))); // Very rough, at least tells us URL still exists
    }

    public function testStopForumSpamSubmit()
    {
        $this->assertTrue(is_string(http_download_file('http://www.stopforumspam.com/add.php', null, false))); // Very rough, at least tells us URL still exists
    }
}
