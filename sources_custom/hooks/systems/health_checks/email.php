<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/*EXTRA FUNCTIONS: dns_get_record|imap_.+*/

/**
 * Hook class.
 */
class Hook_health_check_email extends Hook_Health_Check
{
    protected $category_label = 'E-mail';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @return array A pair: category label, list of results
     */
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $this->process_checks_section('testEmailQueue', 'E-mail queue', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testEmailConfiguration', 'E-mail configuration', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testSPF', 'SPF', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testEmailOperation', 'E-mail operation (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testSMTPBlacklisting', 'SMTP blacklisting', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

        return array($this->category_label, $this->results);
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testEmailQueue($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (get_option('mail_queue_debug') == '1') {
            return;
        }

        $sql = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'logged_mail_messages WHERE m_queued=1 AND m_date_and_time<' . strval(time() - 60 * 60 * 1);
        $count = $GLOBALS['SITE_DB']->query_value_if_there($sql);

        $this->assert_true($count == 0, 'The e-mail queue has e-mails still not sent within the last hour');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testEmailConfiguration($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if ((php_function_allowed('getmxrr')) && (php_function_allowed('checkdnsrr'))) {
            $domains = $this->get_mail_domains();

            foreach ($domains as $domain => $email) {
                $mail_hosts = array();
                $result = @getmxrr($domain, $mail_hosts);
                $this->assert_true($result, 'Could not look up MX records for our [tt]' . $email . '[/tt] e-mail address');

                foreach (array_unique($mail_hosts) as $host) {
                    $this->assert_true(@checkdnsrr($host, 'A'), 'Mail server MX DNS does not seem to be set up properly for our [tt]' . $email . '[/tt] e-mail address (host=[tt]' . $host . '[/tt])');

                    if ((php_function_allowed('fsockopen')) && (php_function_allowed('gethostbyname'))/* && (php_function_allowed('gethostbyaddr'))*/) {
                        // See if SMTP running
                        $errno = 0;
                        $errstr = '';
                        $socket = @fsockopen($host, 25, $errno, $errstr, 4.0);
                        $can_connect = ($socket !== false);
                        $this->assert_true($can_connect, 'Could not connect to SMTP server for [tt]' . $email . '[/tt] address (host=[tt]' . $host . '[/tt]); possibly server network is firewalled on this port though');
                        if ($can_connect) {
                            fread($socket, 1024);
                            fwrite($socket, 'HELO ' . $domain . "\n");
                            $data = fread($socket, 1024);
                            fclose($socket);

                            $matches = array();
                            $has_helo = preg_match('#^250 ([^\s]*)#', $data, $matches) != 0;
                            $this->assert_true($has_helo, 'Could not get HELO response from SMTP server for [tt]' . $email . '[/tt] address (host=[tt]' . $host . '[/tt])');
                            if ($has_helo) {
                                $reported_host = $matches[1];

                                /*
                                $reverse_dns_host = @gethostbyaddr(gethostbyname($host));  Fails way too much

                                $this->assert_true($reported_host == $reverse_dns_host, 'HELO response from SMTP server (' . $reported_host . ') not matching reverse DNS (' . $reverse_dns_host . ') for ' . $email . ' address');
                                */
                            }
                        }
                    } else {
                        $this->state_check_skipped('PHP fsockopen/gethostbyname function(s) not available'); // /gethostbyaddr
                    }
                }

                // What if mailbox missing? Or generally e-mail not received
                if ($manual_checks) {
                    require_code('mail');
                    mail_wrap('Test', 'Test e-mail from Health Check', array($email));
                    $this->state_check_manual('An e-mail was sent to ' . $email . ', confirm it was received');
                }
            }
        } else {
            $this->state_check_skipped('PHP getmxrr/checkdnsrr function(s) not available');
        }
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testSPF($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            $this->state_check_skipped('Will not check automatically because we do not know if SMTP relaying would be in place');
            return;
        }

        if (get_option('smtp_sockets_use') == '0') {
            if ((php_function_allowed('dns_get_record')) && (php_function_allowed('gethostbyname'))) {
                $domains = $this->get_domains();

                foreach ($domains as $self_domain) {
                    $self_ip = @gethostbyname($self_domain);

                    $mail_domains = $this->get_mail_domains();

                    foreach ($mail_domains as $domain => $email) {
                        $passed = $this->do_spf_check($domain, $self_domain, $self_ip);

                        $this->assert_true($passed !== null, 'SPF record is not set for [tt]' . $domain . '[/tt], setting it will significantly reduce the chance of fraud and spam blockage');

                        if ($passed !== null) {
                            $this->assert_true($passed, 'SPF record for [tt]' . $domain . '[/tt] does not allow the server to send (either blocked or neutral). Maybe your local SMTP is relaying via another server, but check that.');
                        }
                    }
                }
            } else {
                $this->state_check_skipped('PHP dns_get_record/gethostbyname function(s) not available');
            }
        }
    }

    /**
     * Find whether SPF on a domain is working.
     *
     * @param  string $domain The domain name
     * @param  string $self_domain Domain of sending server to match against
     * @param  string $self_ip IP address of sending server to match against
     * @return ?boolean Whether the check matches (null: N/A)
     */
    protected function do_spf_check($domain, $self_domain, $self_ip)
    {
        $records = @dns_get_record($domain, DNS_TXT);
        foreach ($records as $record) {
            if (!isset($record['txt'])) {
                continue;
            }
            $_record = $record['txt'];

            $matches = array();
            if (preg_match('#^v=spf1 (.*)#', $_record, $matches) != 0) {
                $passed = false;
                $blocked = false;
                $passed_wildcard = false;

                $parts = explode(' ', $matches[1]);
                foreach ($parts as $part) {
                    // Normalise to something more manageable
                    $prefix = substr($part, 0, 1);
                    if (in_array($prefix, array('+', '-', '#', '?'))) {
                        $part = substr($part, 1);
                    } else {
                        $prefix = '+';
                    }
                    if ($prefix == '~') {
                        $prefix = '-';
                    }

                    if ($this->spf_term_matches($part, $self_domain, $self_ip)) {
                        switch ($prefix) {
                            case '+':
                                $passed = true;
                                break;

                            case '-':
                                $blocked = true;
                                break;
                        }
                    }

                    if ($part == 'all') {
                        switch ($prefix) {
                            case '+':
                                $passed_wildcard = true;
                                break;

                            case '-':
                                // We ignore this, we consider neutrality and blocking all equivalently
                                break;
                        }
                    }
                }

                $this->assert_true(!$passed_wildcard, 'SPF record for [tt]' . $domain . '[/tt] is wildcarded, so offers no real value');

                return ($passed || $passed_wildcard) && !$blocked;
            }
        }

        return null;
    }

    /**
     * Find whether an SPF term matches.
     *
     * @param  string $part The SPF term
     * @param  string $self_domain Domain of sending server to match against
     * @param  string $self_ip IP address of sending server to match against
     * @return boolean Whether the check matches
     */
    protected function spf_term_matches($part, $self_domain, $self_ip)
    {
        if (substr($part, 0, 4) == 'ip4:') {
            $_part = substr($part, 4);

            if (strpos($_part, '/') === false) {
                return ($self_ip == $_part);
            } else {
                require_code('failure');
                return ip_cidr_check($self_ip, $_part);
            }
        }

        if (($part == 'a:' . $self_domain) || ($part == 'a')) {
            return true;
        }

        if (($part == 'mx:' . $self_domain) || ($part == 'mx')) {
            return true;
        }
        if (($part == 'ptr:' . $self_domain) || ($part == 'ptr')) {
            return true;
        }

        if (substr($part, 0, 8) == 'include:') {
            $include = substr($part, 8);
            $ret = $this->do_spf_check($include, $self_domain, $self_ip);

            $this->assert_true($ret !== null, 'SPF include [tt]' . $include . '[/tt] is broken');

            if ($ret === null) {
                $ret = false;
            }
            return $ret;
        }

        if (substr($part, 0, 9) == 'redirect:') {
            $redirect = substr($part, 9);
            $ret = $this->do_spf_check($redirect, $self_domain, $self_ip);

            $this->assert_true($ret !== null, 'SPF redirect [tt]' . $redirect . '[/tt] is broken');

            if ($ret === null) {
                $ret = false;
            }
            return $ret;
        }

        return false;
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testEmailOperation($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('imap_open')) {
            require_code('mail');
            require_code('mail2');

            if ($use_test_data_for_pass === null) {
                $address = get_option('hc_mail_address');

                $server = get_option('hc_mail_server');
                $port = intval(get_option('hc_mail_server_port'));
                $type = get_option('hc_mail_server_type');

                $username = get_option('hc_mail_username');
                $password = get_option('hc_mail_password');
            } else {
                $address = 'test@ocproducts.com';

                $server = 'imap.gmail.com';
                $port = 993;
                $type = 'imaps';

                $username = 'test@ocproducts.com';
                $password = '!Xtest1234';
            }

            if (($address == '') || ($server == '') || ($username == '')) {
                $this->state_check_skipped('Test e-mail account not fully configured');
                return;
            }

            $uniq = uniqid('', true);
            $subject = brand_name() . ' Self-Test (' . $uniq . ')';
            mail_wrap($subject, 'Test', array($address), null, '', '', 3, null, false, null, false, false, false, 'MAIL', true);

            $wait_time = intval(get_option('hc_mail_wait_time'));

            $good = false;
            $time_started = time();
            $ref = _imap_server_spec($server, $port, $type);
            $i = 0;
            do {
                if (php_function_allowed('usleep')) {
                    usleep(3000000);
                }

                $resource = @imap_open($ref . 'INBOX', $username, $password, CL_EXPUNGE);
                $ok = ($resource !== false);
                if ($i == 0) {
                    $this->assert_true($ok, 'Could not connect to IMAP server, [tt]' . $server . '[/tt]');
                    if (!$ok) {
                        return;
                    }
                }
                if ($ok) {
                    $list = imap_search($resource, 'FROM "' . get_site_name() . '"');
                    if ($list === false) {
                        $list = array();
                    }
                    foreach ($list as $l) {
                        $header = imap_headerinfo($resource, $l);

                        $_subject = $header->subject;

                        if (strpos($_subject, $uniq) !== false) {
                            $good = true;
                        }

                        if (strpos($_subject, brand_name() . ' Self-Test') !== false) {
                            imap_delete($resource, $l); // Auto-clean-up
                        }
                    }

                    imap_close($resource);
                }

                $time_taken = time() - $time_started;

                $i++;
            }
            while ((!$good) && ($time_taken < $wait_time) && ($ok));

            $this->assert_true($good, 'Did not receive test e-mail within ' . display_time_period($wait_time));
        } else {
            $this->state_check_skipped('PHP imap_open function not available');
        }
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testSMTPBlacklisting($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        require_code('antispam');

        if ((php_function_allowed('getmxrr')) && (php_function_allowed('gethostbyname'))) {
            $domains = $this->get_mail_domains();

            foreach ($domains as $domain => $email) {
                $mail_hosts = array();
                $result = @getmxrr($domain, $mail_hosts);
                $ok = ($result !== false);
                if (!$ok) {
                    $this->state_check_skipped('Could not look up MX of [tt]' . $domain . '[/tt]');
                    continue;
                }

                foreach ($mail_hosts as $host) {
                    $ip = @gethostbyname($host);
                    $rbls = array(
                        '*.dnsbl.sorbs.net',
                        '*.bl.spamcop.net',
                    );
                    foreach ($rbls as $rbl) {
                        $response = rbl_resolve($ip, $rbl, true);
                        $blocked = ($response === array('127', '0', '0', '2'));
                        $this->assert_true(!$blocked, 'The [tt]' . $host . '[/tt] SMTP server is blocked by [tt]' . $rbl . '[/tt]');
                    }
                }
            }
        } else {
            $this->state_check_skipped('PHP getmxrr/gethostbyname function(s) not available');
        }
    }
}
