<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    hybridauth
 */

/**
 * Hook class.
 */
class Hook_startup_hybridauth
{
    public function run()
    {
        return; // TODO: Do we really need this to happen on every page load? It causes timeouts.

        /*
            if (!addon_installed('hybridauth')) {
                return;
            }

            if (!function_exists('curl_init')) {
                return;
            }

            require_code('hybridauth');
            initiate_hybridauth_session_state();
        */
    }
}
