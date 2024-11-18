<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_config_karma_points_idolisr
{
    /**
     * Gets the details relating to the config option.
     *
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'KARMA_POINTS_IDOLISR',
            'type' => 'tick',
            'category' => 'FEATURE',
            'group' => 'KARMA',
            'explanation' => 'CONFIG_OPTION_karma_points_idolisr',
            'shared_hosting_restricted' => '0',
            'order_in_category_group' => 8,
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'karma',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('karma') || get_forum_type() != 'cns') {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }

        if (!addon_installed('idolisr')) {
            return null;
        }

        return '0';
    }
}
