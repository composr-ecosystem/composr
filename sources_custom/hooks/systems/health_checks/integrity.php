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
class Hook_health_check_integrity extends Hook_Health_Check
{
    protected $category_label = 'Software integrity';

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
        $this->process_checks_section('testFileIntegrity', 'File integrity (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testDatabaseIntegrity', 'Database integrity', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testDatabaseCorruption', 'Database corruption (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testUpgradeCompletion', 'Upgrade completion', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testChmod', 'File permissions integrity', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testFileIntegrity($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            $this->state_check_skipped('Will not check automatically due to possibility of intentional modifications');
            return;
        }

        require_code('upgrade');
        require_lang('upgrade');
        $data = run_integrity_check(false, false, false);
        $this->assert_true($data == do_lang('NO_ISSUES_FOUND'), 'Integrity checker reporting potential issues, see upgrader for details');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testDatabaseIntegrity($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return;
        }

        if (!$manual_checks) {
            $this->state_check_skipped('Will not check automatically due to possibility of intentional modifications');
            return;
        }

        require_code('database_repair');
        $repair_ob = new DatabaseRepair();
        list($phase, $sql) = $repair_ob->search_for_database_issues();
        $this->assert_true($sql == '', 'There seem to be some inconsistencies in the database, run the "Correct MySQL schema issues (advanced)" Website Cleanup Tool');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testDatabaseCorruption($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (strpos(get_db_type(), 'mysql') !== false) {
            $tables = $GLOBALS['SITE_DB']->query_select('db_meta', array('DISTINCT m_table'));
            foreach ($tables as $table) {
                $results = $GLOBALS['SITE_DB']->query('CHECK TABLE ' . get_table_prefix() . $table['m_table']);
                $msg_text = $results[0]['Msg_text'];
                $ok = ($msg_text == 'OK') || (strpos($msg_text, 'closed the table properly') !== false/*common false positive*/) || (strpos($msg_text, 'doesn\'t support check') !== false);

                $message = 'Corrupt table likely needing repairing: [tt]' . $table['m_table'] . '[/tt] gave status "' . $results[0]['Msg_text'] . '"';
                if (!$ok) {
                    if ($automatic_repair) {
                        $results_repair = $GLOBALS['SITE_DB']->query('REPAIR TABLE ' . get_table_prefix() . $table['m_table']);
                        $ok_repair = $results[0]['Msg_text'] == 'OK';
                        if ($ok_repair) {
                            $message = 'Corrupt table automatically repaired: [tt]' . $table['m_table'] . '[/tt] gave status "' . $results[0]['Msg_text'] . '"';
                        }
                    }
                }
                $this->assert_true($ok, $message);
            }
        } else {
            $this->state_check_skipped('Can only check when running MySQL');
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
    public function testUpgradeCompletion($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        require_code('upgrade');

        $version_files = cms_version_number();

        $_version_database = get_value('version');
        $version_database = floatval($_version_database);
        $this->assert_true($version_files <= $version_database, 'Database seems to need an upgrade (' . float_format($version_files, 1) . ' vs ' . float_format($version_database, 1) . '), run upgrader');

        $_version_database = get_value('cns_version');
        $version_database = floatval($_version_database);
        $this->assert_true($version_files <= $version_database, 'Database seems to need an upgrade (' . float_format($version_files, 1) . ' vs ' . float_format($version_database, 1) . '), run upgrader');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testChmod($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        require_code('upgrade');
        require_lang('upgrade');

        $this->assert_true(check_perms() == do_lang('FU_ALL_CHMODDED_GOOD'), 'File permissions (chmodding) are not complete, see upgrader for details');
    }
}
