<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_config_syndicate_site_activity_default
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'SYNDICATE_SITE_ACTIVITY_DEFAULT',
            'type' => 'line',
            'category' => 'COMPOSR_APIS',
            'group' => 'ACTIVITY',
            'explanation' => 'CONFIG_OPTION_syndicate_site_activity_default',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,

            'addon' => 'activity_feed',
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
