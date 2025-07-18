
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
class sensible_git_branches_test_set extends cms_test_case
{
    public function testSensibleBranches()
    {
        if (!addon_installed('composr_homesite')) {
            $this->assertTrue(false, 'composr_homesite addon is required');
            return;
        }

        require_code('version');
        require_code('composr_homesite');

        $branches = get_composr_branches();
        foreach ($branches as $branch) {
            $this->assertTrue(($branch['status'] != VERSION_MAINLINE) || ($branch['git_branch'] == 'master'), $branch['git_branch'] . ' does not seem to have the expected version status');
        }
    }
}
