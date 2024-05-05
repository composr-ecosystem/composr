<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_config_ios_cert_passphrase
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'IOS_CERT_PASSPHRASE',
            'type' => 'line',
            'category' => 'CMS_APIS',
            'group' => 'COMPOSR_MOBILE_SDK',
            'explanation' => 'CONFIG_OPTION_ios_cert_passphrase',
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
    public function get_default() : ?string
    {
        if (!addon_installed('composr_mobile_sdk')) {
            return null;
        }

        return '';
    }
}
