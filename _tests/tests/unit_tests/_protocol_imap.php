<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

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
d) uncomment "allowplaintext" line

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
        $this->key_options = $this->load_key_options('mail_');

        // Create connection
        require_code('mail');
        require_code('mail2');
        list($mbox, $server_spec, $host, $username, $password, $port, $type) = $this->get_imap_connection();
        if ($mbox === false) {
            $this->assertTrue(false, 'IMAP connection failed: ' . @strval(imap_errors()));
            return;
        }

        imap_close($mbox);

        parent::setUp();
    }

    protected function get_imap_login_details()
    {
        return [
            get_option('mail_server_host'),
            get_option('mail_username'),
            get_option('mail_password'),
            intval(get_option('mail_server_port')),
            get_option('mail_server_type'),
        ];
    }

    protected function get_imap_connection()
    {
        list($host, $username, $password, $port, $type) = $this->get_imap_login_details();

        $server_spec = _imap_server_spec($host, $port);

        $mbox = imap_open($server_spec, $username, $password);

        return [$mbox, $server_spec, $host, $username, $password, $port, $type];
    }

    public function testBounceFinder()
    {
        if (!function_exists('imap_open')) {
            return;
        }

        list($mbox, $server_spec, $host, $username, $password, $port, $type) = $this->get_imap_connection();
        if ($mbox === false) {
            return;
        }

        // Create test folder
        $test_folder = 'test';
        $folders = find_mail_folders($host, $port, $type, $username, $password);
        if (!in_array($test_folder, $folders)) {
            $success = imap_createmailbox($mbox, $test_folder);
            $this->assertTrue($success, 'Failed to create test folder: ' . @strval(imap_errors()));
        }

        // Test find_mail_folders
        $folders = find_mail_folders($host, $port, $type, $username, $password);
        $this->assertTrue(in_array($test_folder, $folders));

        // Create test messages
        $this->inject_email($username . '@localhost', 'tester@localhost', 'This is a test ok', 'Test message');
        $this->inject_email($username . '@localhost', 'tester@localhost', 'This is a test bounce', 'Delivery to the following recipient failed permanently: bounce@localhost');

        // Test bounce correctly detected, and no false positives
        $bounces = _find_mail_bounces($host, $port, $type, 'INBOX', $username, $password);
        $_bounces = array_keys($bounces);
        $this->assertTrue(in_array('bounce@localhost', $_bounces) && !in_array('ok@localhost', $_bounces) && !in_array('tester@localhost', $_bounces));

        imap_close($mbox);
    }

    protected function inject_email($to, $from, $subject, $body)
    {
        $c_envelope = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'date' => date('r'),
        ];
        $c_body = [
            'contents.data' => $body,
        ];
        $mime = imap_mail_compose($c_envelope, [$c_body]);

        $socket = fsockopen($this->key_options['mail_server_lmtp_socket']);
        $this->inject_email_line($socket, 'LHLO localhost');
        $this->inject_email_line($socket, 'MAIL FROM:<' . $from . '>');
        $this->inject_email_line($socket, 'RCPT TO:<' . $to . '>');
        $this->inject_email_line($socket, 'DATA');
        $this->inject_email_line($socket, $mime . "\r\n.");
        $this->inject_email_line($socket, 'QUIT');
        fclose($socket);
    }

    protected function inject_email_line($socket, $line_in)
    {
        fwrite($socket, $line_in . "\r\n");
        $line_out = fread($socket, 1024);
        if ($this->debug) {
            var_dump($line_out);
        }
    }
}
