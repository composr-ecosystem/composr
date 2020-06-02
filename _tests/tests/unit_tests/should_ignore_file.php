<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class should_ignore_file_test_set extends cms_test_case
{
    public function testShouldIgnoreFile()
    {
        require_code('files');

        $this->assertTrue(should_ignore_file('data_custom/unit_test_positive_ignore_sampler.xxx', IGNORE_UNSHIPPED_VOLATILE), 'Failing positive ignore');

        $this->assertTrue(!should_ignore_file('data_custom/unit_test_negative_ignore_sampler.xxx', IGNORE_UNSHIPPED_VOLATILE), 'Failing negative ignore');

        $this->assertTrue(!should_ignore_file('unit_test_positive_ignore_sampler.xxx', IGNORE_UNSHIPPED_VOLATILE), 'Failing negative ignore (root file)'); // should not fail in root, as ignore rule is scoped to data_custom
    }
}
