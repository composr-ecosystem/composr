<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    better_mail
 */

/*EXTRA FUNCTIONS: error_log|Swift_.**/

/*FORCE_ORIGINAL_LOAD_FIRST*/

/**
 * Hook class.
 */
class Hx_health_check_email extends Hook_health_check_email
{
    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @param  ?array $urls_or_page_links List of URLs and/or page-links to operate on, if applicable (null: those configured)
     * @param  ?array $comcode_segments Map of field names to Comcode segments to operate on, if applicable (null: N/A)
     */
    public function testSMTPLogin(int $check_context, bool $manual_checks = false, bool $automatic_repair = false, ?bool $use_test_data_for_pass = null, ?array $urls_or_page_links = null, ?array $comcode_segments = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            $this->log('Skipped; we are running from installer.');
            return;
        }
        if ($check_context == CHECK_CONTEXT__SPECIFIC_PAGE_LINKS) {
            $this->log('Skipped; running on specific page links.');
            return;
        }

        if (!addon_installed('better_mail')) {
            return;
        }

        if ((get_option('smtp_sockets_use') == '0') || (!php_function_allowed('fsockopen'))) {
            $this->stateCheckSkipped('SMTP mailer not enabled');

            return;
        }

        $host = get_option('smtp_sockets_host');
        $port = intval(get_option('smtp_sockets_port'));
        $username = get_option('smtp_sockets_username');
        $password = get_option('smtp_sockets_password');

        require_lang('mail');

        $error = null;

        require_code('swift_mailer/vendor/autoload');
        $transport = (new Swift_SmtpTransport($host, $port))
            ->setUsername($username)
            ->setPassword($password);

        $encryption = get_value('mail_encryption');
        if ($encryption === null) {
            if ($port == 25) {
                $encryption = 'tcp'; // No encryption
            } elseif ($port == 465) {
                $encryption = 'ssl';
            } elseif ($port == 587)  {
                $encryption = 'tls';
            }
        }

        $disabled_ssl_verify = ((function_exists('get_value')) && (get_value('disable_ssl_for__' . $host) === '1'));

        $crt_path = get_file_base() . '/data/curl-ca-bundle.crt';
        $ssl_options = [
            'verify_peer' => !$disabled_ssl_verify,
            'verify_peer_name' => !$disabled_ssl_verify,
            'cafile' => $crt_path,
            'SNI_enabled' => true,
        ];

        $transport->setEncryption($encryption);
        $transport->setStreamOptions(['ssl' => $ssl_options]);

        $mailer = new Swift_Mailer($transport);

        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

        $attempts = 3;

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $transport->start();

                $error = null;
                break;
            }
            catch (Swift_SwiftException $e) {
                $error = $e->getMessage();
            }
        }

        $this->assertTrue($error === null, 'SMTP login failed with ' . (($error === null) ? 'N/A' : $error));

        if ($error !== null) {
            if (running_script('cron_bridge')) {
                if (php_function_allowed('error_log')) {
                    @error_log(brand_name() . ' mailer: ERROR ' . $error); // We log this, as Health Check is not going to be able to send an e-mail
                }
            }
        }
    }
}
