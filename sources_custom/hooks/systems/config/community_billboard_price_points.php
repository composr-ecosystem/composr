<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_config_community_billboard_price_points
{
    /**
     * Gets the details relating to the config option.
     *
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'PRICE_community_billboard_price_points',
            'type' => 'integer',
            'category' => 'ECOMMERCE_PRODUCTS',
            'group' => 'COMMUNITY_BILLBOARD_MESSAGE',
            'explanation' => 'CONFIG_OPTION_community_billboard_price_points',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,
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
    public function get_default() : ?string
    {
        if (!addon_installed('community_billboard')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }
        return '200';
    }
}
