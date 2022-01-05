<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/**
 * Hook class.
 */
class Hook_config_hybridauth_sync_email
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'HYBRIDAUTH_SYNC_EMAIL',
            'type' => 'tick',
            'category' => 'COMPOSR_APIS',
            'group' => 'HYBRIDAUTH',
            'explanation' => 'CONFIG_OPTION_hybridauth_sync_email',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 7,
            'required' => true,
            'public' => false,
            'addon' => 'hybridauth',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('hybridauth')) {
            return null;
        }

        return '0';
    }
}
