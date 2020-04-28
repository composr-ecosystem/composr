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

/*
Cyrus-imapd is a simple IMAP server that doesn't require integration with system logins, making it safe to use for *testing*.

Installation steps tested on Ubuntu...

1) Run "sudo apt install cyrus-imapd package"

2) Edit /etc/cyrus.conf...
a) disable any non-imap service

3) Edit /etc/imapd.conf...
a) uncomment "admins:" line
b) uncomment "sasl_minimum_layer" line
c) uncomment "sasl_pwcheck_method" line except set it to "alwaystrue"

4) Run "sudo systemctl enable cyrus-imapd"

5) Run "sudo systemctl start cyrus-imapd"

6) IMAP is now running on port 143 and accept any arbitrary logins

7) Add the username Apache is running as to the mail group "sudo usermod -a -G mail <you>"

8) Logout and login / Reboot
*/

/**
 * Composr test case class (unit testing).
 */
class _protocol_imap_test_set extends cms_test_case
{
    protected $key_options;

    public function setUp()
    {
        if (!function_exists('imap_open')) {
            $this->assertTrue(false, 'PHP IMAP extension is required');
            return;
        }

        // Set to local IMAP server on test account
        $this->key_options = $this->load_key_options('imap_');

        // Create connection
        require_code('mail2');
        list($mbox, $server_spec, $host, $username, $password, $port) = $this->get_imap_connection();
        if ($mbox === false) {
            $this->assertTrue(false, 'IMAP connection failed');
            return;
        }

        // Delete any folders on test account to get to a clean state
        $folders = imap_list($mbox, $server_spec, '*');
        foreach ($folders  as $folder) {
            imap_deletemailbox($mbox, $folder);
        }

        imap_close($mbox);

        parent::setUp();
    }

    protected function get_imap_login_details()
    {
        return [
            get_option('imap_host'),
            get_option('imap_username'),
            get_option('imap_password'),
            get_option('imap_port'),
        ];
    }

    protected function get_imap_connection()
    {
        list($host, $username, $password, $port) = $this->get_imap_login_details();

        $server_spec = _imap_server_spec($host, $port);

        $mbox = imap_open($server_spec, $username, $password);

        return [$mbox, $server_spec, $host, $username, $password, $port];
    }

    public function testImapEmpty()
    {
        if (!function_exists('imap_open')) {
            return;
        }

        list($mbox, $server_spec, $host, $username, $password, $port) = $this->get_imap_connection();
        if ($mbox === false) {
            return;
        }

        $folders = imap_list($mbox, $server_spec, '*');
        $this->assertTrue(empty($folders));

        imap_close($mbox);
    }

    public function testBounceFinder()
    {
        if (!function_exists('imap_open')) {
            return;
        }

        list($mbox, $server_spec, $host, $username, $password, $port) = $this->get_imap_connection();
        if ($mbox === false) {
            return;
        }

        $folder = 'test';

        // Create test folder
        imap_createmailbox($mbox, $folder);

        // Test find_mail_folders
        $folders = find_mail_folders($host, $port, $username, $password);
        $this->assertTrue(array_values($folders) == [$folder]);

        // Create test messages
        $this->inject_email('ok@localhost', 'This is a test ok', 'Test message');
        $this->inject_email('bounce@localhost', 'This is a test bounce', 'Delivery to the following recipient failed permanently');

        // Test bounce correctly detected, and no false positives
        $bounces = _find_mail_bounces($host, $port, $type, $folder, $username, $password);
        $this->assertTrue(array_keys($bounces) == ['bounce@localhost']);

        imap_close($mbox);
    }

    protected function inject_email($from, $subject, $body)
    {
        $envelope = [
            'from' => $from,
            'subject' => $subject,
        ];
        $body = [
            'contents.data' => $body,
        ];
        $mime = imap_mail_compose($envelope, $body);

        $socket = fsockopen($this->key_options['imap_lmtp_socket']);
        $this->inject_email_line($socket, "LHLO localhost\r\n");
        $this->inject_email_line($socket, "MAIL FROM:<" . $from . ">\r\n");
        $this->inject_email_line($socket, "RCPT TO:<" . get_option('imap_username') . "@localhost>\r\n");
        $this->inject_email_line($socket, "DATA\r\n");
        $this->inject_email_line($socket, $body . "\r\n.\r\n");
        $this->inject_email_line($socket, "QUIT");
        fclose($socket);
    }

    protected function inject_email_line($socket, $line)
    {
        fwrite($socket, $line;
        $line = fread($socket);
        if ($this->debug) {
            var_dump($line);
        }
    }
}
