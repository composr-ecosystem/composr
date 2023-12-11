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
class Hook_health_check_upkeep_backups extends Hook_Health_Check
{
    protected $category_label = 'Backups';

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
        $this->process_checks_section('testBackups', 'Backups', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testBackups($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!addon_installed('backup')) {
            return;
        }

        $backup_schedule_time = intval(get_value('backup_schedule_time'));
        $last_backup = get_value('last_backup');
        if (($backup_schedule_time > 0) && ($last_backup !== null)) {
            $path = get_custom_file_base() . '/exports/backups';
            $found = false;
            $dh = opendir($path);
            while (($f = readdir($dh)) !== false) {
                if ((substr($f, -4) == '.tar') || (substr($f, -3) == '.gz')) {
                    $size = filesize($path . '/' . $f);
                    $found = $found || ($size > 5000000);
                }
            }
            closedir($dh);

            $this->assert_true($found, 'Could not find a scheduled backup file that looks complete');
        } else {
            $this->state_check_skipped('Automatic backups have never run');
        }
    }
}
