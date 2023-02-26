<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
        if (!addon_installed('hybridauth')) {
            return [];
        }

        if (!function_exists('curl_init')) {
            return [];
        }

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        require_code('hybridauth_admin');
        list($hybridauth, $admin_storage, $providers) = initiate_hybridauth_admin();

        $keep = symbol_tempcode('KEEP');

        $services = [];

        foreach ($providers as $provider => $info) {
            $service = $this->create_service_row($hybridauth, $provider, $info, $keep);
            $services[$service['LABEL']] = $service;
        }

        // We also allow unlimited extra configurations
        foreach ($providers as $provider => $info) {
            foreach ($info['alternate_configs'] as $alternate_config) {
                if ($alternate_config != 'admin') {
                    list($_hybridauth, , $_providers) = initiate_hybridauth_admin(0, $alternate_config, $provider);
                    $service = $this->create_service_row($_hybridauth, $provider, $_providers[$provider], $keep, $alternate_config);
                    $services[$service['LABEL']] = $service;
                }
            }
        }

        ksort($services, SORT_NATURAL | SORT_FLAG_CASE);

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

        return $services;
    }

    protected function create_service_row($hybridauth, $provider, $info, $keep, $alternate_config = null)
    {
        $configured = $info['enabled'];
        try {
            $adapter = $hybridauth->getAdapter($provider);
            $connected = $adapter->isConnected();
        } catch (Exception $e) {
            $configured = false;
            $connected = false;
        }

        if ($configured) {
            $url = find_script('hybridauth_admin') . '?provider=' . urlencode($provider) . '&hybridauth_blank_state=1&alternate_config=admin';
            if ($alternate_config !== null) {
                $url .= '&alternate_config=' . urlencode($alternate_config);
            }
            $url .= $keep->evaluate();
        } else {
            $url = null;
        }

        $config_url = build_url(['page' => 'admin_hybridauth'], get_page_zone('admin_hybridauth', false, 'adminzone', 'minimodules'));

        $label = $info['label'];
        if ($alternate_config !== null) {
            $label .= ' [' . $alternate_config . ']';
        }
        $label .= ' (Hybridauth-driven)';

        return [
            'LABEL' => $label,
            'PROTOCOL' => '',
            'AVAILABLE' => true,

            'CONFIGURED' => $configured,
            'CONFIG_URL' => $config_url,
            'CONNECTED' => $connected,
            'CONNECT_URL' => $url,
            'CLIENT_ID' => isset($info['keys']['id']) ? $info['keys']['id'] : '',
            'CLIENT_SECRET' => isset($info['keys']['secret']) ? $info['keys']['secret'] : '',
            'API_KEY' => isset($info['keys']['key']) ? $info['keys']['key'] : (isset($info['other_parameters']['api_key']) ? $info['other_parameters']['api_key'] : ''),
            'REFRESH_TOKEN' => '',
        ];
    }
}
