<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_config_patreon_webhook_secret
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'PATREON_WEBHOOK_SECRET',
            'type' => 'line',
            'category' => 'COMPOSR_APIS',
            'group' => 'PATREON',
            'explanation' => 'CONFIG_OPTION_patreon_webhook_secret',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,
            'required' => false,
            'public' => false,
            'addon' => 'patreon',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('patreon')) {
            return null;
        }

        return '';
    }
}
