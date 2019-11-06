<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class log_refs_test_set extends cms_test_case
{
    public function testNoUnexpectedLogs()
    {
        require_code('files2');

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        $all_code = '';
        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, array('php'));
        foreach ($files as $path) {
            $all_code .= cms_file_get_contents_safe(get_file_base() . '/' . $path);
        }

        $codebook = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt', FILE_READ_LOCK | FILE_READ_BOM);

        $web_config = cms_file_get_contents_safe(get_file_base() . '/web.config', FILE_READ_LOCK | FILE_READ_BOM);

        $defined_logs = array();
        $hook_obs = find_all_hook_obs('systems', 'logs', 'Hook_logs_');
        foreach ($hook_obs as $hook_ob) {
            $defined_logs = array_merge($defined_logs, array_keys($hook_ob->enumerate_logs()));
        }

        $matches = array();
        $logs_in_code = array();
        $num_matches = preg_match_all('#data_custom/\w+\.log#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $log = $matches[0][$i];

            if (!in_array(basename($log), array('performance.log', 'performance_warnings.log', 'sugarcrm.log', 'tapatalk.log'))) {
                $logs_in_code[] = $log;
            }
        }
        $logs_in_code = array_unique($logs_in_code);

        foreach ($logs_in_code as $log) {
            $this->assertTrue(strpos($codebook, $log) !== false, 'Missing Codebook listing for ' . $log);

            $this->assertTrue(in_array(basename($log), $defined_logs), 'Missing logs hook reference for ' . $log);

            $this->assertTrue(strpos($web_config, '<add segment="' . basename($log) . '" />') !== false, 'Missing web.config skip for ' . $log);
        }
    }
}
