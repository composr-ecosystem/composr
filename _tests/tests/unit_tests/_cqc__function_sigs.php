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
class _cqc__function_sigs_test_set extends cms_test_case
{
    public function testAdminZone()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(300);
        }

        $result = http_get_contents(get_base_url() . '/_tests/codechecker/phpdoc_parser.php', array('timeout' => 10000.0));
        foreach (explode('<br />', $result) as $line) {
            $this->assertTrue($this->should_filter_cqc_line($line), $line);
        }
    }
}
