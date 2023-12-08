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

/**
 * Hook class.
 */
class Hook_health_check_domains extends Hook_Health_Check
{
    protected $category_label = 'Domains';

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
        $this->process_checks_section('testDNSResolution', 'DNS resolution', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testDomainExpiry', 'Domain expiry', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testDNSResolution($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if ($this->is_localhost_domain()) {
            return;
        }

        if (php_function_allowed('checkdnsrr')) {
            $domains = $this->get_domains();

            foreach ($domains as $domain) {
                $this->assert_true(@checkdnsrr($domain, 'A'), 'DNS does not seem to be set up properly for [tt]' . $domain . '[/tt]');
            }
        } else {
            $this->state_check_skipped('PHP checkdnsrr function not available');
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
    public function testDomainExpiry($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if ($this->is_localhost_domain()) {
            return;
        }

        if ((php_function_allowed('shell_exec')) && (php_function_allowed('escapeshellarg'))) {
            $domains = $this->get_domains();

            foreach ($domains as $domain) {
                if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
                    $cmd = 'whois ' . escapeshellarg('domain ' . $domain);
                    $data = shell_exec($cmd);
                    if (strpos($data, 'Unknown AS number') !== false) {
                        $cmd = 'whois ' . escapeshellarg($domain);
                        $data = shell_exec($cmd);
                    }
                } else {
                    $this->state_check_skipped('No implementation for doing whois lookups on this platform');
                    return;
                }

                $matches = array();
                if (preg_match('#(Expiry date|Expiration date|Expiration):\s*([^\s]*)#im', $data, $matches) != 0) {
                    $expiry = strtotime($matches[2]);
                    if ($expiry > 0) {
                        $this->assert_true($expiry > time() - 60 * 60 * 24 * 7, 'Domain name [tt]' . $domain . '[/tt] seems to be expiring within a week or already expired');
                    } else {
                        $this->state_check_skipped('Error reading expiry date for ' . $domain);
                    }
                } else {
                    $this->state_check_skipped('Could not find expiry date for [tt]' . $domain . '[/tt] (this happens for some domains, or may mean the [tt]whois[/tt] command is not installed on the server)');
                }
            }
        } else {
            $this->state_check_skipped('PHP shell_exec/escapeshellarg function(s) not available');
        }
    }
}
