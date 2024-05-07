<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_config_points_support_credits
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'POINTS_SUPPORT_CREDITS',
            'type' => 'integer',
            'category' => 'POINTS',
            'group' => 'CUSTOMERS',
            'explanation' => 'CONFIG_OPTION_points_support_credits',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'cms_homesite_support_credits',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cms_homesite_support_credits') || !addon_installed('points')) {
            return null;
        }

        return '50';
    }
}
