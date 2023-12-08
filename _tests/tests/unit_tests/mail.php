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
class mail_test_set extends cms_test_case
{
    public function testAttachmentCleanup()
    {
        foreach (array('MAIL', 'NOTIFICATIONS') as $mode) {
            $a = cms_tempnam();
            cms_file_put_contents_safe($a, 'test');
            $b = get_custom_file_base() . '/safe_mode_temp/' . uniqid('', true);
            cms_file_put_contents_safe($b, 'test');
            $attachments = array($a => 'foo.txt', $b => 'bar.txt');

            $GLOBALS['SITE_INFO']['no_email_output'] = '1';

            $this->assertTrue(file_exists($a), $mode . ' 1 : Expected temp file to exist, but it did not.');
            $this->assertTrue(file_exists($b), $mode . ' 1 : Expected safe mode temp file to exist, but it did not.');

            switch ($mode) {
                case 'MAIL':
                    require_code('mail');
                    mail_wrap('test', 'test', array('test@example.com'), null, '', '', 3, $attachments);
                    break;

                case 'NOTIFICATIONS':
                    require_code('notifications');
                    set_mass_import_mode();
                    $_GET['keep_debug_notifications'] = '1';
                    dispatch_notification('error_occurred', '', 'test', 'test', array(get_member()), get_member(), 1, false, true, '', '', '', '', '', '', $attachments);
                    break;
            }

            $this->assertTrue(!file_exists($a), $mode . ' 2 : Expected temp file to NOT exist, but it did.');
            $this->assertTrue(!file_exists($b), $mode . ' 2 : Expected safe mode temp file to NOT exist, but it did.');
        }
    }
}
