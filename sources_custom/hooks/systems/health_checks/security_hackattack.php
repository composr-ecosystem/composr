<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class Hook_health_check_security_hackattack extends Hook_Health_Check
{
    protected $category_label = 'Hack-attacks';

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
        $this->process_checks_section('testOverseasAccess', 'Overseas access', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testHackAttacks', 'Attack frequency', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testFailedLogins', 'Failed logins', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testRateLimitSpike', 'Rate-limit spiking', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testOverseasAccess($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (addon_installed('stats')) {
            $test = $GLOBALS['SITE_DB']->query_select_value_if_there('ip_country', 'id');
            $ok = ($test !== null);

            if ($ok) {
                require_code('locations');

                $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
                $members = $GLOBALS['FORUM_DRIVER']->member_group_query($admin_groups);
                foreach ($members as $member) {
                    $id = $GLOBALS['FORUM_DRIVER']->mrow_id($member);
                    $username = $GLOBALS['FORUM_DRIVER']->mrow_username($member);

                    $countries = array();
                    $rows = $GLOBALS['SITE_DB']->query_select('stats', array('DISTINCT ip'), array('member_id' => $id), 'AND date_and_time>' . strval(time() - 60 * 60 * 24 * 7));
                    foreach ($rows as $row) {
                        $country = geolocate_ip($row['ip']);
                        if ($country !== null) {
                            $countries[] = '[tt]' . $country . '[/tt]';
                        }
                    }

                    $this->assert_true(count($countries) <= 1, 'Admin account "' . $username . '" appears to have logged in from multiple countries (' . implode(', ', $countries) . ')');
                }
            } else {
                $this->state_check_skipped('Geolocation data not installed so cannot do admin country checks');
            }
        } else {
            $this->state_check_skipped('Could not find geolocation history without the stats addon being installed');
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
    public function testHackAttacks($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $sql = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'hackattack WHERE date_and_time>' . strval(time() - 60 * 60 * 24);
        $num_failed = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assert_true($num_failed < 100, integer_format($num_failed) . ' hack-attack alerts happened today');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testFailedLogins($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $sql = 'SELECT COUNT(*) FROM ' . get_table_prefix() . 'failedlogins WHERE date_and_time>' . strval(time() - 60 * 60 * 24);
        $num_failed = $GLOBALS['SITE_DB']->query_value_if_there($sql);
        $this->assert_true($num_failed < 100, integer_format($num_failed) . ' failed logins happened today');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testRateLimitSpike($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        global $RATE_LIMITING_DATA;
        $RATE_LIMITING_DATA = array();

        $rate_limiter_path = get_custom_file_base() . '/data_custom/rate_limiter.php';
        if (is_file($rate_limiter_path)) {
            $fp = fopen($rate_limiter_path, 'rb');
            flock($fp, LOCK_SH);
            include($rate_limiter_path);
            flock($fp, LOCK_UN);
            fclose($fp);
        }

        $threshold_sample = intval(get_option('hc_requests_window_size'));
        $threshold_rps = floatval(get_option('hc_requests_per_second_threshold'));

        $threshold_sample_compound = intval(get_option('hc_compound_requests_window_size'));
        $threshold_rps_compound = floatval(get_option('hc_compound_requests_per_second_threshold'));

        /*  Test
        $RATE_LIMITING_DATA = array(
            '1.2.3.4' => array_fill(0, 30, time()),
        );
        */

        if (!empty($RATE_LIMITING_DATA)) {
            global $SITE_INFO;
            $rate_limit_time_window = empty($SITE_INFO['rate_limit_time_window']) ? 10 : intval($SITE_INFO['rate_limit_time_window']);

            $times_compound = array();

            foreach ($RATE_LIMITING_DATA as $ip => $times) {
                $requests_per_second = floatval(count($times)) / floatval($rate_limit_time_window);
                $ok = (count($times) < $threshold_sample) || ($requests_per_second < $threshold_rps);
                $this->assert_true($ok, 'Heavy visitor load @ ' . float_format($requests_per_second, 2, true) . ' PHP requests per second (for a sample size over ' . integer_format($threshold_sample) . ') requests from IP ' . $ip);

                $times_compound = array_merge($times_compound, $times);
            }

            $requests_per_second = floatval(count($times_compound)) / floatval($rate_limit_time_window);
            $ok = (count($times_compound) < $threshold_sample) || ($requests_per_second < $threshold_rps);
            $this->assert_true($ok, 'Heavy visitor load @ ' . float_format($requests_per_second, 2, true) . ' PHP requests per second (for a sample size over ' . integer_format($threshold_sample_compound) . ') requests from all IPs together');
        }
    }
}
