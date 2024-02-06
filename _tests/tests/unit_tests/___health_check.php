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

// php _tests/index.php ___health_check

// Or better, just run through the UI chunk-by-chunk

/**
 * Composr test case class (unit testing).
 */
class ___health_check_test_set extends cms_test_case
{
    public function testHealthCheck()
    {
        require_code('health_check');

        $sections_to_run = null;

        if ($this->only !== null) {
            $sections_to_run = [$this->only];
        }

        $has_fails = false;
        $categories = run_health_check($has_fails, $sections_to_run, true, true, true, false, true);

        foreach ($categories as $category_label => $sections) {
            foreach ($sections['SECTIONS'] as $section_label => $results) {
                foreach ($results['RESULTS'] as $result) {
                    $this->assertTrue($result['RESULT'] != HEALTH_CHECK__FAIL, $category_label . ': ' . $section_label . ': ' . $result['MESSAGE']->evaluate());
                }
            }
        }
    }
}
