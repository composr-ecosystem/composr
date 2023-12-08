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
class Hook_health_check_performance_server extends Hook_Health_Check
{
    protected $category_label = 'Server performance';

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
        $this->process_checks_section('testDiskSpace', 'Disk space', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testCPULoad', 'CPU load', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testServerUptime', 'Server uptime', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testIOLoad', 'I/O load', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testHangingProcesses', 'Hanging processes', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testRAM', 'RAM', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testDiskSpace($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('disk_free_space')) {
            $disk_space_threshold = intval(get_option('hc_disk_space_threshold')) * 1024 * 1024;

            require_code('files');

            $free_space = disk_free_space(get_custom_file_base());
            $this->assert_true($free_space > $disk_space_threshold, 'Disk space very low @ ' . clean_file_size($free_space));
        } else {
            $this->state_check_skipped('PHP disk_free_space function not available');
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
    public function testCPULoad($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('shell_exec')) {
            $cpu = null;

            if (strpos(PHP_OS, 'Darwin') !== false) {
                $result = explode("\n", shell_exec('iostat'));
                array_shift($result);
                array_shift($result);
                if (isset($result[0])) {
                    $matches = array();
                    if (preg_match('#(\d+)\s+(\d+)\s+(\d+)\s+\d+\.\d+\s+\d+\.\d+\s+\d+\.\d+\s*$#', $result[0], $matches) != 0) {
                        $cpu = floatval($matches[1]) + floatval($matches[2]);
                    }
                }
            } elseif (strpos(PHP_OS, 'Linux') !== false) {
                $result = explode("\n", shell_exec('iostat'));
                array_shift($result);
                array_shift($result);
                array_shift($result);
                if (isset($result[0])) {
                    $matches = array();
                    if (preg_match('#^\s*(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)#', $result[0], $matches) != 0) {
                        $cpu = floatval($matches[1]) + floatval($matches[2]) + floatval($matches[3]);
                    }
                }

                if ($cpu === null) {
                    $result = explode("\n", shell_exec('top -b -n 1'));
                    array_shift($result);
                    array_shift($result);
                    if (isset($result[0])) {
                        $matches = array();
                        if (preg_match('#^%Cpu\(s\):\s*(\d+\.\d+) us,\s*(\d+\.\d+) sy,\s*(\d+\.\d+) ni#', $result[0], $matches) != 0) {
                            $cpu = floatval($matches[1]) + floatval($matches[2]) + floatval($matches[3]);
                        }
                    }
                }
            } else {
                $this->state_check_skipped('No implementation for finding CPU load on this platform');
                return;
            }

            /*  This technique is okay in theory, but there's too much rounding when we're looking at a narrow threshold
            usleep(2000000); // Let CPU recover a bit from our own script
            $result = explode("\n", shell_exec('ps -A -o %cpu'));
            $cpu = 0.0;
            foreach ($result as $r) {
                if (is_numeric(trim($cpu))) {
                    $cpu += floatval($r);
                }
            }
            */

            if ($cpu !== null) {
                $threshold = floatval(get_option('hc_cpu_pct_threshold'));

                $this->assert_true($cpu < $threshold, 'CPU utilisation is very high @ ' . float_format($cpu) . '%');
            } else {
                if (strpos(PHP_OS, 'Linux') !== false) {
                    $this->state_check_skipped('Failed to detect CPU load (might need to install [tt]sysstat[/tt])');
                } else {
                    $this->state_check_skipped('Failed to detect CPU load');
                }
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
    public function testServerUptime($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('shell_exec')) {
            $uptime = null;

            if (php_function_allowed('sys_getloadavg')) {
                $_uptime = sys_getloadavg();
                $uptime = $_uptime[0];
            } else {
                if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
                    $data = shell_exec('uptime');

                    $matches = array();
                    if (preg_match('#load averages:\s*(\d+\.\d+)#', $data, $matches) != 0) {
                        $uptime = floatval($matches[1]);
                    }
                } else {
                    $this->state_check_skipped('No implementation for finding server load on this platform');
                    return;
                }
            }

            if ($uptime !== null) {
                $threshold = intval(get_option('hc_uptime_threshold'));
                $this->assert_true($uptime < floatval($threshold), '"uptime" (server load) is very high @ ' . float_format($uptime) . '%');
            } else {
                $this->state_check_skipped('Failed to detect server load');
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
    public function testIOLoad($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('shell_exec')) {
            $load = null;

            if (strpos(PHP_OS, 'Linux') !== false) {
                $result = explode("\n", shell_exec('iostat'));
                array_shift($result);
                array_shift($result);
                array_shift($result);
                if (isset($result[0])) {
                    $matches = array();
                    if (preg_match('#^\s*(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)#', $result[0], $matches) != 0) {
                        $load = floatval($matches[4]);
                    }
                }

                if ($load === null) {
                    $result = explode("\n", shell_exec('top -b -n 1'));
                    array_shift($result);
                    array_shift($result);
                    if (isset($result[0])) {
                        $matches = array();
                        if (preg_match('#(\d+\.\d+) wa#', $result[0], $matches) != 0) {
                            $load = floatval($matches[1]);
                        }
                    }
                }
            } else {
                $this->state_check_skipped('No implementation for finding I/O load on this platform');
                return;
            }

            if ($load !== null) {
                $threshold = floatval(get_option('hc_io_pct_threshold'));

                $this->assert_true($load < $threshold, 'I/O load is causing high wait time @ ' . float_format($load) . '%');
            } else {
                if (strpos(PHP_OS, 'Linux') !== false) {
                    $this->state_check_skipped('Failed to detect I/O load (might need to install [tt]sysstat[/tt])');
                } else {
                    $this->state_check_skipped('Failed to detect I/O load');
                }
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
    public function testHangingProcesses($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('shell_exec')) {
            $commands_regexp = get_option('hc_processes_to_monitor');
            if ($commands_regexp == '') {
                return;
            }
            $threshold_minutes = intval(get_option('hc_process_hang_threshold'));

            $done = false;
            $ps_cmd = 'ps -opid,etime,comm';
            if ($use_test_data_for_pass !== null) {
                $ps_cmd .= ' -A';
            }
            $_result = shell_exec($ps_cmd);
            $result = explode("\n", $_result);
            foreach ($result as $r) {
                $matches = array();
                if (preg_match('#^(\d+)\s+(\d+(:(\d+))*)\s+(' . $commands_regexp . ')\s*$#', $r, $matches) != 0) {
                    $seconds = 0;
                    $time_parts = array_reverse(explode(':', $matches[2]));
                    foreach ($time_parts as $i => $_time_part) {
                        $time_part = intval($_time_part);

                        switch ($i) {
                            case 0:
                                $seconds += $time_part;
                                break;

                            case 1:
                                $seconds += $time_part * 60;
                                break;

                            case 2:
                                $seconds += $time_part * 60 * 60;
                                break;

                            case 3:
                            default: // We assume anything else is days, we don't know what other units may be here, and it's longer than we care of anyway
                                $seconds += $time_part * 60 * 60 * 24;
                                break;
                        }
                    }

                    $cmd = $matches[5];
                    $pid = $matches[1];

                    $this->assert_true($seconds < 60 * $threshold_minutes, 'Process [tt]' . $cmd . '[/tt] (' . $pid . ') has been running a long time @ ' . display_time_period($seconds));
                }
            }

            if (empty($_result)) {
                $this->state_check_skipped('Failed to list running processes');
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
    public function testRAM($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (php_function_allowed('shell_exec')) {
            require_code('files');

            $bytes_free = null;

            $matches = array();

            if (strpos(PHP_OS, 'Darwin') !== false) {
                $data = shell_exec('vm_stat');
                if (preg_match('#^Pages free:\s*(\d+)#m', $data, $matches) != 0) {
                    $bytes_free = intval($matches[1]) * 4 * 1024;
                    if (preg_match('#^Pages inactive:\s*(\d+)#m', $data, $matches) != 0) { // We consider this free. Mac is going to try and use all RAM for something, so we have to use a weird definition
                        $bytes_free += intval($matches[1]) * 4 * 1024;
                    }
                }
            } elseif (strpos(PHP_OS, 'Linux') !== false) {
                $data = shell_exec('free');
                if (preg_match('#^Mem:\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)#m', $data, $matches) != 0) {
                    $bytes_free = intval($matches[6]) * 1024;
                } elseif (preg_match('#^Mem:\s+(\d+)\s+(\d+)\s+(\d+)#m', $data, $matches) != 0) {
                    $bytes_free = intval($matches[3]) * 1024;
                }
            } elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $data = shell_exec('wmic OS get FreePhysicalMemory /Value');
                if (preg_match('#FreePhysicalMemory=(\d+)#m', $data, $matches) != 0) {
                    $bytes_free = intval($matches[1]) * 1024;
                }
            } else {
                $this->state_check_skipped('No implementation for finding free RAM on this platform');
                return;
            }

            if ($bytes_free !== null) {
                $mb_threshold = intval(get_option('hc_ram_threshold'));
                $this->assert_true($bytes_free > $mb_threshold * 1024 * 1024, 'Server is low on RAM @ ' . clean_file_size($bytes_free));
            } else {
                $this->state_check_skipped('Failed to detect free RAM');
            }
        } else {
            $this->state_check_skipped('PHP shell_exec function not available');
        }
    }
}
