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
class Hook_config_karma_ecommerce
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'KARMA_ECOMMERCE',
            'type' => 'float',
            'category' => 'FEATURE',
            'group' => 'KARMA',
            'explanation' => 'CONFIG_OPTION_karma_ecommerce',
            'shared_hosting_restricted' => '0',
            'order_in_category_group' => 4,
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

        if (!addon_installed('ecommerce')) {
            return null;
        }

        return '1.00';
    }
}
