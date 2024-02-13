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

/*
Notes...
 - The cache_age property is not supported. It would significantly complicate the API and hurt performance, and we don't know a use case for it. The spec says it is optional to support.
 - Link/semantic-webpage rendering will not use passed description parameter, etc. This is intentional: the normal flow of rendering through a standardised media template is not used.
*/

/**
 * Hook class.
 */
class Hook_oembed_hybridauth_admin
{
    public function get_oembed_from_url($url, $params)
    {
        if (!addon_installed('hybridauth')) {
            return null;
        }

        if (!function_exists('curl_init')) {
            return null;
        }

        require_code('hybridauth_admin');
        require_lang('hybridauth');

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth, $admin_storage) = initiate_hybridauth_admin();

        $providers = find_all_hybridauth_admin_providers_matching(HYBRIDAUTH__AUTHENTICATED_OEMBED);
        foreach ($providers as $provider => $info) {
            if (!$info['enabled']) {
                continue;
            }

            try {
                $adapter = $hybridauth->getAdapter($provider);
                $connected = $adapter->isConnected();
            } catch (Exception $e) {
                $connected = false;
            }

            if (!$connected) {
                continue;
            }

            try {
                $data = $adapter->getOEmbedFromURL($url, $params);

                if ($data !== null) {
                    $ret = json_decode(json_encode($data), true); // We want it in array format
                    return $ret;
                }
            } catch (Exception $e) {
                require_code('failure');
                cms_error_log($e->getMessage(), 'error_occurred_api');
            }
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

        return null;
    }
}
