<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    weather
 */

/**
 * Hook class.
 */
class Hook_config_openweathermap_api_key
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'OPENWEATHERMAP_API_KEY',
            'type' => 'line',
            'category' => 'CMS_APIS',
            'group' => 'WEATHER_REPORT',
            'explanation' => 'CONFIG_OPTION_openweathermap_api_key',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'weather',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('weather')) {
            return null;
        }

        return '';
    }
}
