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
class Hook_health_check_cron extends Hook_Health_Check
{
    protected $category_label = 'CRON';

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
        $this->process_checks_section('testCronSetUp', 'CRON set up', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testCronSlow', 'Slow CRON', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testCronSetUp($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $this->assert_true(cron_installed(), 'CRON not running, it is needed for various features to work');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testCronSlow($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $last_cron_started = get_value('last_cron_started', null, true);
        $last_cron_finished = get_value('last_cron_finished', null, true);

        $threshold = intval(get_option('hc_cron_threshold'));

        if (($last_cron_started !== null) && ($last_cron_finished !== null)) {
            // Completed cycle
            $time = intval($last_cron_finished) - intval($last_cron_started);
            $this->assert_true($time < $threshold, 'The system scheduler is running slow @ ' . display_time_period($time) . ' to run');
        } elseif (($last_cron_started !== null) && ($last_cron_finished === null)) {
            // Mid cycle
            $time = time() - intval($last_cron_started);
            $this->assert_true($time < $threshold, 'The system scheduler has taken ' . display_time_period($time) . ' and not finished -- it is either running very slow, or it failed');
        } else {
            // No cycle
            $this->state_check_skipped('The system scheduler never ran');
        }
    }
}
