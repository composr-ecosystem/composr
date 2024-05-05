<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    confluence
 */

/**
 * Hook class.
 */
class Hook_config_confluence_cache_time
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'CONFLUENCE_CACHE_TIME',
            'type' => 'integer',
            'category' => 'CMS_APIS',
            'group' => 'CONFLUENCE',
            'explanation' => 'CONFIG_OPTION_confluence_cache_time',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 5,
            'required' => true,
            'public' => false,
            'addon' => 'confluence',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('confluence')) {
            return null;
        }

        return '5';
    }
}
