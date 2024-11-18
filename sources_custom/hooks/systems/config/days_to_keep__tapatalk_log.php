<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_config_days_to_keep__tapatalk_log
{
    /**
     * Gets the details relating to the config option.
     *
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'DAYS_TO_KEEP__TAPATALK_LOG',
            'type' => 'integer',
            'category' => 'PRIVACY',
            'group' => 'LOGS',
            'explanation' => 'CONFIG_OPTION_days_to_keep__tapatalk_log',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'cns_tapatalk',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cns_tapatalk')) {
            return null;
        }

        if (!is_file(get_custom_file_base() . '/data_custom/tapatalk.log')) {
            return null;
        }
        return '365';
    }
}
