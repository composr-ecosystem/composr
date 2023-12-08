<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_config_enable_notifications_instant_ios
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'NOTIFICATIONS_INSTANT_IOS',
            'type' => 'tick',
            'category' => 'COMPOSR_APIS',
            'group' => 'COMPOSR_MOBILE_SDK',
            'explanation' => 'CONFIG_OPTION_enable_notifications_instant_ios',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'composr_mobile_sdk',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            return null;
        }

        if (!is_file(get_custom_file_base() . '/data_custom/modules/ios/server_certificates.pem')) {
            return null;
        }

        return '0';
    }
}
