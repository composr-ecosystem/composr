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

class Hook_oauth_screen_sup_hybridauth_admin
{
    public function get_services()
    {
        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        require_code('hybridauth_admin');
        list($hybridauth, $admin_storage, $providers) = initiate_hybridauth_admin();

        $keep = symbol_tempcode('KEEP');

        $services = [];

        foreach ($providers as $provider => $info) {
            $configured = !empty($info['keys']);
            try {
                $adapter = $hybridauth->getAdapter($provider);
                $connected = $adapter->isConnected();
            } catch (Exception $e) {
                $configured = false;
                $connected = false;
            }

            if ($configured) {
                $url = find_script('hybridauth_admin') . '?provider=' . urlencode($provider) . '&hybridauth_blank_state=1' . $keep->evaluate();
            } else {
                $url = null;
            }

            $services[] = [
                'LABEL' => $info['label'] . ' (Hybridauth-driven)',
                'PROTOCOL' => '',
                'AVAILABLE' => true,

                'CONFIGURED' => $configured,
                'CONFIG_URL' => null,
                'CONNECTED' => $connected,
                'CONNECT_URL' => $url,
                'CLIENT_ID' => isset($info['keys']['id']) ? $info['keys']['id'] : '',
                'CLIENT_SECRET' => isset($info['keys']['secret']) ? $info['keys']['secret'] : '',
                'API_KEY' => isset($info['keys']['key']) ? $info['keys']['key'] : (isset($info['other_parameters']['api_key']) ? $info['other_parameters']['api_key'] : ''),
                'REFRESH_TOKEN' => '',
            ];
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

        return $services;
    }
}
