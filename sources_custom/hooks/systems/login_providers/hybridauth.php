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

        // Try logging in
        if (
            (($member_id === null) || (is_guest($member_id))) && // Is a guest
            (!running_script('hybridauth')) && // Not running the hybridauth script
            ((get_page_name() == 'join') || ((currently_logging_in()) && (get_param_string('type', 'browse') != 'logout'))) // On a login screen (except logout)
        ) {
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

            if ($before_type_strictness !== false) {
                cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
            }
            if ($before_xss_detect !== false) {
                cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
            }
        }

        return $member_id;
    }

    /**
     * Standard logout provider hook.
     *
     * @param  MEMBER $member_id The member to be logged out
     * @param  ID_TEXT $compat_scheme The compat scheme to be logged out
     */
    public function logout(int $member_id, string $compat_scheme)
    {
        if (!addon_installed('hybridauth')) {
            return;
        }

        // Cannot log other members out in Hybridauth; can only log ourselves out
        if ($member_id != get_member(true)) {
            return;
        }

        require_code('hybridauth');

        $is_hybridauth_account = is_hybridauth_special_type($compat_scheme);
        if (!$is_hybridauth_account) {
            return;
        }

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        $hybridauth = initiate_hybridauth();

        try {
            $adapter = $hybridauth->getAdapter($compat_scheme);
            $adapter->disconnect();
        } catch (Hybridauth\Exception $e) {
            // Silent failure
        }

        if ($before_type_strictness !== false) {
            cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        }
        if ($before_xss_detect !== false) {
            cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
        }
    }
}
