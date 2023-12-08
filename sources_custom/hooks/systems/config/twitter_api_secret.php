<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    twitter_support
 */

/**
 * Hook class.
 */
class Hook_config_twitter_api_secret
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'TWITTER_API_SECRET',
            'type' => 'line',
            'category' => 'COMPOSR_APIS',
            'group' => 'TWITTER_SYNDICATION',
            'explanation' => 'CONFIG_OPTION_twitter_api_secret',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,

            'addon' => 'twitter_support',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '';
    }
}
