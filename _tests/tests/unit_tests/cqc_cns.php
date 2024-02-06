<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class cqc_cns_test_set extends cms_test_case
{
    public function testSite()
    {
        cms_set_time_limit(120);
        $url = get_base_url() . '/_tests/codechecker/codechecker.php?subdir=sources&filter=cns_.*';
        $url = $this->extend_cqc_call($url);
        $result = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 10000.0]);
        foreach (explode('<br />', $result) as $line) {
            $this->assertTrue($this->should_filter_cqc_line($line), $line);
        }
    }
}
