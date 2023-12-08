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
class Hook_health_check_stability extends Hook_Health_Check
{
    protected $category_label = 'Stability';

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
        $this->process_checks_section('testManualLogs', 'Manual stability checks', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPageIntegrity', 'Page integrity', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testBlockIntegrity', 'Block integrity (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testErrorLog', 'Error log', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testManualLogs($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        $path = get_custom_file_base() . '/data_custom/errorlog.php';
        if (strlen($path) < 25) {
            return;
        }

        $this->state_check_manual('Check the web server error logs, e.g. for 404 errors you may want to serve via a redirect');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testPageIntegrity($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);
            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');

                continue;
            }

            $this->assert_true(strpos($data, '</html>') !== false, '"' . $page_link . '" page appears broken, missing closing HTML tag');
        }

        $threshold_page_size = intval(get_option('hc_page_size_threshold')) * 1024;

        require_code('files');

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);
            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');

                continue;
            }

            $size = strlen($data);
            $this->assert_true($size < $threshold_page_size, '"' . $page_link . '" page is very large @ ' . clean_file_size($size));
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
    public function testBlockIntegrity($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        require_code('zones2');
        $blocks = find_all_blocks();
        foreach ($blocks as $block => $type) {
            if (strpos($type, '_custom') !== false) {
                continue;
            }

            $test = do_block($block, array());
            $this->assert_true(is_object($test), 'Broken block [tt]' . $block . '[/tt]');
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
    public function testErrorLog($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $path = get_custom_file_base() . '/data_custom/errorlog.php';
        $myfile = fopen($path, 'rb');
        if ($myfile !== false) {
            $filesize = filesize($path);

            fseek($myfile, max(0, $filesize - 50000));

            fgets($myfile); // Skip line part-way-through

            $threshold_time = time() - 60 * 60 * 24 * 1;
            $threshold_count = intval(get_option('hc_error_log_day_flood_threshold'));

            $dates = array();
            while (!feof($myfile)) {
                $line = fgets($myfile);
                if ($line === false) {
                    break;
                }

                $matches = array();
                if ((preg_match('#^\[([^\[\]]*)\] #', $line, $matches) != 0) && (preg_match('#^\[([^\[\]]*)\] PHP\s+(Stack trace:|\d+\. )#', $line) == 0)/*xdebug*/) {
                    $timestamp = @strtotime($matches[1]);
                    if (($timestamp !== false) && ($timestamp > $threshold_time)) {
                        $dates[] = $timestamp;
                    }
                }
            }

            fclose($myfile);

            $this->assert_true(count($dates) < $threshold_count, 'Large number of logged errors @ ' . integer_format(count($dates)) . ' in the last day');

            if ($manual_checks) {
                if (filesize($path) > 20) {
                    $this->state_check_manual('Check the [page="adminzone:admin_errorlog"]error log[/page]');
                }
            }
        } else {
            $this->state_check_skipped('Could not find the error log');
        }
    }
}
