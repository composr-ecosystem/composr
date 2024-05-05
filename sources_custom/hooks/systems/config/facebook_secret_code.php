<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_config_facebook_secret_code
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'FACEBOOK_SECRET',
            'type' => 'line',
            'category' => 'CMS_APIS',
            'group' => 'FACEBOOK',
            'explanation' => 'CONFIG_OPTION_facebook_secret_code',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,
            'required' => false,
            'public' => false,
            'addon' => 'facebook_support',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('facebook_support')) {
            return null;
        }

        return '';
    }
}
