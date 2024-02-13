<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    google_analytics
 */

/**
 * Hook class.
 */
class Hook_config_ga_property_view_id
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'GA_PROPERTY_VIEW_ID',
            'type' => 'line',
            'category' => 'SITE',
            'group' => 'LOGGING',
            'explanation' => 'CONFIG_OPTION_ga_property_view_id',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 7,
            'required' => false,
            'public' => false,
            'addon' => 'google_analytics',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('google_analytics')) {
            return null;
        }

        return '';
    }
}
