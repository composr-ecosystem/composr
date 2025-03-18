<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_gitlab
 */

/**
 * Hook class.
 */
class Hook_config_gitlab_webhook_secret
{
    /**
     * Gets the details relating to the config option.
     *
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'GITLAB_WEBHOOK_SECRET',
            'type' => 'line',
            'category' => 'CMS_APIS',
            'group' => 'GITLAB',
            'explanation' => 'CONFIG_OPTION_gitlab_webhook_secret',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'cms_homesite_gitlab',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cms_homesite_gitlab')) {
            return null;
        }

        return '';
    }
}
