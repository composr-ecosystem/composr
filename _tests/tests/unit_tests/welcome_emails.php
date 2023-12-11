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
class welcome_emails_test_set extends cms_test_case
{
    public $email_id;

    public function setUp()
    {
        parent::setUp();

        require_code('cns_general_action');
        require_code('cns_general_action2');
        $this->email_id = cns_make_welcome_email('test_mail', 'test subject', 'test content', 1262671781, 0, null, '');
        $this->assertTrue('test_mail' == $GLOBALS['SITE_DB']->query_select_value('f_welcome_emails', 'w_name', array('id' => $this->email_id)));
    }

    public function testEditWelcomeEmail()
    {
        cns_edit_welcome_email($this->email_id, 'test_mail1', 'test_subject1', 'test content1', 1262671781, 0, null, '');
        $this->assertTrue('test_mail1' == $GLOBALS['SITE_DB']->query_select_value('f_welcome_emails', 'w_name', array('id' => $this->email_id)));
    }

    public function tearDown()
    {
        cns_delete_welcome_email($this->email_id);

        parent::tearDown();
    }
}
