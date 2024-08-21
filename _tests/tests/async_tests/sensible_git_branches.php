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
class sensible_git_branches_test_set extends cms_test_case
{
    public function testSensibleBranches()
    {
        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'cms_homesite addon is required');
            return;
        }

        require_code('version');
        require_code('cms_homesite');

        $branches = get_composr_branches();
        foreach ($branches as $branch) {
            $this->assertTrue(($branch['status'] != VERSION_MAINLINE) || (in_array($branch['git_branch'], ['master', 'main'])), $branch['git_branch'] . ' does not seem to have the expected version status');
        }
    }
}
