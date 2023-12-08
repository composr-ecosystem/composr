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
class Hook_health_check_upkeep extends Hook_Health_Check
{
    protected $category_label = 'Upkeep';

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
        $this->process_checks_section('testComposrVersion', brand_name() . ' version', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPHPVersion', 'PHP version', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testAdminAccountStaleness', 'Admin account staleness', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testCopyrightDate', 'Copyright date', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testStaffChecklist', 'Staff checklist', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testComposrVersion($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $is_discontinued = $this->call_composr_homesite_api('is_release_discontinued', array('version' => cms_version_number()));
        $this->assert_true($is_discontinued !== true, 'The ' . brand_name() . ' version is discontinued');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testPHPVersion($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        require_code('version2');

        $v = strval(PHP_MAJOR_VERSION) . '.' . strval(PHP_MINOR_VERSION);

        $this->assert_true(is_php_version_supported($v), 'Unsupported PHP version ' . $v);
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testAdminAccountStaleness($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        $threshold = time() - 60 * 60 * 24 * intval(get_option('hc_admin_stale_threshold'));

        $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
        $members = $GLOBALS['FORUM_DRIVER']->member_group_query($admin_groups);
        foreach ($members as $member) {
            $member_id = $GLOBALS['FORUM_DRIVER']->mrow_id($member);
            $last_visit = $GLOBALS['FORUM_DRIVER']->mrow_lastvisit($member);
            $username = $GLOBALS['FORUM_DRIVER']->mrow_username($member);

            $diff = ($last_visit === null) ? '(never)' : display_time_period(time() - $last_visit);
            if (($automatic_repair) && (get_forum_type() == 'cns')) {
                $GLOBALS['FORUM_DB']->query_update('f_members', array('m_validated' => 0), array('id' => $member_id), '', 1);
                $this->assert_true(($last_visit === null) || ($last_visit > $threshold), 'Admin account "' . $username . '" not logged in for a long time @ ' . $diff . ', automatically marked as non-validated');
            } else {
                $this->assert_true(($last_visit === null) || ($last_visit > $threshold), 'Admin account "' . $username . '" not logged in for a long time @ ' . $diff . ', consider deleting');
            }
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
    public function testCopyrightDate($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $data = $this->get_page_content();
        if ($data === null) {
            $this->state_check_skipped('Could not download page from website');
            return;
        }

        if ((date('m-d') == '00-01') || (date('m-d') == '12-31')) {
            // Allow for inconsistencies around new year
            $this->state_check_skipped('Too close to new year to run check');
            return;
        }

        $current_year = intval(date('Y', tz_time(time(), get_server_timezone())));

        $year = null;
        $matches = array();
        if (preg_match('#(Copyright|&copy;|©).*(\d{4})[^\d]{1,10}(\d{4})#', $data, $matches) != 0) {
            $_year_first = intval($matches[2]);
            $_year = intval($matches[3]);
            if (($_year - $_year_first > 0) && ($_year - $_year_first < 100) && ($_year > $current_year - 10) && ($_year <= $current_year)) {
                $year = $_year;
            }
        } elseif (preg_match('#(Copyright|&copy;|©).*(\d{4})#', $data, $matches) != 0) {
            $_year = intval($matches[2]);
            if (($_year > $current_year - 10) && ($_year <= $current_year)) {
                $year = $_year;
            }
        }

        if ($year !== null) {
            $this->assert_true($year == $current_year, 'Copyright date seems outdated');
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
    public function testStaffChecklist($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        require_code('blocks/main_staff_checklist');

        $_hooks = find_all_hooks('blocks', 'main_staff_checklist');
        foreach (array_keys($_hooks) as $hook) {
            require_code('hooks/blocks/main_staff_checklist/' . filter_naughty_harsh($hook));
            $object = object_factory('Hook_checklist_' . filter_naughty_harsh($hook), true);
            if (is_null($object)) {
                continue;
            }
            $ret = $object->run();

            foreach ($ret as $r) {
                list(, $seconds_due_in, $num_to_do) = $r;

                if ($seconds_due_in !== null) {
                    $ok = ($seconds_due_in >= 0);
                    $this->assert_true($ok, 'Staff checklist items for [tt]' . $hook . '[/tt] due ' . (($seconds_due_in == -1) ? '(ASAP)' : display_time_period($seconds_due_in)) . ' ago');
                    break;
                }

                if ($num_to_do !== null) {
                    $ok = ($num_to_do == 0);
                    $this->assert_true($ok, 'Staff checklist items for [tt]' . $hook . '[/tt], ' . integer_format($num_to_do) . ' item(s)');
                    break;
                }
            }
        }
    }
}
