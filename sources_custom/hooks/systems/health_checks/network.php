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
class Hook_health_check_network extends Hook_Health_Check
{
    protected $category_label = 'Network';

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
        $this->process_checks_section('testExternalAccess', 'External access', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPacketLoss', 'Packet loss (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testTransferLatency', 'Transfer latency', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testTransferSpeed', 'Transfer speed', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testExternalAccess($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        if ($this->is_localhost_domain()) {
            return;
        }

        if (get_base_url() == 'https://compo.sr') {
            return;
        }

        require_code('json');

        $url = 'https://compo.sr/uploads/website_specific/compo.sr/scripts/testing.php?type=http_status_check&url=' . urlencode($this->get_page_url());
        for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or Google issue
            $data = http_download_file($url, null, false);

            if ($data !== null) {
                break;
            }
            if (php_function_allowed('usleep')) {
                usleep(5000000);
            }
        }
        $result = @json_decode($data, true);
        $this->assert_true($result === '200', 'Could not access website externally, got error code ' . $result);
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testPacketLoss($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if (php_function_allowed('shell_exec')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $cmd = 'ping -n 10 8.8.8.8';
            } else {
                $cmd = 'ping -c 10 8.8.8.8';
            }
            $data = shell_exec($cmd);

            $matches = array();
            if (preg_match('# (\d(\.\d+)?%) packet loss#', $data, $matches) != 0) {
                $this->assert_true(floatval($matches[1]) == 0.0, 'Unreliable Internet connection on server');
            } else {
                $this->state_check_skipped('Could not get a recognised ping response');
            }
        } else {
            $this->state_check_skipped('PHP shell_exec function not available');
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
    public function testTransferLatency($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        $threshold = floatval(get_option('hc_transfer_latency_threshold'));

        $ok = false;
        $time = null;

        for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or Google issue
            $time_before = microtime(true);

            $data = http_download_file('http://www.google.com/', null, false);

            if ($data === null) {
                $ok = false;
                if (php_function_allowed('usleep')) {
                    usleep(5000000);
                }

                continue;
            }

            $time_after = microtime(true);

            $time = ($time_after - $time_before);

            $ok = ($time < $threshold);
            if ($ok) {
                break;
            }
            if (php_function_allowed('usleep')) {
                usleep(5000000);
            }
        }

        $this->assert_true($ok, 'Slow transfer latency @ ' . (($time === null) ? 'N/A' : float_format($time)) . ' seconds (downloading Google home page)');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testTransferSpeed($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        if (get_base_url() == 'https://compo.sr') {
            return;
        }

        $threshold_in_megabits_per_second = floatval(get_option('hc_transfer_speed_threshold'));

        $test_file_path = get_file_base() . '/data/curl-ca-bundle.crt';

        $data_to_send = str_repeat(file_get_contents($test_file_path), 5);
        $post_params = array('test_data' => $data_to_send);

        $ok = false;
        $megabits_per_second = null;

        for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or compo.sr issue
            $time_before = microtime(true);

            $data = http_download_file('https://compo.sr/uploads/website_specific/compo.sr/scripts/testing.php?type=test_upload', null, false, true, 'Composr', $post_params);

            if ($data === null) {
                $ok = false;
                $megabits_per_second = 0.0;
                if (php_function_allowed('usleep')) {
                    usleep(5000000);
                }

                continue;
            }

            $time_after = microtime(true);

            $time = ($time_after - $time_before);

            $megabytes_per_second = floatval(strlen($data_to_send)) / (1024.0 * 1024.0 * $time);
            $megabits_per_second = $megabytes_per_second * 8.0;

            $ok = ($megabits_per_second > $threshold_in_megabits_per_second);
            if ($ok) {
                break;
            }
            if (php_function_allowed('usleep')) {
                usleep(5000000);
            }
        }

        $this->assert_true($ok, 'Slow speed transfering data to a remote machine @ ' . (($megabits_per_second === null) ? 'N/A' : float_format($megabits_per_second)) . ' Megabits per second');
    }
}
