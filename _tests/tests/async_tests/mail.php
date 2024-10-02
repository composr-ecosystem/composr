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
class mail_test_set extends cms_test_case
{
    public function testAttachmentCleanup()
    {
        foreach (['MAIL', 'NOTIFICATIONS'] as $mode) {
            $a = cms_tempnam();
            cms_file_put_contents_safe($a, 'test');
            $b = get_custom_file_base() . '/temp/' . uniqid('', true);
            cms_file_put_contents_safe($b, 'test');
            $attachments = [$a => 'foo.txt', $b => 'bar.txt'];

            $GLOBALS['SITE_INFO']['no_email_output'] = '1';

            $this->assertTrue(file_exists($a));
            $this->assertTrue(file_exists($b));

            $expect_files_still = false;

            switch ($mode) {
                case 'MAIL':
                    require_code('mail');
                    $dispatcher = dispatch_mail('test', 'test', '', ['test@example.com'], null, '', '', ['attachments' => $attachments, 'bypass_queue' => true, 'leave_attachments_on_failure' => true]);
                    if (!$dispatcher->worked) {
                        $expect_files_still = true;
                    }
                    break;

                case 'NOTIFICATIONS':
                    require_code('notifications');
                    set_mass_import_mode();
                    $_GET['keep_debug_notifications'] = '1';
                    dispatch_notification('error_occurred', '', 'test', 'test', [get_member()], get_member(), ['attachments' => $attachments]);
                    break;
            }

            if (!$expect_files_still) {
                $this->assertTrue(!file_exists($a));
                $this->assertTrue(!file_exists($b));
            }

            if (file_exists($a)) {
                unlink($a);
            }
            if (file_exists($b)) {
                unlink($b);
            }
        }
    }
}
