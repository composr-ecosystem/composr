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
class Hook_login_provider_hybridauth
{
    /**
     * Standard login provider hook.
     *
     * @param  ?MEMBER $member_id Member ID already detected as logged in (null: none). May be a guest ID.
     * @return ?MEMBER Member ID now detected as logged in (null: none). May be a guest ID.
     */
    public function try_login(?int $member_id) : ?int
    {
        if (get_forum_type() != 'cns') {
            return $member_id;
        }

        if (!addon_installed('hybridauth')) {
            return $member_id;
        }

        if (!function_exists('curl_init')) {
            return $member_id;
        }

        // Too early in bootstrapping
        if (!function_exists('require_lang')) {
            return $member_id;
        }

        if ((($member_id === null) || (is_guest($member_id))) && (!running_script('hybridauth')) && (get_page_name() == 'join') || (currently_logging_in())) {
            require_code('hybridauth');
            require_lang('hybridauth');

            $before_type_strictness = ini_get('ocproducts.type_strictness');
            cms_ini_set('ocproducts.type_strictness', '0');
            $before_xss_detect = ini_get('ocproducts.xss_detect');
            cms_ini_set('ocproducts.xss_detect', '0');

            $hybridauth = initiate_hybridauth();

            // Log back in whatever is still connected
            if (isset($_SESSION['provider'])) {
                $provider = $_SESSION['provider'];

                try {
                    $adapter = $hybridauth->getAdapter($provider);

                    $success = $adapter->isConnected();
                    if ($success) {
                        $userProfile = $adapter->getUserProfile();

                        $member_id = hybridauth_handle_authenticated_account($provider, $userProfile);
                    }
                } catch (Exception $e) {
                    $adapter->disconnect();
                    // Silent failure; user can always start to log in again. Maybe a revoked token for example
                }
            }

            cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
            cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
        }

        return $member_id;
    }
}
