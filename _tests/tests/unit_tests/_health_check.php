<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _health_check_test_set extends cms_test_case
{
    public function testHealthCheck()
    {
        require_code('health_check');

        $has_fails = false;
        $categories = run_health_check($has_fails, null, true, true, true, false, true);

        foreach ($categories as $category_label => $sections) {
            foreach ($sections['SECTIONS'] as $section_label => $results) {
                foreach ($results['RESULTS'] as $result) {
                    $this->assertTrue($result['RESULT'] != HEALTH_CHECK__FAIL, $category_label . ': ' . $section_label . ': ' . str_replace('%', 'pct', $result['MESSAGE']->evaluate()));
                }
            }
        }
    }
}
