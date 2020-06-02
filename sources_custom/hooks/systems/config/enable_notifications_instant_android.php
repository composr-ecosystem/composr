<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_config_enable_notifications_instant_android
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return [
            'human_name' => 'NOTIFICATIONS_INSTANT_ANDROID',
            'type' => 'line',
            'category' => 'COMPOSR_APIS',
            'group' => 'COMPOSR_MOBILE_SDK',
            'explanation' => 'CONFIG_OPTION_enable_notifications_instant_android',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,

            'addon' => 'composr_mobile_sdk',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return null;
        }

        return '';
    }
}
