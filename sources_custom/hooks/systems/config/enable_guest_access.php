<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_config_enable_guest_access
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'TAPATALK_ENABLE_GUEST_ACCESS',
            'type' => 'tick',
            'category' => 'COMPOSR_APIS',
            'group' => 'TAPATALK',
            'explanation' => 'CONFIG_OPTION_enable_guest_access',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'cns_tapatalk',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cns_tapatalk')) {
            return null;
        }

        return '0';
    }
}
