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
class Hook_health_check_install_environment extends Hook_Health_Check
{
    protected $category_label = 'Installation environment';

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
        $this->process_checks_section('testInstallEnvironment', 'PHP functionality etc', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testUnicode', 'Database Unicode settings', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPCRE', 'PCRE settings', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testInstallEnvironment($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        // Various checks
        $hooks = find_all_hooks('systems', 'checks');
        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/systems/checks/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_check_' . filter_naughty_harsh($hook));
            $warning = $ob->run();
            foreach ($warning as $_warning) {
                $this->assert_true(false, '[html]' . $_warning->evaluate() . '[/html]');
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
    public function testUnicode($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $cnt = $GLOBALS['SITE_DB']->query_value_if_there('SELECT ' . db_function('LENGTH', array('\'' . db_escape_string(chr(hexdec('C2')) . chr(hexdec('A3'))) . '\'')));
        $this->assert_true($cnt == 1, 'Database connection is not encoding Unicode properly (non-latin characters, utf8)');

        $cnt = $GLOBALS['SITE_DB']->query_value_if_there('SELECT ' . db_function('LENGTH', array('\'' . db_escape_string(chr(hexdec('F0')) . chr(hexdec('9F')) . chr(hexdec('98')) . chr(hexdec('8E'))) . '\'')));
        $this->assert_true($cnt == 1, 'Database connection is not encoding Unicode properly (emojis, utf8mb4)');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testPCRE($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $this->assert_true(preg_replace('#\n#', '', "\n") == '', 'PHP is using a non-default copy of the PCRE library that is not configured with the usual line endings, which will cause some major problems'); // Checks correct line endings
        $this->assert_true(@preg_replace('#\n#u', '', "\n") !== false, 'PCRE does not have inbuilt Unicode support, which means Unicode may not work correctly'); // Checks correct line endings
    }
}
