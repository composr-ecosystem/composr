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
class cqc_hooks_test_set extends cms_test_case
{
    public function testHooks()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }
        $result = http_get_contents(get_base_url() . '/_tests/codechecker/code_quality.php?subdir=sources/hooks&api=1', array('timeout' => 10000.0));
        foreach (explode('<br />', $result) as $line) {
            $this->assertTrue($this->should_filter_cqc_line($line), $line);
        }
    }
}
