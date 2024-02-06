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
class webstandards_test_set extends cms_test_case
{
    public function testWebStandardsFailuresStillWork()
    {
        require_code('webstandards');
        require_lang('webstandards');

        $result = check_xhtml('<!DOCTYPE html> <br>');
        $found = false;
        foreach ($result['errors'] as $r) {
            if (strpos($r['error'], 'tag which is required to self-close did not') !== false) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }
}
