<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'PRICE_community_billboard_price_points',
            'type' => 'integer',
            'category' => 'ECOMMERCE',
            'group' => 'COMMUNITY_BILLBOARD_MESSAGE',
            'explanation' => 'CONFIG_OPTION_community_billboard_price_points',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,
            'required' => false,
            'public' => false,

            'addon' => 'community_billboard',
        );
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

        if (!addon_installed('points')) {
            return null;
        }
        return '200';
    }
}
