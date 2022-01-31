<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    git_status
 */

/**
 * Hook class.
 */
class Hook_cloud_propagation_git_status
{
    /**
     * Handle an RPC, if we can.
     *
     * @param  string $type The procedure to call
     */
    public function rpc(string $type)
    {
        if (!addon_installed('git_status')) {
            return;
        }

        if ($type == 'git_pull') {
            require_code('git_status');
            git_pull();
        }
    }
}
