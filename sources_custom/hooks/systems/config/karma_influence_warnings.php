<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_config_karma_influence_warnings
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'KARMA_INFLUENCE_WARNINGS',
            'type' => 'integer',
            'category' => 'FEATURE',
            'group' => 'KARMIC_INFLUENCE',
            'explanation' => 'CONFIG_OPTION_karma_influence_warnings',
            'shared_hosting_restricted' => '0',
            'order_in_category_group' => 7,
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

        if (!addon_installed('cns_warnings')) {
            return null;
        }

        return '90';
    }
}
