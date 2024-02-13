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
 * Process a logout.
 */
function handle_active_logout()
{
    non_overridden__handle_active_logout();

    if (addon_installed('hybridauth')) {
        // Log out of Hybridauth too...

        require_code('hybridauth');

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        $hybridauth = initiate_hybridauth();

        if (isset($_SESSION['provider'])) {
            $provider = $_SESSION['provider'];

            try {
                $adapter = $hybridauth->getAdapter($provider);
                $adapter->disconnect();
            } catch (Hybridauth\Exception $e) {
                // Silent failure
            }
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
    }
}
