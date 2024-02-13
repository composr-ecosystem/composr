<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    twitter_support
 */

/**
 * Hook class.
 */
class Hook_config_twitter_allow_signups
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'TWITTER_ALLOW_SIGNUPS',
            'type' => 'tick',
            'category' => 'COMPOSR_APIS',
            'group' => 'TWITTER_OAUTH',
            'explanation' => 'CONFIG_OPTION_twitter_allow_signups',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,
            'required' => true,
            'public' => false,
            'addon' => 'twitter_support',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('twitter_support')) {
            return null;
        }

        return '0';
    }
}
