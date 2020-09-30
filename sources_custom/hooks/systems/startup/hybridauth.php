<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/**
 * Hook class.
 */
class Hook_startup_hybridauth
{
    public function run()
    {
        if (!addon_installed('hybridauth')) {
            return;
        }

        require_code('hybridauth');
        initiate_hybridauth_session_state();
    }
}
