<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    idolisr
 */

/**
 * Hook class.
 */
class Hook_config_idolisr_roles
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'IDOLISR_ROLES',
            'type' => 'line',
            'category' => 'POINTS',
            'group' => 'IDOLISR',
            'explanation' => 'CONFIG_OPTION_idolisr_roles',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'idolisr',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('idolisr') || (!addon_installed('points'))) {
            return null;
        }

        return 'Helpful soul,Support expert,Programming god,Themeing genius,Community ambassador';
    }
}
