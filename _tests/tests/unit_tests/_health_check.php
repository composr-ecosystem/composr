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

// php _tests/index.php _health_check

/**
 * Composr test case class (unit testing).
 */
class _health_check_test_set extends cms_test_case
{
    public function testHealthCheck()
    {
        require_code('health_check');

        $sections_to_run = null;
        $limit_to = get_param_string('limit_to', null);
        if (($limit_to === null) && (isset($_SERVER['argv'][2]))) {
            $limit_to = $_SERVER['argv'][2];
        }
        if ($limit_to !== null) {
            $sections_to_run = array($limit_to);
        }

        $has_fails = false;
        $categories = run_health_check($has_fails, $sections_to_run, true, true, true, false, true);

        foreach ($categories as $category_label => $sections) {
            foreach ($sections['SECTIONS'] as $section_label => $results) {
                foreach ($results['RESULTS'] as $result) {
                    $this->assertTrue($result['RESULT'] != HEALTH_CHECK__FAIL, $category_label . ': ' . $section_label . ': ' . str_replace('%', '%%', $result['MESSAGE']->evaluate()));
                }
            }
        }
    }
}
