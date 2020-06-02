<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_config_system_community_billboard
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return [
            'human_name' => 'SYSTEM_COMMUNITY_BILLBOARD',
            'type' => 'comcodeline',
            'category' => 'FEATURE',
            'group' => 'COMMUNITY_BILLBOARD',
            'explanation' => 'CONFIG_OPTION_system_community_billboard',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,

            'addon' => 'community_billboard',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!addon_installed('community_billboard')) {
            return null;
        }

        return '';
    }
}
