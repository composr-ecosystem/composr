<?php

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Jason L Verhagen (jlverhagen@tfo.net)
 * @package    twitter_support
 */

/**
 * Hook class.
 */
class Hook_config_twitterfeed_update_time
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'TWITTER_FEED_UPDATE_TIME',
            'type' => 'integer',
            'category' => 'CMS_APIS',
            'group' => 'TWITTER_FEED_INTEGRATION',
            'explanation' => 'CONFIG_OPTION_twitterfeed_update_time',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
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

        return '30';
    }
}
