<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_config_points_CHOOSE_IOTD
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'CHOOSE_IOTD',
            'type' => 'integer',
            'category' => 'POINTS',
            'group' => 'COUNT_POINTS_GIVEN',
            'explanation' => 'CONFIG_OPTION_points_CHOOSE_IOTD',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'iotds',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return addon_installed('points') ? '35' : null;
    }
}
