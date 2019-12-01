<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_config_search_iotds
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return [
            'human_name' => 'DEFAULT_SEARCH_IOTDS',
            'type' => 'tick',
            'category' => 'SEARCH',
            'group' => 'SEARCH_DEFAULTS',
            'explanation' => 'CONFIG_OPTION_search_iotds',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,

            'addon' => 'iotds',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!addon_installed('iotds')) {
            return null;
        }

        if (!addon_installed('search')) {
            return null;
        }

        return '1';
    }
}
