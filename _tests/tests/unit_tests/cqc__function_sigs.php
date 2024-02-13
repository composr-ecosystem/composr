<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class cqc__function_sigs_test_set extends cms_test_case
{
    public function testAdminZone()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $result = http_get_contents(get_base_url() . '/_tests/codechecker/phpdoc_parser.php?base_path=' . urlencode(get_file_base()), ['convert_to_internal_encoding' => true, 'timeout' => 10000.0]);
        foreach (explode('<br />', $result) as $line) {
            $this->assertTrue($this->should_filter_cqc_line($line), $line);
        }
    }
}
