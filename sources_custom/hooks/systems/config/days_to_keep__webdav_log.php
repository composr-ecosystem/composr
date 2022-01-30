<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    webdav
 */

/**
 * Hook class.
 */
class Hook_config_days_to_keep__webdav_log
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'DAYS_TO_KEEP__WEBDAV_LOG',
            'type' => 'integer',
            'category' => 'PRIVACY',
            'group' => 'LOGS',
            'explanation' => 'CONFIG_OPTION_days_to_keep__webdav_log',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'webdav',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('webdav')) {
            return null;
        }

        if (!CMSLoggers::webdav()->is_active()) {
            return null;
        }
        return '365';
    }
}
